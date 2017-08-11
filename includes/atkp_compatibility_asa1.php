<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_compatibility_asa1
    {
        /**
         * Construct the plugin object
         */
        public function __construct($pluginbase)
        {
        }
        
        function substr_startswith($haystack, $needle) {
            return substr($haystack, 0, strlen($needle)) === $needle;
        }


        public function compatibilitymode_configuration_page()
        {        
            if (ATKPTools::exists_post_parameter('savecompatibilitymode') && check_admin_referer('save', 'save')) {
                if (!current_user_can('manage_options')) {
                    wp_die(__('You do not have sufficient permissions to access this page', ATKP_PLUGIN_PREFIX));
            }

            $isactive = ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_asa_activate', 'bool'); 
            $shopid = ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_asa_shopid', 'string'); 
            $allcollections = ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_asa_allcollections', 'bool'); 
            $poststatus = ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_asa_poststatus', 'string'); 
            
           update_option(ATKP_PLUGIN_PREFIX.'_asa_activate', $isactive);
           update_option(ATKP_PLUGIN_PREFIX.'_asa_allcollections', $allcollections);
           update_option(ATKP_PLUGIN_PREFIX.'_asa_shopid', $shopid);
           update_option(ATKP_PLUGIN_PREFIX.'_asa_poststatus', $poststatus);
                          
           for ($i = 1; $i <= 5; $i++) {
                update_option(ATKP_PLUGIN_PREFIX.'_asa_templatename'.$i,  ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_asa_templatename'.$i, 'string'));
                update_option(ATKP_PLUGIN_PREFIX.'_asa_templateid'.$i, ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_asa_templateid'.$i, 'string'));
           }
           
            if($isactive) {
                $importresults = array();
                
                try {
                    array_push( $importresults, '*** import started: '. date("Y-m-d H:i:s"));
                    
                    if($shopid != '') {
                        require_once ATKP_PLUGIN_DIR.'/includes/helper/atkp_asa1_helper.php';                        
                        
                        $asahelper = new atkp_asa1_helper();
                        
                        $args = array(
                                'post_type'    => array( 'page', 'post' ),
                                'post_status'=>'publish', 
                                'posts_per_page'=>-1
                            );
                        
                        $posts = new WP_Query ( $args );
                        
                        while($posts->have_posts()) : 
                             $posts->the_post();
                    
                            $asahelper->createProductsFromPost(get_the_ID(), get_the_content(), $importresults);
                    
                            $asahelper->createListsFromPost(get_the_ID(), get_the_content(), $importresults);
                    
                        endwhile;
                         
                        wp_reset_postdata();
                        
                        if($allcollections) {
                            $asahelper->createAllLists($importresults);
                        }
                    } else {
                        array_push( $importresults, 'default shop required');
                    }
                                    
                    array_push( $importresults, '*** import finished: '. date("Y-m-d H:i:s"));
                                    
                    
                } catch(Exception $e) {
        	        array_push( $importresults, '*** global exception: '. $e->getMessage());
        	    } 
        	                	    
                update_option(ATKP_PLUGIN_PREFIX.'_asa_importresult', implode("\n", $importresults));
            }
        
        }
        $mytab = ATKPTools::get_get_parameter( 'tab', 'int');
		
		if ($mytab != 0 ) $tab = $mytab; else $tab = 1;
                    ?>
        <div class="wrap">
           <!-- <h2><?php _e('Affiliate Toolkit - Compatibility mode', ATKP_PLUGIN_PREFIX) ?></h2>      -->      
            
            <form method="POST" action="?page=<?php echo ATKP_PLUGIN_PREFIX.'_affiliate_toolkit-compatibility&tab='.$tab ?>"><!--_affiliate_toolkit-bestseller-->
                <?php wp_nonce_field("save", "save"); ?>
                <table class="form-table" style="width:1024px">
                     <!--<tr valign="top">
                        <th scope="row" style="background-color:gainsboro; padding:7px" colspan="2">
                            <?php _e('Amazon Simple Admin 1', ATKP_PLUGIN_PREFIX) ?>
                        </th>
                    </tr>-->
                     <tr valign="top">
                        <th scope="row">
                           
                        </th>
                        <td>
                            <input type="checkbox" id="<?php echo ATKP_PLUGIN_PREFIX.'_asa_activate' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_asa_activate' ?>" value="1" <?php echo checked(1, get_option(ATKP_PLUGIN_PREFIX.'_asa_activate',0), true); ?>>
                         <label for="<?php echo ATKP_PLUGIN_PREFIX.'_asa_activate' ?>">
                                <?php _e('Activate Compatibility mode', ATKP_PLUGIN_PREFIX) ?>
                            </label>
                        </td>
                    </tr>
                    
                   <tr valign="top">
                        <th scope="row">
                            <label for="">
                                <?php _e('Shop (default)', ATKP_PLUGIN_PREFIX) ?>:
                            </label> 
                        </th>
                        <td>
                        <select id="<?php echo ATKP_PLUGIN_PREFIX.'_asa_shopid' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_asa_shopid' ?>" style="width:300px">                            
                            <?php
                            require_once ATKP_PLUGIN_DIR.'/includes/atkp_shop.php';
                              
                            $selectedshopid = get_option( ATKP_PLUGIN_PREFIX.'_asa_shopid') ;
                            
                            $shps = atkp_shop::get_list($selectedshopid);
                                
                            foreach($shps as $shp) {
                                if ($shp->selected == true) 
                                        $sel = ' selected'; 
                                    else 
                                        $sel = '';
                                                 
                                echo '<option value="' .$shp->id . '"' . $sel . ' > ' .  esc_attr($shp->title) . '</option>';
                            }
                             ?>
                        </select>
                            
                           
                        
                         </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row">
                            <label for="">
                                <?php _e('Product status (default)', ATKP_PLUGIN_PREFIX) ?>:
                            </label> 
                        </th>
                        <td>
                            <select name="<?php echo ATKP_PLUGIN_PREFIX.'_asa_poststatus' ?>">
                            <?php
                            
                            $durations = array(
                                                'publish' => __('Publish', ATKP_PLUGIN_PREFIX),
                                                'draft' => __('Draft', ATKP_PLUGIN_PREFIX),
                                              );
                            
                            foreach ($durations as $value => $name) {
                                if ($value == get_option(ATKP_PLUGIN_PREFIX.'_asa_poststatus', 'publish')) 
                                    $sel = ' selected'; 
                                else 
                                    $sel = '';
                                
                                $item_translated = '';
                                                            
                                echo '<option value="' . $value . '"' . $sel . '>' . $name . '</option>';
                            } ?>
                            </select>                   
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row">
                           
                        </th>
                        <td>
                            <input type="checkbox" id="<?php echo ATKP_PLUGIN_PREFIX.'_asa_allcollections' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_asa_allcollections' ?>" value="1" <?php echo checked(1, get_option(ATKP_PLUGIN_PREFIX.'_asa_allcollections',0), true); ?>>
                         <label for="<?php echo ATKP_PLUGIN_PREFIX.'_asa_allcollections' ?>">
                                <?php _e('Import all Collection (not only used)', ATKP_PLUGIN_PREFIX) ?>
                            </label>
                        </td>
                    </tr>
                    
                     <tr>
                        <th scope="row">
                            <label for="">
                                <?php _e('ASA Template mapping', ATKP_PLUGIN_PREFIX) ?>:
                            </label> 
                        </th>
                        <td>
                       <?php for ($i = 1; $i <= 5; $i++) {
                       $sel = get_option(ATKP_PLUGIN_PREFIX.'_asa_templateid'.$i, '');
                       
                       ?>
                            <input type="text" id="<?php echo ATKP_PLUGIN_PREFIX.'_asa_templatename'.$i ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_asa_templatename'.$i ?>" value="<?php echo  get_option(ATKP_PLUGIN_PREFIX.'_asa_templatename'.$i, ''); ?>">
                            <select name="<?php echo ATKP_PLUGIN_PREFIX.'_asa_templateid'.$i ?>" id="<?php echo ATKP_PLUGIN_PREFIX.'_asa_templateid'.$i ?>">
                            
                            <?php
                                echo '<option value="">'.__('default', ATKP_PLUGIN_PREFIX).'</option>';
                                
                                echo '<option value="bestseller" '.selected( 'bestseller', $sel, false ).'>'.__('bestseller', ATKP_PLUGIN_PREFIX).'</option>';
                                echo '<option value="wide" '.selected( 'wide', $sel, false ).'>'.__('wide', ATKP_PLUGIN_PREFIX).'</option>';
                                echo '<option value="secondwide" '.selected( 'secondwide', $sel, false ).'>'.__('secondwide', ATKP_PLUGIN_PREFIX).'</option>';
                                echo '<option value="box" '.selected( 'box', $sel, false ).'>'.__('box', ATKP_PLUGIN_PREFIX).'</option>';
                                                                    
                                if(ATKP_PLUGIN_VERSION >= 30) {
                                    global $post;
                                    $args = array( 'post_type' => ATKP_TEMPLATE_POSTTYPE, 'posts_per_page'   => 300, 'post_status'      => 'publish');
                                    $posts_array = get_posts($args);
                                    foreach ( $posts_array as $prd ) { 
                                        echo '<option value="' .$prd->ID . '" '.selected( $prd->ID , $sel, false ).'>' . $prd->post_title.' ('.$prd->ID.')' . '</option>';
                                     };
                                }
                            ?>
                            
                            
                            </select>
                            
                            <br />
                        <?php } ?>      
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="">
                                <?php _e('Last import result', ATKP_PLUGIN_PREFIX) ?>:
                            </label> 
                        </th>
                        <td>
                        <?php
                        $asa_importresult = get_option(ATKP_PLUGIN_PREFIX.'_asa_importresult');
                        
                        
                        ?>
                            
                            <textarea readonly style="width:100%;height:400px" id="<?php echo ATKP_PLUGIN_PREFIX.'_asa_importresult' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_asa_importresult' ?>"><?php echo esc_textarea($asa_importresult); ?></textarea>
		
                        </td>
                    </tr>
                    <tr >
                    
<td colspan="2"><?php _e('NOTE: When the compatibility mode is enabled, all posts and pages are analyzed. The products and lists are automatically created. This can take (depending on size) several minutes to complete.', ATKP_PLUGIN_PREFIX) ?></td>
</tr>

                    <tr valign="top">
                     
                        <td>
                            <?php submit_button('', 'primary', 'savecompatibilitymode', false); ?> 
                        </td>
                    </tr>
                </table>
            </form>    
       
            
        </div> <?php
    }
}
?>