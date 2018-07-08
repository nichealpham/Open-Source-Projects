<?php

	/**
	 * Notification box
	 *
	 * @since 1.0.7
	 */
	class TP_Image_Optimizer_Notice extends TP_Image_Optimizer_Service {

		public function __construct() {

			parent::__construct();

			add_action('wp_ajax_tpio_verify_coupon', array($this, 'ajax_verify_coupon'));
		}

		/**
		 * Verify coupon
		 *
		 * @category Ajax
		 * @return json
		 * @since    1.0.7
		 */
		public function ajax_verify_coupon() {

			if ('POST' !== strtoupper($_SERVER['REQUEST_METHOD'])) {
				return;
			}

			if (empty($_POST['action']) || 'tpio_verify_coupon' !== $_POST['action'] || empty($_POST['_coupon_nonce']) || !wp_verify_nonce($_POST['_coupon_nonce'], 'tpio_verify_coupon')) {
				wp_send_json_error(esc_html__('Security key was not validated.', 'tp-image-optimizer'));
			}

			$coupon_code = isset($_POST['coupon_code']) ? sanitize_text_field($_POST['coupon_code']) : '';

			if (empty($coupon_code)) {
				wp_send_json_error(esc_html__('Please enter a coupon code', 'tp-image-optimizer'));
			}

			$response = wp_remote_post($this->__get('service') . 'verify-coupon', array(
				'headers' => array(
					'authentication' => $this->__get('token')
				),
				'body'    => array(
					'coupon' => $coupon_code,
				)
			));

			$status_code = wp_remote_retrieve_response_code($response);

			if ($status_code == 404) {
				wp_send_json_error(esc_html__('Service cannot established.', 'tp-image-optimizer'));
			}

			if (!is_wp_error($response)) {

				$response = wp_remote_retrieve_body($response);

				$response = json_decode($response);

				if (!empty($response->success)) {

					$html = '<strong>' . esc_html__('Done! Your coupon has been applied successfully.', 'tp-image-optimizer') . '</strong> <br/>';
					$html .= wp_kses_post($response->data);
					delete_transient('tp_image_optimizer_statistics_service');
					wp_send_json_success($html);
				} else {
					if (!empty($response->data)) {
						wp_send_json_error($response->data);
					}
				}
			}

			wp_send_json_error(esc_html__('Apply coupon has some error', 'tp-image-optimizer'));
		}

	}

	new TP_Image_Optimizer_Notice();
