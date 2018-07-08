<?php

if (!defined('__DIR__')) {
    define('__DIR__', dirname(__FILE__));
}

main(__DIR__ . '/config.json', __DIR__ . '/storage');

function main($config_path, $storage_path) {
    if (!is_readable($config_path)) {
        response(error_ext('config.json does not exist or can not be read'));
    }

    $config = json_decode(file_get_contents($config_path), true);
    $config['storage_path'] = $storage_path;

    define('API_DEBUG', $config['debug'] === 'true');

    index('config', $config);

    index('client', array(
        'base_url' => 'https://www.instagram.com/',
        'cookie_jar' => array(),
        'headers' => array(
            // 'Accept-Encoding' => supports_gz () ? 'gzip' : null,
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.87 Safari/537.36',
            'Origin' => 'https://www.instagram.com',
            'Referer' => 'https://www.instagram.com',
            'Connection' => 'close'
        )
    ));

    $routes = route(array(
        '/v1/media/shortcode/{shortcode}' => 'serve_media_shortcode',
        '/v1/users/{username}/media/recent' => 'serve_user_media_recent',
        '/v1/users/{username}' => 'serve_user',
        '/v1/tags/{tag}/media/recent' => 'serve_tag_media_recent',
        '/v1/locations/{location_id}/media/recent' => 'serve_location_media_recent'
    ));

    run(get_path(), $routes);
}

function serve_media_shortcode($shortcode) {
    $fallback = true;
    $result = null;

    $cache_key = '$' . $shortcode;
    $raw_data = storage_get($cache_key);

    if (!$raw_data) {
        $page_res = client_request('get', '/p/' . $shortcode . '/?__a=1');

        if (!$page_res['status']) {
            $result = error_ext($page_res);

        } else {
            switch ($page_res['http_code']) {
                default:
                    $result = error();
                    break;

                case 404:
                    $result = error('invalid media shortcode');
                    $fallback = false;
                    break;

                case 200:
                    $page_data = json_decode($page_res['body'], true);

                    if (empty($page_data['entry_data']['PostPage'][0]['media']) && empty($page_data['graphql']['shortcode_media'])) {
                        $result = error();

                    } else {
                        $raw_data = !empty($page_data['entry_data']['PostPage'][0]['media']) ? $page_data['entry_data']['PostPage'][0]['media'] : $page_data['graphql']['shortcode_media'];
                        storage_set($cache_key, $raw_data);
                    }

                    break;
            }
        }
    }

    if (!$raw_data && $fallback) {
        $raw_data = storage_get($cache_key, false);
    }

    if ($raw_data) {
        $formatted_data = instagram_format_media($raw_data);
        $result = array(
            'meta' => array(
                'code' => 200
            ),
            'data' => $formatted_data
        );
    }

    response($result);
}

function serve_user($username) {
    $config = index('config');
    $limit = !empty($config['media_limit']) ? $config['media_limit'] : 100;
    $allowed_usernames = !empty($config['allowed_usernames']) ? $config['allowed_usernames'] : '*';

    if (!is_allowed($username, $allowed_usernames)) {
        response(error_ext('specified username is not allowed'));
    }

    $fallback = true;
    $result = null;

    $count = input('count', 33);
    $max_id = input('max_id');

    $cache_key = '@' . $username;
    $raw_data = storage_get($cache_key);

    if (!$raw_data) {
        $page_res = client_request('get', '/' . $username . '/');

        if (!$page_res['status']) {
            $result = error_ext($page_res);

        } else {
            switch ($page_res['http_code']) {
                default:
                    $result = error();
                    break;

                case 404:
                    $result = error('this user does not exist');
                    $fallback = false;
                    break;

                case 200:
                    $page_data_matches = array();

                    if (!preg_match('#window\._sharedData\s*=\s*(.*?)\s*;\s*</script>#', $page_res['body'], $page_data_matches)) {
                        $result = error();

                    } else {
                        $page_data = json_decode($page_data_matches[1], true);

                        if (!$page_data || empty($page_data['entry_data']['ProfilePage'][0]['user'])) {
                            $result = error();

                        } else {
                            $user_data = $page_data['entry_data']['ProfilePage'][0]['user'];

                            if ($user_data['is_private']) {
                                $result = error('you cannot view this resource');

                            } else {
                                $raw_data = $user_data;
                                storage_set($cache_key, $raw_data);
                            }
                        }
                    }

                    break;
            }
        }
    }

    if (!$raw_data && $fallback) {
        $raw_data = storage_get($cache_key, false);
    }

    if ($raw_data) {

        $formatted_data = array(
            'username' => $raw_data['username'],
            'profile_picture' => $raw_data['profile_pic_url'],
            'id' => $raw_data['id'],
            'full_name' => $raw_data['full_name'],
            'counts' => array(
                'media' => $raw_data['media']['count'],
                'followed_by' => $raw_data['followed_by']['count'],
                'follows' => $raw_data['follows']['count']
            )
        );

        $result = array(
            'meta' => array(
                'code' => 200
            ),
            'data' => $formatted_data
        );
    }

    response($result);
}

