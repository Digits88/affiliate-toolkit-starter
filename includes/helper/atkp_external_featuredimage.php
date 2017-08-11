<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_external_featuredimage {

        public function __construct()
        {
            add_filter('post_thumbnail_html', array($this, 'post_thumbnail_html'), 10, 5);
        }
        
        public function post_thumbnail_html($html, $post_id, $post_image_id, $size, $attr) {
    		if ($this->has_external_thumbnail($post_id)) {
    			$html = $this->get_external_thumbnail($post_id, $size, $attr);
    		}
    
    		return $html;
    	}

        public function has_external_thumbnail($post_id = null) {
    		$thumbnail = $this->get_productid($post_id);
    
    		if (!$thumbnail || $thumbnail== '') {
    			return false;
    		} else {
    			return true;
    		}
    	}
    	
    	public function get_productid($post_id = null) {
    		$post_id = (null === $post_id) ? get_the_ID() : $post_id;
    
            //prüfen ob atkp-produkt dann image holen
            //prüfen ob woocommerce-produkt dann image holen
            //prüfen ob hauptprodukt?
            //überprüfen ob aktiviert
                        
            $posttype = get_post_type($post_id);
            
            if($posttype == ATKP_PRODUCT_POSTTYPE) {
                //at-product
            } else if($posttype == 'product') {
                //woocommerce
                //TODO: laden aus der veknüpfung
                
                
            } else {
                $productid = ATKPTools::get_post_setting( $post_id, ATKP_PLUGIN_PREFIX.'_product');   
                
                if(isset($productid) && $productid != '') {
                    $post_id = $productid;
                } else
                    return false;
            }
            
            return $post_id;            
    	}
    
    	public function get_external_thumbnail($post_id = null, $size = false, $attr = array()) {
    		global $_wp_additional_image_sizes;
    
            $prdid = $this->has_external_thumbnail($post_id);
    
    		if (!$prdid) {
    			return false;
    		}
    		
    		
    		
    
    		if (is_array($size)) {
    			$width = $size[0];
    			$height = $size[1];
    		} else if (isset($_wp_additional_image_sizes[$size])) {
    			$width = $_wp_additional_image_sizes[$size]['width'];
    			$height = $_wp_additional_image_sizes[$size]['height'];
    			$additional_classes = 'attachment-' . $size . ' ';
    		}
    
    		$width = ($width && $width > 0) ? 'width:'.$width.'px;' : '';
    		$height = ($height && $height > 0) ? 'height:'.$height.'px;' : '';
        
            $imageurl = '';        
    		$title = ATKPTools::get_post_setting( $prdid, ATKP_PRODUCT_POSTTYPE.'_title');
            
            //todo: je nach thumbnail größe einbinden
            
            $imageurl = ATKPTools::get_post_setting( $prdid, ATKP_PRODUCT_POSTTYPE.'_smallimageurl');
            if($imageurl == '')
                $imageurl = ATKPTools::get_post_setting( $prdid, ATKP_PRODUCT_POSTTYPE.'_mediumimageurl');
            if($imageurl == '')
                $imageurl = ATKPTools::get_post_setting( $prdid, ATKP_PRODUCT_POSTTYPE.'_largeimageurl');
    
            if($imageurl == '')
                return false;
    
    		if (isset($attr['class'])) {
    			$additional_classes .= $attr['class'];
    		}
    
    		$style = isset($attr['style']) ? 'style="' . $attr['style'] . '" ' : null;
    
    		//if (is_feed()) {
    		//	$html = sprintf('<img src="%s" %s' . 'class="%s wp-post-image" '. 'alt="%s" />', esc_attr($imageurl), $style, $additional_classes, $title);
    		//} else {
    			$html = sprintf('<img src="%s" %s' . 'class="%s wp-post-image" '. 'alt="%s" />', esc_attr($imageurl), $style, $additional_classes, $title);
    		//}
    
    		return $html;
    	}
    

    }