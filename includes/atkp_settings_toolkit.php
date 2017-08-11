<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_settings_toolkit
    {
        private $base = null;
        /**
         * Construct the plugin object
         */
        public function __construct($pluginbase)
        {
            $base  = $pluginbase;
        }

        private function display_cron_job($hookParam = ATKP_EVENT, $output = true)
        {
            $crontype = get_option(ATKP_PLUGIN_PREFIX.'_crontype', 'wpcron');
            
            switch($crontype) {
                default:
                case 'wpcron':
                    return ATKPTools::exists_cron_job($hookParam, $output);
                    break;
                case 'external':
                case 'externaloutput':
                    
                    break;                
            } 
            
            
        }
    
        public function toolkit_configuration_page()
        {
            if (ATKPTools::exists_post_parameter('saveglobal') && check_admin_referer('save', 'save')) {
                //speichern der einstellungen
                
                if (!current_user_can('manage_options')) {
                    wp_die(__('You do not have sufficient permissions to access this page', ATKP_PLUGIN_PREFIX));
                }
                
                update_option(ATKP_PLUGIN_PREFIX.'_access_csv_intervall', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_access_csv_intervall', 'int'));
                
                $oldduration = get_option(ATKP_PLUGIN_PREFIX.'_cache_duration', 1440);
                $duration = ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_cache_duration', 'int');           
                
                update_option(ATKP_PLUGIN_PREFIX.'_cache_duration', $duration);
                
                
                update_option(ATKP_PLUGIN_PREFIX.'_crontype', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_crontype', 'string'));        
              
            
                if(ATKP_PLUGIN_VERSION >= 20) {
                    $cronjob = new atkp_cronjob(array());
                    
                    if(isset($cronjob)) {
                        ATKPSettings::load_settings();
            
                        $cronjob->my_update();                        
                    }
                }
             
            }
            
            
            ?>
            <div class="wrap">
            <div id="tabs">
            

                
                
                
                
                
                <form method="POST" action="?page=<?php echo ATKP_PLUGIN_PREFIX.'_affiliate_toolkit-plugin' ?>">
                <?php wp_nonce_field("save", "save"); ?>
                    <table class="form-table" style="width:1024px">   
                        <tr valign="top">
                            <th scope="row" style="background-color:gainsboro; padding:7px" colspan="2">
                                <?php _e('Global settings', ATKP_PLUGIN_PREFIX) ?>
                            </th>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('cronjob type', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <select name="<?php echo ATKP_PLUGIN_PREFIX.'_crontype' ?>">
                                <?php
                                $crontype = get_option(ATKP_PLUGIN_PREFIX.'_crontype', 'wpcron');
                                $durations = array(
                                                    'wpcron' => __('Wordpress Cronjob', ATKP_PLUGIN_PREFIX),                                                  
                                                  );
                                
                                foreach ($durations as $value => $name) {
                                    if ($value == $crontype) 
                                        $sel = ' selected'; 
                                    else 
                                        $sel = '';
                                          
                                    echo '<option value="' . $value . '"' . $sel . '>' . esc_attr($name) . '</option>';
                                } ?>
                                </select>      
                                
                                
                                
                            </td>
                        </tr>
           
                        
                        <tr valign="top">
                            <th scope="row" style="background-color:gainsboro; padding:7px" colspan="2">
                                <?php _e('Settings for data cache', ATKP_PLUGIN_PREFIX) ?>
                            </th>
                        </tr>
    
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Cache duration', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <select name="<?php echo ATKP_PLUGIN_PREFIX.'_cache_duration' ?>">
                                <?php
                                
                                $durations = array(
                                                    60 => __('1 Hour', ATKP_PLUGIN_PREFIX),
                                                    360 => __('6 Hours', ATKP_PLUGIN_PREFIX),
                                                    720 => __('12 Hours', ATKP_PLUGIN_PREFIX),
                                                    1440 => __('1 Day', ATKP_PLUGIN_PREFIX),
                                                    4320 => __('3 Days', ATKP_PLUGIN_PREFIX),
                                                    10080 => __('1 Week', ATKP_PLUGIN_PREFIX),
                                                  );
                                
                                foreach ($durations as $value => $name) {
                                    if ($value == get_option(ATKP_PLUGIN_PREFIX.'_cache_duration', 1440)) 
                                        $sel = ' selected'; 
                                    else 
                                        $sel = '';
                                    
                                    $item_translated = '';
                                                                
                                    echo '<option value="' . $value . '"' . $sel . '>' . $name . '</option>';
                                } ?>
                                </select><br />
                                <span style=" font-weight:bold"><?php $this->display_cron_job(ATKP_EVENT, true); ?></span>
                            </td>
                        </tr>
                                            
  
                        <tr valign="top">
                            <th scope="row">                      
                            </th>
                            <td>
                                <?php submit_button('', 'primary', 'saveglobal', false); ?>
                            </td>
                        </tr>
                        
                    </table>
                </form>                
       
    </div>
</div>
            
            
                <?php
            }
}
?>