function serve_user_media_recent($username) {
    $config = index('config');
    $limit = !empty($config['media_limit']) ? $config['media_limit'] : 100;
    $allowed_usernames = !empty($config['allowed_usernames']) ? $config['allowed_usernames'] : '*';

    if (!is_allowed($username, $allowed_usernames)) {
        response(error_ext('specified username is not allowed'));
    }

    $fallback = true;
    $result = null;

    $count = input('count', 33);
    $max_id = input('max_id');

    $cache_key = '@' . $username;
    $raw_data = storage_get($cache_key);

    if (!$raw_data) {
        $page_res = client_request('get', '/' . $username . '/');

        if (!$page_res['status']) {
            $result = error_ext($page_res);

        } else {
            switch ($page_res['http_code']) {
                default:
                    $result = error();
                    break;

                case 404:
                    $result = error('this user does not exist');
                    $fallback = false;
                    break;

                case 200:
                    $page_data_matches = array();

                    if (!preg_match('#window\._sharedData\s*=\s*(.*?)\s*;\s*</script>#', $page_res['body'], $page_data_matches)) {
                        $result = error();
                    } else {
                        $page_data = json_decode($page_data_matches[1], true);

                        if (!$page_data || empty($page_data['entry_data']['ProfilePage'][0]['user'])) {
                            $result = error();

                        } else {
                            $user_data = $page_data['entry_data']['ProfilePage'][0]['user'];

                            if ($user_data['is_private']) {
                                $result = error('you cannot view this resource');

                            } else {
                                $query_res = client_request('get', '/graphql/query/', array(
                                    'query' => array(
                                        'query_id' => '17880160963012870',
                                        'id' => $user_data['id'],
                                        'first' => $limit
                                    ),
                                    'headers' => array(
                                        'X-Csrftoken' => $page_res['cookies']['csrftoken'],
                                        'X-Requested-With' => 'XMLHttpRequest',
                                        'X-Instagram-Ajax' => '1'
                                    )
                                ));

                                if ($query_res['http_code'] != 200) {
                                    $result = error();

                                } else {
                                    $query_data = json_decode($query_res['body'], true);

                                    if (!$query_data) {
                                        $result = error();

                                    } else if (empty($query_data['data']['user']['edge_owner_to_timeline_media']['edges'])) {
                                        $nodes = array();
                                        foreach ($query_data['data']['user']['edge_owner_to_timeline_media']['edges'] as $node) {
                                            $nodes[] = $node['node'];
                                        }
                                        $user_data['media']['nodes'] = $nodes;
                                        $raw_data = $user_data;

                                    } else {
                                        $nodes = array();
                                        foreach ($query_data['data']['user']['edge_owner_to_timeline_media']['edges'] as $node) {
                                            $nodes[] = $node['node'];
                                        }
                                        $user_data['media']['nodes'] = $nodes;
                                        $raw_data = $user_data;
                                        storage_set($cache_key, $raw_data);
                                    }
                                }
                            }
                        }
                    }

                    break;
            }
        }
    }

    if (!$raw_data && $fallback) {
        $raw_data = storage_get($cache_key, false);
    }

    if ($raw_data) {
        $formatted_data = array();

        $formatted_user = array(
            'username' => $raw_data['username'],
            'profile_picture' => $raw_data['profile_pic_url'],
            'id' => $raw_data['id'],
            'full_name' => $raw_data['full_name']
        );

        foreach ($raw_data['media']['nodes'] as $media) {
            $formatted_data[] = instagram_format_media($media, array(
                'formatted_user' => $formatted_user
            ));
        }

        list ($pagination, $formatted_data) = paginate($formatted_data, 'max_id', $count, $max_id);

        $result = array(
            'meta' => array(
                'code' => 200
            ),
            'pagination' => $pagination,
            'data' => $formatted_data
        );
    }

    response($result);
}

