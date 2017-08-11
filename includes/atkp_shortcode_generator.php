<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_shortcode_generator
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
            

    		
    	
    		
        }

        function shortcode_popup() {
           
         //TODO: implement cache 
         ?>
         
         <div id="atkp-generator-wrap" style="display:none">
			<div id="atkp-generator">
			<div id="atkp-generatorheader">
			    <b>Affiliate-Toolkit Shortcodes</b>
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
        	max-width: 1000px;
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
		</style>
         
        <script>
        
          jQuery(document).ready(function($) {
        var $generator = $('#atkp-generator'),mce_selection = '';
        
         
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
        					mce_selection = (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor != null && tinyMCE.activeEditor.hasOwnProperty('selection')) ? tinyMCE.activeEditor.selection.getContent({
        						format: "text"
        					}) : '';
        				},
        				close: function() {
        					// Remove narrow class
        					$generator.removeClass('atkp-generator-narrow');
        					// Show filters
        				
        					// Clear selection
        					mce_selection = '';
        					
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
        	
        	$('#atkp-generator').on('click', '#atkp-generator-insert', function(e) {
        		// Prepare data
        		$j('#<?php echo ATKP_SHORTCODE.'_shortcode_btn' ?>').trigger('click');
        		
        		var shortcode = $j('#<?php echo ATKP_SHORTCODE.'_shortcode_txt' ?>').val();

        		// Close popup
        		$.magnificPopup.close();
  
        		// Prevent default action
        		e.preventDefault();
        		// Save original activeeditor
        		window.su_wpActiveEditor = window.wpActiveEditor;
        		// Set new active editor
        		window.wpActiveEditor = window.su_generator_target;
        		// Insert shortcode
        		window.wp.media.editor.insert(shortcode);
        		// Restore previous editor
        		window.wpActiveEditor = window.su_wpActiveEditor;
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
    				'text'      => __( 'Affiliate-Toolkit shortcodes', ATKP_PLUGIN_PREFIX ),
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
                        
                //add_meta_box( 
                //    ATKP_SHORTCODE.'_detail_box',
                //    __( 'Affiliate Toolkit Shortcodes', ATKP_PLUGIN_PREFIX),
                //    array(&$this, 'template_detail_box_content'),
                 //   $type,
                //    'side',
                //    'default'
                //);
                
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
                        <select class="widefat" id="<?php echo ATKP_PLUGIN_PREFIX.'_product' ?>" name="<?php echo ATKP_PLUGIN_PREFIX.'_product' ?>">
                                            <?php
                            $val = ATKPTools::get_post_setting($post->ID, ATKP_PLUGIN_PREFIX.'_product' );
                                               
                            echo '<option value="" '.($val == '' ? 'selected' : '').'>' . __('None', ATKP_PLUGIN_PREFIX) . '</option>';
                                            
                            global $post;
                            $args = array( 'post_type' => ATKP_PRODUCT_POSTTYPE, 'posts_per_page'   => 300, 'post_status'      => array('publish', 'draft'));
                            $posts_array = get_posts($args);
                                foreach ( $posts_array as $prd ) {                                                                         
                                    echo '<option value="' .$prd->ID . '"' . ($val == $prd->ID ? 'selected' : '') . '>' . $prd->post_title.' ('.$prd->ID.')' . '</option>';
                                 };
                             ?>
                        </select>
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
			               
            $productid = ATKPTools::get_post_parameter(ATKP_PLUGIN_PREFIX.'_product', 'int'); 
    
            //lade das produkt und schau ob es vorhanden ist
            $prd = get_post($productid);
    
            if(!isset($prd) || $prd == null) {
                //wenn es nicht existiert, verknüpfung löschen
                ATKPTools::set_post_setting( $post_id, ATKP_PLUGIN_PREFIX.'_product', null);
            }
            else {            
                //wenn das Produkt exisitert, dann lege in diesem Beitrag ein benutzerdefiniertes Feld an
                ATKPTools::set_post_setting( $post_id, ATKP_PLUGIN_PREFIX.'_product', $productid);  
    
    
                
                //im produkt selbst kann es mehrere verknüpfte Beiträge geben
                $postids = array();
                
                $args = array (
                'post_type'              => array( 'post', 'page' ),  // YOUR POST TYPE
                'post_status'   => array('publish', 'draft'),
                'meta_query'             => array(
                    array(
                        'key'       => ATKP_PLUGIN_PREFIX.'_product',  
                        'value'     => $productid,  
                        'compare'   => '=',  
                        'type'      => 'CHAR',
                        ),
                    ),
                );
    
                // The Query
                $query = new WP_Query( $args );
                
                if ( $query->have_posts() ) {
                     while ( $query->have_posts() ) {
                         $query->the_post();
                		array_push($postids, $query->post->ID);
                	}
                	
                }
                
                ATKPTools::set_post_setting( $productid, ATKP_PRODUCT_POSTTYPE.'_postid', $postids);
                
            }
            
         }
        
        function template_detail_box_content( $post ) {
            ?>
            
            
            <p>
            <label for="<?php echo ATKP_SHORTCODE.'_type' ?>"><?php _e( 'Type:' , ATKP_PLUGIN_PREFIX); ?></label> 
            <select class="widefat" id="<?php echo ATKP_SHORTCODE.'_type' ?>" name="<?php echo ATKP_SHORTCODE.'_type' ?>">
                                            <?php
                                            echo '<option value="1">'.__('product', ATKP_PLUGIN_PREFIX).'</option>';
                                            echo '<option value="2">'.__('list', ATKP_PLUGIN_PREFIX).'</option>';
                                            echo '<option value="3">'.__('field', ATKP_PLUGIN_PREFIX).'</option>';
                                            echo '<option value="4">'.__('link', ATKP_PLUGIN_PREFIX).'</option>';                                           
             ?>
            </select>
            </p>

    <div id="<?php echo ATKP_SHORTCODE.'_product_div' ?>">
            <p>
            <label for="<?php echo ATKP_SHORTCODE.'_product' ?>"><?php _e( 'Product:' , ATKP_PLUGIN_PREFIX); ?></label> 
            <select class="widefat" id="<?php echo ATKP_SHORTCODE.'_product' ?>" name="<?php echo ATKP_SHORTCODE.'_product' ?>">
                                            <?php
                                            
                                            global $post;
            $args = array( 'post_type' => ATKP_PRODUCT_POSTTYPE, 'posts_per_page'   => 300, 'post_status'      => array('publish', 'draft'));
            $posts_array = get_posts($args);
            foreach ( $posts_array as $prd ) { 
                                                    
                                                    echo '<option value="' .$prd->ID . '"' . $sel . '>' . $prd->post_title.' ('.$prd->ID.')' . '</option>';
             };
             ?>
            </select>
            </p>
            
            </div>
                <div id="<?php echo ATKP_SHORTCODE.'_list_div' ?>">
            <p>
            <label for="<?php echo ATKP_SHORTCODE.'_list' ?>"><?php _e( 'List:' , ATKP_PLUGIN_PREFIX); ?></label> 
            <select class="widefat" id="<?php echo ATKP_SHORTCODE.'_list' ?>" name="<?php echo ATKP_SHORTCODE.'_list' ?>">
                                            <?php
                                            
                                            global $post;
            $args = array( 'post_type' => ATKP_LIST_POSTTYPE, 'posts_per_page'   => 300, 'post_status'      => 'publish');
            $posts_array = get_posts($args);
            foreach ( $posts_array as $prd ) { 
                                                                
                                                    echo '<option value="' .$prd->ID . '"' . $sel . '>' . $prd->post_title.' ('.$prd->ID.')' . '</option>';
             };
             ?>
            </select></p>
            
            
            <p>
            <label for="<?php echo ATKP_SHORTCODE.'_limit' ?>"><?php _e( 'Limit:' , ATKP_PLUGIN_PREFIX); ?></label> 
            <input class="widefat" id="<?php echo ATKP_SHORTCODE.'_limit' ?>" name="<?php echo ATKP_SHORTCODE.'_limit' ?>" type="number" min="1" value="" />
            </p>
            
            <p>
            <input type="checkbox" id="<?php echo ATKP_SHORTCODE.'_random' ?>" name="<?php echo ATKP_SHORTCODE.'_random' ?>">
                                        
                                         <label for="<?php echo ATKP_SHORTCODE.'_random' ?>">
                                                <?php _e('Random sort', ATKP_PLUGIN_PREFIX) ?>
                                            </label> 
            </p>
            </div>
            <div id="<?php echo ATKP_SHORTCODE.'_template_div' ?>">
            <p>
            <label for="<?php echo ATKP_SHORTCODE.'_template' ?>"><?php _e( 'Template:' , ATKP_PLUGIN_PREFIX); ?></label> 
            <select class="widefat" id="<?php echo ATKP_SHORTCODE.'_template' ?>" name="<?php echo ATKP_SHORTCODE.'_template' ?>">
                                            <?php
                                            echo '<option value="">'.__('default', ATKP_PLUGIN_PREFIX).'</option>';
                                            
                                            echo '<option value="bestseller">'.__('bestseller', ATKP_PLUGIN_PREFIX).'</option>';
                                            echo '<option value="wide">'.__('wide', ATKP_PLUGIN_PREFIX).'</option>';
                                            echo '<option value="secondwide">'.__('secondwide', ATKP_PLUGIN_PREFIX).'</option>';
                                            echo '<option value="box">'.__('box', ATKP_PLUGIN_PREFIX).'</option>';
                                            echo '<option value="detailoffers">'.__('all offers', ATKP_PLUGIN_PREFIX).'</option>';
                                            
                                            
                                            if(ATKP_PLUGIN_VERSION >= 30) {
                                            global $post;
            $args = array( 'post_type' => ATKP_TEMPLATE_POSTTYPE, 'posts_per_page'   => 300, 'post_status'      => array('publish', 'draft'));
            $posts_array = get_posts($args);
            foreach ( $posts_array as $prd ) { 
                                             
                                                    echo '<option value="' .$prd->ID . '"' . $sel . '>' . $prd->post_title.' ('.$prd->ID.')' . '</option>';
             };
             }?>
            </select>
            </p>
            
            <p>
            <label for="<?php echo ATKP_SHORTCODE.'_containercssclass' ?>"><?php _e( 'Container CSS Class:' , ATKP_PLUGIN_PREFIX); ?></label> 
            <input class="widefat" id="<?php echo ATKP_SHORTCODE.'_containercssclass' ?>" name="<?php echo ATKP_SHORTCODE.'_containercssclass' ?>" type="text" value="" />
            </p>
            
            <p>
            <label for="<?php echo ATKP_SHORTCODE.'_elementcssclass' ?>"><?php _e( 'Element CSS Class:' , ATKP_PLUGIN_PREFIX); ?></label> 
            <input class="widefat" id="<?php echo ATKP_SHORTCODE.'_elementcssclass' ?>" name="<?php echo ATKP_SHORTCODE.'_elementcssclass' ?>" type="text" value="" />
            </p>
            </div>
            
            <div id="<?php echo ATKP_SHORTCODE.'_align_div' ?>">
            <p>
            <label for="<?php echo ATKP_SHORTCODE.'_align' ?>"><?php _e( 'Align:' , ATKP_PLUGIN_PREFIX); ?></label> 
             <select class="widefat" id="<?php echo ATKP_SHORTCODE.'_align' ?>" name="<?php echo ATKP_SHORTCODE.'_align' ?>">
                <option value=""><?php _e( 'no alignment' , ATKP_PLUGIN_PREFIX); ?></option>  
                <option value="atkp-left atkp-clearfix"><?php _e( 'left' , ATKP_PLUGIN_PREFIX); ?></option>  
                <option value="atkp-center"><?php _e( 'center' , ATKP_PLUGIN_PREFIX); ?></option>
                <option value="atkp-right atkp-clearfix"><?php _e( 'right' , ATKP_PLUGIN_PREFIX); ?></option>
            </select>
            </p>
            </div>
            
                <div id="<?php echo ATKP_SHORTCODE.'_content_div' ?>">
            <p>
            <label for="<?php echo ATKP_SHORTCODE.'_content' ?>"><?php _e( 'Content:' , ATKP_PLUGIN_PREFIX); ?></label> 
            <input class="widefat" id="<?php echo ATKP_SHORTCODE.'_content' ?>" name="<?php echo ATKP_SHORTCODE.'__content' ?>" type="text" value="" />
            </p>
            </div>
            
            <div id="<?php echo ATKP_SHORTCODE.'_field_div' ?>">
            <p>
            <label for="<?php echo ATKP_SHORTCODE.'_field' ?>"><?php _e( 'Field:' , ATKP_PLUGIN_PREFIX); ?></label> 
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
            </p>
            <p>
            <input type="checkbox" id="<?php echo ATKP_SHORTCODE.'_link' ?>" name="<?php echo ATKP_SHORTCODE.'_link' ?>">
                                        
                                         <label for="<?php echo ATKP_SHORTCODE.'_link' ?>">
                                                <?php _e('Hyperlink', ATKP_PLUGIN_PREFIX) ?>
                                            </label> 
            </p>
            </div>
            <div id="<?php echo ATKP_SHORTCODE.'_button_div' ?>">
            <p><label for="<?php echo ATKP_SHORTCODE.'_buttontype' ?>"><?php _e( 'Buttontype:' , ATKP_PLUGIN_PREFIX); ?></label>                       
             <select class="widefat" id="<?php echo ATKP_SHORTCODE.'_buttontype' ?>" name="<?php echo ATKP_SHORTCODE.'_buttontype' ?>">
                                            <?php                                           
                                            
            echo '<option value="">'.__('default', ATKP_PLUGIN_PREFIX).'</option>';
            echo '<option value="addtocart">'.__('add to cart', ATKP_PLUGIN_PREFIX).'</option>';
            echo '<option value="link">'.__('link', ATKP_PLUGIN_PREFIX).'</option>';
            echo '<option value="product">'.__('product page', ATKP_PLUGIN_PREFIX).'</option>';
             ?>
            </select>
            </p>
            </div>
            <div class="atkp-short_result">
            <input type="text" id="<?php echo ATKP_SHORTCODE.'_shortcode_txt' ?>" style="width:100%" readonly="">
            
            <div>
            <a href="javascript:void(0);" id="<?php echo ATKP_SHORTCODE.'_shortcode_btn' ?>" style="margin-top:5px"  class="button"><i class="fa fa-refresh"></i>&nbsp;<?php _e( 'refresh shortcode', ATKP_PLUGIN_PREFIX)?> </a>
            
            <a href="javascript:void(0);" id='atkp-generator-insert' class="button button-primary" style="margin-top:5px"><i class="fa fa-check"></i>&nbsp;<?php echo __('Insert shortcode', ATKP_PLUGIN_PREFIX) ?></a>
			</div>
			&nbsp;
            </div>
            <script type="text/javascript">
                var $j = jQuery.noConflict();
                $j(document).ready(function($) {                  
                      
                      $j('#<?php echo ATKP_SHORTCODE.'_type' ?>').change(function () {
                        
                        var $productdiv =  $j('#<?php echo ATKP_SHORTCODE.'_product_div' ?>');
                        var $listdiv =  $j('#<?php echo ATKP_SHORTCODE.'_list_div' ?>');
                        var $templatediv =  $j('#<?php echo ATKP_SHORTCODE.'_template_div' ?>');
                        
                        var $contentdiv =  $j('#<?php echo ATKP_SHORTCODE.'_content_div' ?>');
                        var $fielddiv =  $j('#<?php echo ATKP_SHORTCODE.'_field_div' ?>');
                        var $aligndiv =  $j('#<?php echo ATKP_SHORTCODE.'_align_div' ?>');
                        var $buttondiv =  $j('#<?php echo ATKP_SHORTCODE.'_button_div' ?>');
                        
                        $productdiv.hide();
                         $listdiv.hide();
                          $templatediv.hide();
                          $aligndiv.hide();
                          
                          $contentdiv.hide();
                          $fielddiv.hide();
                          $buttondiv.hide();
        
                        
                        switch($j('#<?php echo ATKP_SHORTCODE.'_type' ?>').val()) {
                            
                            case '1':
                                //product
                                $productdiv.show();
                                $templatediv.show();
                                $contentdiv.show();
                                $buttondiv.show();
                                $aligndiv.show();
                                break;
                            case '2':
                                //list
                                $templatediv.show();
                                $listdiv.show();
                                $contentdiv.show();
                                $buttondiv.show();
                                break;
                            case '3':
                                //field
                                $productdiv.show();
                                $fielddiv.show();
                                $aligndiv.show();             
                                break;
                            case '4':
                                //link
                                $productdiv.show();
                                $contentdiv.show();
                                break;
                        }
                          
                          
                      });
                      
                      
                      $j('#<?php echo ATKP_SHORTCODE.'_type' ?>').trigger("change");                      
                      
                      
                      $j('#<?php echo ATKP_SHORTCODE.'_shortcode_btn' ?>').click(function (e) {
                          var $shortcode = '';

                          switch($j('#<?php echo ATKP_SHORTCODE.'_type' ?>').val()) {
                            
                            case '1':
                                //product
                                $shortcode = '[atkp_product'
                                
                                if($j('#<?php echo ATKP_SHORTCODE.'_product' ?>').val() != '')
                                    $shortcode += ' id=\'' + $j('#<?php echo ATKP_SHORTCODE.'_product' ?>').val() + '\'';
                                    
                                if($j('#<?php echo ATKP_SHORTCODE.'_template' ?>').val() != '')
                                    $shortcode += ' template=\'' + $j('#<?php echo ATKP_SHORTCODE.'_template' ?>').val() + '\'';
                                
                                if($j('#<?php echo ATKP_SHORTCODE.'_elementcssclass' ?>').val() != '')
                                    $shortcode += ' elementcss=\'' + $j('#<?php echo ATKP_SHORTCODE.'_elementcssclass' ?>').val() + '\'';
                                    
                                var $containercss ='';
                                
                                 if($j('#<?php echo ATKP_SHORTCODE.'_align' ?>').val() != '')
                                    $containercss = $j('#<?php echo ATKP_SHORTCODE.'_align' ?>').val();
                                if($j('#<?php echo ATKP_SHORTCODE.'_containercssclass' ?>').val() != '')
                                    $containercss = $containercss + ' ' + $j('#<?php echo ATKP_SHORTCODE.'_containercssclass' ?>').val();
                                
                                    
                                if($containercss != '')
                                    $shortcode += ' containercss=\'' + $containercss + '\'';
                                
                                if($j('#<?php echo ATKP_SHORTCODE.'_buttontype' ?>').val() != '')
                                    $shortcode += ' buttontype=\'' + $j('#<?php echo ATKP_SHORTCODE.'_buttontype' ?>').val() + '\'';
                                
                                
                                $shortcode += ']';
                                
                                if($j('#<?php echo ATKP_SHORTCODE.'_content' ?>').val() != '')
                                    $shortcode += $j('#<?php echo ATKP_SHORTCODE.'_content' ?>').val();
                                
                                
                                $shortcode += '[/atkp_product]';                                
                                
                                break;
                            case '2':
                                //list
                                $shortcode = '[atkp_list'
                                
                                 if($j('#<?php echo ATKP_SHORTCODE.'_list' ?>').val() != '')
                                    $shortcode += ' id=\'' + $j('#<?php echo ATKP_SHORTCODE.'_list' ?>').val() + '\'';
                                    
                                if($j('#<?php echo ATKP_SHORTCODE.'_template' ?>').val() != '')
                                    $shortcode += ' template=\'' + $j('#<?php echo ATKP_SHORTCODE.'_template' ?>').val() + '\'';
                                
                                if($j('#<?php echo ATKP_SHORTCODE.'_elementcssclass' ?>').val() != '')
                                    $shortcode += ' elementcss=\'' + $j('#<?php echo ATKP_SHORTCODE.'_elementcssclass' ?>').val() + '\'';
                                    
                                if($j('#<?php echo ATKP_SHORTCODE.'_containercssclass' ?>').val() != '')
                                    $shortcode += ' containercss=\'' + $j('#<?php echo ATKP_SHORTCODE.'_containercssclass' ?>').val() + '\'';
                                
                                if($j('#<?php echo ATKP_SHORTCODE.'_buttontype' ?>').val() != '')
                                    $shortcode += ' buttontype=\'' + $j('#<?php echo ATKP_SHORTCODE.'_buttontype' ?>').val() + '\'';
                                
                                if($j('#<?php echo ATKP_SHORTCODE.'_limit' ?>').val() != '')
                                    $shortcode += ' limit=\'' + $j('#<?php echo ATKP_SHORTCODE.'_limit' ?>').val() + '\'';
                                    
                                if($j('#<?php echo ATKP_SHORTCODE.'_random' ?>').prop('checked'))
                                    $shortcode += ' randomsort=\'yes\'';
                                
                                $shortcode += ']';
                                
                                if($j('#<?php echo ATKP_SHORTCODE.'_content' ?>').val() != '')
                                    $shortcode += $j('#<?php echo ATKP_SHORTCODE.'_content' ?>').val();
                                
                                $shortcode += '[/atkp_list]'; 
                                
                                break;
                            case '3':
                                //field
                                
                                $shortcode = '[atkp_product'
                                
                                if($j('#<?php echo ATKP_SHORTCODE.'_product' ?>').val() != '')
                                    $shortcode += ' id=\'' + $j('#<?php echo ATKP_SHORTCODE.'_product' ?>').val() + '\'';
                                    
                                 if($j('#<?php echo ATKP_SHORTCODE.'_field' ?>').val() != '')
                                    $shortcode += ' field=\'' + $j('#<?php echo ATKP_SHORTCODE.'_field' ?>').val() + '\'';
                                    
                                var $containercss ='';
                                
                                 if($j('#<?php echo ATKP_SHORTCODE.'_align' ?>').val() != '')
                                    $containercss = $j('#<?php echo ATKP_SHORTCODE.'_align' ?>').val();
                                    
                                if($containercss != '')
                                    $shortcode += ' containercss=\'' + $containercss + '\'';
                                    
                                if($j('#<?php echo ATKP_SHORTCODE.'_link'?>').prop('checked')) {
                                     $shortcode += ' link=\'yes\'';
                                }
                                
                                $shortcode += ']';
                            
                                
                                $shortcode += '[/atkp_product]';                           
                                   
             
                                break;
                            case '4':
                                //link
                                $shortcode = '[atkp_product]'
                                
                                 $shortcode = '[atkp_product'
                                
                                if($j('#<?php echo ATKP_SHORTCODE.'_product' ?>').val() != '')
                                    $shortcode += ' id=\'' + $j('#<?php echo ATKP_SHORTCODE.'_product' ?>').val() + '\'';
                                    
                               
                                     $shortcode += ' link=\'yes\'';
                                
                                
                                $shortcode += ']';
                                
                                if($j('#<?php echo ATKP_SHORTCODE.'_content' ?>').val() != '')
                                    $shortcode += $j('#<?php echo ATKP_SHORTCODE.'_content' ?>').val();
                                
                                
                                $shortcode += '[/atkp_product]';                           
                                break;
                        }
                        
                        $j('#<?php echo ATKP_SHORTCODE.'_shortcode_txt' ?>').val($shortcode)
                          
                      });
                      
                      $j('#<?php echo ATKP_SHORTCODE.'_shortcode_txt' ?>').click(function (e) {
                          

                          $j('#<?php echo ATKP_SHORTCODE.'_shortcode_txt' ?>').select();
                          
                      });
                });  
            </script>
            
            <?php
        }
    }

?>