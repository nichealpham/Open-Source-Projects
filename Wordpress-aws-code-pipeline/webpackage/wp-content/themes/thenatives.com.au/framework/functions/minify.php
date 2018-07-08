<?php

if(!is_user_logged_in() || !current_user_can('administrator')) {
    remove_action('wp_head', 'wp_print_styles', 8);
    remove_action('wp_head', 'wp_print_head_scripts', 9);
    remove_action('wp_footer', 'wp_print_footer_scripts', 20);
    add_action('wp_head', 'thenatives_print_styles_noscript', 8);
    function thenatives_print_styles_noscript()
    {
        if (!is_admin()) {
            $handles = wp_styles()->queue;
            $expects = array();
            echo '<style>';
            $style = WP_Styles();
            foreach ($handles as $handle) {
                $file = $style->registered[$handle];
                $url = $file->src;
                $file = str_replace(get_bloginfo('url'), '', $url);
                $file = getcwd() . $file;
                if (file_exists($file)) {
                    echo minify_css($url);
                } else {
                    $expects[] = $handle;
                }
            }
            echo '</style>';
            foreach ($expects as $handle) {
                $style->do_item($handle);
            }
        }
    }

    function minify_css($url)
    {
        $file = str_replace(get_bloginfo('url'), '', $url);
        $file = getcwd() . $file;
        if (file_exists($file)) {
            $content = stream_get_contents(fopen($file, "rb"));
            $folder = str_replace('/' . substr(strrchr($url, '/'), 1), '', $url);
            while (strpos($content, '/*') !== false) {
                $url_pos = strpos($content, '/*');
                $string = substr($content, $url_pos);
                $length = strpos($string, '*/');
                $string = substr($string, 0, $length + 2);
                $content = str_replace($string, '', $content);
            }
            $temp = $content;
            while (strpos($temp, 'url(') !== false) {
                $url_pos = strpos($temp, 'url(') ? strpos($temp, 'url(') : '';
                $string = substr($temp, $url_pos);
                $length = strpos($string, ')');
                $mainlink = substr($string, 4, $length - 4);
                $link = str_replace('"', '', $mainlink);
                $link = str_replace("'", '', $link);
                $count = 0;
                $checkurl = false;
                foreach (explode('/', $link) as $i) {
                    if ($i == '..') {
                        $link = substr($link, '3');
                        $count++;
                        $checkurl = true;
                    } else {
                        if ($i == '.') {
                            $link = substr($link, '2');
                            $checkurl = true;
                        } elseif ($i == '') {
                            $link = substr($link, '1');
                            $checkurl = true;
                        } elseif (substr($i, 0, 4) != 'http') {
                            $checkurl = true;
                        }
                        break;
                    }
                }
                $tempFolder = $folder;
                while ($count) {
                    $tempFolder = str_replace('/' . substr(strrchr($folder, '/'), 1), '', $folder);
                    $count--;
                }
                if ($checkurl) {
                    $link = $tempFolder . '/' . $link;
                }
                $content = str_replace($mainlink, $link, $content);
                $temp = substr($temp, ($url_pos + 5 + $length));
            }
            while (strpos($content, '@import') !== false) {
                $pos = strpos($content, '@import');
                $temp = substr($content, $pos);
                $string = substr($temp, 0, strpos($temp, ';'));
                $url_pos = strpos($string, 'url(') ? strpos($string, 'url(') : '';
                $length = (strpos($string, ')') - $url_pos) - 4;
                $link = substr($string, ($url_pos + 4), $length);
                $link = str_replace('"', '', $link);
                $link = str_replace("'", '', $link);
                $count = 0;
                $checkurl = false;
                foreach (explode('/', $link) as $i) {
                    if ($i == '..') {
                        $link = substr($link, '3');
                        $count++;
                        $checkurl = true;
                    } else {
                        if ($i == '.') {
                            $link = substr($link, '2');
                            $checkurl = true;
                        } elseif ($i == '') {
                            $link = substr($link, '1');
                            $checkurl = true;
                        } elseif (substr($i, 0, 4) != 'http') {
                            $checkurl = true;
                        }
                        break;
                    }
                }
                $tempFolder = $folder;
                while ($count) {
                    $tempFolder = str_replace('/' . substr(strrchr($folder, '/'), 1), '', $folder);
                    $count--;
                }
                if ($checkurl) {
                    $link = $tempFolder . '/' . $link;
                }
                $temp = '';
                $temp .= minify_css($link);
                $content = str_replace($string . ';', $temp, $content);
            }
            $content = str_replace("\r\n", '', trim($content));
            return $content;
        }
        return '';
    }

    remove_action('wp_print_footer_scripts', '_wp_footer_scripts');
    add_action('wp_print_footer_scripts', 'thenatives_print_script');
    add_action('wp_footer', 'thenatives_print_script');
    function thenatives_print_script()
    {
        print_footer_scripts();
    }

    function wp_html_compression_finish($html)
    {
        return new WP_HTML_Compression($html);
    }

    function wp_html_compression_start()
    {
        ob_start('wp_html_compression_finish');
    }

    add_action('get_header', 'wp_html_compression_start');
}