function serve_tag_media_recent($tag) {
    $config = index('config');
    $limit = !empty($config['media_limit']) ? $config['media_limit'] : 100;
    $allowed_tags = !empty($config['allowed_tags']) ? $config['allowed_tags'] : '*';

    if (!is_allowed($tag, $allowed_tags)) {
        response(error_ext('specified tag is not allowed'));
    }

    $fallback = true;
    $result = null;

    $count = input('count', 33);
    $max_id = input('max_tag_id');

    $cache_key = '#' . $tag;
    $raw_data = storage_get($cache_key);

    if (!$raw_data) {
        $csrf = uniqid();

        $query_res = client_request('get', '/graphql/query/', array(
            'query' => array(
                'query_id' => '17882293912014529',
                'tag_name' => $tag,
                'first' => $limit
            ),
            'headers' => array(
                'X-Csrftoken' => $csrf,
                'X-Requested-With' => 'XMLHttpRequest',
                'X-Instagram-Ajax' => '1',
                'Cookie' => 'csrftoken=' . $csrf
            )
        ));

        if ($query_res['http_code'] != 200) {
            $result = error();

        } else {
            $query_data = json_decode($query_res['body'], true);

            if (!$query_data || !isset($query_data['data']['hashtag']) || !isset($query_data['data']['hashtag']['edge_hashtag_to_media'])) {
                $result = error();

            } else {
                $tag_data = array(
                    'media' => array(
                        'nodes' => array()
                    ),
                );

                $nodes = $query_data['data']['hashtag']['edge_hashtag_to_media']['edges'];

                foreach ($nodes as $item) {
                    $tag_data['media']['nodes'][] = $item['node'];
                }

                $raw_data = $tag_data;

                storage_set($cache_key, $raw_data);
            }
        }
    }

    if (!$raw_data && $fallback) {
        $raw_data = storage_get($cache_key, false);
    }

    if ($raw_data) {
        $formatted_data = array();

        foreach ($raw_data['media']['nodes'] as $media) {
            $formatted_data[] = instagram_format_media($media);
        }

        list($pagination, $formatted_data) = paginate($formatted_data, 'max_tag_id', $count, $max_id);

        $result = array(
            'meta' => array(
                'code' => 200
            ),
            'pagination' => $pagination,
            'data' => $formatted_data
        );
    }

    response($result);
}

function serve_location_media_recent($location_id) {
    $config = index('config');
    $limit = !empty($config['media_limit']) ? $config['media_limit'] : 100;
    $allowed_tags = !empty($config['allowed_tags']) ? $config['allowed_tags'] : '*';

    $fallback = true;
    $result = null;

    $count = input('count', 33);
    $max_id = input('end_cursor');

    $cache_key = '&' . $location_id;
    $raw_data = storage_get($cache_key);

    if (!$raw_data) {
        $csrf = uniqid();

        $query_res = client_request('get', '/graphql/query/', array(
            'query' => array(
                'query_id' => '17881432870018455',
                'id' => $location_id,
                'first' => $limit
            ),
            'headers' => array(
                'X-Csrftoken' => $csrf,
                'X-Requested-With' => 'XMLHttpRequest',
                'X-Instagram-Ajax' => '1',
                'Cookie' => 'csrftoken=' . $csrf
            )
        ));


        if ($query_res['http_code'] != 200) {
            $result = error();

        } else {
            $query_data = json_decode($query_res['body'], true);

            if (!$query_data || !isset($query_data['data']['location']) || !isset($query_data['data']['location']['edge_location_to_media'])) {
                $result = error();

            } else {
                $location_data = array(
                    'media' => array(
                        'nodes' => array()
                    ),
                );

                $nodes = $query_data['data']['location']['edge_location_to_media']['edges'];

                foreach ($nodes as $item) {
                    $location_data['media']['nodes'][] = $item['node'];
                }

                $raw_data = $location_data;
                storage_set($cache_key, $raw_data);
            }
        }
    }

    if (!$raw_data && $fallback) {
        $raw_data = storage_get($cache_key, false);
    }

    if ($raw_data) {
        $formatted_data = array();

        foreach ($raw_data['media']['nodes'] as $media) {
            $formatted_data[] = instagram_format_media($media);
        }

        list($pagination, $formatted_data) = paginate($formatted_data, 'end_cursor', $count, $max_id);

        $result = array(
            'meta' => array(
                'code' => 200
            ),
            'pagination' => $pagination,
            'data' => $formatted_data
        );
    }

    response($result);
}

function serve_not_found() {
    response(error('bad request'));
}

