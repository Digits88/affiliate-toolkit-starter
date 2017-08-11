<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_shortcode_generator2
    {   
        /**
         * Construct the plugin object
         */
        public function __construct($pluginbase)
        {
            add_action( 'add_meta_boxes', array(&$this, 'shortcode_boxes' ));
            
            add_action( 'save_post', array(&$this, 'product_detail_save' ));      
            
            //https://www.sitepoint.com/adding-a-media-button-to-the-content-editor/
            add_action('media_buttons', array(&$this, 'shortcode_buttons'));
            
            
           	add_action('admin_head', array(&$this, 'atkp_add_my_tc_button'));
        }
        
        function atkp_add_my_tc_button() {
            global $typenow;
            // check user permissions
            //if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
            //return;
            //}
            // verify the post type
            
            $sel_post_types = get_option(ATKP_PLUGIN_PREFIX.'_custom_posttypes', null);
            
            if($sel_post_types == null || !is_array($sel_post_types))
                $sel_post_types = array();
            
            array_push($sel_post_types, 'post');
            array_push($sel_post_types, 'page');
        
            if( ! in_array( $typenow, $sel_post_types) )
                return;
            // check if WYSIWYG is enabled
            //if ( get_user_option('rich_editing') == 'true') {
                add_filter('mce_external_plugins', array(&$this, 'addbuttons'));
                add_filter('mce_buttons', array(&$this, 'registerbuttons'));
            //}
        }
        
        function addbuttons($plugin_array) {
            $plugin_array['atkp_button_picker'] = plugins_url( '/js/editor-button.js', ATKP_PLUGIN_FILE ); // CHANGE THE BUTTON SCRIPT HERE
            return $plugin_array;
        }
        
        function registerbuttons($buttons) {
           array_push($buttons, 'separator', 'atkp_button_picker');
           return $buttons;
        }

        function shortcode_popup() {
           
         //TODO: implement cache 
         ?>
         
         <div id="atkp-generator-wrap" style="display:none">
			<div id="atkp-generator">
			<div id="atkp-generatorheader">
			    <b><?php _e('affiliate-toolkit Shortcodes', ATKP_PLUGIN_PREFIX); ?></b>
			</div>
			<?php 
			
			$this->template_detail_box_content('');
			
			?>
			
			</div>
		</div>
		
		<style>
		
		body.atkp-mfp-shown .mfp-bg { z-index: 101000 !important; }
        body.atkp-mfp-shown .mfp-wrap { z-index: 101001 !important; }
        body.atkp-mfp-shown .mfp-preloader { z-index: 101002 !important; }
        body.atkp-mfp-shown .mfp-content { z-index: 101003 !important; }
        body.atkp-mfp-shown button.mfp-close,
        body.atkp-mfp-shown button.mfp-arrow { z-index: 101004 !important; }
        
        #atkp-generator-wrap { display: none; }
        #atkp-generator {
        	position: relative;
        	width: 85%;
        	max-width: 700px;
        	height:550px;
        	margin: 60px auto;
        	padding: 20px;
        	background: #fff;
        	-webkit-box-shadow: 0 2px 25px #000;
        	-moz-box-shadow: 0 2px 25px #000;
        	box-shadow: 0 2px 25px #000;
        	-webkit-transition: max-width .2s;
        	-moz-transition: max-width .2s;
        	transition: max-width .2s;
        }
        
        fieldset {
            margin: 8px;
            border: 1px solid silver;
            padding: 8px;    
            border-radius: 4px;
        }
        
        legend {
            padding: 2px;    
        }
        
        .atkp_prdresult, .atkp_createresult {
            height:400px;
            overflow-y: scroll;
        }
        
        .atkp-nav {
            bottom:20px;
            position:absolute;
        }
        
        i.mce-i-atkp_button_icon {
            background-image: url(<?php echo plugins_url( 'images/affiliate_toolkit_menu.png', ATKP_PLUGIN_FILE ); ?>);
            background-repeat: no-repeat;   
        }
        
		</style>
         
             
         
        <script type="text/javascript">
        
            var atkp_selection = '';
            var atkp_editorvisible = false;
        
          jQuery(document).ready(function($) {
              
            function atkpButtonHtml (e, c, ed, defaultValue) {
       
                var elId = jQuery(e).attr('id');

                $('body').on('click', '#' + elId, function(e) { 
                    generator_button.trigger("click");
                });

                return false;
            }

            QTags.addButton( 'atkp_html_button', 'AT Shortcode', atkpButtonHtml);
              
            var $generator = $('#atkp-generator');
            var generator_button = $('.atkp-generator-button');
            
            $('body').on('click', '.mce-atkp_button_picker', function(e) { 
                generator_button.trigger("click");
            });
            
            //$('body').on('click', '.atkp-generator-button', function(e) {
         
             $('body').on('click', '.atkp-generator-button', function(e) { 
                e.preventDefault();
        		// Save the target
        		window.atkp_generator_target = $(this).data('target');
        		// Get open shortcode
        		var shortcode = $(this).data('shortcode');
        		// Open magnificPopup
        		$(this).magnificPopup({
        			type: 'inline',
        			alignTop: true,
        			callbacks: {
        				open: function() {
        					$('body').addClass('atkp-mfp-shown');
        					// Save selection
        					
        					if((typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor != null && tinyMCE.activeEditor.hasOwnProperty('selection'))) {
            					atkp_selection = tinyMCE.activeEditor.selection.getContent({ format: "text"	});   
            					atkp_editorvisible = !tinyMCE.activeEditor.isHidden();
        					} else {
        					    atkp_selection = '';
        					    atkp_editorvisible = false;
        					}
        					
        					//set actual selection to hyperlinkbox
        					$j('#<?php echo ATKP_SHORTCODE.'_contentlink' ?>').val(atkp_selection);
                                					
        					if(atkp_editorvisible) 
        					    $('#atkp-generator-insert').show();
        					else
        					    $('#atkp-generator-insert').hide();
        					    
        					
        				},
        				close: function() {
        					// Remove narrow class
        					$generator.removeClass('atkp-generator-narrow');
        				
        					// Clear selection
        					atkp_selection = '';
        					
        					$('body').removeClass('atkp-mfp-shown');
        				}
        			}
        		}).magnificPopup('open');
        	});
         
             $('#atkp-generator').on('click', '.atkp-generator-close', function(e) {
        		// Close popup
        		$.magnificPopup.close();
        		// Prevent default action
        		e.preventDefault();
        	});
        	
         });
         </script>
         <?php
         
  
            
        }
        
        function shortcode_buttons($args = array()) {
            //echo '<a href="#" id="insert-my-media" class="button">Affiliate-Toolkit Shortcode</a>';
            
            $target = is_string( $args ) ? $args : 'content';
    		// Prepare args
    		$args = wp_parse_args( $args, array(
    				'target'    => $target,
    				'text'      => __( 'affiliate-toolkit Shortcodes', ATKP_PLUGIN_PREFIX ),
    				'class'     => 'button',
    				'icon'      => plugins_url( 'images/affiliate_toolkit_menu.png', ATKP_PLUGIN_FILE ),
    				'echo'      => true,
    				'shortcode' => false
    			) );
    		// Prepare icon
    		if ( $args['icon'] ) $args['icon'] = '<img src="' . $args['icon'] . '" /> ';
    		// Print button
    		$button = '<a href="javascript:void(0);" class="atkp-generator-button ' . $args['class'] . '" title="' . $args['text'] . '" data-target="' . $args['target'] . '" data-mfp-src="#atkp-generator" data-shortcode="' . (string) $args['shortcode'] . '">' . $args['icon'] . $args['text'] . '</a>';
    		
    	    add_action( 'wp_footer',    array(&$this, 'shortcode_popup' ) );
		    add_action( 'admin_footer', array(&$this, 'shortcode_popup' ) );
		    
		    wp_register_style( 'magnific-popup', plugins_url( 'css/magnific-popup.css', ATKP_PLUGIN_FILE ), false, '0.9.9', 'all' );
        	wp_register_script( 'magnific-popup', plugins_url( 'js/magnific-popup.js', ATKP_PLUGIN_FILE ), array( 'jquery' ), '0.9.9', true );
        	wp_localize_script( 'magnific-popup', 'atkp_magnific_popup', array(
        			'close'   => __( 'Close (Esc)', ATKP_PLUGIN_PREFIX ),
        			'loading' => __( 'Loading...', ATKP_PLUGIN_PREFIX ),
        			'prev'    => __( 'Previous (Left arrow key)', ATKP_PLUGIN_PREFIX ),
        			'next'    => __( 'Next (Right arrow key)', ATKP_PLUGIN_PREFIX ),
        			'counter' => sprintf( __( '%s of %s', ATKP_PLUGIN_PREFIX ), '%curr%', '%total%' ),
        			'error'   => sprintf( __( 'Failed to load this link. %sOpen link%s.', ATKP_PLUGIN_PREFIX ), '<a href="%url%" target="_blank"><u>', '</u></a>' )
        		) );
            
            wp_enqueue_style( 'magnific-popup' );
            wp_enqueue_script( 'magnific-popup' );
    		
    		if ( $args['echo'] ) 
    		    echo $button;
    		else
    		    return $button;
        }

        function shortcode_boxes() {
            
            
            $types = array( 'post', 'page' );

            foreach( $types as $type ) {

                add_meta_box( 
                    ATKP_PLUGIN_PREFIX.'_product_box',
                    __( 'Affiliate Toolkit Product', ATKP_PLUGIN_PREFIX),
                    array(&$this, 'product_detail_box_content'),
                    $type,
                    'normal',
                    'default'
                );
            }
            
        }
        
        function product_detail_box_content( $post )
        {            
             wp_nonce_field( plugin_basename( __FILE__ ), 'product_detail_box_content_nonce' ); 
            ?>
            
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="<?php echo ATKP_PLUGIN_PREFIX.'_product' ?>"><?php _e( 'Main product:' , ATKP_PLUGIN_PREFIX); ?></label> 
                    </th>
                    <td>
                        <select disabled class="widefat" id="<?php echo ATKP_PLUGIN_PREFIX.'_product' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_product' ?>">
                                            <?php
                              
                            echo '<option value="" '.($val == '' ? 'selected' : '').'>' . __('None', ATKP_PLUGIN_PREFIX) . '</option>';
                            
                             ?>
                        </select>
                    </td>
                </tr>
                <tr>
                <td colspan="2">
                 <div style="text-align:center;width:100%;margin-top:10px" > <?php ATKPHomeLinks::echo_banner(); ?></div>
                </td>
                </tr>
            </table
               
            <?php
            
        }
        
        function product_detail_save( $post_id ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
				return;
							
			$nounce =  ATKPTools::get_post_parameter('product_detail_box_content_nonce', 'string');
		  
			if(!wp_verify_nonce($nounce, plugin_basename( __FILE__ ) ) )
				return;
			
            
         }
        
        function template_detail_box_content( $post ) {
            ?>
            
            <div class="atkp-pages">
                <h2 id="atkp-steptitle"><?php _e('What do you want?', ATKP_PLUGIN_PREFIX); ?></h2>
                    
                <div id="atkp-firstpage">
                
                    <table style="width:100%;text-align: center">
                    <tr>
                        <td>
                            <div style="margin:10px">
                                <a onclick="atkp_nextpage('searchproductorlist', '<?php echo ATKP_PRODUCT_POSTTYPE; ?>')" href="javascript:void(0);">
                                    <i class="fa fa-search" aria-hidden="true" style="font-size:4em;text-align:center"></i>
                                    <span style="display:block;margin-top:10px">
                                    <?php _e('Search for an already imported product and use it in your post.', ATKP_PLUGIN_PREFIX); ?>
                                    </span>
                                </a>
                            </div>
                        </td>
                        <td>
                            <div style="margin:10px">
                                <a onclick="atkp_nextpage('createproduct', '<?php echo ATKP_PRODUCT_POSTTYPE; ?>')" href="javascript:void(0);">
                                    <i class="fa fa-download" aria-hidden="true" style="font-size:4em;text-align:center"></i>
                                    <span style="display:block;margin-top:10px">
                                    <?php _e('Import a new product and use this in your post.', ATKP_PLUGIN_PREFIX); ?>
                                    </span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <div style="margin:10px">
                                <a onclick="atkp_nextpage('searchproductorlist', '<?php echo ATKP_LIST_POSTTYPE; ?>')" href="javascript:void(0);">
                                    <i class="fa fa-list" aria-hidden="true" style="font-size:4em;text-align:center"></i>
                                    <span style="display:block;margin-top:10px">
                                    <?php _e('Search an already created list and use it in your post.', ATKP_PLUGIN_PREFIX); ?>
                                    </span>
                                </a>
                            </div>
                        </td>
                        <td >
                            <div style="margin:10px">
                                <a onclick="atkp_nextpage('createlist', '<?php echo ATKP_LIST_POSTTYPE; ?>')" href="javascript:void(0);">
                                    <i class="fa fa-plus-square-o" aria-hidden="true" style="font-size:4em;text-align:center"></i>
                                    <span style="display:block;margin-top:10px">
                                    <?php _e('Create a new list (manual, best seller, etc.) and use it in your post.', ATKP_PLUGIN_PREFIX); ?>
                                    </span>
                                </a>
                            </div>
                        </td>
                    </tr>                    
                    </table>
                    
                    <div style="text-align:center;width:100%;margin-top:60px" > <?php ATKPHomeLinks::echo_banner(); ?></div>
                
                </div>
            
            
                <div id="atkp-searchproductorlist">
                   
                    <div>
                        <div>
                            <label for=""><?php _e('Keyword:', ATKP_PLUGIN_PREFIX) ?></label> 
                            <input type="text" id="atkp_txt_prdsearch" name="atkp_txt_prdsearch" value="">  
                            <input type="submit" class="button" id="atkp_btn_prdsearch" value="<?php _e('Search', ATKP_PLUGIN_PREFIX) ?>" >
                        </div>
                        
                        <div id="atkp_prdloading" style="display: none;text-align:center">
                            <img src="<?php echo plugin_dir_url( ATKP_PLUGIN_FILE ) ?>/images/spin.gif" style="width:32px" alt="loading" />
                        </div>
                    </div>
        
                    <div id="atkp_prdresult" class="atkp_prdresult" style="border-width:1px; border-style: solid;border-color:gray;margin-top:5px;margin-bottom:5px">
                    
                    </div>
                   
                   <a onclick="atkp_previouspage()" href="javascript:void(0);" id="atkp-back" class="button atkp-nav"><?php _e('Back', ATKP_PLUGIN_PREFIX) ?></a>
                
                </div>
                
                <div id="atkp-createproduct">
                    
                    <div>
                        <div>
                            <label for=""><?php _e('Shop:', ATKP_PLUGIN_PREFIX) ?></label> 
                            <select id="atkp_create_shopid" name="atkp_create_shopid" style="width:300px">                            
                                <?php
                                
                                require_once ATKP_PLUGIN_DIR.'/includes/atkp_shop.php';
                                
                                $shps = atkp_shop::get_list('');
                                
                                foreach($shps as $shp) {
                                    if ($shp->selected == true) 
                                            $sel = ' selected'; 
                                        else 
                                            $sel = '';
                                                                                
                                    echo '<option value="' .$shp->id . '"' . $sel . ' > ' .  esc_attr($shp->title) . '</option>';
                                }
                                
                                ?>
                            </select>&nbsp;
                        
                            <label for=""><?php _e('Keyword:', ATKP_PLUGIN_PREFIX) ?></label> 
                            <input type="text" id="atkp_txt_createsearch" name="atkp_txt_createsearch" value="">  
                            <input type="submit" class="button" id="atkp_btn_createsearch" value="<?php _e('Search', ATKP_PLUGIN_PREFIX) ?>" >
                        </div>
                        
                        <div id="atkp_createloading" style="display: none;text-align:center">
                            <img src="<?php echo plugin_dir_url( ATKP_PLUGIN_FILE ) ?>/images/spin.gif" style="width:32px" alt="loading" />
                        </div>
                    </div>
        
                    <div id="atkp_createresult" class="atkp_createresult" style="border-width:1px; border-style: solid;border-color:gray;margin-top:5px;margin-bottom:5px">
                    
                    </div>
                   
                   <a onclick="atkp_previouspage()" href="javascript:void(0);" id="atkp-back" class="button atkp-nav"><?php _e('Back', ATKP_PLUGIN_PREFIX) ?></a>
                </div>
                
                <div id="atkp-createlist">
                    
                    <div>
                        <div>
                        <table style="width:100%">
                            <tr>
                                <td style="width:30%">
                                    <label for=""><?php _e('Name:', ATKP_PLUGIN_PREFIX) ?></label> 
                                </td>
                                <td>
                                    <input type="text" id="atkp_txt_createlistname" name="atkp_txt_createlistname" value=""> <br />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for=""><?php _e('Shop:', ATKP_PLUGIN_PREFIX) ?></label> 
                                </td>
                                <td>
                                    <select id="atkp_create_listshopid" name="atkp_create_listshopid" style="width:300px">                            
                                        <?php
                                        echo '<option value="" > ' .  esc_attr(__('Manual list', ATKP_PLUGIN_PREFIX)) . '</option>';
                                        
                                        require_once ATKP_PLUGIN_DIR.'/includes/atkp_shop.php';
                                        
                                        $shps = atkp_shop::get_list('');
                                        
                                        foreach($shps as $shp) {
                                            if ($shp->selected == true) 
                                                    $sel = ' selected'; 
                                                else 
                                                    $sel = '';
                                                    
                                            $datasources = $shp->provider->get_supportedlistsources();
                                               
                                            if($datasources != '')
                                                echo '<option data-sources="'.$datasources.'" value="' .$shp->id . '"' . $sel . ' > ' .  esc_attr($shp->title) . '</option>';
                                        }
                                        
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for=""><?php _e('Quelle:', ATKP_PLUGIN_PREFIX) ?></label> 
                                </td>
                                <td>
                                    <select name="atkp_create_listsource" id="atkp_create_listsource">
                                        <?php
                                        
                                        $durations = array(
                                                            10 => __('Category - Best Seller', ATKP_PLUGIN_PREFIX),
                                                            20 => __('Search', ATKP_PLUGIN_PREFIX),                            
                                                          );
                                        
                                        foreach ($durations as $value => $name) {
                                            if ($value == $selectedsourceval) 
                                                $sel = ' selected'; 
                                            else 
                                                $sel = '';
                                            
                                            $item_translated = '';
                                                                        
                                            echo '<option value="' . $value . '"' . $sel . '>' . esc_attr($name) . '</option>';
                                        } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label id="atkp_lbl_createlistsearchterm" for=""><?php _e('Keyword:', ATKP_PLUGIN_PREFIX) ?></label> 
                                </td>
                                <td>
                                    <input type="text" id="atkp_txt_createlistsearchterm" name="atkp_txt_createlistsearchterm" value=""> <br /> 
                                </td>
                            </tr>
                            
                            <tr>
                                <td></td>
                                <td>&nbsp;</td>
                            </tr>
                            
                            <tr>
                                <td></td>
                                <td><?php _e('This is a snap position. Further adjustments must still be made in the list editor.', ATKP_PLUGIN_PREFIX); ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>&nbsp;</td>
                            </tr>
                            
                            <tr>
                                <td></td>
                                <td>
                                    <a class="button" id="atkp_btn_createlist" onclick="atkp_createlist('searchtemplate', cnttype, '')" href="javascript:void(0);"><?php _e('Create list and use', ATKP_PLUGIN_PREFIX) ?></a>
                                </td>
                            </tr>
                        </table>
                        </div>
                    </div>
        <br /><br />
                   
                   <a onclick="atkp_previouspage()" href="javascript:void(0);" id="atkp-back" class="button atkp-nav"><?php _e('Back', ATKP_PLUGIN_PREFIX) ?></a>
                </div>
                
                <div id="atkp-searchtemplate" style="overflow-y:scroll;height:440px;">
                    
                    <div id="atkp-current">
                     
                    </div>
                    
                    
                    <fieldset class="atkp-group" id="atkp-group-template">
                        <legend><input type="radio" name="outputtype" value="template" checked> <?php _e( 'Template' , ATKP_PLUGIN_PREFIX); ?></legend>
                        <table style="width:100%">
                            <tr>
                                <td style="width:30%">
                                    <label for="<?php echo ATKP_SHORTCODE.'_template' ?>"><?php _e( 'Template:' , ATKP_PLUGIN_PREFIX); ?></label>
                                </td>
                                <td>
                                    <select class="widefat" id="<?php echo ATKP_SHORTCODE.'_template' ?>" name="<?php echo ATKP_SHORTCODE.'_template' ?>">
                                        <?php
                                        echo '<option value="">'.__('default', ATKP_PLUGIN_PREFIX).'</option>';
                                        
                                        echo '<option value="bestseller">'.__('bestseller', ATKP_PLUGIN_PREFIX).'</option>';
                                        echo '<option value="wide">'.__('wide', ATKP_PLUGIN_PREFIX).'</option>';
                                        echo '<option value="secondwide">'.__('secondwide', ATKP_PLUGIN_PREFIX).'</option>';
                                        echo '<option value="box">'.__('box', ATKP_PLUGIN_PREFIX).'</option>';
                                        
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            
                            
                            
                            
                            <tr>
                                <td>
                                    <label for="<?php echo ATKP_SHORTCODE.'_containercssclass' ?>"><?php _e( 'Container CSS Class:' , ATKP_PLUGIN_PREFIX); ?></label> 
                                </td>
                                <td>
                                     <input class="widefat" id="<?php echo ATKP_SHORTCODE.'_containercssclass' ?>" name="<?php echo ATKP_SHORTCODE.'_containercssclass' ?>" type="text" value="" />
                               </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <label for="<?php echo ATKP_SHORTCODE.'_elementcssclass' ?>"><?php _e( 'Element CSS Class:' , ATKP_PLUGIN_PREFIX); ?></label>
                                </td>
                                <td>
                                       <input class="widefat" id="<?php echo ATKP_SHORTCODE.'_elementcssclass' ?>" name="<?php echo ATKP_SHORTCODE.'_elementcssclass' ?>" type="text" value="" />
                               </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <label for="<?php echo ATKP_SHORTCODE.'_align' ?>"><?php _e( 'Align:' , ATKP_PLUGIN_PREFIX); ?></label> 
                                </td>
                                <td>
                                   <select class="widefat" id="<?php echo ATKP_SHORTCODE.'_align' ?>" name="<?php echo ATKP_SHORTCODE.'_align' ?>">
                                        <option value=""><?php _e( 'no alignment' , ATKP_PLUGIN_PREFIX); ?></option>  
                                        <option value="atkp-left atkp-clearfix"><?php _e( 'left' , ATKP_PLUGIN_PREFIX); ?></option>  
                                        <option value="atkp-center"><?php _e( 'center' , ATKP_PLUGIN_PREFIX); ?></option>
                                        <option value="atkp-right atkp-clearfix"><?php _e( 'right' , ATKP_PLUGIN_PREFIX); ?></option>
                                    </select>
                               </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <label for="<?php echo ATKP_SHORTCODE.'_content' ?>"><?php _e( 'Content:' , ATKP_PLUGIN_PREFIX); ?></label> 
                                </td>
                                <td>
                                    <input class="widefat" id="<?php echo ATKP_SHORTCODE.'_content' ?>" name="<?php echo ATKP_SHORTCODE.'__content' ?>" type="text" value="" />
                               </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <label for="<?php echo ATKP_SHORTCODE.'_buttontype' ?>"><?php _e( 'Buttontype:' , ATKP_PLUGIN_PREFIX); ?></label>
                                </td>
                                <td>
                                    <select class="widefat" id="<?php echo ATKP_SHORTCODE.'_buttontype' ?>" name="<?php echo ATKP_SHORTCODE.'_buttontype' ?>">
                                        <?php                                           
                                        echo '<option value="">'.__('default', ATKP_PLUGIN_PREFIX).'</option>';
                                        echo '<option value="addtocart">'.__('add to cart', ATKP_PLUGIN_PREFIX).'</option>';
                                        echo '<option value="link">'.__('link', ATKP_PLUGIN_PREFIX).'</option>';
                                         ?>
                                    </select>
                               </td>
                            </tr>
                             <tr class="atkp-onlylist">
                                <td>
                                    <label for="<?php echo ATKP_SHORTCODE.'_limit' ?>"><?php _e( 'Limit:' , ATKP_PLUGIN_PREFIX); ?></label> 
                                </td>
                                <td>
                                    <input class="widefat" id="<?php echo ATKP_SHORTCODE.'_limit' ?>" name="<?php echo ATKP_SHORTCODE.'_limit' ?>" type="number" min="1" max="10" />
                                </td>
                            </tr>
                            
                            <tr class="atkp-onlylist">
                                <td>
                                    
                                </td>
                                <td>
                                    <input type="checkbox" id="<?php echo ATKP_SHORTCODE.'_random' ?>" name="<?php echo ATKP_SHORTCODE.'_random' ?>">
                                    <label for="<?php echo ATKP_SHORTCODE.'_random' ?>">
                                                    <?php _e('Random sort', ATKP_PLUGIN_PREFIX) ?>
                                    </label> 
                               </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    
                                </td>
                                <td>
                                    <input type="checkbox" id="<?php echo ATKP_SHORTCODE.'_hidedisclaimer' ?>" name="<?php echo ATKP_SHORTCODE.'_hidedisclaimer' ?>">
                                    <label for="<?php echo ATKP_SHORTCODE.'_hidedisclaimer' ?>">
                                                    <?php _e('Hide disclaimer', ATKP_PLUGIN_PREFIX) ?>
                                    </label> 
                               </td>
                            </tr>
                                                                          
                        </table>
                    </fieldset>
                    
                    <fieldset class="atkp-group" id="atkp-group-field">
                        <legend><input type="radio" name="outputtype" value="field"> <?php _e( 'Field' , ATKP_PLUGIN_PREFIX); ?></legend>
                        <table style="width:100%">
                            <tr>
                                <td style="width:30%">
                                    <label for="<?php echo ATKP_SHORTCODE.'_field' ?>"><?php _e( 'Field:' , ATKP_PLUGIN_PREFIX); ?></label> 
                                    
                                </td>
                                <td>
                                    <select class="widefat" id="<?php echo ATKP_SHORTCODE.'_field' ?>" name="<?php echo ATKP_SHORTCODE.'_field' ?>">
                                        <?php                                           
                                                                        
                                        require_once  ATKP_PLUGIN_DIR.'/includes/helper/atkp_template_helper.php';
                                        
                                        $templatehelper = new atkp_template_helper();
                                        $placeholders = $templatehelper->getPlaceholders();
                                                                        
                                        foreach ( $placeholders as $placeholder => $caption ) { 
                                                echo '<option value="' .$placeholder . '">' . $caption . '</option>';                                                                          
                                         };
                                     ?>
                                    </select> 
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    
                                    
                                    
                                </td>
                                <td>
                                    <input type="checkbox" id="<?php echo ATKP_SHORTCODE.'_fieldlink' ?>" name="<?php echo ATKP_SHORTCODE.'_fieldlink' ?>">
                                    <label for="<?php echo ATKP_SHORTCODE.'_fieldlink' ?>">
                                            <?php _e('Hyperlink', ATKP_PLUGIN_PREFIX) ?>
                                    </label>  
                                </td>
                            </tr>
                            
                        </table>
                    </fieldset>
                    
                    <fieldset class="atkp-group" id="atkp-group-link">
                        <legend><input type="radio" name="outputtype" value="link"> <?php _e( 'Hyperlink' , ATKP_PLUGIN_PREFIX); ?></legend>
                        <table style="width:100%">
                            <tr>
                                <td style="width:30%">
                                    <label for="<?php echo ATKP_SHORTCODE.'_contentlink' ?>"><?php _e( 'Content:' , ATKP_PLUGIN_PREFIX); ?></label> 
                                </td>
                                <td>
                                    <input class="widefat" id="<?php echo ATKP_SHORTCODE.'_contentlink' ?>" name="<?php echo ATKP_SHORTCODE.'_contentlink' ?>" type="text" value="" />
                               </td>
                            </tr>                            
                        </table>
                    </fieldset>
                    
                    <fieldset class="atkp-group" style="display:none" id="atkp-group-compare">
                        <legend><input type="radio" name="outputtype" value="compare"> <?php _e( 'compare table' , ATKP_PLUGIN_PREFIX); ?></legend>
                        <table style="width:100%">
                            <tr>
                                <td  style="width:30%">
                                    <label for=""><?php _e('Compare values (multi select):', ATKP_PLUGIN_PREFIX) ?></label> 
                                </td>
                                <td>
                                    <select style="width:100%" id="<?php echo ATKP_SHORTCODE.'_comparevalues' ?>" name="<?php echo ATKP_SHORTCODE.'_comparevalues' ?>"  multiple="multiple">
                                        <?php                                           
                                                                        
                                        require_once  ATKP_PLUGIN_DIR.'/includes/helper/atkp_template_helper.php';
                                        
                                        $templatehelper = new atkp_template_helper();
                                        $placeholders = $templatehelper->getPlaceholders();
                                                                        
                                        foreach ( $placeholders as $placeholder => $caption ) { 
                                                echo '<option value="' .$placeholder . '">' . $caption . '</option>';                                                                          
                                         };
                                        ?>
                                    </select> 
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    
                                    
                                    
                                </td>
                                <td>
                                    <input type="checkbox" id="<?php echo ATKP_SHORTCODE.'_link' ?>" name="<?php echo ATKP_SHORTCODE.'_link' ?>">
                                    <label for="<?php echo ATKP_SHORTCODE.'_link' ?>">
                                            <?php _e('Horizontal scrollbars', ATKP_PLUGIN_PREFIX) ?>
                                    </label>  
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    
                                    
                                    
                                </td>
                                <td>
                                    <input type="checkbox" id="<?php echo ATKP_SHORTCODE.'_link' ?>" name="<?php echo ATKP_SHORTCODE.'_link' ?>">
                                    <label for="<?php echo ATKP_SHORTCODE.'_link' ?>">
                                            <?php _e('Hide header', ATKP_PLUGIN_PREFIX) ?>
                                    </label>  
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    
                                </td>
                                <td>
                                    <input type="checkbox" id="<?php echo ATKP_SHORTCODE.'_hidedisclaimer' ?>" name="<?php echo ATKP_SHORTCODE.'_hidedisclaimer' ?>">
                                    <label for="<?php echo ATKP_SHORTCODE.'_hidedisclaimer' ?>">
                                                    <?php _e('Hide disclaimer', ATKP_PLUGIN_PREFIX) ?>
                                    </label> 
                               </td>
                            </tr>
                        </table>
                    </fieldset>
                    
                    
                    <table style="width:100%">
                        <tr>
                            <td  style="width:30%">
                                <label for=""><?php _e('Override affiliate-id (amazon):', ATKP_PLUGIN_PREFIX) ?></label> 
                            </td>
                            <td>
                                <input type="text" id="<?php echo ATKP_SHORTCODE.'_override-tracking_id' ?>" name="<?php echo ATKP_SHORTCODE.'_override-tracking_id' ?>" value=""> <br /> 
                            </td>
                        </tr>
                    </table>
                    
                    
                    <div class="atkp-nav" style="display:inline-block">
                       <a onclick="atkp_previouspage()" href="javascript:void(0);" id="atkp-back" class="button" style="float:left;margin-right:10px"><?php _e('Back', ATKP_PLUGIN_PREFIX) ?></a>
                       <a onclick="atkp_createshortcode('clipboard')" href="javascript:void(0);" id="atkp-generator-paste" style="float:left;margin-right:10px"  class="button"><i class="fa fa-refresh"></i>&nbsp;<?php _e( 'Copy shortcode to clipboard', ATKP_PLUGIN_PREFIX)?> </a>
                       <a onclick="atkp_createshortcode('insert')" href="javascript:void(0);" id='atkp-generator-insert' class="button button-primary" style="float:left;margin-right:10px"><i class="fa fa-check"></i>&nbsp;<?php echo __('Insert shortcode', ATKP_PLUGIN_PREFIX) ?></a>
    		        </div>
                </div>
                
            </div>
             <script type="text/javascript">
                         
             
                var $j = jQuery.noConflict();
                $j(document).ready(function($) {    
                    //atkp_txt_prdsearch
                    //atkp_btn_prdsearch
                    //atkp_prdresult
                    //atkp_prdloading
                    
                    
                    
                    atkp_nextpage('','');
                    
                    $j('#atkp_create_listshopid').change(function(){
                        var option = $j('option:selected', $j('#atkp_create_listshopid')).attr('data-sources');
                        
                        if(option != null)
                            supportedsources = option.split(",");
                        
                        $j('#atkp_create_listsource option[value=10]').hide();
                        $j('#atkp_create_listsource option[value=11]').hide();
                        $j('#atkp_create_listsource option[value=20]').hide();
                        $j('#atkp_create_listsource option[value=30]').hide();
                        $j('#atkp_create_listsource option[value=40]').hide();
                        
                        if(option != null) {
                            $j.each(supportedsources, function( index, value ) {
                              $j('#atkp_create_listsource option[value='+value+']').show();  
                            });
                        
                            $j('#atkp_create_listsource').val('20').change();
                            $j('#atkp_create_listsource').prop('disabled', false);
                            
                            $j('#atkp_lbl_createlistsearchterm').show();
                            $j('#atkp_txt_createlistsearchterm').show();
                        } else {
                            $j('#atkp_create_listsource').prop('disabled', true);
                            
                            $j('#atkp_lbl_createlistsearchterm').hide();
                            $j('#atkp_txt_createlistsearchterm').hide();
                        }
                    });  
                    
                    
                    $j('#atkp_create_listshopid').trigger("change");
                    
                    $j("#atkp_btn_prdsearch").click(function(e) {
                  
                        $j("#atkp_prdresult").html('');
                        $j("#atkp_prdresult").hide();
                        $j("#atkp_prdloading").show();
                      
                        $j.ajax({
                            type: "POST",
                            url: "<?php echo ATKPTools::get_endpointurl(); ?>",
                            data: { action: "atkp_search_local_products", type: cnttype, keyword: $j('#atkp_txt_prdsearch').val(), request_nonce:"<?php echo wp_create_nonce('atkp-search-nonce') ?>" },
                              
                            dataType: "json",
                            success : function(data) {
                                try {
                                    //$j("#atkp_prdresult").html(JSON.stringify(data, null, 2));
                                    
                                    var count = 0;
                                    $j.each(data, function(key, value) {
                                        count++;
                                    });
                                        
                                    if(count > 0) {
                                                    
                                      if(typeof data[0].error != 'undefined')
                                      {
                                          $j("#atkp_prdresult").html('<span style="color:red">' + data[0].error + '<br /> '+ data[0].message+'</span>');
                                      }else {
                                              
                                        var outputresult = '<table style="width:100%">';
                                        var cnttext ='';
                                        $j.each( data, function( index, value ) {
                                        
                                        if(cnttype == '<?php echo ATKP_PRODUCT_POSTTYPE; ?>')   {
                                            cnttext = '<?php _e('Use product', ATKP_PLUGIN_PREFIX) ?>';
                                            outputresult += '<tr style="height:100px;">';
                                            outputresult += '<td style="margin:5px;width:110px;max-height:90px;text-align:center"><img style="max-width:100px" src="'+value.imageurl+'" /></td>';
                                        } else {
                                            cnttext = '<?php _e('Use list', ATKP_PLUGIN_PREFIX) ?>';
                                            outputresult += '<tr style="">';                                        
                                        }
                                        
                                          outputresult += '<td>ID: '+value.id+' - '+value.shop+'<br /><a href="'+value.editurl+'" target="_blank">'+value.title+'</a></td>';
                                          outputresult += '<td style="width:25px; text-align:right"><a data-id="'+value.id+'" class="button" onclick="atkp_nextpage(\'searchtemplate\', cnttype, '+value.id+')" href="javascript:void(0);">'+cnttext+'</a></td>';
                                          outputresult += '</tr>';
                                        });
                            
                                        outputresult += '</table>';   
                                        $j("#atkp_prdresult").html(outputresult);
                                      }
                                    } else {
                                        $j("#atkp_prdresult").html('<span><?php _e('No results', ATKP_PLUGIN_PREFIX); ?></span>');   
                                    }
                                } catch (err) {
                                        $j("#atkp_prdresult").html('<span style="color:red">' +err.message + '</span>');
                                        $j("#atkp_prdloading").hide();
                                        $j("#atkp_prdresult").show();
                                }
                              
                               
                                $j("#atkp_prdloading").hide();
                                $j("#atkp_prdresult").show();
                            },
                            error: function (xhr, status) {   
                                $j("#atkp_prdresult").html('<span style="color:red">' + xhr.responseText + '</span>');
                                $j("#atkp_prdloading").hide();
                                $j("#atkp_prdresult").show();
                            }    
                        });                       
                        
                    });
                    
                    $j("#atkp_btn_createsearch").click(function(e) {
                  
                        $j("#atkp_createresult").html('');
                        $j("#atkp_createresult").hide();
                        $j("#atkp_createloading").show();
                      
                        $j.ajax({
                            type: "POST",
                            url: "<?php echo ATKPTools::get_endpointurl(); ?>",
                            data: { action: "atkp_search_products", shop: $j('#atkp_create_shopid').val(), keyword: $j('#atkp_txt_createsearch').val(), request_nonce:"<?php echo wp_create_nonce('atkp-search-nonce') ?>" },
                              
                            dataType: "json",
                            success : function(data) {
                                try {
                                    //$j("#atkp_prdresult").html(JSON.stringify(data, null, 2));
                                    
                                    var count = 0;
                                    $j.each(data, function(key, value) {
                                        count++;
                                    });
                                        
                                    if(count > 0) {
                                                    
                                      if(typeof data[0].error != 'undefined')
                                      {
                                          $j("#atkp_createresult").html('<span style="color:red">' + data[0].error + '<br /> '+ data[0].message+'</span>');
                                      }else {
                                              
                                        var outputresult = '<table style="width:100%">';
                                        var cnttext ='';
                                        $j.each( data, function( index, value ) {
                                        
                                        if(cnttype == '<?php echo ATKP_PRODUCT_POSTTYPE; ?>')   {
                                            if(value.productid != 'null') 
                                                cnttext = '<?php _e('Use product', ATKP_PLUGIN_PREFIX) ?>';
                                            else
                                                cnttext = '<?php _e('Import and use product', ATKP_PLUGIN_PREFIX) ?>';
                                            outputresult += '<tr style="height:100px;">';
                                            outputresult += '<td style="margin:5px;width:110px;max-height:90px;text-align:center"><img style="max-width:100px" src="'+value.imageurl+'" /></td>';
                                        } else {
                                            cnttext = '<?php _e('Use list', ATKP_PLUGIN_PREFIX) ?>';
                                            outputresult += '<tr style="">';                                        
                                        }
                                        
                            
                                        
                                          outputresult += '<td><a href="'+value.producturl+'" target="_blank">'+value.title+'</a><br />ID: '+value.asin+' - EAN: '+(value.ean == null ? '' : value.ean)+'</td>';
                                          
                                          
                                          
                                          outputresult += '<td style="width:25px; text-align:right"><input type="button" id="atkp-btn-import-'+value.asin+'" name="atkp-btn-import-'+value.asin+'" data-id="'+value.productid +'" data-asin="'+value.asin+'" onclick="atkp_importproduct(\'searchtemplate\', cnttype, \''+value.asin+'\', '+value.productid+')" class="import-button button" title="'+ cnttext+'" value="'+cnttext+'" /></td>';
                			
                                          
                                          //outputresult += '<td style="width:25px; text-align:right"><a id="atkp-btn-import-'+value.asin+'" data-id="'+value.productid +'" data-asin="'+value.asin+'" class="button" onclick="atkp_importproduct(\'searchtemplate\', cnttype, \''+value.asin+'\', '+value.productid+')" href="javascript:void(0);">'+cnttext+'</a></td>';
                                          outputresult += '</tr>';
                                        });
                            
                                        outputresult += '</table>';   
                                        $j("#atkp_createresult").html(outputresult);
                                      }
                                    } else {
                                        $j("#atkp_createresult").html('<span><?php _e('No results', ATKP_PLUGIN_PREFIX); ?></span>');   
                                    }
                                } catch (err) {
                                        $j("#atkp_createresult").html('<span style="color:red">' +err.message + '</span>');
                                        $j("#atkp_createloading").hide();
                                        $j("#atkp_createresult").show();
                                }
                              
                               
                                $j("#atkp_createloading").hide();
                                $j("#atkp_createresult").show();
                            },
                            error: function (xhr, status) {   
                                $j("#atkp_createresult").html('<span style="color:red">' + xhr.responseText + '</span>');
                                $j("#atkp_createloading").hide();
                                $j("#atkp_createresult").show();
                            }    
                        });                       
                        
                    });
                    
                    $j('#atkp_create_listsource').change(function(){
                        
                        if($j('#atkp_create_listsource').val() == 10 || $j('#atkp_create_listsource').val() == 11) {
                            $j('#atkp_lbl_createlistsearchterm').html('<?php _e('Browsenode-ID:', ATKP_PLUGIN_PREFIX) ?>');
                        } else {
                            $j('#atkp_lbl_createlistsearchterm').html('<?php _e('Keyword:', ATKP_PLUGIN_PREFIX) ?>');
                        }
                        
                    });
                    
                    $j('#atkp_create_listsource').trigger("change");
                    
            });  
            
            function atkp_createshortcode(type) {
                
                var outputtype = $j('input[name=outputtype]:checked').val();
                var shortcode = '';
                                
                switch(cnttype) {
                    case 'atkp_product':
                        shortcode = '[atkp_product';
                        
                        if(cntid != '')
                            shortcode += ' id=\'' + cntid + '\'';
                        break;
                    case 'atkp_list':
                        shortcode = '[atkp_list';
                        
                        if(cntid != '')
                            shortcode += ' id=\'' + cntid + '\'';   
                            
                        if($j('#<?php echo ATKP_SHORTCODE.'_limit' ?>').val() != '')
                    		shortcode += ' limit=\'' + $j('#<?php echo ATKP_SHORTCODE.'_limit' ?>').val() + '\'';
                    		
                    	if($j('#<?php echo ATKP_SHORTCODE.'_random' ?>').prop('checked'))
                    		shortcode += ' randomsort=\'yes\'';
                        break;
                    
                }    
                
                if($j('#<?php echo ATKP_SHORTCODE.'_override-tracking_id' ?>').val() != '')
                    shortcode += ' tracking_id=\'' + $j('#<?php echo ATKP_SHORTCODE.'_override-tracking_id' ?>').val() + '\'';
                
                switch(outputtype) {
                    case 'template':
                        if($j('#<?php echo ATKP_SHORTCODE.'_template' ?>').val() != '')
                    		shortcode += ' template=\'' + $j('#<?php echo ATKP_SHORTCODE.'_template' ?>').val() + '\'';
                    	if($j('#<?php echo ATKP_SHORTCODE.'_elementcssclass' ?>').val() != '')
                    		shortcode += ' elementcss=\'' + $j('#<?php echo ATKP_SHORTCODE.'_elementcssclass' ?>').val() + '\'';
                    		
                    	var containercss ='';
    
                    	 if($j('#<?php echo ATKP_SHORTCODE.'_align' ?>').val() != '')
                    		containercss = $j('#<?php echo ATKP_SHORTCODE.'_align' ?>').val();
                    	if($j('#<?php echo ATKP_SHORTCODE.'_containercssclass' ?>').val() != '')
                    		containercss += ' ' + $j('#<?php echo ATKP_SHORTCODE.'_containercssclass' ?>').val();
                    	                            		
                    	if(containercss != '')
                    		shortcode += ' containercss=\'' + containercss + '\'';
                    	
                    	if($j('#<?php echo ATKP_SHORTCODE.'_buttontype' ?>').val() != '')
                    		shortcode += ' buttontype=\'' + $j('#<?php echo ATKP_SHORTCODE.'_buttontype' ?>').val() + '\'';
                    	                    
    	                if($j('#<?php echo ATKP_SHORTCODE.'_hidedisclaimer'?>').prop('checked')) 
                            shortcode += ' hidedisclaimer=\'yes\'';
                    		
                    	shortcode += ']';
                
                        if($j('#<?php echo ATKP_SHORTCODE.'_content' ?>').val() != '')
    	                    shortcode += $j('#<?php echo ATKP_SHORTCODE.'_content' ?>').val();
    	                    
                        break;
                    case 'field':
                        
                        if($j('#<?php echo ATKP_SHORTCODE.'_field' ?>').val() != '')
                    		shortcode += ' field=\'' + $j('#<?php echo ATKP_SHORTCODE.'_field' ?>').val() + '\'';
                        
                        if($j('#<?php echo ATKP_SHORTCODE.'_fieldlink'?>').prop('checked')) 
                            shortcode += ' link=\'yes\'';
                        
                        shortcode += ']';
                        break;
                    case 'link':
                        shortcode += ' link=\'yes\'';
                            		                            
                        shortcode += ']';
                        
                        if($j('#<?php echo ATKP_SHORTCODE.'_contentlink' ?>').val() != '')
    	                    shortcode += $j('#<?php echo ATKP_SHORTCODE.'_contentlink' ?>').val();
    	                    
                        break;
                }
                
                switch(cnttype) {
                    case 'atkp_product':
                      
                        shortcode += '[/atkp_product]'; 
                        break;
                    case 'atkp_list':
                       
                        shortcode += '[/atkp_list]'; 
                        break;
                    
                }               
                
                switch(type) {
                    case 'clipboard':
                        var result = window.prompt('<?php _e('Copy to clipboard: Ctrl+C, Enter', ATKP_PLUGIN_PREFIX) ?>', shortcode);
                        
                        // Close popup
                        if(result != null)
                            $j.magnificPopup.close();
                        break;
                    case 'insert':
                        
                        // Close popup
                		$j.magnificPopup.close();
          
                		// Prevent default action
                		//e.preventDefault();
                		// Save original activeeditor
                		//window.su_wpActiveEditor = window.wpActiveEditor;
                		// Set new active editor
                		//window.wpActiveEditor = window.su_generator_target;
                		// Insert shortcode
                		
                		tinyMCE.activeEditor.selection.setContent(shortcode);
                		//old: window.wp.media.editor.insert(shortcode);
                		
                		// Restore previous editor
                		//window.wpActiveEditor = window.su_wpActiveEditor;
                		// Check for target content editor
                		// if (typeof window.su_generator_target === 'undefined') return;
                		// Insert into default content editor
                		// else if (window.su_generator_target === 'content') window.wp.media.editor.insert(shortcode);
                		// Insert into ET page builder (text box)
                		// else if (window.su_generator_target === 'et_pb_content_new') window.wp.media.editor.insert(shortcode);
                		// Insert into textarea
                		// else {
                		// var $target = $('textarea#' + window.su_generator_target);
                		// if ($target.length > 0) $target.val($target.val() + shortcode);
                		// }
                         
                        break;                    
                }       
            }
                
            function atkp_previouspage() {
                var prvpagetype = '';
                                
                atkp_nextpage(prvpagetype, cnttype, cntid, true);
            }
            
            var cntpagetype ='';
            var cnttype ='';
            var cntid ='';     
            
            function atkp_createlist(pagetype, type, listid) {
                btn = $j('#atkp_btn_createlist');
                 
                btn.prop('disabled', true);
                <?php $noncex = wp_create_nonce('atkp-import-nonce'); ?>
                
                $j.ajax({
                  type: "POST",
                  url: "<?php echo ATKPTools::get_endpointurl();  ?>",
                  data: { action: "atkp_create_list", shop: $j('#atkp_create_listshopid').val(), title: $j('#atkp_txt_createlistname').val(), searchterm: $j('#atkp_txt_createlistsearchterm').val(), listtype: $j('#atkp_create_listsource').val(), request_nonce:"<?php echo $noncex ?>" },
                  
                  dataType: "json",
                  success : function(data) {
                        try {
                            if(data.length == 0)
                            {
                                alert('unknown issue');
                                return;
                            } else if(typeof data[0].error != 'undefined')
                              {
                                  alert(data[0].error+': '+data[0].message);
                                  return;
                              }    
                        
                            atkp_nextpage(pagetype, type, data[0].postid);
                        } catch (err) {
                           alert(err.message);
                        }
                        
                        $j('#atkp_btn_createlist').prop('disabled', false);
                        
                      },
                      error: function (xhr, status) {   
                        alert(xhr.responseText);
                        $j('#atkp_btn_createlist').prop('disabled', false);
                      }    
                });
            }    
            
            function atkp_importproduct(pagetype, type, asin, productid) {
                btn = $j('#atkp-btn-import-'+asin);
                
                productid = btn.attr('data-id');
                
                if(productid != '' && productid != 'null') {
                    //produkt bereits importiert                
                    atkp_nextpage(pagetype, type, productid); 
                    return;
                }
                btn.prop('disabled', true);
                                                
                $j.ajax({
                  type: "POST",
                  url: "<?php echo ATKPTools::get_endpointurl(); ?>",
                  data: { action: "atkp_import_product", shop: $j('#atkp_create_shopid').val(), asin: asin, asintype: 'ASIN', title: '', status: 'draft', request_nonce:"<?php echo wp_create_nonce('atkp-import-nonce') ?>" },
                  
                  dataType: "json",
                  success : function(data) {
                        try {
                            if(data.length == 0)
                            {
                                alert('unknown issue');
                                return;
                            } else if(typeof data[0].error != 'undefined')
                              {
                                  alert(data[0].error+': '+data[0].message);
                                  return;
                              }    
                        
                            btn.attr('data-id',data[0].postid);
                            btn.html('<?php _e('Use product', ATKP_PLUGIN_PREFIX) ?>');
                            
                            
                            atkp_nextpage(pagetype, type, data[0].postid);
                            //$j('#atkp-btn-'+$asin).html('<img style="vertical-align:middle" src="<?php echo plugins_url('images/yes.png', ATKP_PLUGIN_FILE) ?>" alt="<?php echo __('Imported', ATKP_PLUGIN_PREFIX) ?>"/><a style="margin-left:5px" href="'+$posturl+'" target="_blank"><?php echo __('Product imported.', ATKP_PLUGIN_PREFIX) ?></a><br />');
                            
                        } catch (err) {
                           alert(err.message);
                        }
                        
                        btn.prop('disabled', false);
                        
                      },
                      error: function (xhr, status) {   
                        alert(xhr.responseText);
                        btn.prop('disabled', false);
                      }    
                });
            }
            
            function atkp_show_info(div, type, id) {
                div.html('');
                
                $j.ajax({
                  type: "POST",
                  url: "<?php echo ATKPTools::get_endpointurl(); ?>",
                  data: { action: "atkp_get_object", post_type: type, post_id: id,request_nonce:"<?php echo wp_create_nonce('atkp-get-nonce') ?>" },
                  
                  dataType: "json",
                  success : function(data) {
                        try {
                            if(data.length == 0)
                            {
                                alert('unknown issue');
                                return;
                            } else if(typeof data[0].error != 'undefined')
                            {
                                alert(data[0].error+': '+data[0].message);
                                return;
                            }    
                            
                            if(data.title == '')
                                data.title = '<?php _e('New post', ATKP_PLUGIN_PREFIX); ?>';
                            
                            //div.html(JSON.stringify(data.title, null, 2));
                            div.html('<a href="'+data.edit_url+'" target="_blank">'+data.title+'</a>'); //'Type: '+ type + ', ID: ' + id + ', Title: '+ JSON.stringify(data, null, 2));    
                        } catch (err) {
                           alert(err.message);
                        }
                        
                      },
                      error: function (xhr, status) {   
                        alert(xhr.responseText);
                      }    
                });
            }
            
            function atkp_nextpage(pagetype, type, id = '', isback = false) {
                
                $j('#atkp-firstpage').hide();
                $j('#atkp-searchproductorlist').hide();
                $j('#atkp-createproduct').hide();
                $j('#atkp-createlist').hide();
                $j('#atkp-searchtemplate').hide();
                
                switch(pagetype) {
                    default:
                        $j('#atkp-firstpage').show();
                        $j('#atkp-steptitle').html('<?php _e('What do you want?', ATKP_PLUGIN_PREFIX) ?>');
                        
                        break;
                    case 'searchproductorlist':
                        $j('#atkp-'+pagetype).show();
                        
                        if(!isback && cnttype != type) {
                            $j("#atkp_prdresult").html('');
                        }
                        
                        if(type == '<?php echo ATKP_PRODUCT_POSTTYPE; ?>')
                            $j('#atkp-steptitle').html('<?php _e('Search product', ATKP_PLUGIN_PREFIX) ?>');
                        else
                            $j('#atkp-steptitle').html('<?php _e('Search list', ATKP_PLUGIN_PREFIX) ?>');
                        break;
                    case 'createproduct':
                        $j('#atkp-'+pagetype).show();
                        
                        $j('#atkp-steptitle').html('<?php _e('Create product', ATKP_PLUGIN_PREFIX) ?>');
                        break;
                    case 'createlist':
                        $j('#atkp-'+pagetype).show();
                        
                        $j('#atkp-steptitle').html('<?php _e('Create list', ATKP_PLUGIN_PREFIX) ?>');
                        break;
                    case 'searchtemplate':
                        atkp_show_info($j('#atkp-current'), type, id);
                                                
                        $j('#atkp-group-field').show();
                        $j('#atkp-group-link').show();
                        $j('.atkp-onlylist').show();                        
                        
                        switch(type) {
                            case 'atkp_list':
                                $j('#atkp-group-field').hide();
                                $j('#atkp-group-link').hide();
                                
                                break;     
                            case 'atkp_product':
                                $j('.atkp-onlylist').hide();
                                break;
                        }
                        
                        
                        $j('#atkp-'+pagetype).show();
                        
                        $j('#atkp-steptitle').html('<?php _e('Output', ATKP_PLUGIN_PREFIX) ?>');
                        break;
                    
                }
                
                if(!isback) {
                    cntpagetype = pagetype;
                    cnttype = type;
                    cntid = id;
                }
            }
            </script>
            
            <?php
        }
    }

?>