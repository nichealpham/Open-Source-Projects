<?php

final class ITSEC_Four_Oh_Four_Settings extends ITSEC_Settings {
	public function get_id() {
		return '404-detection';
	}
	
	public function get_defaults() {
		return array(
			'check_period'    => 5,
			'error_threshold' => 20,
			'white_list'      => array(
				'/favicon.ico',
				'/robots.txt',
				'/apple-touch-icon.png',
				'/apple-touch-icon-precomposed.png',
				'/wp-content/cache',
				'/browserconfig.xml',
				'/crossdomain.xml',
				'/labels.rdf',
				'/trafficbasedsspsitemap.xml',
			),
			'types'           => array(
				'.jpg',
				'.jpeg',
				'.png',
				'.gif',
				'.css',
			),
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_Four_Oh_Four_Settings() );