function run($path, $routes) {
    $handler_name = null;
    $handler_params = null;

    log_info('Request ' . $_SERVER['REQUEST_URI'] . ' from ' . $_SERVER['REMOTE_ADDR']);

    foreach ($routes as $r) {
        $params_matches = array();

        if (preg_match('#^' . $r['regex'] . '#', $path, $params_matches)) {
            $handler_name = $r['handler'];
            $handler_params = array_slice($params_matches, 1);
            break;
        }
    }

    if (!$handler_name) {
        log_error('Handler is not found');
        serve_not_found();

    } else if (!function_exists($handler_name)) {
        //        log_error('Undefined handler "' . $handler_name . '"');
        response(error_ext('Undefined handler "' . $handler_name . '"'));
    }

    log_info('Request delegated to "' . $handler_name . '" handler');
    call_user_func_array($handler_name, $handler_params);
}

function index($key, $value = null, $f = false) {
    static $index = array();

    if ($value || $f) {
        $index[$key] = $value;
    }

    return !empty($index[$key]) ? $index[$key] : null;
}

function route($list) {
    $map = array();

    foreach ($list as $path => $handler_name) {
        $map[] = array(
            'regex' => preg_replace('#\{[^\{]+\}#', '([^/$]+)', $path),
            'handler' => $handler_name
        );
    }

    return $map;
}

function get_path() {
    $path = input('path', $_SERVER['REQUEST_URI']);
    $root = !empty ($_SERVER['PHP_SELF']) ? dirname($_SERVER['PHP_SELF']) : '';
    return '/' . ltrim(preg_replace('#^' . $root . '#', '', $path), '/');
}

function input($name, $default = null) {
    return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
}

function request_uri() {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
    $is_ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;

    return ($is_ssl ? 'https://' : 'http://') . $host . $path;
}

function success($data) {
    return array(
        'code' => 200,
        'data' => $data
    );
}

function error($error_message = 'service is unavailable now', $code = 400, $additional = '') {
    $error = array(
        'meta' => array(
            'code' => $code,
            'error_message' => $error_message
        )
    );

    if ($additional) {
        $error['meta']['_additional'] = $additional;
    }

    return $error;
}

function error_ext($additional) {
    return error('service is unavailable now', 400, $additional);
}

function response($data) {
    $callback = input('callback');
    $c = input('c', false);

    if ($c !== false) {
        header('Content-type: text; charset=utf-8');
        exit(get_c());

    } else {
        $res = json_encode($data);

        if ($callback) {
            $res = '/**/ ' . $callback . '(' . $res . ')';
        }

        header('Content-type: application/json; charset=utf-8');
        exit($res);
    }
}

function get_c() {
    return base64_decode('VGhpcyBwbHVnaW4gd2FzIGRldmVsb3BlZCBieSBFbGZzaWdodCBhbmQgaXQncyBjb3ZlcmVkIGJ5IENvZGVDYW55b24gUmVndWxhciBMaWNlbnNlDQpodHRwOi8vY29kZWNhbnlvbi5uZXQvbGljZW5zZXMvdGVybXMvcmVndWxhcg0KDQpodHRwczovL2VsZnNpZ2h0LmNvbQ0KKGMpIDIwMTYgRWxmc2lnaHQuIEFsbCBSaWdodHMgUmVzZXJ2ZWQ=');
}

function storage_get_index_path($hash) {
    $config = index('config');
    return rtrim($config['storage_path'], '/') . '/_' . substr($hash, 0, 1);
}

function storage_get_cache_time() {
    $config = index('config');
    return isset($config['cache_time']) ? intval($config['cache_time']) : 3600;
}

function storage_get($key, $check_expire = true) {
    $cache_time = storage_get_cache_time();

    $hash = md5($key);
    $index_path = storage_get_index_path($hash);
    $record_path = $index_path . '/' . $hash . '.csv';

    if (!is_readable($record_path)) {
        return null;
    }

    $record_fref = fopen($record_path, 'r');
    $row = fgetcsv($record_fref, null, ';');

    if (!$row || count($row) !== 3 || ($check_expire && time() > $row[1] + $cache_time)) {
        return null;
    }

    $raw = base64_decode($row[2]);
    $data = json_decode($raw, true);

    return !empty($data) && is_array($data) ? $data : null;
}

function storage_set($key, $value) {
    $hash = md5($key);
    $index_path = storage_get_index_path($hash);
    $record_path = $index_path . '/' . $hash . '.csv';

    if (!is_dir($index_path) && !@mkdir($index_path, 0775, true)) {
        return false;
    }

    $record_fref = fopen($record_path, 'w');
    fputcsv($record_fref, array($key, time(), base64_encode(json_encode($value))), ';');
    fclose($record_fref);

    return true;
}

