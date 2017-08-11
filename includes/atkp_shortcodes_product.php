<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_shortcodes_product
    {   
        /**
         * Construct the plugin object
         */
        public function __construct($pluginbase)
        {
            add_shortcode(ATKP_PRODUCT_SHORTCODE, array(&$this, 'shortcode'));
            add_action( 'wp_footer', array($this, 'infobox_div' ));
            


        }
        function infobox_div() {
            echo('<!-- Anfang DIV für die InfoBox -->');
            echo('<div id="atkp-infobox" class="atkp-popup" style="z-index:1; visibility:hidden;">');
            echo('<div id="BoxInnen"><span id="BoxInhalte">&nbsp;</span></div>');
            echo('</div>');
            echo('<!-- Ende DIV für die InfoBox -->');
            
        }
        
        function shortcode($atts, $content = "") {
            try {
            
                $a = shortcode_atts( array(
                    'id' => '',
                    'ean' => '',
                    'template' => '',
                    'elementcss' => '',
                    'containercss'=>'',
                    'buttontype' => '',
                    'field' =>'',
                    'link' =>'',
                    'hidedisclaimer' => 'no',
                    'tracking_id' => '',
                ), $atts );
                
                $id = '';
                $template = 'box';
                $buttontype = 'notset';
                $field ='';
                $link = false;
                $elementcss = '';
                $containercss = '';
                $tracking_id = '';
                $ean = '';
                                
                if (isset($a['id'])) {
                    $id = $a['id'];
                }
                
                if(!is_numeric($id) && $id != '') {
                    require_once ATKP_PLUGIN_DIR.'/includes/atkp_product.php';
                    
                    $id2 = atkp_product::idbyname($id);
                    
                    if($id2 == null)
                        throw new Exception('product (name) not found: '.$id);
                    else
                        $id = $id2;
                }
                
                if(isset($a['ean']) && $id == '') {
                    $ean = $a['ean'];
                    if($ean != '') {
                        require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product.php';
                        $id = atkp_product::idbyean($ean);   
                        
                        if($id != null)
                            $id = $id;
                    }
                }
                
                if (isset($a['template']) && !empty($a['template'])) 
                    $template = $a['template'];
                    
                if (isset($a['elementcss']) && !empty($a['elementcss'])) 
                    $elementcss = $a['elementcss'];
                if (isset($a['containercss']) && !empty($a['containercss'])) 
                    $containercss = $a['containercss'];
                    
                if (isset($a['field']) && !empty($a['field'])) 
                    $field = $a['field'];
                    
                if (isset($a['buttontype']) && !empty($a['buttontype'])) 
                    $buttontype = $a['buttontype'];
                        
                if (isset($a['hidedisclaimer']) && !empty($a['hidedisclaimer'])) 
                    if($a['hidedisclaimer'] == 'yes')
                        $hidedisclaimer =true;
                    else if($a['hidedisclaimer'] == 'no')
                        $hidedisclaimer =false;
                        
                if (isset($a['link']) && $a['link'] == 'yes')
                    $link = true;
                    
                if (isset($a['tracking_id']) && !empty($a['tracking_id'])) 
                    $tracking_id = $a['tracking_id'];
                    
                                     
                if($id == '') {
                    if(get_post_type() == ATKP_PRODUCT_POSTTYPE) {
                        $id = get_the_ID();   
                        $hidedisclaimer =true;                         
                       
                    } else if(get_post_type() == 'product') {
                        $woo_id = get_the_ID(); 
                        $hidedisclaimer =true; 
                        //woocommerce
                        //INFO: Auch in external_featuredimage in verwendung
                       
                        $eanfield = get_option(ATKP_PLUGIN_PREFIX.'_woo_ean_field', '');
                        $keytype = get_option(ATKP_PLUGIN_PREFIX.'_woo_keytype', 'ean');
                        
                        if($eanfield == '' || $eanfield == 'sku')
                            $ean =  ATKPTools::get_post_setting( $woo_id, '_sku'); 
                        else
                            $ean = ATKPTools::get_post_setting( $woo_id, $eanfield);
                            
                        require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product.php';    
                            
                        if($keytype == 'id') {
                            $exists = atkp_product::exists($ean);
                            
                            if($exists)
                                $id = $ean;
                        }
                        else
                            $id =  atkp_product::idbyean($ean);
                    }
                    else {
                       $queried_object = get_queried_object();

                        if ( $queried_object ) {
                            $post_id = $queried_object->ID;
                            $product_temp = ATKPTools::get_post_setting( $post_id, ATKP_PLUGIN_PREFIX.'_product');
                            
                            if($product_temp != null)
                                $id = $product_temp;
                        } 
                    }
                }
                  
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_output.php';
                
                $output = new atkp_output();
                  
                return $output->get_product_output($id, $template, $content, $buttontype, $field, $link, $elementcss, $containercss, $hidedisclaimer, $tracking_id);
            
            } catch(Exception $e) { 
                return 'Exception: '. $e->getMessage();
            }
         }
    }
    
    
?>