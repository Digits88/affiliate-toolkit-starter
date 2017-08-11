<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_posttypes_product
    {   
        /**
         * Construct the plugin object
         */
        public function __construct($pluginbase)
        {
            $this->register_productPostType();
            
            $enableProductpages = get_option(ATKP_PLUGIN_PREFIX.'_product_enabled', false);
            if($enableProductpages) {
                add_filter( 'the_content', array(&$this, 'my_the_content_filter'), 0); 
            }
                       
			if(is_admin()) {
				
				add_action( 'add_meta_boxes', array(&$this, 'product_boxes' ));
				add_action( 'save_post', array(&$this, 'product_detail_save' )); 
						   
				add_thickbox();
				
				ATKPTools::add_column(ATKP_PRODUCT_POSTTYPE, __('Status', ATKP_PLUGIN_PREFIX), function($post_id){
					
					$selectedshopid = ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_shopid', 0); 
                
                    
                    if($selectedshopid != '')
                        $shps = atkp_shop::load($selectedshopid, false);
                    
                    if(!isset($shps) || $shps == null)
                        echo '<span>'.__('Manual product', ATKP_PLUGIN_PREFIX).'</span>';
                    else
                        echo '<span>'. __('Shop', ATKP_PLUGIN_PREFIX).':</span> <span>'.$shps->title.'</span>';
					
					$updatedon = ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_updatedon', true );
					
					if(isset($updatedon) && $updatedon != '') {
						$infotext = __('%refresh_date% at %refresh_time%', ATKP_PLUGIN_PREFIX);
						
						$infotext = str_replace('%refresh_date%',  date_i18n( get_option( 'date_format' ), $updatedon), $infotext);
						$infotext = str_replace('%refresh_time%',  date_i18n( get_option( 'time_format' ), $updatedon), $infotext);
																				
						echo '<br /><span>'. __('Updated on', ATKP_PLUGIN_PREFIX).':</span> <span>'.$infotext.'</span>';
					}
					
					$asin = ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_asin', true );
					$asintype =  ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_asintype', true );
					
					if($asin != '') {
    					echo '<br /><span>'. __('Key', ATKP_PLUGIN_PREFIX).':</span> <span>'.$asin.'</span>';
					    echo '<br /><span>'. __('Type', ATKP_PLUGIN_PREFIX).': ';
					
					       if($asintype == 'TITLE')
					            echo __('Title', ATKP_PLUGIN_PREFIX);
					       else if($asintype == 'EAN')
					            echo __('EAN', ATKP_PLUGIN_PREFIX);
					        else 
					            echo __('Unique productid', ATKP_PLUGIN_PREFIX);
					   echo '</span>';
					}
					$message = ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_message', true );
										
					if(isset($message) && $message != '') {
						echo '<br /><span>'. __('Message', ATKP_PLUGIN_PREFIX).':</span> <span style="color:red">'.$message.'</span>';
					}
					
					
				}, 3);
				
				add_action( 'admin_enqueue_scripts', array($this, 'image_enqueue' ));
				add_action('admin_head', array($this, 'hidey_admin_head'));
				
				
				ATKPTools::add_column(ATKP_PRODUCT_POSTTYPE, __('Main image', ATKP_PLUGIN_PREFIX), function($post_id){
					
					$imageurl = ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_smallimageurl', true );
					if($imageurl == '')
					    $imageurl = ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_mediumimageurl', true );
					if($imageurl == '')
					    $imageurl = ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_largeimageurl', true );
					
					if($imageurl != '')
    					echo '<img src="'.$imageurl.'" style="max-width:60px" />';
				}, 1);
			}
        }
        
        

        function hidey_admin_head() {
            echo '<style type="text/css">';
            echo '.column-'.sanitize_title(__('Main image', ATKP_PLUGIN_PREFIX)).' { width: 70px; }';
            echo '</style>';
        }
        
        /**
         * Loads the image management javascript
         */
        function image_enqueue() {
            global $typenow;
            if( $typenow == ATKP_PRODUCT_POSTTYPE ) {
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
        
                
        function register_productPostType() {
              $labels = array(
                'name'               => __( 'Products', ATKP_PLUGIN_PREFIX),
                'singular_name'      => __( 'Product', ATKP_PLUGIN_PREFIX ),
                'add_new_item'       => __( 'Add New Product' , ATKP_PLUGIN_PREFIX),
                'edit_item'          => __( 'Edit Product', ATKP_PLUGIN_PREFIX ),
                'new_item'           => __( 'New Product', ATKP_PLUGIN_PREFIX ),
                'all_items'          => __( 'Products', ATKP_PLUGIN_PREFIX ),
                'view_item'          => __( 'View Product' , ATKP_PLUGIN_PREFIX),
                'search_items'       => __( 'Search Products' , ATKP_PLUGIN_PREFIX),
                'not_found'          => __( 'No products found', ATKP_PLUGIN_PREFIX ),
                'not_found_in_trash' => __( 'No products found in the Trash' , ATKP_PLUGIN_PREFIX), 
                'parent_item_colon'  => '',
                'menu_name'          => __( 'AT Products', ATKP_PLUGIN_PREFIX ),
              );
              
             $enableProductpages = false;
             $enableComments = false;
             $productslug = get_option(ATKP_PLUGIN_PREFIX.'_product_slug');
             if($productslug =='')
                $productslug = 'product';
             
              
              $rewrite = false;
              $supports = array( 'title' );
              
              if($enableProductpages) {
                  $rewrite = array(
                    'slug'                => $productslug,
                    'with_front'          => true,
                    'pages'               => true,
                    'feeds'               => true,
                );
                
                if($enableComments)
                    $supports = array( 'title', 'comments', 'author', 'trackbacks', 'editor', 'thumbnail' );
                else
                    $supports = array( 'title', 'author', 'thumbnail' );
              }
              
              $args = array(
                'labels'        => $labels,
                'description'   => 'Holds our products and product specific data',
                
                'public' => $enableProductpages,  // it's not public, it shouldn't have it's own permalink, and so on
                'publicly_queriable' => $enableProductpages,  // you should be able to query it
                'show_ui' => true,  // you should be able to edit it in wp-admin
                'exclude_from_search' => !$enableProductpages,  // you should exclude it from search results
                'show_in_nav_menus' => true,  // you shouldn't be able to add it to menus
                'has_archive' => $enableProductpages,  // it shouldn't have archive page
                'rewrite' => $rewrite,
                'taxonomies' => array(),
                'query_var' => true,
                
                'menu_position' => 20,
                'supports'      => $supports,
                'capability_type' => 'post',
                
              );
              register_post_type(ATKP_PRODUCT_POSTTYPE, $args );  
                    
                    
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_udfield.php';
                
                $taxonomies = atkp_udtaxonomy::load_taxonomies();
                
                if($taxonomies != null) 
                foreach($taxonomies as $taxonomy) {
					//falls taxonomie bereits von anderem plugin installiert wurde
					if(taxonomy_exists($taxonomy->name) || $taxonomy->name == '')
						continue;
					
					if($taxonomy->caption == '')
						$taxonomy->caption = $taxonomy->name;
					
					if($taxonomy->captionplural == '')
						$taxonomy->captionplural = $taxonomy->caption;					
						 
                    $labels = array(
                		'name'              => $taxonomy->captionplural,
                		'singular_name'     => $taxonomy->caption,
                	    'search_items'      => sprintf(__('Search %s', ATKP_PLUGIN_PREFIX), $taxonomy->captionplural),
                		'all_items'         =>  sprintf(__('All %s', ATKP_PLUGIN_PREFIX), $taxonomy->captionplural),
                		'edit_item'         =>  sprintf(__('Edit %s', ATKP_PLUGIN_PREFIX), $taxonomy->caption),
                		'update_item'       =>  sprintf(__('Update %s', ATKP_PLUGIN_PREFIX), $taxonomy->caption),
                		'add_new_item'      =>  sprintf(__('Add New %s', ATKP_PLUGIN_PREFIX), $taxonomy->caption),
                		'new_item_name'     =>  sprintf(__('New %s', ATKP_PLUGIN_PREFIX), $taxonomy->caption),
                		'menu_name'         => $taxonomy->captionplural
                	);
                	
                	// register taxonomy
					
					register_taxonomy($taxonomy->name, ATKP_PRODUCT_POSTTYPE, array(
						'hierarchical' => true,
						'labels' => $labels,
						'query_var' => true,
						'show_admin_column' => false,
						'show_ui' => $taxonomy->showui,
						'public' => $enableProductpages,
						'capabilities' => array(
                            'manage_terms' => 'edit_posts',
                            'edit_terms' => 'edit_posts',
                            'delete_terms' => 'edit_posts',
                            'assign_terms' => 'edit_posts'
                        )
					) );
                }
            	
            	
          }
          
        function my_the_content_filter($content) {
              global $post;
    
                if (ATKP_PRODUCT_POSTTYPE == get_post_type() ){
                    
                    if(is_single()) {                
                          $producttemplate = stripslashes( get_option(ATKP_PLUGIN_PREFIX.'_product_template'));
                          
                          if($producttemplate != '') {
                            $producttemplate= str_replace('%content%', $content, $producttemplate);
                          
                            return  html_entity_decode($producttemplate); 
                          }
                          
                    } else if (is_archive()) {
                        $producttemplate = stripslashes(get_option(ATKP_PLUGIN_PREFIX.'_product_archivetemplate'));
                          if($producttemplate != '') {
                              $producttemplate= str_replace('%content%', $content, $producttemplate);
                            
                            return  html_entity_decode($producttemplate); 
                          }
                    }
                }
              // otherwise returns the database content
              return $content;
        }

          
          
          
function product_boxes() {
    
    add_meta_box( 
        ATKP_PRODUCT_POSTTYPE.'_shop_box',
        __( 'Shop Information', ATKP_PLUGIN_PREFIX),
        array(&$this, 'product_shop_box_content'),
        ATKP_PRODUCT_POSTTYPE,
        'normal',
        'default'
    );
    
    add_meta_box( 
        ATKP_PRODUCT_POSTTYPE.'_detail_box',
        __( 'Detail Information', ATKP_PLUGIN_PREFIX),
        array(&$this, 'product_detail_box_content'),
        ATKP_PRODUCT_POSTTYPE,
        'normal',
        'default'
    );
    
}



function product_shop_box_content( $post ) {
  wp_nonce_field( plugin_basename( __FILE__ ), 'product_shop_box_content_nonce' ); 
  
   require_once ATKP_PLUGIN_DIR.'/includes/atkp_shop.php';
  
  ?>  
   <table class="form-table">
   <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Shop', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                            <select id="<?php echo ATKP_PRODUCT_POSTTYPE.'_shopid' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_shopid' ?>" style="width:300px">                            
                                <?php
                                
                                $selectedshopid = ATKPTools::get_post_setting( $post->ID, ATKP_PRODUCT_POSTTYPE.'_shopid') ;
                                
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
                            <th scope="row" style="padding: 15px 0px;">
                                <select name="<?php echo ATKP_PRODUCT_POSTTYPE.'_asintype' ?>" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_asintype' ?>" style="width:100%">
                                    <?php
                                    $asintype =  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_asintype', true );
                                    if($asintype == '' || $asintype == null)
                                        $asintype = 'ASIN';
                                    
                                    $durations = array(
                                                        'ASIN' => __('Unique productid', ATKP_PLUGIN_PREFIX),
                                                        'EAN' => __('EAN', ATKP_PLUGIN_PREFIX),
                                                        'TITLE' => __('Title', ATKP_PLUGIN_PREFIX),   
                                                      );
                                    
                                    foreach ($durations as $value => $name) {
                                        if ($value == $asintype) 
                                            $sel = ' selected'; 
                                        else 
                                            $sel = '';
                                               
                                        echo '<option value="' . $value . '"' . $sel . '>' . esc_attr($name) . '</option>';
                                    } ?>
                                </select> 
                                
                                <script type="text/javascript">
                                    var $j = jQuery.noConflict();
                                    /*
                                     * Attaches the image uploader to the input field
                                     */
                                    $j(document).ready(function($){
                                     
                                     
                                     $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_asintype' ?>').change(function () {
                                             
                                        if($j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_asintype' ?>').val() == 'ASIN')
                                             $j('#searchproduct-button').show();
                                        else
                                             $j('#searchproduct-button').hide();
                                     });
                                     
                                        
                                         $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_asintype' ?>').trigger("change");
                                     
                                      
                                    });
                                
                                </script>
                            </th>
                            <td>
                                <input type="text" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_asin' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_asin' ?>" value="<?php echo  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_asin', true ); ?>"> <label id="<?php echo ATKP_PRODUCT_POSTTYPE.'_asin_caption' ?>"><?php echo  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_asin_caption', true ); ?></label>    <br />                         
                                <input type="button" id="searchproduct-button" class="button product-lookup thickbox" title="<?php _e( 'Search unique productid', ATKP_PLUGIN_PREFIX)?>" alt="#TB_inline?height=400&amp;width=500&amp;inlineId=modal-asin-lookup" value="<?php _e( 'Search unique productid', ATKP_PLUGIN_PREFIX)?>" />
                                
                            </td>
                        </tr>
                                            
                        
                        
                        <?php
                        
                        $updatedon = ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_updatedon', true );
    $message = ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_message', true );

                        ?>
                        
                        <tr valign="top">
                            <th scope="row">
                            </th>
                            <td><i>
                                <?php 
                                if(isset($updatedon) && $updatedon != '') {
                                    $infotext = __('Product updated on %refresh_date% at %refresh_time%', ATKP_PLUGIN_PREFIX);
                                    
                                    $infotext = str_replace('%refresh_date%',  date_i18n( get_option( 'date_format' ), $updatedon), $infotext);
                                    $infotext = str_replace('%refresh_time%',  date_i18n( get_option( 'time_format' ), $updatedon), $infotext);
                                    
                                    
                                echo $infotext; ?><br /><?php } ?>
                                <?php echo  '<span style="color:red;">'.$message.'</span>'; ?>
                            </i></td>
                        </tr>
   </table>
   
   
   
   <div id="modal-asin-lookup" style="display:none;">
   
   <div class="atkp-lookupbox"> 
    <div><label for=""><?php _e('Keyword:', ATKP_PLUGIN_PREFIX) ?></label> <input type="text" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupsearch' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupsearch' ?>" value="">  <input type="submit" class="button" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupbtnsearch' ?>" value="<?php _e('Search', ATKP_PLUGIN_PREFIX) ?>" >
    <div id="LoadingImage" style="display: none;text-align:center"><img src="<?php echo plugin_dir_url( ATKP_PLUGIN_FILE ) ?>/images/spin.gif" style="width:32px" alt="loading" /></div>
    </div>
    
    <div id="<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupresult' ?>" class="<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupresult' ?>">
    
    </div>
    

    
		</div>