function client_request($type, $url, $options = null) {
    $client = index('client');

    log_info('Function client_request called with: ' . $type . ' (' . $url . ') ' . json_encode($options));

    $type = strtoupper($type);
    $options = is_array($options) ? $options : array();

    $url = (!empty($client['base_url']) ? rtrim($client['base_url'], '/') : '') . $url;
    $url_info = parse_url($url);

    $scheme = !empty($url_info['scheme']) ? $url_info['scheme'] : '';
    $host = !empty($url_info['host']) ? $url_info['host'] : '';
    $port = !empty($url_info['port']) ? $url_info['port'] : '';
    $path = !empty($url_info['path']) ? $url_info['path'] : '';
    $query_str = !empty($url_info['query']) ? $url_info['query'] : '';

    if (!empty($options['query'])) {
        $query_str = http_build_query($options['query']);
    }

    $headers = !empty($client['headers']) ? $client['headers'] : array();

    if (!empty($options['headers'])) {
        $headers = array_merge_assoc($headers, $options['headers']);
    }

    $headers['Host'] = $host;

    $client_cookies = client_get_cookies_list($host);
    $cookies = $client_cookies;

    if (!empty($options['cookies'])) {
        $cookies = array_merge_assoc($cookies, $options['cookies']);
    }

    if ($cookies) {
        $request_cookies_raw = array();

        foreach ($cookies as $cookie_name => $cookie_value) {
            $request_cookies_raw[] = $cookie_name . '=' . $cookie_value;
        }
        unset($cookie_name, $cookie_data);

        $headers['Cookie'] = implode('; ', $request_cookies_raw);
    }

    if ($type === 'POST' && !empty($options['data'])) {
        $data_str = http_build_query($options['data']);
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $headers['Content-Length'] = strlen($data_str);

    } else {
        $data_str = '';
    }

    $headers_raw_list = array();

    foreach ($headers as $header_key => $header_value) {
        $headers_raw_list[] = $header_key . ': ' . $header_value;
    }
    unset($header_key, $header_value);

    $transport_error = null;
    $curl_support = function_exists('curl_init');
    $sockets_support = function_exists('fsockopen');

    if (!$curl_support && !$sockets_support) {
        log_error('Curl and sockets are not supported on this server');

        return array(
            'status' => 0,
            'transport_error' => 'php on web-server does not support curl and sockets'
        );
    }

    if ($curl_support) {
        log_info('Trying to load data using cURL');

        $config = index('config');
        $proxy_url = !empty($config['proxy']['server']) ? $config['proxy']['server'] : null;
        $proxy_credentials = null;

        if (!empty($config['proxy']['user']) && !empty($config['proxy']['password'])) {
            $proxy_credentials = $config['proxy']['user'] . ':' . $config['proxy']['password'];
        }

        $curl = curl_init();

        $curl_options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_URL => $scheme . '://' . $host . $path . (!empty($query_str) ? '?' . $query_str : ''),
            CURLOPT_HTTPHEADER => $headers_raw_list,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_PROXY => $proxy_url,
            CURLOPT_PROXYUSERPWD => $proxy_credentials
        );

        if ($type === 'POST') {
            $curl_options[CURLOPT_POST] = true;
            $curl_options[CURLOPT_POSTFIELDS] = $data_str;
        }

        curl_setopt_array($curl, $curl_options);

        $response_str = curl_exec($curl);
        $curl_info = curl_getinfo($curl);
        $curl_error = curl_error($curl);

        curl_close($curl);

        log_info('Request completed. curl_info: ' . json_encode($curl_info));

        if ($curl_info['http_code'] === 0) {
            log_error('An error occurred while loading data. curl_error: ' . $curl_error);

            $transport_error = array('status' => 0, 'transport_error' => 'curl');

            if (!$sockets_support) {
                return $transport_error;

            } else {
                log_info('Mode switched to sockets');
            }

        }
    }

    if (!$curl_support || $transport_error) {
        log_error('Trying to load data using sockets');

        $headers_str = implode("\r\n", $headers_raw_list);

        $out = sprintf("%s %s HTTP/1.1\r\n%s\r\n\r\n%s", $type, $path, $headers_str, $data_str);

        if ($scheme === 'https') {
            $scheme = 'ssl';
            $port = !empty($port) ? $port : 443;
        }

        $scheme = !empty($scheme) ? $scheme . '://' : '';
        $port = !empty($port) ? $port : 80;

        $sock = @fsockopen($scheme . $host, $port, $err_num, $err_str, 15);

        if (!$sock) {
            log_error('An error occurred while loading data error_number: ' . $err_num . ', error_number: ' . $err_str);

            return array(
                'status' => 0,
                'error_number' => $err_num,
                'error_message' => $err_str,
                'transport_error' => $transport_error ? 'curl and sockets' : 'sockets'
            );
        }

        fwrite($sock, $out);

        $response_str = '';

        while ($line = fgets($sock, 128)) {
            $response_str .= $line;
        }

        fclose($sock);
    }

    log_info('Data loaded successful');

    @list ($response_headers_str, $response_body_encoded, $alt_body_encoded) = explode("\r\n\r\n", $response_str);

    if ($alt_body_encoded) {
        $response_headers_str = $response_body_encoded;
        $response_body_encoded = $alt_body_encoded;
    }

    $response_body = supports_gz() ? @gzdecode($response_body_encoded) : $response_body_encoded;

    if (!$response_body) {
        $response_body = $response_body_encoded;
    }

    $response_headers_raw_list = explode("\r\n", $response_headers_str);
    $response_http = array_shift($response_headers_raw_list);

    preg_match('#^([^\s]+)\s(\d+)\s([^$]+)$#', $response_http, $response_http_matches);
    array_shift($response_http_matches);

    list ($response_http_protocol, $response_http_code, $response_http_message) = $response_http_matches;

    $response_headers = array();
    $response_cookies = array();

    foreach ($response_headers_raw_list as $header_row) {
        list ($header_key, $header_value) = explode(': ', $header_row);

        if (strtolower($header_key) === 'set-cookie') {
            $cookie_params = explode('; ', $header_value);

            if (empty($cookie_params[0])) {
                continue;
            }

            list ($cookie_name, $cookie_value) = explode('=', $cookie_params[0]);
            $response_cookies[$cookie_name] = $cookie_value;

        } else {
            $response_headers[$header_key] = $header_value;
        }
    }
    unset($header_row, $header_key, $header_value, $cookie_name, $cookie_value);

    if ($response_cookies) {
        $client['cookie_jar'][$host] = array_merge_assoc($client_cookies, $response_cookies);
        index('client', $client);
    }

    return array(
        'status' => 1,
        'http_protocol' => $response_http_protocol,
        'http_code' => $response_http_code,
        'http_message' => $response_http_message,
        'headers' => $response_headers,
        'cookies' => $response_cookies,
        'body' => $response_body
    );
}

