<?php
/**
 * Plugin Name: Affiliate Toolkit Starter
 * Plugin URI: https://www.affiliate-toolkit.com
 * Description: Import products and bestseller lists from Amazon and include them in your posts easily by use of shortcodes and more.
 * Version: 2.1
 * Author: Christof Servit
 * Author URI: https://www.servit.biz
 * Text Domain: ATKP
 * Domain Path: /lang
 * License: GPL2
 */
   
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    define('ATKP_PLUGIN_PREFIX', 'ATKP');
    define('ATKP_PLUGIN_DIR', dirname(__FILE__));
    define('ATKP_PLUGIN_FILE', __FILE__);
    
    define('ATKP_PLUGIN_VERSION', 20);
    
    
    add_action('plugins_loaded', 'my_affiliate_toolkit_lang');
    function my_affiliate_toolkit_lang() {
    	load_plugin_textdomain(ATKP_PLUGIN_PREFIX , false, dirname(plugin_basename(__FILE__)) .'/lang' );
    }
    
    require_once  ATKP_PLUGIN_DIR.'/includes/atkp_basics.php';
    require_once  ATKP_PLUGIN_DIR.'/includes/atkp_widget.php';
    
    //** Plugin initialisieren **//
    
    add_action('init', 'my_affiliate_toolkit_init');
        
    function my_affiliate_toolkit_init() {
        if ( version_compare( get_bloginfo( 'version' ), '4.0', '<' ) )
        {
            wp_die( "You must update WordPress to use this plugin!" );
        }
        
        ATKPSettings::load_settings();
       	
        if(is_admin()) {
            require_once  ATKP_PLUGIN_DIR.'/affiliate-toolkit-settings.php';
            $atkp_settings = new atkp_settings(array());
            
            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_settings_toolkit.php';
    		require_once  ATKP_PLUGIN_DIR.'/includes/atkp_settings_advanced.php';
    		require_once  ATKP_PLUGIN_DIR.'/includes/atkp_settings_display.php';
    		
    		require_once  ATKP_PLUGIN_DIR.'/includes/atkp_compatibility_asa1.php';
    		
    		require_once  ATKP_PLUGIN_DIR.'/includes/atkp_tools_debug.php';
    		
            $atkp_settings::$settings = array(
                __('Basic Settings', ATKP_PLUGIN_PREFIX) => array(new atkp_settings_toolkit(array()), 'toolkit_configuration_page'),
                __('Advanced Settings', ATKP_PLUGIN_PREFIX) => array(new atkp_settings_advanced(array()), 'advanced_configuration_page'),       
                __('Display Settings', ATKP_PLUGIN_PREFIX) => array(new atkp_settings_display(array()), 'display_configuration_page'),   		
            );
            
            require_once  ATKP_PLUGIN_DIR.'/affiliate-toolkit-compatibility.php';
            $atkp_compatibility = new atkp_compatibility(array());
            
            $atkp_compatibility::$modes = array(
        		__('Amazon Simple Admin 1', ATKP_PLUGIN_PREFIX) => array(new atkp_compatibility_asa1(array()), 'compatibilitymode_configuration_page'),	
            );
            
            require_once  ATKP_PLUGIN_DIR.'/affiliate-toolkit-tools.php';
            $atkp_tools = new atkp_tools(array());
            
            $atkp_tools::$tools = array(
        		__('Debug', ATKP_PLUGIN_PREFIX) => array(new atkp_tools_debug(array()), 'debug_configuration_page'),	
            );
            
                
            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_posttypes_shop.php';
            new atkp_posttypes_shop(array());
            
            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_posttypes_product.php';
            new atkp_posttypes_product(array());
            
            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_posttypes_list.php';
            new atkp_posttypes_list(array());
            
            if(get_option(ATKP_PLUGIN_PREFIX.'_useoldgenerator', 0) == 1) {
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_shortcode_generator.php';
                new atkp_shortcode_generator(array());
            } else {            
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_shortcode_generator2.php';
                new atkp_shortcode_generator2(array());
            }
            add_action( 'admin_enqueue_scripts', 'my_affiliate_toolkit_admin_styles' );
            
        } else {    
            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_shortcodes_product.php';
            new atkp_shortcodes_product(array());
            
            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_shortcodes_list.php';
            new atkp_shortcodes_list(array());
            
            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_shortcodes_asa1.php';
            new atkp_shortcodes_asa1(array());
            
    		require_once  ATKP_PLUGIN_DIR.'/includes/atkp_posttypes_product.php';
            new atkp_posttypes_product(array());
    		
            add_action('wp_enqueue_scripts', 'my_affiliate_toolkit_styles');
            
            //enable shortcodes at widget area
            //add_filter('widget_text', 'do_shortcode');
        }
        
        require_once  ATKP_PLUGIN_DIR.'/includes/atkp_cronjob.php';
        new atkp_cronjob(array());
        
      
    }

    require_once  ATKP_PLUGIN_DIR.'/includes/atkp_endpoints.php';
    new atkp_endpoints(array());  
    
    function my_affiliate_toolkit_admin_styles($hook) {
    
        if ( 'toplevel_page_ATKP_affiliate_toolkit-plugin' == $hook ||  'affiliate-toolkit_page_ATKP_affiliate_toolkit-compatibility' == $hook ||  'affiliate-toolkit_page_ATKP_affiliate_toolkit-tools' == $hook ) {
            wp_register_style( 'atkp-styles', plugins_url('/css/admin-style.css', __FILE__));
            wp_enqueue_style( 'atkp-styles' );
        } else if('post.php' == $hook || 'post-new.php' == $hook) {
            wp_register_style( 'atkp-styles', plugins_url('/css/style.css', __FILE__));
            wp_enqueue_style( 'atkp-styles' );
            
            if(get_option(ATKP_PLUGIN_PREFIX.'_useoldgenerator', 0) != 1) {
                wp_register_style( 'atkp-fontawesome', plugins_url('/lib/font-awesome/css/font-awesome.min.css', __FILE__));
                wp_enqueue_style( 'atkp-fontawesome' );
            }
            //wp_register_style( 'atkp-select2-styles', plugins_url('/lib/select2/dist/css/select2.css', __FILE__));
            //wp_enqueue_style( 'atkp-select2-styles' );
            
            //wp_register_script( 'atkp-select2-scripts', plugins_url('/lib/select2/dist/js/select2.js', __FILE__));    
            //wp_enqueue_script( 'atkp-select2-scripts' );
        }
        
        
    }
    
    function my_affiliate_toolkit_styles() {
        wp_register_style( 'atkp-styles', plugins_url('/css/style.css', __FILE__));
    		
    	ATKPTools::add_global_styles('atkp-styles');
        wp_enqueue_style( 'atkp-styles' );
        
        wp_register_script( 'atkp-scripts', plugins_url('/js/library.js', __FILE__));    
        wp_enqueue_script( 'atkp-scripts' );
    }
    
    define('ATKP_INIT', '1');


 
 
 ?>