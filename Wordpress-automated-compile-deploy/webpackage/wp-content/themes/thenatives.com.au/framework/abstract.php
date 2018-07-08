<?php
class Thenatives {
	protected $options = array();
	protected $arrFunctions = array();
	protected $arrShortcodes = array();
	protected $arrWidgets = array();
	protected $arrIncludes = array();
	public function __construct($options){
		$this->options = $options;
		$this->initArrFunctions();
		$this->initArrWidgets();
		$this->initArrShortcodes();
		$this->initArrIncludes();
		$this->constant($options);
	}

	public function init(){
		$this->initIncludes();
		add_action('after_setup_theme', array($this,'themesetup'));
		add_action('wp_enqueue_scripts', array($this,'themestyle'));
		$this->initFunctions();
		$this->initShortcodes();
		$this->initWidgets();
	}

	protected function constant($options){
		define('THEME_NAME', $options['theme_name']);
		define('THEME_SLUG', $options['theme_slug'].'_');
		define('THEME_DIR', get_template_directory());
		define('THEME_CACHE', get_template_directory().'/cache_theme/');
		define('THEME_URI', get_template_directory_uri());
		define('THEME_FRAMEWORK', THEME_DIR . '/framework');
		define('THEME_FRAMEWORK_URI', THEME_URI . '/framework');
		define('THEME_FUNCTIONS', THEME_FRAMEWORK . '/functions');
		define('THEME_SHORTCODE', THEME_FRAMEWORK . '/shortcodes');
		define('THEME_WIDGETS', THEME_FRAMEWORK . '/widgets');
		define('THEME_INCLUDES', THEME_FRAMEWORK . '/includes');
		define('THEME_INCLUDES_AJAX', THEME_INCLUDES . '/ajax');
		define('THEME_LIB', THEME_FRAMEWORK . '/lib');
		define('THEME_INCLUDES_URI', THEME_URI . '/framework/includes');
		define('THEME_EXTENSION', THEME_FRAMEWORK . '/extension');
		define('THEME_EXTENDS_EXTENDVC_URI', THEME_FRAMEWORK.'/extendvc');
		define('THEME_IMAGES', THEME_URI . '/images');
        define('THEME_SCSS', THEME_URI . '/scss');
		define('THEME_CSS', THEME_URI . '/css');
		define('THEME_JS', THEME_URI . '/js');
		define('THEME_FONT', THEME_URI . '/fonts');
		define('USING_CSS_CACHE', true);
	}

	protected function initArrFunctions(){
		$this->arrFunctions = array('filter_theme','sidebar','general','header','footer','style','editor','navigation','breadcrumbs','user','ajax','post','post_type','taxonomy','cron','minify');
	}
	
	protected function initFunctions(){
		foreach($this->arrFunctions as $function){
			if(file_exists(THEME_FUNCTIONS."/{$function}.php"))
			{
				require_once THEME_FUNCTIONS."/{$function}.php";
			}
		}
	}
	
	protected function initArrShortcodes(){
		$this->arrShortcodes = array('');
	}
	
	protected function initShortcodes(){
		foreach($this->arrShortcodes as $shortcode){
			if(file_exists(THEME_SHORTCODE."/{$shortcode}.php")){
				require_once THEME_SHORTCODE."/{$shortcode}.php";
			}
		}
	}
	
	protected function initArrWidgets(){
		$this->arrWidgets = array();
	}

	protected function initWidgets(){
		foreach($this->arrWidgets as $widget){
			if(file_exists(THEME_WIDGETS."/{$widget}.php"))
			{
				require_once THEME_WIDGETS."/{$widget}.php";
			}
		}
		add_action( 'widgets_init', array($this,'loadWidgets'));
	}
	
	public function loadWidgets(){
		foreach($this->arrWidgets as $widget)
		register_widget( 'WP_Widget_'.ucfirst($widget) );
	}
	
	protected function initArrIncludes(){
		$this->arrIncludes = array('class-tgm-plugin-activation','minify-html','optimizer');
	}

	protected function initIncludes(){
		foreach($this->arrIncludes as $include){
			if(file_exists(THEME_LIB."/{$include}.php")){
				require_once THEME_LIB."/{$include}.php";
			}
		}
	}

	public function themesetup() {
        add_editor_style();
        //add_theme_support( 'post-formats', array( 'link', 'gallery', 'quote', 'image' ) );
        add_theme_support( 'title-tag' );
        if ( ! function_exists( '_wp_render_title_tag' ) ) {
            add_action( 'wp_head', 'theme_slug_render_title' );
        }
        add_theme_support( 'post-thumbnails' );
        $defaults = array(
            'default-color' => '#e8e8e8',
        );
        add_theme_support( 'custom-background', $defaults );
		load_theme_textdomain( 'thenatives', get_template_directory() . '/languages' );
		$locale = get_locale();
		$locale_file = get_template_directory() . "/languages/$locale.php";
		if ( is_readable( $locale_file ) )
			require_once( $locale_file );
		$this->addMenuWidget();
	}