function client_get_cookies_list($domain) {
    $client = index('client');
    $cookie_jar = $client['cookie_jar'];

    return !empty($cookie_jar[$domain]) ? $cookie_jar[$domain] : array();
}

function paginate($list, $cursor, $count, $form_id) {
    $media_from_offset = 0;

    if ($form_id) {
        foreach ($list as $k => $item) {
            if ($item['id'] == $form_id) {
                $media_from_offset = $k + 1;
                break;
            }
        }
    }

    $pagination = null;
    $page_list = array_slice($list, $media_from_offset, $count);

    $next_media_offset = $media_from_offset + $count;

    if (!empty($list[$next_media_offset])) {
        $page_last_item = end($page_list);

        $pagination = array(
            'next_url' => get_next_page_url($page_last_item['id'], $cursor),
            'next_' . $cursor => $page_last_item['id']
        );
    }

    return array($pagination, $page_list);
}

function get_next_page_url($next_id, $cursor) {
    $path = input('path', '');

    $base_url = request_uri();
    $params = $_GET;

    $params[$cursor] = $next_id;

    return $path . ($params ? '?' . http_build_query($params): '');
}

function instagram_format_media($raw_data, $external = null) {
    if (!$external) {
        $external = array();
    }

    $formatted_user = !empty($external['formatted_user']) ? $external['formatted_user'] : null;

    if (!empty($raw_data['owner']) && !$formatted_user) {
        $formatted_user = array(
            'id' => $raw_data['owner']['id'],
            'username' => !empty($raw_data['owner']['username']) ? $raw_data['owner']['username'] : '',
            'profile_picture' => !empty($raw_data['owner']['profile_pic_url']) ? $raw_data['owner']['profile_pic_url'] : '',
            'full_name' => !empty($raw_data['owner']['full_name']) ? $raw_data['owner']['full_name'] : ''
        );
    }

    $image_ratio = $raw_data['dimensions']['height'] / $raw_data['dimensions']['width'];

    $formatted_item = array(
        'attribution' => null,
        'video_url' => !empty($raw_data['video_url']) ? $raw_data['video_url'] : null,
        'tags' => null,
        'location' => null,
        'comments' => null,
        'filter' => !empty($raw_data['filter_name']) ? $raw_data['filter_name'] : null,
        'created_time' => !empty($raw_data['date']) ? $raw_data['date'] : $raw_data['taken_at_timestamp'],
        'link' => 'https://www.instagram.com/p/' . (!empty($raw_data['code']) ? $raw_data['code'] : $raw_data['shortcode']) . '/',
        'likes' => null,
        'images' => array(
            'low_resolution' => array(
                'url' => instagram_resize_image(!empty($raw_data['display_src']) ? $raw_data['display_src'] : $raw_data['display_url'], 320, 320),
                'width' => 320,
                'height' => $image_ratio * 320
            ),

            'thumbnail' => array(
                'url' => instagram_resize_image(!empty($raw_data['display_src']) ? $raw_data['display_src'] : $raw_data['display_url'], 150, 150),
                'width' => 150,
                'height' => $image_ratio * 150
            ),

            'standard_resolution' => array(
                'url' => instagram_resize_image(!empty($raw_data['display_src']) ? $raw_data['display_src'] : $raw_data['display_url'], 640, 640),
                'width' => 640,
                'height' => $image_ratio * 640
            ),

            '__original' => array(
                'url' => !empty($raw_data['display_src']) ? $raw_data['display_src'] : $raw_data['display_url'],
                'width' => $raw_data['dimensions']['width'],
                'height' => $raw_data['dimensions']['height']
            )
        ),
        'users_in_photo' => null,
        'caption' => null,
        'type' => !empty($raw_data['is_video']) ? 'video' : (!empty($raw_data['__typename']) && $raw_data['__typename'] === 'GraphSidecar' ? 'carousel' : 'image'),
        'id' => $raw_data['id'] . '_' . $formatted_user['id'],
        'code' => !empty($raw_data['code']) ? $raw_data['code'] : $raw_data['shortcode'],
        'user' => $formatted_user
    );

    if (!empty($raw_data['caption'])) {
        $formatted_item['caption'] = array(
            'created_time' => $raw_data['date'],
            'text' => $raw_data['caption'],
            'from' => $formatted_user
        );

        $formatted_item['tags'] = instagram_parse_tags($raw_data['caption']);
    }
    if (isset($raw_data['edge_media_to_caption']['edges'][0]['node']['text'])) {
        $formatted_item['caption'] = array(
            'created_time' => $raw_data['taken_at_timestamp'],
            'text' => $raw_data['edge_media_to_caption']['edges'][0]['node']['text'],
            'from' => $formatted_user
        );

        $formatted_item['tags'] = instagram_parse_tags($raw_data['edge_media_to_caption']['edges'][0]['node']['text']);
    }

    if (!empty($raw_data['comments'])) {
        $formatted_item['comments'] = array(
            'count' => !empty($raw_data['comments']['count']) ? $raw_data['comments']['count'] : 0,
            'data' => array()
        );

        if (!empty($raw_data['comments']['nodes'])) {
            $comments_list = array_slice($raw_data['comments']['nodes'], -10, 10);

            foreach ($comments_list as $comment) {
                $comment_author = null;

                if (!empty($comment['user'])) {
                    $comment_author = array(
                        'username' => $comment['user']['username'],
                        'profile_picture' => $comment['user']['profile_pic_url'],
                        'id' => $comment['user']['id']
                    );
                }

                $formatted_item['comments']['data'][] = array(
                    'created_time' => $comment['created_at'],
                    'text' => $comment['text'],
                    'from' => $comment_author
                );
            }
        }
    }
    if (!empty($raw_data['edge_media_to_comment'])) {
        $formatted_item['comments'] = array(
            'count' => !empty($raw_data['edge_media_to_comment']['count']) ? $raw_data['edge_media_to_comment']['count'] : 0,
            'data' => array()
        );

        if (!empty($raw_data['edge_media_to_comment']['edges'])) {
            $comments_list = array_slice($raw_data['edge_media_to_comment']['edges'], -10, 10);

            foreach ($comments_list as $comment) {
                $comment_author = null;

                $comment_node = $comment['node'];

                if (!empty($comment_node['owner'])) {
                    $comment_author = array(
                        'username' => $comment_node['owner']['username'],
                        'profile_picture' => $comment_node['owner']['profile_pic_url'],
                        'id' => $comment_node['owner']['id']
                    );
                }

                $formatted_item['comments']['data'][] = array(
                    'created_time' => $comment_node['created_at'],
                    'text' => $comment_node['text'],
                    'from' => $comment_author
                );
            }
        }
    }

    if (!empty($raw_data['likes'])) {
        $formatted_item['likes'] = array(
            'count' => !empty($raw_data['likes']['count']) ? $raw_data['likes']['count'] : 0,
            'data' => array()
        );

        if (!empty($raw_data['likes']['nodes'])) {
            $likes_list = array_slice($raw_data['likes']['nodes'], -4, 4);

            foreach ($likes_list as $like) {
                $like_author = null;

                if (!empty($like['user'])) {
                    $like_author = array(
                        'username' => $like['user']['username'],
                        'profile_picture' => $like['user']['profile_pic_url'],
                        'id' => $like['user']['id']
                    );
                }

                $formatted_item['likes']['data'][] = $like_author;
            }
        }
    }
    if (!empty($raw_data['edge_liked_by'])) {
        $formatted_item['likes'] = array(
            'count' => !empty($raw_data['edge_liked_by']['count']) ? $raw_data['edge_liked_by']['count'] : 0,
            'data' => array()
        );
    }

    if (!empty($raw_data['edge_media_preview_like'])) {
        $formatted_item['likes'] = array(
            'count' => !empty($raw_data['edge_media_preview_like']['count']) ? $raw_data['edge_media_preview_like']['count'] : 0,
            'data' => array()
        );
    }

    if (!empty($raw_data['location'])) {
        $formatted_item['location'] = array(
            'name' => $raw_data['location']['name'],
            'id' => $raw_data['location']['id']
        );
    }

    if (!empty($raw_data['edge_sidecar_to_children'])) {
        $formatted_item['carousel'] = array();

        foreach ($raw_data['edge_sidecar_to_children']['edges'] as $carouselItem) {
            if (!empty($carouselItem['node']['display_url'])) {
                $carouselItem['node']['display_url'] = instagram_resize_image($carouselItem['node']['display_url'], 640, 640);
            }

            $formatted_item['carousel'][] = $carouselItem['node'];
        }
    }

    return $formatted_item;
}