</div>
   
      <div id="modal-asin-lookupoffer" style="display:none;">
   
   <div class="atkp-lookupbox"> 
    <div><label for=""><?php _e('Keyword:', ATKP_PLUGIN_PREFIX) ?></label> <input type="text" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupsearchoffer' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupsearchoffer' ?>" value="">  <input type="submit" class="button" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupbtnsearchoffer' ?>" value="<?php _e('Search', ATKP_PLUGIN_PREFIX) ?>" >
    <div id="LoadingImageoffer" style="display: none;text-align:center"><img src="<?php echo plugin_dir_url( ATKP_PLUGIN_FILE ) ?>/images/spin.gif" style="width:32px" alt="loading" /></div>
    </div>
    
    <div id="<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupresultoffer' ?>" class="<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupresult' ?>">
    
    </div>
    

    
		</div>
</div>
   
   
     <script type="text/javascript">
     var reviewinforefresh = false;
      var $j = jQuery.noConflict();
      $j(document).ready(function() {
      reviewinforefresh = $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshreviewinforegulary' ?>').is(":checked");

      
      $j("#<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupbtnsearch' ?>").click(function(e) {
          
          $j("#<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupresult' ?>").html('');
          $j("#LoadingImage").show();
          
          $j.ajax({
              type: "POST",
              url: "<?php echo ATKPTools::get_endpointurl(); ?>",
              data: { action: "atkp_search_products", shop: $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_shopid' ?>').val(), keyword: $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupsearch' ?>').val(), request_nonce:"<?php echo wp_create_nonce('atkp-search-nonce') ?>" },
              
              dataType: "json",
              success : function(data) {
                try {
                    var count = 0;
                        $j.each(data, function(key, value) {
                            count++;
                        });
                        
                        if(count > 0) {
                                    
                          if(typeof data[0].error != 'undefined')
                          {
                              $j("#<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupresult' ?>").html('<span style="color:red">' + data[0].error + '<br /> '+ data[0].message+'</span>');
                          }else {
                              
                              var outputresult = '<ul class="product-link">';
                          
                            $j.each( data, function( index, value ) {
                               
                              outputresult += '<li>';
                               if(typeof value.imageurl != 'undefined' && value.imageurl != null)
                                outputresult += '<img src="'+value.imageurl+'" />';
                              outputresult += '<h3 data-id='+value.asin+'>'+value.title+'</h3>';
                              outputresult += '<p>ID: '+value.asin+' <br /><a href="'+value.producturl+'" target="_blank"><?php _e('View product', ATKP_PLUGIN_PREFIX) ?></a></p>';
                              outputresult += '</li>';
                            });
                
                            outputresult += '</ul>';   
                            $j("#<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupresult' ?>").html(outputresult);
                          }
                        }
                } catch (err) {
                        $j("#<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupresult' ?>").html('<span style="color:red">' +err.message + '</span>');
                        $j("#LoadingImage").hide();
                    }
                  
                  $j('ul.product-link li h3').click(function(e) 
                    { 
                        var id = $j(this).attr("data-id");
                        $j("#<?php echo ATKP_PRODUCT_POSTTYPE.'_asin' ?>").val(id);
                        $j("#<?php echo ATKP_PRODUCT_POSTTYPE.'_asin' ?>").trigger('change');
                    tb_remove();
                    });
                   
                    $j("#LoadingImage").hide();
                },
                  error: function (xhr, status) {   
                    $j("#<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupresult' ?>").html('<span style="color:red">' + xhr.responseText + '</span>');
                    $j("#LoadingImage").hide();
                  }    
            });
      });
      
       $j("#<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupbtnsearchoffer' ?>").click(function(e) {
          
          $j("#<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupresultoffer' ?>").html('');
          $j("#LoadingImageoffer").show();
         
          $j.ajax({
              type: "POST",
              url: "<?php echo ATKPTools::get_endpointurl(); ?>",
              data: { action: "atkp_search_products", shop: $j('#addoffer_shopid').val(), keyword: $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupsearchoffer' ?>').val(), request_nonce:"<?php echo wp_create_nonce('atkp-search-nonce') ?>" },
              
              dataType: "json",
              success : function(data) {
                  try {
                      var count = 0;
                        $j.each(data, function(key, value) {
                            count++;
                        });
                        
                        if(count > 0) {
                          
                              if(typeof data[0].error != 'undefined')
                              {
                                  $j("#<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupresultoffer' ?>").html('<span style="color:red">' + data[0].error + '<br /> '+ data[0].message+'</span>');
                              }else {
                                  
                                  var outputresult = '<ul class="product-link">';
                              
                                $j.each( data, function( index, value ) {
                                   
                                  outputresult += '<li>';
                                   if(typeof value.imageurl != 'undefined' && value.imageurl != null)
                                    outputresult += '<img src="'+value.imageurl+'" />';
                                  outputresult += '<h3 data-id='+value.asin+'>'+value.title+'</h3>';
                                  outputresult += '<p>ID: '+value.asin+' <br /><a href="'+value.producturl+'" target="_blank"><?php _e('View product', ATKP_PLUGIN_PREFIX) ?></a></p>';
                                  outputresult += '</li>';
                                });
                    
                                outputresult += '</ul>';   
                                $j("#<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupresultoffer' ?>").html(outputresult);
                              }
                        }
                    } catch (err) {
                        $j("#<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupresultoffer' ?>").html('<span style="color:red">' +err.message + '</span>');
                        $j("#LoadingImageoffer").hide();
                    }
                  
                  
                  $j('ul.product-link li h3').click(function(e) 
                    { 
                        var id = $j(this).attr("data-id");
                        var deletebtncaption = '<?php  _e('Delete', ATKP_PLUGIN_PREFIX) ?>';
                        var uid = idGen.getId();
                        
                       $j('#prices .mainrow:last').after('<tr><td>&nbsp;</td></tr><tr style="border-top:1px gray solid"><td colspan="5">&nbsp;</td></tr><tr valign="top"><td >'+$j('#addoffer_shopid option:selected').text()+'<br />'+id+'<input type="hidden" name="atkp_product_offer_shopid_'+uid+'" value="'+$j('#addoffer_shopid option:selected').val()+'" /><input type="hidden" name="atkp_product_offer_number_'+uid+'" value="'+id+'" /></td><td></td><td></td><td></td><td></td><td><input type="button" id="removeoffer-button_'+uid+'" class="button remove-offer" value="'+deletebtncaption+'" /></td></tr>');
                        $j('#atkp_offerschanged').val('1');
                        
                        $j('#removeoffer-button_'+uid).click(function(e){
    
                        if (confirm('<?php _e('Are you sure?', ATKP_PLUGIN_PREFIX) ?>')) {
                            $j(this).parent().parent().prev().remove();
                            $j(this).parent().parent().prev().remove();
                            $j(this).parent().parent().remove();
                            $j('#atkp_offerschanged').val('1');
                        }
                                                                            
                    });
                        
                    tb_remove();
                    });
                   
                    $j("#LoadingImageoffer").hide();
                },
                  error: function (xhr, status) {   
                    $j("#<?php echo ATKP_PRODUCT_POSTTYPE.'_prdlookupresultoffer' ?>").html('<span style="color:red">' + xhr.responseText + '</span>');
                    $j("#LoadingImageoffer").hide();
                  }    
            });
      });
      
          $j('.hideoffer').change(function () {
              $j('#atkp_offerschanged').val('1');              
          });
      
            $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_shopid' ?>').change(function () {
                 
                
                $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshreviewinforegulary' ?>').trigger("change");
                $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshproducturlregulary' ?>').trigger("change");
                $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshpriceinforegulary' ?>').trigger("change");
                $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshimagesregulary' ?>').trigger("change");
            });
            
            $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_shopid' ?>').trigger("change");
            
            $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_asin' ?>').change(function () {
           
           
           $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_asin_caption' ?>').empty();
           
                if($j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_asin' ?>').val() != '' && $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_shopid' ?>').val() != '')
                {
                    //asin wurde geändertt
                    
                    /* aktualisere die angebote nicht mit dem speichern automatisch    $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshmoreoffers' ?>').prop('checked', true); */
                    $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshmoreoffersregulary' ?>').prop('checked', true);

                    $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshproductinfo' ?>').prop('checked', true);
                    
                    $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshpriceinfo' ?>').prop('checked', true);
                    
                    $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshproducturl' ?>').prop('checked', true);
                    $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshimages' ?>').prop('checked', true);
                    
                     $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshimagesregulary' ?>').prop('checked', true);
                    
                    $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshproducturlregulary' ?>').prop('checked', true);
                    
                    $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshpriceinforegulary' ?>').prop('checked', true);
                    
                    
                    //was ist, wenn er manuelle bewertung eingetragen hat? dann sollte das nicht gesetzt werden!!
                    //if(reviewinforefresh) {
                        $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshreviewinforegulary' ?>').prop('checked', true);
                        $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshreviewinfo' ?>').prop('checked', true);
                    //}
                    
                } 
        
            });
             
             
            
            
    });
    </script>
   
   
   <?php
  
}

