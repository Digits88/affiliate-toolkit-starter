<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_tools
    {
        public static $tools;
        
        /**
         * Construct the plugin object
         */
        public function __construct($pluginbase)
        {
            add_action( 'admin_menu', array(&$this, 'admin_menu'));
        }
        
        function admin_menu() {

            add_submenu_page(
                    ATKP_PLUGIN_PREFIX.'_affiliate_toolkit-plugin',
                    __('Tools', ATKP_PLUGIN_PREFIX),
                    __('Tools', ATKP_PLUGIN_PREFIX),            
                    'manage_options',            
                    ATKP_PLUGIN_PREFIX.'_affiliate_toolkit-tools',
                    array(&$this, 'toolkit_tools')          
            );  

        }



public function toolkit_tools() {
    if(!is_user_logged_in ())
     wp_die(__('You do not have sufficient permissions to access this page', ATKP_PLUGIN_PREFIX));
    
     ?>       

   <h2 class="nav-tab-wrapper">
   <?php
	$mytab = ATKPTools::get_get_parameter( 'tab', 'int');
			
    if ($mytab != 0 ) $tab = $mytab; else $tab = 1;
   
   $current = 1;
   foreach(atkp_tools::$tools as $key => $value){
    $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        //echo '<a class="nav-tab'.$class.'" href="'.admin_url().'/index.php??page='.ATKP_PLUGIN_PREFIX.'_affiliate_toolkit-plugin&tab='.$count.">'.$key.'xx</a>";   
       
        
        ?> <a class="nav-tab<?php echo $class ?>" href="<?php echo admin_url() ?>admin.php?page=<?php echo ATKP_PLUGIN_PREFIX.'_affiliate_toolkit-tools' ?>&tab=<?php echo $current ?>"><?php echo $key ?></a><?php
    $current = $current +1;
   }
   ?>

    </h2>


  <?php

$current = 1;
 foreach(atkp_tools::$tools as $key => $value){
    if( $tab == $current ) {
        call_user_func($value);
     break;   
    }
     $current = $current +1;
 }
  
 ?>
 
 
 <div class="atkp-credits">
					<h3 class="hndle"><?php echo __('Affiliate Toolkit Starter', ATKP_PLUGIN_PREFIX) . ' '. ATKPSettings::plugin_get_version() ?></h3>
					<div class="inside">
					    <a href="http://www.affiliate-toolkit.com" target="_blank" title="affiliate-toolkit.com - Affiliate Marketing Plugin"><img style="margin-left:40px; margin-right:40px" src="<?php echo plugins_url('images/logo-admin.png', __FILE__ ); ?>" title="affiliate-toolkit.com - Affiliate Marketing Plugin" alt="affiliate-toolkit.com - Affiliate Marketing Plugin"></a>
					
						<h4 class="inner"><?php _e('Do you need help?', ATKP_PLUGIN_PREFIX) ?></h4>
						<p class="inner"><?php echo ATKPHomeLinks::ReplaceLinkType(__('If you have problems with our plugin, take a look first at our <a href="%link_help%" target="_blank">Help Center</a>. If you need further assistance, please <a href="%link_support%" target="_blank">contact us</a>.', ATKP_PLUGIN_PREFIX)) ?></p>
						<hr>
						<h4 class="inner"><?php _e('Like the plugin?', ATKP_PLUGIN_PREFIX) ?></h4>
						
						<p class="inner">
						    <?php echo ATKPHomeLinks::ReplaceLinkType(__('Add your review and recommend it further. As an affiliate you can order even make money! More information can be found <a href="%link_affiliate%" target="_blank">here</a>!', ATKP_PLUGIN_PREFIX)) ?>
						</p>
						<p class="inner">
						    <?php echo ATKPHomeLinks::ReplaceLinkType(__('We look forward contributing any feedback to improve the plugin. Send us your praise or your criticism simply our <a href="%link_contact%" target="_blank">contact form</a>.', ATKP_PLUGIN_PREFIX)) ?>
						</p>
						<hr>
						<p class="atkp-link inner">Created by <a href="http://www.servit.biz" target="_blank" title="servit.biz - Quality plugins for WordPress"><img src="<?php echo plugins_url('images/logo_servit-biz.png', __FILE__ ); ?>" title="servit.biz - Quality plugins for WordPress" alt="servit.biz - Quality plugins for WordPress"></a></p>
					</div>
				</div>
    <div style="text-align:center;width:1024px;margin-top:10px" > <?php ATKPHomeLinks::echo_banner(); ?></div>
 
 <?php
            
}
    }
    
    
?>