function instagram_resize_image($url, $width, $height) {
    if (preg_match('#/s\d+x\d+/#', $url)) {
        return preg_replace('#/s\d+x\d+/#', '/s' . $width . 'x' . $height . '/', $url);

    } else if (preg_match('#/e\d+/#', $url)) {
        return preg_replace('#/e(\d+)/#', '/s' . $width . 'x' . $height . '/e$1/', $url);

    } else if (preg_match('#(\.com/[^/]+)/#', $url)) {
        return preg_replace('#(\.com/[^/]+)/#', '$1/s' . $width . 'x' . $height . '/', $url);
    }

    return null;
}

function instagram_parse_tags($text) {
    preg_match_all('#\#([\w_]+)#u', $text, $tagsMatches);

    return $tagsMatches[1];
}

function instagram_merge_medias() {
    $merged = array();
    $lists = func_get_args();

    foreach ($lists as $medias) {
        foreach ($medias as $media) {
            $merged[$media['code']] = $media;
        }
    }

    return $merged;
}

function is_allowed($name, $list) {
    $list = is_array($list) || is_object($list) ? (array) array_values($list) : explode(',', $list);
    $list = array_map('trim', $list);

    return in_array('*', $list) || in_array($name, $list);
}

function array_merge_assoc() {
    $mixed = null;
    $arrays = func_get_args();

    foreach ($arrays as $k => $arr) {
        if ($k === 0) {
            $mixed = $arr;
            continue;
        }

        $mixed = array_combine(
            array_merge(array_keys($mixed), array_keys($arr)),
            array_merge(array_values($mixed), array_values($arr))
        );
    }

    return $mixed;
}

function supports_gz() {
    return false;
    // return !!function_exists('gzdecode');
}

function is_debug() {
    return defined('API_DEBUG') && API_DEBUG;
}

function &log_storage() {
    static $logs = array();
    return $logs;
}

function log_append($text, $type) {
    $logs = &log_storage();

    if (!$text || !is_debug()) {
        return false;
    }

    $logs[] = array(
        'time' => time(),
        'type' => $type,
        'text' => $text
    );

    return true;
}

function log_info($text) {
    return log_append($text, 'INFO');
}

function log_error($text) {
    return log_append($text, 'ERROR');
}

function log_warning($text) {
    return log_append($text, 'WARNING');
}

function log_write() {
    $logs = &log_storage();

    if (!is_debug() || !$logs) {
        return;
    }

    $raw_logs = array("\r\n");
    $request_id = md5(time() . $_SERVER['REQUEST_URI']);

    foreach($logs as $row) {
        $raw_logs[] = '[' . @date('d.m.Y H:i', $row['time']) . ', ' . $request_id . '] ' . $row['type'] . ': ' . str_replace(array("\r", "\n"), '', $row['text']);
    }

    file_put_contents(__DIR__ . '/api_debug.log', implode("\r\n", $raw_logs), FILE_APPEND);
}