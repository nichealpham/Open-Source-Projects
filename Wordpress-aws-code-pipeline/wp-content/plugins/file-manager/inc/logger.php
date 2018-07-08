<?php
/**
 * 
 * @file logger.php
 * @description This file will hold the logging functionality of the plugin
 * 
 * */

// Security check
defined('ABSPATH') || die();

if(!function_exists('logger')):

/**
 * 
 * @function logger
 * 
 * Logs file file manager actions
 * 
 * */
function logger($cmd, $result, $args, $elfinder) {
	
	global $FileManager;
	
	$log = sprintf("[%s] %s: %s \n", date('r'), strtoupper($cmd), var_export($result, true));
	$logfile = $FileManager->upload_path . DS . 'log.txt';
	$dir = dirname($logfile);
	if (!is_dir($dir) && !mkdir($dir)) {
		return;
	}
	if (($fp = fopen($logfile, 'a'))) {
		fwrite($fp, $log);
		fclose($fp);
	}
	return;

	foreach ($result as $key => $value) {
		if (empty($value)) {
			continue;
		}
		$data = array();
		if (in_array($key, array('error', 'warning'))) {
			array_push($data, implode(' ', $value));
		} else {
			if (is_array($value)) { // changes made to files
				foreach ($value as $file) {
					$filepath = (isset($file['realpath']) ? $file['realpath'] : $elfinder->realpath($file['hash']));
					array_push($data, $filepath);
				}
			} else { // other value (ex. header)
				array_push($data, $value);
			}
		}
		$log .= sprintf(' %s(%s)', $key, implode(', ', $data));
	}
	$log .= "\n";

	$logfile = $FileManager->upload_path . DS . 'log.txt';
	$dir = dirname($logfile);
	if (!is_dir($dir) && !mkdir($dir)) {
		return;
	}
	if (($fp = fopen($logfile, 'a'))) {
		fwrite($fp, $log);
		fclose($fp);
	}
}

endif;
