<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_tools_debug
    {
        /**
         * Construct the plugin object
         */
        public function __construct($pluginbase)
        {
            
        }
        
        public function debug_configuration_page()
        {        
            $imported = false;
                    
            if (ATKPTools::exists_post_parameter('savedebug') && check_admin_referer('save', 'save')) {
                            if (!current_user_can('manage_options')) {
                    wp_die(__('You do not have sufficient permissions to access this page', ATKP_PLUGIN_PREFIX));
                }               
        
                update_option(ATKP_PLUGIN_PREFIX.'_loglevel', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_loglevel', 'string'));
               
            }
            
            $mytab = ATKPTools::get_get_parameter( 'tab', 'int');
            
            $nounce = wp_create_nonce('atkp-download-log');
           	
			if ($mytab != 0 ) $tab = $mytab; else $tab = 1;
                        ?>
            <div class="wrap">
               <!-- <h2><?php _e('Affiliate Toolkit - Woo', ATKP_PLUGIN_PREFIX) ?></h2>      -->      
                
                <form method="POST" action="?page=<?php echo ATKP_PLUGIN_PREFIX.'_affiliate_toolkit-tools&tab='.$tab ?>"><!--_affiliate_toolkit-bestseller-->
                    <?php wp_nonce_field("save", "save"); ?>
                    <table class="form-table" style="width:1024px">
                        <tr valign="top">
                            <th scope="row" style="background-color:gainsboro; padding:7px" colspan="2">
                                <?php _e('Configuration', ATKP_PLUGIN_PREFIX) ?>
                            </th>
                        </tr>
                    
                         <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Log Level', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                            <select  id="<?php echo ATKP_PLUGIN_PREFIX.'_loglevel' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_loglevel' ?>" style="width:300px">
                                <?php
                                $selected = get_option(ATKP_PLUGIN_PREFIX.'_loglevel');
                                                         
                                echo '<option value="off" '.($selected == '' || $selected == 'off' ? 'selected' : '').' >'.__('OFF', ATKP_PLUGIN_PREFIX).'</option>';
                                 
                                echo '<option value="debug" '.($selected == 'debug' ? 'selected' : '').'>'.__('DEBUG', ATKP_PLUGIN_PREFIX).'</option>';      
                                
                                echo '<option value="error" '.($selected == 'error' ? 'selected' : '').'>'.__('ERROR', ATKP_PLUGIN_PREFIX).'</option>';   
                                
                                
                             ?>
</select>                   <br />
                                
                                <?php  echo '<a '.((file_exists(ATKP_LOGFILE)) ? '' : 'disabled' ).' class="button" href="'.ATKPTools::get_endpointurl().'?action=atkp_download_logfile&request_nonce='.$nounce.'">'.__('Download Logfile', ATKP_PLUGIN_PREFIX).'</a>'; ?>
                                &nbsp;
                                <?php  echo '<a '.((file_exists(ATKP_LOGFILE)) ? '' : 'disabled' ).' class="button" href="'.ATKPTools::get_endpointurl().'?action=atkp_clear_logfile&request_nonce='.$nounce.'">'.__('Clear Logfile', ATKP_PLUGIN_PREFIX).'</a>'; ?>
               
                             </td>
                        </tr>
                        
                        <tr valign="top">
                         
                            <td>
                                <?php submit_button('', 'primary', 'savedebug', false); ?>             </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row" style="background-color:gainsboro; padding:7px" colspan="2">
                                <?php _e('Status', ATKP_PLUGIN_PREFIX) ?>
                            </th>
                        </tr>
                        
                        <tr valign="top">
                            <td scope="row"  colspan="2">
                                <textarea readonly style="width:100%;height:250px"><?php echo esc_textarea($this->get_wpinfo()); ?></textarea>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <td scope="row"  colspan="2">
                                <textarea readonly style="width:100%;height:250px"><?php echo esc_textarea($this->get_phpinfo()); ?></textarea>
                            </td>
                        </tr>
                        
                    </table>
                </form>    
           
                
            </div> <?php
        }
        
        private function get_phpinfo() {
            return print_r($this->parse_phpinfo(), true);
        }
        
        function parse_phpinfo() {
            //retrieve php info for current server
        	if (!function_exists('ob_start') || !function_exists('phpinfo') || !function_exists('ob_get_contents') || !function_exists('ob_end_clean') || !function_exists('preg_replace')) {
        		return 'This information is not available.';
        	} else {
        		ob_start();
        		phpinfo();
        		$s = ob_get_contents();
        		ob_end_clean();
        	 
        		//$s = preg_replace( '%^.*<body>(.*)</body>.*$%ms','$1',$pinfo);
        		
        	}  
            
            $s = strip_tags($s, '<h2><th><td>');
            $s = preg_replace('/<th[^>]*>([^<]+)<\/th>/', '<info>\1</info>', $s);
            $s = preg_replace('/<td[^>]*>([^<]+)<\/td>/', '<info>\1</info>', $s);
            $t = preg_split('/(<h2[^>]*>[^<]+<\/h2>)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
            $r = array(); $count = count($t);
            $p1 = '<info>([^<]+)<\/info>';
            $p2 = '/'.$p1.'\s*'.$p1.'\s*'.$p1.'/';
            $p3 = '/'.$p1.'\s*'.$p1.'/';
            for ($i = 1; $i < $count; $i++) {
                if (preg_match('/<h2[^>]*>([^<]+)<\/h2>/', $t[$i], $matchs)) {
                    $name = trim($matchs[1]);
                    $vals = explode("\n", $t[$i + 1]);
                    foreach ($vals AS $val) {
                        if (preg_match($p2, $val, $matchs)) { // 3cols
                            $r[$name][trim($matchs[1])] = array(trim($matchs[2]), trim($matchs[3]));
                        } elseif (preg_match($p3, $val, $matchs)) { // 2cols
                            $r[$name][trim($matchs[1])] = trim($matchs[2]);
                        }
                    }
                }
            }
            return $r;
        }
        
        public function isMinimumVersion($version)
        {
            return version_compare(get_bloginfo('version'), $version) >= 0;
        }
        
        private function getThemeData()
        {
            $themeData =null;
            
            if ($this->isMinimumVersion('3.4')) {
                $themeData = wp_get_theme();
            } else {
                $themeData = get_theme_data(get_stylesheet());
            }
         
            return $themeData;
        }
        
        private function getPlugins() {
            $array = get_plugins();
            
            return $array;            
        }
        
        private function get_wpinfo() {
            
            $context = array(
                'plugin_name' => __('Affiliate Toolkit', ATKP_PLUGIN_PREFIX),
                'plugin_version' => ATKPSettings::plugin_get_version(),
                'OS' => PHP_OS,
                'uname' => php_uname(),
                'wp_version' => get_bloginfo('version'),
                'wp_charset' => get_bloginfo('charset'),
                'wp_count_users' => count_users()['total_users'],
                'wp_debug' => WP_DEBUG == true ? 'true' : 'false',
                'wp_debug_log' => WP_DEBUG_LOG == true ? 'true' : 'false',
                'wp_debug_display' => WP_DEBUG_DISPLAY == true ? 'true' : 'false',
                'plugins' => $this->getPlugins(),
                'theme' => $this->getThemeData(),
                'php_version' => phpversion(),
                'php_memory_limit' => ini_get('memory_limit'),
                'php_include_path' => get_include_path(),
                'php_open_basedir' => ini_get('open_basedir'),
                'php_ipv6' => defined('AF_INET6') ? "PHP was compiled without --disable-ipv6 option" : "PHP was compiled with --disable-ipv6 option",
                'mysql_version' => !empty($mysql_server_info) ? $mysql_server_info : '',
                'mysql_client' => !empty($mysql_client_info) ? $mysql_client_info : '',
                'server_software' => $_SERVER['SERVER_SOFTWARE'],
            );
            
                
            if (function_exists('mysql_get_server_info')) {
                $mysql_server_info = @mysql_get_server_info();
            } else {
                $mysql_server_info = '';
            }
    
            if (function_exists('mysql_get_client_info')) {
                $mysql_client_info = @mysql_get_client_info();
            } else {
                $mysql_client_info = '';
            }
            
            $context['mysql_version'] = !empty($mysql_server_info) ? $mysql_server_info : '';
            $context['mysql_client'] = !empty($mysql_client_info) ? $mysql_client_info : '';
            $context['server_software'] = $_SERVER['SERVER_SOFTWARE'];            
            
            if (function_exists('apache_get_version')) {
                $context['apache_version'] = apache_get_version();
            }
            if (function_exists('apache_get_modules')) {
                $context['apache_modules'] = apache_get_modules();
            }           
            
            return print_r($context, true);   
        }
        
        private function get_logfile() {
            if(file_exists(ATKP_PLUGIN_DIR.'/log/log.txt'))
                return file_get_contents(ATKP_PLUGIN_DIR.'/log/log.txt');
            else
                return 'file not found';   
        }
        
}

?>