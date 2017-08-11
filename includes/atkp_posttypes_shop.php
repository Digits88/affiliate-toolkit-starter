<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_posttypes_shop
    {   
        /**
         * Construct the plugin object
         */
        public function __construct($pluginbase)
        {
            $this->register_shopPostType();
            
            add_action( 'add_meta_boxes', array(&$this, 'list_boxes' ));
            add_action( 'save_post', array(&$this, 'list_detail_save' ));
            
            add_action( 'admin_enqueue_scripts', array($this, 'image_enqueue' ));
            add_action('admin_head', array($this, 'hidey_admin_head'));
				
				
            ATKPTools::add_column(ATKP_SHOP_POSTTYPE, __('Status', ATKP_PLUGIN_PREFIX), function($post_id){
                    $selwebservice = ATKPTools::get_post_setting($post_id, ATKP_SHOP_POSTTYPE.'_access_webservice');
        
                    if($selwebservice != ATKP_SUBSHOPTYPE) {
                        $error = ATKPTools::get_post_setting($post_id, ATKP_SHOP_POSTTYPE.'_access_message');
                        
                        if ($error == null || empty($error)) {
                            echo '<span style="color:green">' . __('Connected', ATKP_PLUGIN_PREFIX) . '</span>';
                        } else {                   
                            echo '<span style="color:red">' . __('Disconnected', ATKP_PLUGIN_PREFIX) . ' ('.$error . ')</span>';
                        }     
                        
                        if($selwebservice == '7') {
                            require_once ATKP_PLUGIN_DIR.'/includes/shopproviders/atkp_shop_provider_base.php';
                            require_once ATKP_PLUGIN_DIR.'/includes/shopproviders/atkp_shop_provider_csv.php';
                            $csvprovider = new atkp_shop_provider_csv(); 
                            
                            $rows =$csvprovider->count_imported_rows($post_id);
                            $lastimport = $csvprovider->get_lastimport_date($post_id);
                            echo '<br /><span>'.sprintf(__('%s rows imported. Last Import: %s', ATKP_PLUGIN_PREFIX), $rows, $lastimport).'</span>';
                            
                            unset($csvprovider);
                        }
                    }
                }, 3);
            
            ATKPTools::add_column(ATKP_SHOP_POSTTYPE, __('Logo', ATKP_PLUGIN_PREFIX), function($post_id){
					
					$webservice = ATKPTools::get_post_setting($post_id, ATKP_SHOP_POSTTYPE.'_access_webservice');
					
					if($webservice == '2' || $webservice == '3' || $webservice == '5') {
					    
					} else {			
                        try {
    				    	$shps = atkp_shop::load($post_id, true);
                            			
    					    $imageurl = $shps->get_logourl();
    					
        					if($imageurl != '')
            					echo '<img src="'.$imageurl.'" alt="'.esc_attr($shps->get_title()).'"  title="'.esc_attr($shps->get_title()).'"style="max-width:60px" />';
                        } catch (Exception $e) {
                            echo $e->getMessage();   
                        }
				    }
				}, 1);
        }
        
        /**
         * Loads the image management javascript
         */
        function image_enqueue() {
            global $typenow;
            if( $typenow == ATKP_SHOP_POSTTYPE ) {
                wp_enqueue_media();
         
                // Registers and enqueues the required javascript.
                wp_register_script( 'meta-box-image', plugin_dir_url( ATKP_PLUGIN_FILE ) . 'js/meta-box-image.js', array( 'jquery' ) );
                wp_localize_script( 'meta-box-image', 'meta_image',
                    array(
                        'title' => __( 'Choose or Upload an Image', ATKP_PLUGIN_PREFIX ),
                        'button' => __( 'Use this image', ATKP_PLUGIN_PREFIX ),
                    )
                );
                wp_enqueue_script( 'meta-box-image' );
            }
        }
        
        function hidey_admin_head() {
            echo '<style type="text/css">';
            echo '.column-'.sanitize_title(__('Logo', ATKP_PLUGIN_PREFIX)).' { width: 70px; }';
            echo '</style>';
        }
        
        function register_shopPostType() {
  $labels = array(
    'name'               => __( 'Shops', ATKP_PLUGIN_PREFIX ),
    'singular_name'      => __( 'Shop', ATKP_PLUGIN_PREFIX ),    
    'add_new_item'       => __( 'Add New Shop', ATKP_PLUGIN_PREFIX ),
    'edit_item'          => __( 'Edit Shop' , ATKP_PLUGIN_PREFIX),
    'new_item'           => __( 'New Shop' , ATKP_PLUGIN_PREFIX),
    'all_items'          => __( 'Shops' , ATKP_PLUGIN_PREFIX),
    'view_item'          => __( 'View Shop' , ATKP_PLUGIN_PREFIX),
    'search_items'       => __( 'Search Shops' , ATKP_PLUGIN_PREFIX),
    'not_found'          => __( 'No lists found' , ATKP_PLUGIN_PREFIX),
    'not_found_in_trash' => __( 'No lists found in the Trash' , ATKP_PLUGIN_PREFIX), 
    'parent_item_colon'  => '',
    'menu_name'          => __( 'Shops' , ATKP_PLUGIN_PREFIX),
  );
  $args = array(
    'labels'        => $labels,
    'description'   => 'Holds our Shop',
    
    'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
    'publicly_queriable' => true,  // you should be able to query it
    'show_ui' => true,  // you should be able to edit it in wp-admin
    'exclude_from_search' => true,  // you should exclude it from search results
    'show_in_nav_menus' => false,  // you shouldn't be able to add it to menus
    'has_archive' => false,  // it shouldn't have archive page
    'rewrite' => false,  // it shouldn't have rewrite rules
    'hierarchical' => true,
    'capability_type' => 'page',
    
    'menu_position' => 200,
    'supports'      => array( 'title'),
    'show_in_menu' => ATKP_PLUGIN_PREFIX.'_affiliate_toolkit-plugin',
  );
  register_post_type(ATKP_SHOP_POSTTYPE, $args );         
  }

function list_boxes() {
    
    add_meta_box( 
        ATKP_SHOP_POSTTYPE.'_detail_box',
        __( 'Shop Information', ATKP_PLUGIN_PREFIX),
        array(&$this, 'list_detail_box_content'),
        ATKP_SHOP_POSTTYPE,
        'normal',
        'default'
    );
    
}

function list_detail_box_content( $post ) {
        
    wp_nonce_field( plugin_basename( __FILE__ ), 'shop_detail_box_content_nonce' ); 

    require_once ATKP_PLUGIN_DIR.'/includes/shopproviders/atkp_shop_provider_base.php';

    $selwebservice = ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_access_webservice');
    
    $alreadysaved = (bool)$selwebservice != '';
    
    if($selwebservice == ATKP_SUBSHOPTYPE) {
        
        $parentid = wp_get_post_parent_id($post->ID);
        $title = get_the_title($parentid);
        
        if($title == '')
            $title = __('open shop', ATKP_PLUGIN_PREFIX);
        
        $access_test = '<a href="'.admin_url('/post.php?post='.$parentid.'&action=edit').'" target="_blank">'.$title.'</a>';
    } else {
        $error = ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_access_message');
        
        if(!$alreadysaved) {
            $error =     __('credentials empty', ATKP_PLUGIN_PREFIX);
        }
        
        if (($error == null || empty($error))) {
            $access_test = '<span style="color:green">' . __('Connected', ATKP_PLUGIN_PREFIX) . '</span>';
        } else {        
            $access_test = '<span style="color:red">' . __('Disconnected', ATKP_PLUGIN_PREFIX) . ' ('.$error . ')</span>';
        }       
    }

  ?>  
   <table class="form-table">
    <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Data distributor', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <select <?php echo ($alreadysaved ? 'disabled' : '') ?> name="<?php echo ATKP_SHOP_POSTTYPE.'_access_webservice' ?>" id="<?php echo ATKP_SHOP_POSTTYPE.'_access_webservice' ?>">
                                <?php
                                
                                if($selwebservice == ATKP_SUBSHOPTYPE) {
                                    echo '<option value="' . ATKP_SUBSHOPTYPE . '" selected>' . __('Subshop', ATKP_PLUGIN_PREFIX) . '</option>';
                                } else {
                                    
                                    $locations = atkp_shop_provider_base::retrieve_providers();
                                    
                                    
                                    foreach ($locations as $value => $provider) {
                                        if ($value == $selwebservice) 
                                            $sel = ' selected'; 
                                        else 
                                            $sel = '';
                                        
                                                                    
                                        echo '<option value="' . $value . '"' . $sel . '>' . $provider->get_caption() . '</option>';
                                    } 
                                
                                }
                                ?>
                                </select>
                            </td>
                        </tr>                        
   
    <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php if($selwebservice == ATKP_SUBSHOPTYPE) {  _e('Parent shop', ATKP_PLUGIN_PREFIX); } else { _e('Status', ATKP_PLUGIN_PREFIX); } ?>:
                                </label> 
                            </th>
                            <td>
                                <?php echo $access_test; ?>
                            </td>
                        </tr>
                        </table>
                        
                        <?php
                         if($selwebservice == ATKP_SUBSHOPTYPE) {
                                                        
                            ?>
                            <table class="form-table">
                            
                        
                            
                            <?php
                            
                            $this->output_detail($post);
                                
                         } else {                            
                            foreach ($locations as $value => $provider) {
                            
                                if($alreadysaved) {
                                    if ($value != $selwebservice) 
                                        continue;
                                }
                            
                                echo '<div id="api-'.$value.'">';
                                echo '<table class="form-table">';
                                echo $provider->get_configuration($post);
                                echo '</table>';
                                echo '</div>';
                            }
                        
                        
                        ?>                        
                        <table class="form-table">
                            
                                         
                             <?php 
                             if(ATKPTools::has_subshops($selwebservice)) {
                                 ?>
                                 <tr valign="top">
                                    <th scope="row">
                                        <label for="">
                                            <?php _e('Subshops', ATKP_PLUGIN_PREFIX) ?>:
                                        </label> 
                                    </th>
                                    <td>
                                 <div style="border:1px solid #ccc; width:600px; height: 250px; overflow-y: scroll;padding:5px"> <?php 
                                $defaultshops=  ATKPTools::get_post_setting( $post->ID, ATKP_SHOP_POSTTYPE.'_default_shops');
                                  
                                $selectedshops=  ATKPTools::get_post_setting( $post->ID, ATKP_SHOP_POSTTYPE.'_selected_shops');
                                  
                                if(is_array($defaultshops))
                                    foreach($defaultshops as $subshop) {
                                        $found = false;
                                        if(is_array($selectedshops)) {
                                            foreach($selectedshops as $selectedsubshop) 
                                                if($subshop->shopid == $selectedsubshop->shopid && $subshop->programid == $selectedsubshop->programid)
                                                {
                                                    $found = true;
                                                    break;
                                                }
                                        } else {
                                            //wenn selectedshop nicht gesetzt ist dann ist es noch von der alten subshop logik..
                                            if($subshop->enabled) 
                                                $found = true;
                                        }
                                        ?>
                                        
                                        <input type="checkbox" id="<?php echo ATKP_SHOP_POSTTYPE.'_subshop-'.$subshop->shopid.'-'.$subshop->programid ?>" name="<?php echo ATKP_SHOP_POSTTYPE.'_subshop-'.$subshop->shopid.'-'.$subshop->programid ?>" value="1" <?php echo checked(1, $found, true); ?>>
                                        <label for="<?php echo ATKP_SHOP_POSTTYPE.'_subshop-'.$subshop->shopid.'-'.$subshop->programid ?>">
                                            <?php echo $subshop->title . ($subshop->title2 != '' ? ' (' . $subshop->title2.')' : '') ?>
                                        </label><br />
                                        
                                        <?php
                                    }
                                 ?>
                                  
                                          
                                        </div>
                                    </td>
                                </tr>  
                                 
                                 <?php
                                 
                             } else if($alreadysaved) 
                                 $this->output_detail($post); 
                              if($alreadysaved) {
                             ?>                
                        
                        <tr valign="top">
                            <th scope="row">
                                
                            </th>
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_SHOP_POSTTYPE.'_displayshoplogo' ?>" name="<?php echo ATKP_SHOP_POSTTYPE.'_displayshoplogo' ?>" value="1" <?php echo checked(1, ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_displayshoplogo'), true); ?>>
                                 <label for="<?php echo ATKP_SHOP_POSTTYPE.'_displayshoplogo' ?>">
                                    <?php _e('Display Shop Logo', ATKP_PLUGIN_PREFIX) ?>
                                </label>
                            </td>
                        </tr>
                        
                       
                        
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php echo __('Tooltip', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input  style="width:30%" type="text" id="<?php echo ATKP_SHOP_POSTTYPE.'_text_tooltip' ?>" name="<?php echo ATKP_SHOP_POSTTYPE.'_text_tooltip' ?>" value="<?php echo esc_attr(ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_text_tooltip')); ?>">
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php echo __('Buy at Button', ATKP_PLUGIN_PREFIX).' (html)' ?>:
                                </label> 
                            </th>
                            <td>
                                <input  style="width:30%" type="text" id="<?php echo ATKP_SHOP_POSTTYPE.'_text_buyat' ?>" name="<?php echo ATKP_SHOP_POSTTYPE.'_text_buyat' ?>" value="<?php echo esc_attr(ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_text_buyat')); ?>">
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php echo __('Add to Cart Button', ATKP_PLUGIN_PREFIX).' (html)' ?>:
                                </label> 
                            </th>
                            <td>
                                <input  style="width:30%" type="text" id="<?php echo ATKP_SHOP_POSTTYPE.'_text_addtocart' ?>" name="<?php echo ATKP_SHOP_POSTTYPE.'_text_addtocart' ?>" value="<?php echo esc_attr( ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_text_addtocart')); ?>">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Currency sign', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                            <select  id="<?php echo ATKP_SHOP_POSTTYPE.'_currencysign' ?>" name="<?php echo ATKP_SHOP_POSTTYPE.'_currencysign' ?>" style="width:300px">
                                <?php
                                $selected = ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_currencysign');
                                                         
                                echo '<option value="1" '.($selected == '' || $selected == 1 ? 'selected' : '').' >'.__('&euro; sign', ATKP_PLUGIN_PREFIX).'</option>';
                                 
                                echo '<option value="2" '.($selected == 2 ? 'selected' : '').'>'.__('EUR', ATKP_PLUGIN_PREFIX).'</option>';      
                                
                                echo '<option value="3" '.($selected == 3 ? 'selected' : '').'>'.__('&#36; sign', ATKP_PLUGIN_PREFIX).'</option>';   
                                
                                echo '<option value="4" '.($selected == 4 ? 'selected' : '').'>'.__('USD', ATKP_PLUGIN_PREFIX).'</option>';   
                               
                               echo '<option value="5" '.($selected == 5 ? 'selected' : '').'>'.__('Default', ATKP_PLUGIN_PREFIX).'</option>';   
                               echo '<option value="6" '.($selected == 6 ? 'selected' : '').'>'.__('Userdefined', ATKP_PLUGIN_PREFIX).'</option>';   
                                 
                             ?>
                             
</select>                   <div id="customcurrencysign"><br />
<input  style="width:40px" type="text" id="<?php echo ATKP_SHOP_POSTTYPE.'_currencysign_customprefix' ?>" name="<?php echo ATKP_SHOP_POSTTYPE.'_currencysign_customprefix' ?>" value="<?php echo ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_currencysign_customprefix'); ?>"> 0,00 <input  style="width:40px" type="text" id="<?php echo ATKP_SHOP_POSTTYPE.'_currencysign_customsuffix' ?>" name="<?php echo ATKP_SHOP_POSTTYPE.'_currencysign_customsuffix' ?>" value="<?php echo ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_currencysign_customsuffix'); ?>">
                            </div>
                             </td>
                        </tr>
                        <?php } 
                        }?>
                         </table>
                        
                        
                        
                        
                        <script type="text/javascript">
                        var $j = jQuery.noConflict();
                        /*
                         * Attaches the image uploader to the input field
                         */
                        $j(document).ready(function($){
                         
                         
                         $j('#<?php echo ATKP_SHOP_POSTTYPE.'_currencysign' ?>').change(function () {
                                 
                            if($j('#<?php echo ATKP_SHOP_POSTTYPE.'_currencysign' ?>').val() == '6')
                                 $j('#customcurrencysign').show();
                            else
                                 $j('#customcurrencysign').hide();
                         });
                         
                         $j('#<?php echo ATKP_SHOP_POSTTYPE.'_currencysign' ?>').trigger("change");
                         
                            $j('#<?php echo ATKP_SHOP_POSTTYPE.'_access_webservice' ?>').change(function () {
                                    
                                    switch($j('#<?php echo ATKP_SHOP_POSTTYPE.'_access_webservice' ?>').val()) {
                                        <?php
                                        if($selwebservice != ATKP_SUBSHOPTYPE) 
                                            foreach ($locations as $value => $provider) {
                                
                                                echo 'case \''.$value.'\':';
                                                foreach ($locations as $value2 => $provider2) {
                                                    if($value2 == $value)                                                
                                                        echo '$j(\'#api-'.$value2.'\').show();';
                                                    else
                                                        echo '$j(\'#api-'.$value2.'\').hide();';  
                                                }
                                                echo 'break;';
                                            }
                                        ?>            
                                        
                                        
                                        
                                    }
                                        
                                         
                                
                                        
                                        
                            });
                            
                             $j('#<?php echo ATKP_SHOP_POSTTYPE.'_access_webservice' ?>').trigger("change");
                         
                          
                        });
                        
                        </script>
  
  <?php 
}

function output_detail($post) {
    $customtitle = ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_customtitle');
    $customsmalllogourl = ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_customsmalllogourl');
    $customlogourl = ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_customlogourl');
    $customfield1 = ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_customfield1');
    $customfield2 = ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_customfield2');
    $customfield3 = ATKPTools::get_post_setting($post->ID, ATKP_SHOP_POSTTYPE.'_customfield3');
    
     $subshops=  ATKPTools::get_post_setting( $post->ID, ATKP_SHOP_POSTTYPE.'_default_shops');
     
    // if(is_array($subshops))
    //    var_dump($subshops);
    
    if(is_array($subshops))
        $subshop = $subshops[0];
 ?>

                            <tr>
                                <th scope="row">
                                    <label for="">
                                        <?php _e('Title', ATKP_PLUGIN_PREFIX) ?>:
                                    </label> 
                                </th>
                                <td >
                                    <input  style="width:40%" type="text" id="<?php echo ATKP_SHOP_POSTTYPE.'_customtitle' ?>" name="<?php echo ATKP_SHOP_POSTTYPE.'_customtitle' ?>" value="<?php echo esc_attr($customtitle == '' && isset($subshop) ? $subshop->title : $customtitle); ?>">
                                </td>
                            </tr>
                            
                            <tr>
                              
                                <th scope="row"  >
                                    <label for="">
                                        <?php _e('Shop Logo Small', ATKP_PLUGIN_PREFIX) ?>:
                                    </label> 
                                </th>
                                <td >
                                <?php
                                $smallimageurl = $customsmalllogourl == '' && isset($subshop) ? $subshop->smalllogourl : $customsmalllogourl;
                                $logourl = $customlogourl == '' && isset($subshop) ? $subshop->logourl : $customlogourl;
                                
                                if($smallimageurl != '') {
                                ?>
                                <img id="logosmall-preview" src="<?php echo $smallimageurl; ?>" style="max-width:250px"><br />
                                <?php } ?>
                                
                                    <input  style="width:40%" type="url" id="<?php echo ATKP_SHOP_POSTTYPE.'_customsmalllogourl' ?>" name="<?php echo ATKP_SHOP_POSTTYPE.'_customsmalllogourl' ?>" value="<?php echo esc_attr($smallimageurl); ?>">
                                <input type="button" id="smallimage-button" class="button meta-image-button" value="<?php _e( 'Choose or Upload an Image', ATKP_PLUGIN_PREFIX)?>" />
                                </td>
                            </tr>
                            
                            <tr>
                               
                                <th scope="row"  >
                                    <label for="">
                                        <?php _e('Shop Logo Large', ATKP_PLUGIN_PREFIX) ?>:
                                    </label> 
                                </th>
                                <td >
                                <?php if($logourl != '') {
                                ?>
                                <img id="logo-preview" src="<?php echo $logourl; ?>" style="max-width:250px"><br />
                                <?php } ?>
                                
                                    <input  style="width:40%" type="url" id="<?php echo ATKP_SHOP_POSTTYPE.'_customlogourl' ?>" name="<?php echo ATKP_SHOP_POSTTYPE.'_customlogourl' ?>" value="<?php echo esc_attr($logourl); ?>">
                                    <input type="button" id="largeimage-button" class="button meta-image-button" value="<?php _e( 'Choose or Upload an Image', ATKP_PLUGIN_PREFIX)?>" />
                                
                                
                                
                                <script type="text/javascript">
                                var $j = jQuery.noConflict();
                                /*
                                 * Attaches the image uploader to the input field
                                 */
                                $j(document).ready(function($){
                                 
                                    // Instantiates the variable that holds the media library frame.
                                    var meta_image_frame;
                                    var image_button;
                                    // Runs when the image button is clicked.
                                    $j('.meta-image-button').click(function(e){
                                 
                                        // Prevents the default action from occuring.
                                        e.preventDefault();
                                 
                                        // If the frame already exists, re-open it.
                                        //if ( meta_image_frame ) {
                                        //    meta_image_frame.open();
                                        //    return;
                                        //}
                                 
                                        // Sets up the media library frame
                                        meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
                                            title: meta_image.title,
                                            button: { text:  meta_image.button },
                                            library: { type: 'image' }
                                        });
                                 
                                        image_button = $j(this).attr('id');       
                                     
                                        // Runs when an image is selected.
                                        meta_image_frame.on('select', function(){
                                 
                                            // Grabs the attachment selection and creates a JSON representation of the model.
                                            var media_attachment = meta_image_frame.state().get('selection').first().toJSON();
                                
                                             // Sends the attachment URL to our custom image input field.
                                            if(image_button == $j('#smallimage-button').attr('id'))
                                                $j('#<?php echo ATKP_SHOP_POSTTYPE.'_customsmalllogourl' ?>').val(media_attachment.url);
                                            else if(image_button == $j('#largeimage-button').attr('id'))
                                                $j('#<?php echo ATKP_SHOP_POSTTYPE.'_customlogourl' ?>').val(media_attachment.url);          
                                        });
                                 
                                        // Opens the media library frame.
                                        meta_image_frame.open();
                                    });
                                });
                                                        
                                                        </script>
                                </td>
                            </tr>
                            
                           
 <?php
}

