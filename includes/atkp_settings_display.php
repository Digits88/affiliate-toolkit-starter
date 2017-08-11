<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_settings_display
    {
        /**
         * Construct the plugin object
         */
        public function __construct($pluginbase)
        {
            add_action( 'admin_enqueue_scripts', array(&$this, 'add_color_picker') );

            
            
        }
        
        function add_color_picker( $hook ) {
         
            if( is_admin() ) {
         
                // Add the color picker css file
                wp_enqueue_style( 'wp-color-picker' );
         
                // Include our custom jQuery file with WordPress Color Picker dependency
                wp_enqueue_script( 'custom-script-handle', plugins_url( 'custom-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
            }
        }

        public function display_configuration_page()
        {        
            if (ATKPTools::exists_post_parameter('savedisplay') && check_admin_referer('save', 'save')) {
                if (!current_user_can('manage_options')) {
                    wp_die(__('You do not have sufficient permissions to access this page', ATKP_PLUGIN_PREFIX));
                }
                
                //speichern der einstellungen
               
                
                update_option(ATKP_PLUGIN_PREFIX.'_short_title_length', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_short_title_length', 'int'));     
                
                
                update_option(ATKP_PLUGIN_PREFIX.'_boxstyle', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_boxstyle', 'int'));
                update_option(ATKP_PLUGIN_PREFIX.'_bestsellerribbon', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_bestsellerribbon', 'int'));
                
                update_option(ATKP_PLUGIN_PREFIX.'_showprice', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_showprice', 'bool'));
                
                
                update_option(ATKP_PLUGIN_PREFIX.'_showpricediscount', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_showpricediscount', 'bool'));
                update_option(ATKP_PLUGIN_PREFIX.'_showstarrating', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_showstarrating', 'bool'));
                update_option(ATKP_PLUGIN_PREFIX.'_showrating', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_showrating', 'bool'));
                update_option(ATKP_PLUGIN_PREFIX.'_hideemptyrating', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_hideemptyrating', 'bool'));
                update_option(ATKP_PLUGIN_PREFIX.'_hideemptystars', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_hideemptystars', 'bool'));
                
                
                update_option(ATKP_PLUGIN_PREFIX.'_linkrating', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_linkrating', 'bool'));
                update_option(ATKP_PLUGIN_PREFIX.'_linkimage', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_linkimage', 'bool'));
                
                update_option(ATKP_PLUGIN_PREFIX.'_buttonstyle', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_buttonstyle', 'int'));
                
                
                update_option(ATKP_PLUGIN_PREFIX.'_description_length', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_description_length', 'int'));
                update_option(ATKP_PLUGIN_PREFIX.'_feature_count', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_feature_count', 'int'));
                update_option(ATKP_PLUGIN_PREFIX.'_boxcontent', ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_boxcontent', 'string')); 
            }
            $mytab = ATKPTools::get_get_parameter( 'tab', 'int');
			
			if ($mytab != 0 ) $tab = $mytab; else $tab = 1;
                        ?>
            <div class="wrap">
               <!-- <h2><?php _e('Affiliate Toolkit - Advanced Settings', ATKP_PLUGIN_PREFIX) ?></h2>      -->      
                
                <form method="POST" action="?page=<?php echo ATKP_PLUGIN_PREFIX.'_affiliate_toolkit-plugin&tab='.$tab ?>"><!--_affiliate_toolkit-bestseller-->
                    <?php wp_nonce_field("save", "save"); ?>
                    <table class="form-table" style="width:1024px">
                    

                        <tr valign="top">
                            <th scope="row" style="background-color:gainsboro; padding:7px" colspan="2">
                                <?php _e('Display', ATKP_PLUGIN_PREFIX) ?>
                            </th>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                               
                            </th>
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PLUGIN_PREFIX.'_showprice' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_showprice' ?>" value="1" <?php echo checked(1, get_option(ATKP_PLUGIN_PREFIX.'_showprice',1), true); ?>>
                             <label for="<?php echo ATKP_PLUGIN_PREFIX.'_showprice' ?>">
                                    <?php _e('Show price', ATKP_PLUGIN_PREFIX) ?>
                                </label>
                            </td>
                        </tr>
                        
                        
                        <tr valign="top">
                            <th scope="row">
                               
                            </th>
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PLUGIN_PREFIX.'_showpricediscount' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_showpricediscount' ?>" value="1" <?php echo checked(1, get_option(ATKP_PLUGIN_PREFIX.'_showpricediscount',1), true); ?>>
                             <label for="<?php echo ATKP_PLUGIN_PREFIX.'_showpricediscount' ?>">
                                    <?php _e('Show price discount', ATKP_PLUGIN_PREFIX) ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                               
                            </th>
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PLUGIN_PREFIX.'_showstarrating' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_showstarrating' ?>" value="1" <?php echo checked(1, get_option(ATKP_PLUGIN_PREFIX.'_showstarrating',1), true); ?>>
                             <label for="<?php echo ATKP_PLUGIN_PREFIX.'_showstarrating' ?>">
                                    <?php _e('Show star rating', ATKP_PLUGIN_PREFIX) ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                               
                            </th>
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PLUGIN_PREFIX.'_hideemptystars' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_hideemptystars' ?>" value="1" <?php echo checked(1, get_option(ATKP_PLUGIN_PREFIX.'_hideemptystars',0), true); ?>>
                             <label for="<?php echo ATKP_PLUGIN_PREFIX.'_hideemptystars' ?>">
                                    <?php _e('Hide empty stars', ATKP_PLUGIN_PREFIX) ?>
                                </label>
                            </td>
                        </tr>
                        
                        
                        
                        <tr valign="top">
                            <th scope="row">
                               
                            </th>
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PLUGIN_PREFIX.'_showrating' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_showrating' ?>" value="1" <?php echo checked(1, get_option(ATKP_PLUGIN_PREFIX.'_showrating',1), true); ?>>
                             <label for="<?php echo ATKP_PLUGIN_PREFIX.'_showrating' ?>">
                                    <?php _e('Show rating', ATKP_PLUGIN_PREFIX) ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                               
                            </th>
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PLUGIN_PREFIX.'_hideemptyrating' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_hideemptyrating' ?>" value="1" <?php echo checked(1, get_option(ATKP_PLUGIN_PREFIX.'_hideemptyrating',0), true); ?>>
                             <label for="<?php echo ATKP_PLUGIN_PREFIX.'_hideemptyrating' ?>">
                                    <?php _e('Hide empty ratings', ATKP_PLUGIN_PREFIX) ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                               
                            </th>
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PLUGIN_PREFIX.'_linkrating' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_linkrating' ?>" value="1" <?php echo checked(1, get_option(ATKP_PLUGIN_PREFIX.'_linkrating',0), true); ?>>
                             <label for="<?php echo ATKP_PLUGIN_PREFIX.'_linkrating' ?>">
                                    <?php _e('Link rating', ATKP_PLUGIN_PREFIX) ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                               
                            </th>
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PLUGIN_PREFIX.'_linkimage' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_linkimage' ?>" value="1" <?php echo checked(1, get_option(ATKP_PLUGIN_PREFIX.'_linkimage',0), true); ?>>
                             <label for="<?php echo ATKP_PLUGIN_PREFIX.'_linkimage' ?>">
                                    <?php _e('Link image', ATKP_PLUGIN_PREFIX) ?>
                                </label>
                            </td>
                        </tr>
                                                                        
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Bestseller ribbon', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                            <select  id="<?php echo ATKP_PLUGIN_PREFIX.'_bestsellerribbon' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_bestsellerribbon' ?>" style="width:300px">
                                <?php
                                $selected = get_option(ATKP_PLUGIN_PREFIX.'_bestsellerribbon');
                                                         
                                echo '<option value="1" '.($selected == '' || $selected == 1 ? 'selected' : '').' >'.__('First three products', ATKP_PLUGIN_PREFIX).'</option>';
                                 
                                echo '<option value="2" '.($selected == 2 ? 'selected' : '').'>'.__('All products', ATKP_PLUGIN_PREFIX).'</option>';            
                                
                             ?>
</select>                   
                             </td>
                        </tr>
                         
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Box style', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                            <select  id="<?php echo ATKP_PLUGIN_PREFIX.'_boxstyle' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_boxstyle' ?>" style="width:300px">
                                <?php
                                $selected = get_option(ATKP_PLUGIN_PREFIX.'_boxstyle');
                                                         
                                echo '<option value="1" '.($selected == '' || $selected == 1 ? 'selected' : '').' >'.__('Classic Box', ATKP_PLUGIN_PREFIX).'</option>';
                                 
                                echo '<option value="2" '.($selected == 2 ? 'selected' : '').'>'.__('Flat Box with border', ATKP_PLUGIN_PREFIX).'</option>';                                
                                echo '<option value="3" '.($selected == 3 ? 'selected' : '').'>'.__('Flat Box without border', ATKP_PLUGIN_PREFIX).'</option>';
                                
                             ?>
</select>                   
                             </td>
                        </tr>
                        
                         <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Button style', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                            <select  id="<?php echo ATKP_PLUGIN_PREFIX.'_buttonstyle' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_buttonstyle' ?>" style="width:300px">
                                <?php
                                $selected = get_option(ATKP_PLUGIN_PREFIX.'_buttonstyle');
                                                         
                                echo '<option value="1" '.($selected == '' || $selected == 1 ? 'selected' : '').' >'.__('Classic Button', ATKP_PLUGIN_PREFIX).'</option>';
                                echo '<option value="3" '.($selected == 3 ? 'selected' : '').'>'.__('Amazon Button', ATKP_PLUGIN_PREFIX).'</option>';
                                
                                echo '<option value="10" '.($selected == 10 ? 'selected' : '').'>'.__('Flat Button', ATKP_PLUGIN_PREFIX).'</option>';                                
                                
                                echo '<option value="20" '.($selected == 20 ? 'selected' : '').'>'.__('No style', ATKP_PLUGIN_PREFIX).'</option>';  
                             ?>
</select>                   
                             </td>
                        </tr>
                        
                        
                        
                        <tr valign="top">
                            <th scope="row" style="background-color:gainsboro; padding:7px" colspan="2">
                                <?php _e('Text', ATKP_PLUGIN_PREFIX) ?>
                            </th>
                        </tr>
                        
     
                        
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Maximum title length', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input type="number" min="0" max="1000" id="<?php echo ATKP_PLUGIN_PREFIX.'_short_title_length' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_short_title_length' ?>" value="<?php echo get_option(ATKP_PLUGIN_PREFIX.'_short_title_length', 0); ?>">
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Maximum description length', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input type="number" min="0" max="1000" id="<?php echo ATKP_PLUGIN_PREFIX.'_description_length' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_description_length' ?>" value="<?php echo get_option(ATKP_PLUGIN_PREFIX.'_description_length', 0); ?>">
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Maximum feature count', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input type="number" min="0" max="1000" id="<?php echo ATKP_PLUGIN_PREFIX.'_feature_count' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_feature_count' ?>" value="<?php echo get_option(ATKP_PLUGIN_PREFIX.'_feature_count', 0); ?>">
                            </td>
                        </tr>
                        
                        
                        
                        
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Description & Features', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                            <select  id="<?php echo ATKP_PLUGIN_PREFIX.'_boxcontent' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_boxcontent' ?>" style="width:300px">
                                <?php
                                $selected = get_option(ATKP_PLUGIN_PREFIX.'_boxcontent');
                                                         
                                echo '<option value="1" '.($selected == '' || $selected == 1 ? 'selected' : '').' >'.__('Features and (if empty) Description ', ATKP_PLUGIN_PREFIX).'</option>';
                                 
                                echo '<option value="2" '.($selected == 2 ? 'selected' : '').'>'.__('Features', ATKP_PLUGIN_PREFIX).'</option>';                                
                                echo '<option value="3" '.($selected == 3 ? 'selected' : '').'>'.__('Description', ATKP_PLUGIN_PREFIX).'</option>';
                                
                             ?>
</select>                   
                             </td>
                        </tr>
                        
                        
                        <tr valign="top">
                            <th scope="row">                      
                            </th>
                            <td>
                                <?php submit_button('', 'primary', 'savedisplay', false); ?>
                            </td>
                        </tr>
                    </table>
                </form>    
            </div> <?php
        }
}
?>