	public function themestyle() {
	    if(is_user_page()) {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_register_script('timepicker-script', THEME_JS . "/jquery-ui-timepicker.min.js", array('jquery'), '', true);
            wp_enqueue_script('timepicker-script');
            wp_register_script('stripe-script', "https://js.stripe.com/v1/", array('jquery'), '', true);
            wp_enqueue_script('stripe-script');
            wp_register_script( 'croppie-script', THEME_JS . "/croppie.min.js", array('jquery'), '', true );
            wp_enqueue_script('croppie-script');

            wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
            wp_register_style( 'croppie-style', THEME_CSS . "/croppie.css" );
            wp_enqueue_style('croppie-style');
            wp_register_style( 'timepicker-style', THEME_CSS . "/jquery-ui-timepicker.min.css" );
            wp_enqueue_style('timepicker-style');
        }
        wp_register_script( 'bootstrap-script', THEME_JS . "/bootstrap.min.js", array('jquery'), '', true );
        wp_enqueue_script('bootstrap-script');
        wp_register_script( 'slick-script', THEME_JS . "/slick.min.js", array('jquery'), '', true );
        wp_enqueue_script('slick-script');
        wp_register_script( 'functions-script', THEME_JS . "/functions.js", array('jquery'), '', true );
        wp_enqueue_script('functions-script');
        wp_enqueue_script('functions-script');
        wp_register_script( 'main-script', THEME_JS . "/main.js", array('jquery'), '', true );
        wp_enqueue_script('main-script');
        wp_register_script( 'ajax-script', THEME_JS . "/ajax.js", array('jquery'),'', true );
        wp_localize_script( 'ajax-script', 'ajax_object', array(
            'url' => admin_url( 'admin-ajax.php' ),
        ));
        wp_enqueue_script('ajax-script');
        wp_register_script( 'global-script', THEME_JS . "/global.js", array('jquery'), '', true );
        wp_enqueue_script('global-script');
        wp_register_style( 'bootstrap-style', THEME_CSS . "/bootstrap.min.css" );
        wp_enqueue_style('bootstrap-style');
        wp_register_style( 'fontawesome-style', THEME_CSS . "/font-awesome.min.css" );
        wp_enqueue_style('fontawesome-style');
        wp_register_style( 'slick-style', THEME_CSS . "/slick.css" );
        wp_enqueue_style('slick-style');
        wp_register_style( 'slick-theme-style', THEME_CSS . "/slick-theme.css" );
        wp_enqueue_style('slick-theme-style');
        wp_register_style( 'main-style', THEME_URI . "/style.css" );
        wp_enqueue_style('main-style');
        wp_register_style( 'global-style', THEME_SCSS . "/global.css" );
        wp_enqueue_style('global-style');
        wp_register_style( 'home-style', THEME_SCSS . "/home.css" );
        wp_enqueue_style('home-style');
        wp_register_style( 'index-style', THEME_SCSS . "/index.css" );
        wp_enqueue_style('index-style');
	}

	private function addMenuWidget() {
		register_nav_menus( array(
			'category' => __( 'Category Navigation', 'thenatives' )
		));
		register_nav_menus( array(
			'page' =>  __( 'Page Navigation', 'thenatives' )
		));
		register_nav_menus( array(
			'social' =>  __( 'Social Navigation', 'thenatives' )
		));
		register_sidebar(array(
			'name' => __('Footer Widget 01', 'thenatives'),
			'id' => 'footer-widget-1',
			'description' => __('Footer Widget 01'),
			'class' => 'footer-widget-1',
			'before_widget' => '<div id="%1$s" class="footer-widget footer-widget-1 widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h2 class="widget-title footer-widget-title footer-widget-1-title">',
			'after_title' => '</h2>'
		));
		register_sidebar(array(
			'name' => __('Footer Widget 02', 'thenatives'),
			'id' => 'footer-widget-2',
			'description' => __('Footer Widget 02'),
			'class' => 'footer-widget-2',
			'before_widget' => '<div id="%1$s" class="footer-widget footer-widget-2 widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h2 class="widget-title footer-widget-title footer-widget-2-title">',
			'after_title' => '</h2>'
		));
		register_sidebar(array(
			'name' => __('Footer Widget 03', 'thenatives'),
			'id' => 'footer-widget-3',
			'description' => __('Footer Widget 03'),
			'class' => 'footer-widget-3',
			'before_widget' => '<div id="%1$s" class="footer-widget footer-widget-3 widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h2 class="widget-title footer-widget-title footer-widget-3-title">',
			'after_title' => '</h2>'
		));
		register_sidebar(array(
			'name' => __('Footer Widget 04', 'thenatives'),
			'id' => 'footer-widget-4',
			'description' => __('Footer Widget 04'),
			'class' => 'footer-widget-4',
			'before_widget' => '<div id="%1$s" class="footer-widget footer-widget-4 widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h2 class="widget-title footer-widget-title footer-widget-4-title">',
			'after_title' => '</h2>'
		));
	}
}