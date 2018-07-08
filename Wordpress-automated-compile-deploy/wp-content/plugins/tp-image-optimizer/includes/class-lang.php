<?php

if (!defined('TP_IMAGE_OPTIMIZER_BASE')) {
    exit; // Exit if accessed directly
}

/**
 * LANGUAGE LOCALIZATION
 * Provide localization for Javascript variable
 *
 * @class    TP_Image_Optimizer_Lang
 * @package  TP_Image_Optimizer/Classes
 * @category Class
 * @version  1.0
 */
if (!class_exists('TP_Image_Optimizer_Lang')) {

    class TP_Image_Optimizer_Lang {

        /**
         * Get main text
         *
         * @return Array Main text
         * @since 1.0.0
         */
        public function get_main_text() {
            $notify = array(
                'detail_of'           => esc_html__('Detail of #', 'tp-image-optimizer'),
                'get_list_attachment' => esc_html__('Examining existing attachments. This may take a few moments...', 'tp-image-optimizer'),
                'pause'               => esc_html__('Paused optimize progress. Don\'t worry, when you click One-click Optimize, we will continue to compress your image.', 'tp-image-optimizer'),
                'saving'              => esc_html__('Saving', 'tp-image-optimizer'),
                'cancel'              => esc_html__('Cancel', 'tp-image-optimizer'),
                'stop'                => esc_html__('Stop', 'tp-image-optimizer'),
                'update_successfull'  => esc_html__('Updated successfully.', 'tp-image-optimizer'),
                'can_close_window'    => esc_html__('TP Image Optimizer will still auto-optimize all your images, even you close this window.', 'tp-image-optimizer'),
                'optimized'           => esc_html__('Optimized', 'tp-image-optimizer'),
                'library'             => esc_html__('STASTICS OF YOUR LIBRARY', 'tp-image-optimizer'),
                'optimizing'          => esc_html__('Optimizing ...', 'tp-image-optimizer'),
            );
            return $notify;
        }

        /**
         *
         * @return Array Success text
         * @since 1.0.0
         */
        public function get_success_notice() {
            $notify = array(
                'processed' => esc_html__('Processed', 'tp-image-optimizer'),
                'finish'    => __('You have already optimize all images, you can re-compress all images in media library with <strong>Force Re-Optimize</strong> option', 'tp-image-optimizer'),
                'optimized' => esc_html__('Success optimized  attachment ID # ', 'tp-image-optimizer'),
                'done'      => __('<strong>Congratulations!</strong> You have already optimized all image of your library ', 'tp-image-optimizer'),
                'complete'  => esc_html__('Have already processed all image.', 'tp-image-optimizer'),
                'share'     => esc_html__('Share plugin to your friend ', 'tp-image-optimizer'),
            );
            return $notify;
        }

        /**
         *
         * @return Array request text
         * @since 1.0.0
         */
        public function get_request_notice() {
            $notify = array(
                'valid'       => esc_html__('API Key is valid', 'tp-image-optimizer'),
                'invalid'     => esc_html__('API Key is invalid', 'tp-image-optimizer'),
                'request_api' => esc_html__('You need enter a valid API ', 'tp-image-optimizer'),
            );
            return $notify;
        }

        /**
         *
         * @return Array loading text
         * @since 1.0.0
         */
        public function get_loading_notice() {
            $notify = array(
                'update'          => esc_html__('Update', 'tp-image-optimizer'),
                'processing'      => esc_html__('Processing', 'tp-image-optimizer'),
                'loading'         => esc_html__('Loading..', 'tp-image-optimizer'),
                'reload'          => esc_html__('Reloading..', 'tp-image-optimizer'),
                'loading_library' => esc_html__('Please wait, processing media list ..', 'tp-image-optimizer'),
                'compressed'      => esc_html__('Compressed', 'tp-image-optimizer'),
                'wait'            => esc_html__('Please wait ...', 'tp-image-optimizer'),
            );
            return $notify;
        }

        /**
         *
         * @return Array Error text
         * @since 1.0.0
         */
        public function get_error_notice() {
            $notify = array(
                'connection' => esc_html__('Connection lost!', 'tp-image-optimizer'),
                'detect'     => __('<strong>Oops</strong>, I\'ve detected some errors on optimizing process, you can fix this by manual optimizer.', 'tp-image-optimizer'),
                'undefined'  => esc_html__('Unexpected error !', 'tp-image-optimizer'),
                'reload'     => esc_html__('Detect an unexpected error! Please reload page to try again ! '),
            );
            return $notify;
        }

        /**
         * Using on Install Panel
         *
         * @return String Install notice
         * @since 1.0.0
         */
        public function get_install_notice() {
            $notify = array(
                'generated_key' => esc_html__('Token has been generated ! ', 'tp-image-optimizer'),
                'generating'    => esc_html__('Generating..', 'tp-image-optimizer'),
                'getting_media' => esc_html__('Getting all media from WordPress library ...', 'tp-image-optimizer'),
                'success'       => esc_html__('Plugin has been installed successfully, this page will reload to apply change...', 'tp-image-optimizer'),
                'error'         => esc_html__('Detect an unexpected error, please try again...', 'tp-image-optimizer'),
            );
            return $notify;
        }

        /**
         *
         * @return String Size Unit
         */
        public function size() {
            $notify = array(
                'B'  => esc_html__(' Bytes', 'tp-image-optimizer'),
                'KB' => esc_html__(' KB', 'tp-image-optimizer'),
                'MB' => esc_html__(' MB', 'tp-image-optimizer'),
            );
            return $notify;
        }

        public function faq() {
            $notify = array(
                // FAQ Service statistics
                'statistics_service_title'  => esc_html__('Stastics by IO service ', 'tp-image-optimizer'),
                'statistics_service'        => esc_html__('This data is collected by the server of TP Image Optimizer. It shows statistics of the whole image optimizing process on your site.', 'tp-image-optimizer'),
                // FAQ Quality
                'quality_title'             => esc_html__('Option quality', 'tp-image-optimizer'),
                'quality'                   => esc_html__('This option allows you to select the optimized image quality. The higher the image quality is, the larger the compressed image size is', 'tp-image-optimizer'),
                // Size title
                'size_title'                => esc_html__('Option size', 'tp-image-optimizer'),
                'size'                      => esc_html__('You set the compressed image size in this item - Popular image sizes in website (thumbnail, large image, etc.) are recommended to speed up your website. Full Option is used for compressing the original image.', 'tp-image-optimizer'),
                // Compress Error
                'compress_error_title'      => esc_html__('Unexpected error !', 'tp-image-optimizer'),
                'compress_error'            => esc_html__('Detect an unexpected error, please try again...', 'tp-image-optimizer'),
                // Force
                'force_title'               => esc_html__('Force Re-Optimize', 'tp-image-optimizer'),
                'force'                     => esc_html__('When you enable "Force Re-Optimize" feature, the plugin will automatically re-optimize all images in your library.This feature will be very useful if you regenerate all WordPress images..', 'tp-image-optimizer'),
                // Compress original image
                'original_title'            => esc_html__('Compress original image', 'tp-image-optimizer'),
                'original'                  => esc_html__("TP Image Optimizer compress your original images by default. Uncheck this option if you dont want to optimize the original images. This will help you save the storage on the hosting.
- Tips : Normally, the cropped images will be shown mainly on your site, instead of original images.", 'tp-image-optimizer'),
                // statistics_original
                'statistics_original_title' => esc_html__('This statistic is for original images only.', 'tp-image-optimizer'),
                'statistics_original'       => esc_html__('If you skip original image compression, the statistic will be 0%. To view the detail statistic, click on the View button on the Detail column', 'tp-image-optimizer'),
                // Run in background
                'run_in_background_title'   => esc_html__('Enable Run in background.', 'tp-image-optimizer'),
                'run_in_background'         => __("<b>If TP Image Optimizer is interrupted when optimizing</b>, please disable 'RUN IN BACKGROUND' feature. The Run in background feature is best if your hosting is strong enough.", 'tp-image-optimizer'),
            );
            return $notify;
        }

        /**
         *
         * @return String Size Unit
         */
        public function cron() {
            $cron = array(
                'stop' => esc_html__('Optimizing process has been stopped! You can try it later.', 'tp-image-optimizer'),
            );
            return $cron;
        }

    }

}