function product_detail_box_content( $post ) {
  wp_nonce_field( plugin_basename( __FILE__ ), 'product_detail_box_content_nonce' ); 
  
   require_once ATKP_PLUGIN_DIR.'/includes/shopproviders/atkp_shop_provider_base.php';
  
  ?>  
  
 <ul class='atkp-tabs'>
    <li><a href='#atkp-tab1'><?php _e( 'Product Information', ATKP_PLUGIN_PREFIX); ?></a></li>
    <li><a href='#atkp-tab2'><?php _e( 'Link Information', ATKP_PLUGIN_PREFIX) ?></a></li>
    <li><a href='#atkp-tab3'><?php _e( 'Review Information', ATKP_PLUGIN_PREFIX)?></a></li>
    <li><a href='#atkp-tab4'><?php _e( 'Images', ATKP_PLUGIN_PREFIX)?></a></li>
    <li><a href='#atkp-tab5'><?php _e( 'Price Information', ATKP_PLUGIN_PREFIX)?></a></li>
   
  </ul>
  <div id='atkp-tab1'>
<table class="form-table">
                        <tr valign="top">
                            <th scope="row">
                                
                            </th>
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshproductinfo' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshproductinfo' ?>" value="1" <?php echo checked(1, 0, true); ?>>
                            <label for="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshproductinfo' ?>">
                                    <?php _e('Refresh product information on save (overwrite existing data)', ATKP_PLUGIN_PREFIX) ?>
                                </label> 
                            </td>
                        </tr>
                        
                        
                                             

                        
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Title', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input disabled style="width:100%" type="text" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_title' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_title' ?>" value="<?php echo esc_attr( ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_title', true )); ?>">                              
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('EAN', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input disabled type="text" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_ean' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_ean' ?>" value="<?php echo  esc_attr( ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_ean', true )); ?>">                              
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('ISBN', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input disabled type="text" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_isbn' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_isbn' ?>" value="<?php echo  esc_attr( ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_isbn', true )); ?>">                              
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Product group', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input disabled   style="width:50%" type="text" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_productgroup' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_productgroup' ?>" value="<?php echo  esc_attr( ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_productgroup', true )); ?>">                              
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Manufacturer', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input disabled  style="width:50%" type="text" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_manufacturer' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_manufacturer' ?>" value="<?php echo  esc_attr( ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_manufacturer', true )); ?>">                              
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Author', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input disabled  style="width:50%" type="text" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_author' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_author' ?>" value="<?php echo  esc_attr( ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_author', true )); ?>">                              
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Number of pages', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input disabled type="number" min="0" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_numberofpages' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_numberofpages' ?>" value="<?php echo  esc_attr( ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_numberofpages', true )); ?>">                              
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Brand', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input disabled style="width:50%" type="text" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_brand' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_brand' ?>" value="<?php echo  esc_attr( ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_brand', true )); ?>">                              
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="">
                                    <?php _e('Description', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <div style="width:100%;height:150px;overflow-y: scroll;">
                                <?php
                                    echo (ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_description'));
                                ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="">
                                    <?php _e('Features', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                               <div style="width:100%;height:150px;overflow-y: scroll;">
                                <?php
                                    echo ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_features');
                                ?>
                                </div>
                            </td>
                        </tr>
                                    
                        
                        
						
                                            
                        
                        </table>
  </div>
  <div id='atkp-tab2'>
  <table class="form-table">
    <tr valign="top">
                            <th scope="row">
                                
                            </th>
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshproducturl' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshproducturl' ?>" value="1" <?php echo checked(1, 0, true); ?>>
                            <label for="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshproducturl' ?>">
                                    <?php _e('Refresh "product url", "customer reviews url" and "add to cart url" on save (overwrite existing data)', ATKP_PLUGIN_PREFIX) ?>
                                </label> 
                            </td>
                        </tr>
   
                        <tr valign="top">
                            <th scope="row">
                                
                            </th>
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshproducturlregulary' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshproducturlregulary' ?>" value="1" <?php echo checked(1, ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_refreshproducturlregulary'), true); ?>>
                            <label for="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshproducturlregulary' ?>">
                                    <?php _e('Refresh "product url", "customer reviews url" and "add to cart url" regulary', ATKP_PLUGIN_PREFIX) ?>
                                </label> 
                            </td>
                            </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Product page URL', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input disabled type="url" style="width:100%" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_producturl' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_producturl' ?>" value="<?php echo esc_attr(ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_producturl', true )); ?>">                              
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Add to cart URL', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input disabled type="url" style="width:100%" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_addtocarturl' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_addtocarturl' ?>" value="<?php echo esc_attr(  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_addtocarturl', true )); ?>">                              
                            </td>
                        </tr>
                         <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Customer Reviews URL', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input disabled type="url" style="width:100%" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_customerreviewsurl' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_customerreviewsurl' ?>" value="<?php echo esc_attr(  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_customerreviewsurl', true )); ?>">                              
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                            </th>
                            <td>
                                <input disabled type="checkbox" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_isownreview' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_isownreview' ?>" value="1" <?php checked(1, ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_isownreview'), true); ?>>
                            <label for="<?php echo ATKP_PRODUCT_POSTTYPE.'_isownreview' ?>">
                                    <?php _e('Override customer reviews', ATKP_PLUGIN_PREFIX) ?>
                                </label> 
                            </td>
                        </tr>    
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Review URL', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input disabled type="url" style="width:100%" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_reviewsurl' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_reviewsurl' ?>" value="<?php echo esc_attr(  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_reviewsurl', true )); ?>">                              
                            </td>
                        </tr>
                        
                       
                        </table>
  </div>
  <div id='atkp-tab3'>
 <table class="form-table">
                        <tr valign="top">
                            <th scope="row">
                                
                            </th>
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshreviewinfo' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshreviewinfo' ?>" value="1"  <?php echo checked(1, 0, true); ?>>
                            <label for="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshreviewinfo' ?>">
                                    <?php _e('Refresh review information on save (overwrite existing data)', ATKP_PLUGIN_PREFIX) ?>
                                </label> 
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                
                            </th>
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshreviewinforegulary' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshreviewinforegulary' ?>" value="1" <?php echo checked(1, ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_refreshreviewinforegulary'), true); ?>>
                            <label for="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshreviewinforegulary' ?>">
                                    <?php _e('Refresh review information regulary', ATKP_PLUGIN_PREFIX) ?>
                                </label> 
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Rating', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input  disabled type="number"  step="0.01" min="0" max="5" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_rating' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_rating' ?>" value="<?php echo  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_rating', true ); ?>">                              
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="">
                                    <?php _e('Review count', ATKP_PLUGIN_PREFIX) ?>:
                                </label> 
                            </th>
                            <td>
                                <input  disabled type="number" min="0" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_reviewcount' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_reviewcount' ?>" value="<?php echo  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_reviewcount', true ); ?>">                              
                            </td>
                        </tr>
                        
                         
                       
                        </table>
  </div>

  <div id='atkp-tab4'>
  <table class="form-table">
       <tr valign="top">

                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshimages' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshimages' ?>" value="1" <?php echo checked(1, 0, true); ?>>
                            <label for="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshimages' ?>">
                                    <?php _e('Refresh images on save (overwrite existing data)', ATKP_PLUGIN_PREFIX) ?>
                                </label> 
                            </td>
                        </tr>
                       
                        <tr valign="top">
               
                            <td>
                                <input type="checkbox" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshimagesregulary' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshimagesregulary' ?>" value="1" <?php echo checked(1, ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_refreshimagesregulary'), true); ?>>
                            <label for="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshimagesregulary' ?>">
                                    <?php _e('Refresh images regulary', ATKP_PLUGIN_PREFIX) ?>                                    
                                </label> 
                          
                            </td>
                            </tr>
                        <tr valign="top">
              
                            <td >
                                
                                <table style="width:100%">
                             
                                <tr class="mainrow">
                                <td style="width:80px;text-align:center;"><?php _e( 'Main image', ATKP_PLUGIN_PREFIX) ?></td>
                                <td style="vertical-align:middle; text-align:center; width:120px">
                                
                                <?php
                                $imageurl = ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_mediumimageurl', true );
                                
                                if($imageurl == '')
                                    $imageurl = ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_smallimageurl', true );
                                    
                                    
                                if($imageurl == '')
                                    $imageurl = ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_largeimageurl', true );
                                
                                ?>
                                
                                
                                        <img style="max-width:250px" src="<?php echo  $imageurl; ?>" />
                                    
                                    </td><td>
                                        <table style="width:100%;margin:1px">
                                             <tr valign="top">
                                                <th>
                                                    <label for="">
                                                        <?php _e('Small image URL', ATKP_PLUGIN_PREFIX) ?>:
                                                    </label> 
                                                </th>
                                                <td>
                                                    <input disabled type="url" style="width:100%" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_smallimageurl' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_smallimageurl' ?>" value="<?php echo  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_smallimageurl', true ); ?>">                              
                                               </td>
                                               <td style="    width: 50px;"> <input disabled type="button" id="smallimage-button" class="button meta-image-button" value="<?php _e( 'Choose or Upload an Image', ATKP_PLUGIN_PREFIX)?>" />
                                                </td>
                                            </tr>
                                            <tr valign="top">
                                                <th >
                                                    <label for="">
                                                        <?php _e('Medium image URL', ATKP_PLUGIN_PREFIX) ?>:
                                                    </label> 
                                                </th>
                                                <td>
                                                    <input disabled type="url"  style="width:100%" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_mediumimageurl' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_mediumimageurl' ?>" value="<?php echo  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_mediumimageurl', true ); ?>">                              
                                                
                                                </td>
                                                <td><input disabled type="button" id="mediumimage-button" class="button meta-image-button" value="<?php _e( 'Choose or Upload an Image', ATKP_PLUGIN_PREFIX)?>" /></td>
                                            </tr>
                                            <tr valign="top">
                                                <th >
                                                    <label for="">
                                                        <?php _e('Large image URL', ATKP_PLUGIN_PREFIX) ?>:
                                                    </label> 
                                                </th>
                                                <td>
                                                    <input disabled type="url" style="width:100%" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_largeimageurl' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_largeimageurl' ?>" value="<?php echo  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_largeimageurl', true ); ?>">                              
                                               
                                                </td>
                                                <td> <input disabled type="button" id="largeimage-button" class="button meta-image-button" value="<?php _e( 'Choose or Upload an Image', ATKP_PLUGIN_PREFIX)?>" /></td>
                                            </tr>
                                        
                                        </table>
                                    </td>
                                </table>
                                
                                </td></tr>
                                
                                
                                <tr valign="top">
                            
                            <td >
                                <input disabled type="button" id="addimage-button" class="button add-image" title="<?php _e( 'Add Image', ATKP_PLUGIN_PREFIX)?>" value="<?php _e( 'Add Image', ATKP_PLUGIN_PREFIX)?>" />
                                
							</td>
                        </tr>
                                <tr>
                                <td>
                                <table style="width:100%;border-collapse:collapse" id="images">
                                                              <?php
                                 require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product_image.php';
                                
                                $newimages = atkp_product_image::load_images($post->ID);
                                
                                 foreach ($newimages as $newimage ) {
                                
                                ?>
                                    <tr style="border-top:1px solid gainsboro;" class="mainrow">
                                        <td style="width:80px;text-align:center;">
                                        <input disabled type="button" id="removeimage-button_<?php echo $newimage->id ?>" class="button remove-image atkp-galleryitem" value="<?php _e( 'Delete', ATKP_PLUGIN_PREFIX)?>" />
                                        </td>
                                        <td style="vertical-align:middle; text-align:center; width:120px">
                                                <img  id="image-preview_<?php echo $newimage->id ?>" src="<?php echo  $newimage->mediumimageurl; ?>"  style="max-width:250px"/>
                                        </td>
                                        <td>
                                            <table style="width:100%">
                                             <tr valign="top">
                                                    <th>
                                                        <label for="">
                                                            <?php _e('Small image URL', ATKP_PLUGIN_PREFIX) ?>:
                                                        </label> 
                                                    </th>
                                                    <td>
                                                        <input disabled type="url" class="galleryimage atkp-galleryitem" style="width:100%" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_smallimageurl_gallery_'.$newimage->id ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_smallimageurl_gallery_'.$newimage->id ?>" value="<?php echo  $newimage->smallimageurl; ?>">                              
                                                    </td>
                                                </tr>    
                                                 <tr valign="top">
                                                    <th>
                                                        <label for="">
                                                            <?php _e('Medium image URL', ATKP_PLUGIN_PREFIX) ?>:
                                                        </label> 
                                                    </th>
                                                    <td>
                                                        <input disabled type="url" class="galleryimage atkp-galleryitem" style="width:100%" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_mediumimageurl_gallery_'.$newimage->id ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_mediumimageurl_gallery_'.$newimage->id ?>" value="<?php echo  $newimage->mediumimageurl; ?>">                              
                                                    </td>
                                                </tr>                                            
                                                <tr valign="top">
                                                    <th >
                                                        <label for="">
                                                            <?php _e('Large image URL', ATKP_PLUGIN_PREFIX) ?>:
                                                        </label> 
                                                    </th>
                                                    <td>
                                                        <input disabled type="url" class="atkp-galleryitem" style="width:100%" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_largeimageurl_gallery_'.$newimage->id ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_largeimageurl_gallery_'.$newimage->id ?>" value="<?php echo $newimage->largeimageurl; ?>">                              
                                                    </td>
                                                </tr>
                                            
                                            </table>
                                        </td>
                                    </tr>
                                           
                                                   
                                                 <?php 
                                 }
                                 ?>
                                 <tr  class="mainrow"></tr>
                                 </table>
                                
                            </td>
                        </tr>
                        </table>
                       
         <script type="text/javascript">
                            
            function Generator() {};
                Generator.prototype.rand =  Math.floor(Math.random() * 26) + Date.now();
                
                Generator.prototype.getId = function() {
                return this.rand++;
            };
            var idGen =new Generator();
            var mediumimagecaption = '<?php  _e('Medium image URL', ATKP_PLUGIN_PREFIX) ?>';
            var smallimagecaption = '<?php  _e('Small image URL', ATKP_PLUGIN_PREFIX) ?>';
            var largeimagecaption = '<?php  _e('Large image URL', ATKP_PLUGIN_PREFIX) ?>';
            var deletebtncaption = '<?php  _e('Delete', ATKP_PLUGIN_PREFIX) ?>';

            var $j = jQuery.noConflict();
            /*
             * Attaches the add field to the input field
             */
            $j(document).ready(function($){
             
                // Runs when the image button is clicked.
                $j('#addimage-button').click(function(e){
                    var id = idGen.getId();

                    $j('#images .mainrow:last').after('<tr style="border-top:1px solid gainsboro;" class="mainrow"><td style="width:80px;text-align:center;"><input type="button" id="removeimage-button_'+id+'" class="button remove-image atkp-galleryitem" value="'+deletebtncaption+'" /></td><td style="vertical-align:middle; text-align:center; width:120px"></td><td><table style="width:100%"><tr valign="top"><th><label for="">'+smallimagecaption+':</label></th><td><input type="url" style="width:100%" class="galleryimage atkp-galleryitem" id="atkp_product_smallimageurl_gallery_'+id+'" name="atkp_product_mediumimageurl_gallery_'+id+'" value=""></td></tr><tr valign="top"><th><label for="">'+mediumimagecaption+':</label></th><td><input type="url" style="width:100%" class="galleryimage atkp-galleryitem" id="atkp_product_mediumimageurl_gallery_'+id+'" name="atkp_product_mediumimageurl_gallery_'+id+'" value=""></td></tr><tr valign="top"><th ><label for="">'+largeimagecaption+':</label></th><td><input type="url" style="width:100%" id="atkp_product_largeimageurl_gallery_'+id+'" class= "atkp-galleryitem" name="atkp_product_largeimageurl_gallery_'+id+'" value=""></td></tr></table></td></tr>');
                
                    
                    $j('#removeimage-button_'+id).click(function(e){
    
                        if (confirm('<?php _e('Are you sure?', ATKP_PLUGIN_PREFIX) ?>')) {
                            $j(this).parent().parent().remove();
                        }
                                                                            
                    });
                });
                
                $j('.remove-image').click(function(e){

                    if (confirm('<?php _e('Are you sure?', ATKP_PLUGIN_PREFIX) ?>')) {
                        $j(this).parent().parent().remove();
                    }
                });



$j('.remove-offer').click(function(e){

                    if (confirm('<?php _e('Are you sure?', ATKP_PLUGIN_PREFIX) ?>')) {
                        $j(this).parent().parent().prev().remove();
                        $j(this).parent().parent().prev().remove();
                        $j(this).parent().parent().remove();
                        $j('#atkp_offerschanged').val('1');
                    }
                });
                
            });

        </script>
        </div>
    <div id='atkp-tab5'>
    
    <table class="form-table"> 
                            <tr valign="top">
                                <th scope="row">
                                    
                                </th>
                                <td>
                                    <input type="checkbox" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshpriceinfo' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshpriceinfo' ?>" value="1"  <?php echo checked(1, 0, true); ?>>
                                <label for="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshpriceinfo' ?>">
                                        <?php _e('Refresh price information on save (overwrite existing data)', ATKP_PLUGIN_PREFIX) ?>
                                    </label> 
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    
                                </th>
                                <td>
                                    <input type="checkbox" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshpriceinforegulary' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshpriceinforegulary' ?>" value="1" <?php echo checked(1, ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_refreshpriceinforegulary'), true); ?>>
                                <label for="<?php echo ATKP_PRODUCT_POSTTYPE.'_refreshpriceinforegulary' ?>">
                                        <?php _e('Refresh price information regulary', ATKP_PLUGIN_PREFIX) ?>
                                    </label> 
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="">
                                        <?php _e('Listprice', ATKP_PLUGIN_PREFIX) ?>:
                                    </label> 
                                </th>
                                <td>
                                    <input disabled type="text" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_listprice' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_listprice' ?>" value="<?php echo  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_listprice', true ); ?>">                              
                                
                                <?php if(ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_listprice', true ) != '') { ?>
                                (<?php echo ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_listpricefloat', true ); ?>)<?php }?>
                                </td>
                            </tr>
                            
                            <tr valign="top">
                                <th scope="row">
                                    <label for="">
                                        <?php _e('Amount saved', ATKP_PLUGIN_PREFIX) ?>:
                                    </label> 
                                </th>
                                <td>
                                    <input disabled type="text" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_amountsaved' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_amountsaved' ?>" value="<?php echo  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_amountsaved', true ); ?>">                              
                                 
                                 <?php if(ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_amountsaved', true ) != '') { ?>
                                 (<?php echo ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_amountsavedfloat', true ); ?>) <?php }?>
                                </td>
                            </tr>
                            
                            <tr valign="top">
                                <th scope="row">
                                    <label for="">
                                        <?php _e('Percentage saved', ATKP_PLUGIN_PREFIX) ?>:
                                    </label> 
                                </th>
                                <td>
                                    <input disabled type="number" min="0" max="100" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_percentagesaved' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_percentagesaved' ?>" value="<?php echo  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_percentagesaved', true ); ?>">                              
                                </td>
                            </tr>
                            
                            <tr valign="top">
                                <th scope="row">
                                    <label for="">
                                        <?php _e('Saleprice', ATKP_PLUGIN_PREFIX) ?>:
                                    </label> 
                                </th>
                                <td>
                                    <input disabled type="text" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_saleprice' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_saleprice' ?>" value="<?php echo  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_saleprice', true ); ?>">                              
                                <?php if(ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_saleprice', true ) != '') { ?>
                                (<?php echo ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_salepricefloat', true ); ?>) <?php }?>
                                </td>
                            </tr>
                           
                            <tr valign="top">
                                <th scope="row">
                                    <label for="">
                                        <?php _e('Availability', ATKP_PLUGIN_PREFIX) ?>:
                                    </label> 
                                </th>
                                <td>
                                    <input disabled type="text" style="width:50%" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_availability' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_availability' ?>" value="<?php echo  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_availability', true ); ?>">                              
                                </td>
                            </tr>
                            
                            <tr valign="top">
                                <th scope="row">
                                    <label for="">
                                        <?php _e('Shipping', ATKP_PLUGIN_PREFIX) ?>:
                                    </label> 
                                </th>
                                <td>
                                    <input disabled type="text" style="width:50%" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_shipping' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_shipping' ?>" value="<?php echo  ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_shipping', true ); ?>">                              
                                <?php if(ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_shipping', true ) != '') { ?>
                                (<?php echo ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_shippingfloat', true ); ?>)<?php }?>
                                </td>
                            </tr>
                            
                            <tr valign="top">
                                <th scope="row">
                                    <label for="">
                                        <?php _e('Is prime', ATKP_PLUGIN_PREFIX) ?>:
                                    </label> 
                                </th>
                                <td>
                                <input disabled type="checkbox" id="<?php echo ATKP_PRODUCT_POSTTYPE.'_isprime' ?>" name="<?php echo ATKP_PRODUCT_POSTTYPE.'_isprime' ?>" value="1" <?php echo checked(1, ATKPTools::get_post_setting($post->ID, ATKP_PRODUCT_POSTTYPE.'_isprime'), true); ?>>                     
                                </td>
                            </tr>
                           
                            </table>
    </div>
    
    
                    <div style="text-align:center;width:100%;margin-top:60px" > <?php ATKPHomeLinks::echo_banner(); ?></div>

  
   
                        
                        
                         <style>
                        .atkp-tabs li {
            				list-style:none;
            				display:inline;
            			}
            
            			.atkp-tabs a {
            				padding:5px 10px;
            				margin-left:-5px;
            				display:inline-block;
            				background:#666;
            				border: 1px solid #666;
            				color:#fff;
            				text-decoration:none;
            				line-height: 1.3;
    font-weight: 600;
    font-size: 14px;
            			}
            
            			.atkp-tabs .active {
            				background:#fff;
            				color:#000;
            			}

                        </style>
                        
                        <script type="text/javascript">
                        var $j = jQuery.noConflict();
                        /*
 * Attaches the image uploader to the input field
 */
$j(document).ready(function($){
 
//tabs

  $j('ul.atkp-tabs').each(function(){
    // For each set of tabs, we want to keep track of
    // which tab is active and its associated content
    var $active, $content, $links = $(this).find('a');

    // If the location.hash matches one of the links, use that as the active tab.
    // If no match is found, use the first link as the initial active tab.
    $active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
    $active.addClass('active');

    $content = $($active[0].hash);

    // Hide the remaining content
    $links.not($active).each(function () {
      $(this.hash).hide();
    });

    // Bind the click event handler
    $j(this).on('click', 'a', function(e){
      // Make the old tab inactive.
      $active.removeClass('active');
      $content.hide();

      // Update the variables with the new link and content
      $active = $(this);
      $content = $(this.hash);

      // Make the tab active.
      $active.addClass('active');
      $content.show();

      // Prevent the anchor's default click action
      e.preventDefault();
    });
  });

//tabs
 
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
                $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_smallimageurl' ?>').val(media_attachment.url);
            else if(image_button == $j('#mediumimage-button').attr('id'))
                $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_mediumimageurl' ?>').val(media_attachment.url);
            else if(image_button == $j('#largeimage-button').attr('id'))
                $j('#<?php echo ATKP_PRODUCT_POSTTYPE.'_largeimageurl' ?>').val(media_attachment.url);          
        });
 
        // Opens the media library frame.
        meta_image_frame.open();
    });
});
                        
                        </script>
  
  <?php 
}


function substr_startswith($haystack, $needle) {
            return substr($haystack, 0, strlen($needle)) === $needle;
        }


    function product_detail_save( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;
	
		$posttype =  ATKPTools::get_post_parameter('post_type', 'string');
	
		if (ATKP_PRODUCT_POSTTYPE != $posttype ) {
			return;
		}
		
		$nounce =  ATKPTools::get_post_parameter('product_detail_box_content_nonce', 'string');
	  
		if(!wp_verify_nonce($nounce, plugin_basename( __FILE__ ) ) )
			return;
      
		require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product_image.php';
    
        $shopid = ATKPTools::get_post_parameter(ATKP_PRODUCT_POSTTYPE.'_shopid', 'string'); 
    
 
        $refreshproductinfo = ATKPTools::get_post_parameter(ATKP_PRODUCT_POSTTYPE.'_refreshproductinfo', 'bool');
        $refreshpriceinfo = ATKPTools::get_post_parameter(ATKP_PRODUCT_POSTTYPE.'_refreshpriceinfo', 'bool');
        $refreshreviewinfo = ATKPTools::get_post_parameter(ATKP_PRODUCT_POSTTYPE.'_refreshreviewinfo', 'bool');
        
        $refreshimages = ATKPTools::get_post_parameter(ATKP_PRODUCT_POSTTYPE.'_refreshimages', 'bool');
        $refreshproducturl = ATKPTools::get_post_parameter(ATKP_PRODUCT_POSTTYPE.'_refreshproducturl', 'bool');
        
        $refreshimagesregulary = ATKPTools::get_post_parameter(ATKP_PRODUCT_POSTTYPE.'_refreshimagesregulary', 'bool');
        
        $refreshreviewinforegulary = ATKPTools::get_post_parameter(ATKP_PRODUCT_POSTTYPE.'_refreshreviewinforegulary', 'bool');
        $refreshpriceinforegulary = ATKPTools::get_post_parameter(ATKP_PRODUCT_POSTTYPE.'_refreshpriceinforegulary', 'bool');
        $refreshproducturlregulary = ATKPTools::get_post_parameter(ATKP_PRODUCT_POSTTYPE.'_refreshproducturlregulary', 'bool');
    	
    
        $asin = ATKPTools::get_post_parameter(ATKP_PRODUCT_POSTTYPE.'_asin', 'string'); 
        $asintype = ATKPTools::get_post_parameter(ATKP_PRODUCT_POSTTYPE.'_asintype', 'string'); 
        
        
        ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_shopid', $shopid);
        ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_asin', $asin);
        ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_asintype', $asintype);
           
        	
        ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshreviewinforegulary', $refreshreviewinforegulary);
        ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshpriceinforegulary', $refreshpriceinforegulary);
    	ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshproducturlregulary', $refreshproducturlregulary);
    	ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshimagesregulary', $refreshimagesregulary);
        
        
        
        $cronjob = new atkp_cronjob(array());
        $cronjob->update_product($post_id, $refreshproductinfo, $refreshpriceinfo, $refreshreviewinfo, $refreshimages, $refreshproducturl, false, false);
    }
    
    }
    
?>