function list_detail_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;
		
	$posttype =  ATKPTools::get_post_parameter('post_type', 'string');

	if (ATKP_SHOP_POSTTYPE != $posttype ) {
		return;
	}
	
	$nounce =  ATKPTools::get_post_parameter('shop_detail_box_content_nonce', 'string');
  
	if(!wp_verify_nonce($nounce, plugin_basename( __FILE__ ) ) )
		return;

	//speichern der einstellungen
                

    require_once ATKP_PLUGIN_DIR.'/includes/shopproviders/atkp_shop_provider_base.php';
    
    $webservice = ATKPTools::get_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_access_webservice');
    
    if($webservice == '' || $webservice == null)
    {
        $webservice = ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_access_webservice', 'string');
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_access_webservice', $webservice);
    }
    
    if($webservice != '' && $webservice != ATKP_SUBSHOPTYPE) {
        $myprovider = atkp_shop_provider_base::retrieve_provider($webservice);
                            
        if($myprovider == null)
    		throw new Exception('provider not found: ' . $webservice);
    }    
        
    if($webservice == ATKP_SUBSHOPTYPE || ($webservice != '2' && $webservice != '3' && $webservice != '5')) {
   
        if(ATKPTools::exists_post_parameter(ATKP_SHOP_POSTTYPE.'_customtitle'))
            ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_customtitle', ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_customtitle', 'string'));
        if(ATKPTools::exists_post_parameter(ATKP_SHOP_POSTTYPE.'_customsmalllogourl'))
            ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_customsmalllogourl', ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_customsmalllogourl', 'url'));
        if(ATKPTools::exists_post_parameter(ATKP_SHOP_POSTTYPE.'_customlogourl'))
            ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_customlogourl', ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_customlogourl', 'url'));
    }
    
    if(ATKPTools::exists_post_parameter(ATKP_SHOP_POSTTYPE.'_currencysign')) {
        
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_displayshoplogo', ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_displayshoplogo', 'bool'));
    }
    
    $tooltip = ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_text_tooltip', 'string');
    
	$buyattext = ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_text_buyat', 'html');
	$addtocarttext = ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_text_addtocart', 'html');
			
    if(isset($myprovider)) {
    	if($buyattext == null || $buyattext == '') 
    		$buyattext = $myprovider->get_defaultbtn1_text();
    	if($addtocarttext == null || $addtocarttext == '') 
    		$addtocarttext = $myprovider->get_defaultbtn2_text();
    	if($tooltip == null || $tooltip == '')
    	    $tooltip = __('Buy now at %s', ATKP_PLUGIN_PREFIX);
    }
	
	ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_text_tooltip', $tooltip);
	ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_text_buyat', $buyattext);
	ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_text_addtocart', $addtocarttext);
    
    if(ATKPTools::exists_post_parameter(ATKP_SHOP_POSTTYPE.'_currencysign'))
	    ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_currencysign', ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_currencysign', 'string'));
    
    if(ATKPTools::exists_post_parameter(ATKP_SHOP_POSTTYPE.'_currencysign_customprefix'))
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_currencysign_customprefix', ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_currencysign_customprefix', 'string'));
    if(ATKPTools::exists_post_parameter(ATKP_SHOP_POSTTYPE.'_currencysign_customsuffix'))
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_currencysign_customsuffix', ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_currencysign_customsuffix', 'string'));
    
    if(isset($myprovider)) {
        $myprovider->set_configuration($post_id);
        
        $message = $myprovider->check_configuration($post_id);
        
        $defaultshops = ATKPTools::get_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_default_shops');
        $selectedshops = array();
        
        if(is_array($defaultshops))
            foreach($defaultshops as $subshop) {
                
                if(ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_subshop-'.$subshop->shopid.'-'.$subshop->programid, 'bool') == true) {
                    array_push($selectedshops, $subshop);
                }
            }
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_selected_shops', $selectedshops);
        
        $defaultshops = $selectedshops;
        
        $title = get_the_title($post_id);
         
        $subshops = array();
        $subshops_saved = get_posts( array( 'post_parent' => $post_id, 'post_type' => ATKP_SHOP_POSTTYPE, 'numberposts' => -1, 'post_status' => array('draft', 'publish') ) );

        foreach($subshops_saved as $sub) {   
            
            array_push($subshops, $sub->ID);
        }
        
        unset($subshops_saved);
        
        $subshops_saved = array();
         
        if(is_array($defaultshops))
            foreach($defaultshops as $subshop) {
               
                $subshopid = ATKPTools::create_subshop( $subshop->title,$subshop->shopid, $subshop->programid, $post_id, $subshop);
                
                array_push($subshops_saved, $subshopid);
            }
            
            
        //var_dump($subshops);exit;
        foreach($subshops as $subshop) {
            $found = false;
            foreach($subshops_saved as $subshop_saved) 
                if($subshop_saved == $subshop) {
                    $found = true;
                    break;
                }
            if(!$found)
                wp_delete_post($subshop);
        }
    
        
        
       
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_access_message', $message);    
    }
    
    }

    }
    
?>