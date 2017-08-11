<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_output
    {   
        public function __construct()
        {
            
        }
        
        /**
        * Erstellt die Ausgabe einer konfigurierten Liste und aufgrund der Parameter.
        *
        * A *description*, that can span multiple lines, to go _in-depth_ into the details of this element
        * and to provide some background information or textual references.
        *
        * @id int Die eindeutige ID der Liste im Wordpress-Custom-Posttype.
        *
        * @template string Entweder ein Standardtemplate (wide, box,...) oder die ID der Vorlage (Customposttype).
        * 
        * @content string Ein benutzerdefinierter Text welcher bis zur Vorlage durchgeschleift wird.  
        *
        * @return string Gibt das vollständige HTML zurück.
        */
        public function get_list_output($id, $template = 'wide', $content='', $buttontype = 'notset', $elementcss = '', $containercss = '', $limit = 10, $randomsort = false, $hidedisclaimer = false, $tracking_id ='') {
            
            $template = apply_filters('atkp_modify_template', $template);
            $template = apply_filters('atkp_modify_template_list', $template, $id, $content);
            
            require_once ATKP_PLUGIN_DIR.'/includes/atkp_product.php';
            require_once ATKP_PLUGIN_DIR.'/includes/atkp_product_image.php'; 
            require_once ATKP_PLUGIN_DIR.'/includes/atkp_product_offer.php';
            require_once ATKP_PLUGIN_DIR.'/includes/helper/atkp_template_helper.php';
            
            //Ausgabe der Liste vorbereiten
            
            $templatehelper = new atkp_template_helper();
            $productlist = array();
            
            $productlist = ATKPCache::get_cache_by_id($id);
            
            //wenn liste noch nicht im cache ist, dann bau diese jetzt auf... das kann vorkommen wenn das plugin deaktiviert wurde und nach längerem wieder aktiviert wurde.
            if($productlist == null) {
                $cronjob = new atkp_cronjob(array());
                $cronjob->update_list($id);   
                $productlist = ATKPCache::get_cache_by_id($id);
            }
                        
            $preferlocalproductinfo = ATKPTools::get_post_setting( $id, ATKP_LIST_POSTTYPE.'_preferlocalproduct');
        
            $outputprds = array();
            if($productlist != null) {
                                
                if($randomsort)
                    shuffle($productlist);
                    
                $counter = 0;
                foreach ($productlist as $product) {
                    try {
                        $type = $product['type'];
                        $value = $product['value'];
                        
                        if($value == '')
                            continue;
                        
                        if($counter >= $limit)
                            break;
                        
                        $counter = $counter +1;
                        
                        switch($type) {
                            case 'product':
                                //nur nach lokalen produkten suchen wenn in der
                                if($preferlocalproductinfo)
                                {
                                    $prdfound = atkp_product::loadbyasin($value->asin);
                                    
                                    if($prdfound != null)
                                        $value = $prdfound;
                                }
                                
                                break;
                            case 'productid':
                                if(get_post_status($value) == 'publish' || get_post_status($value) == 'draft')
                                    $value = atkp_product::load($value);
                                else
                                    $value = null;
                                break;
                        }       
                        if($value != null)
                            array_push($outputprds, $value);
                    } catch(Exception $e) {
                        //TODO: 'Exception: ',  $e->getMessage(), "\n";
                    }
                }  
            }
                        
            $resultValue =  $templatehelper->createOutput($outputprds, $content, $template, $containercss, $elementcss, $buttontype, $id, $hidedisclaimer, 0, $tracking_id);
            return $resultValue;
        }
    
        public function get_filterlist_output($template = 'wide', $content='', $buttontype = 'notset', $elementcss = '', $containercss = '', $items_per_page = 10, $hidedisclaimer = false, $tracking_id ='') {
            
            $template = apply_filters('atkp_modify_template', $template);            
            
            require_once ATKP_PLUGIN_DIR.'/includes/atkp_product.php';
            require_once ATKP_PLUGIN_DIR.'/includes/atkp_product_image.php'; 
            require_once ATKP_PLUGIN_DIR.'/includes/atkp_product_offer.php';
            require_once ATKP_PLUGIN_DIR.'/includes/helper/atkp_template_helper.php';
            
            $templatehelper = new atkp_template_helper();
            
            $page = get_query_var( 'page' )  ? intval(get_query_var( 'page' )) : 1;
            
            $outputprds = array();
            $pages = 1;
            
            global $post;
            $args = array( 
                'post_type' => ATKP_PRODUCT_POSTTYPE, 
                'posts_per_page'   => $items_per_page, 
                'post_status'      => array('publish', 'draft'),
                'paged' => $page,
            );
            
            $myfilters = $templatehelper->createFilterPlaceholderArray('');
            
            foreach($myfilters as $fieldname => $control) {
                if(ATKPTools::exists_get_parameter($fieldname) && ATKPTools::get_get_parameter($fieldname, 'string') !='') {
                    
                }
            }
            
            $filtervalues = array();
            
            foreach($_GET as $key => $value) {
                $fieldvalue =ATKPTools::get_get_parameter($key, 'string');
                $fieldname = $key;
                
                if($fieldvalue !='') {
                     if($fieldname == 'search') {
                        $filtervalues['search'] = ATKPTools::get_get_parameter($key, 'string');
                    } else if($fieldname == 'rating') {
                        $filtervalues['rating'] = ATKPTools::get_get_parameter($key, 'int');
                    } else if($fieldname == 'price') {
                        $filtervalues['rating'] = ATKPTools::get_get_parameter($key, 'double');
                    } else if(ATKPTools::startsWith($key, 'productid')) {
                        $filtervalues[$fieldname] = ATKPTools::get_get_parameter($key, 'int');
                    } else if(ATKPTools::startsWith($key, 'customfield_')) {
                        
                    } else if(ATKPTools::startsWith($key, 'customtaxonomy_')) {
                        
                    } else
                        throw new exception('uknown field: '.$fieldname);
                    
                    
                    echo $fieldname . ': '. ATKPTools::get_get_parameter($fieldname, 'string') . '<br />';
                }
            }
                
            $the_query = new WP_Query($args);
            
            $nextpagelink ='';
            $lastpagelink ='';
            
            if ( $the_query->have_posts() ) { 
                                
                while ( $the_query->have_posts() ) {
            		$the_query->the_post();
            		
            		try {
                        $product = atkp_product::load($the_query->post->ID);
                        array_push($outputprds, $product);
                    } catch(Exception $e) {
                            //TODO: 'Exception: ',  $e->getMessage(), "\n";
                    }
            	}
            	
            	$pages = $the_query->max_num_pages;
            	            	
            	/* Restore original Post Data */
            	wp_reset_postdata();
                
                $resultValue =  $templatehelper->createOutput($outputprds, $content, $template, $containercss, $elementcss, $buttontype, '', $hidedisclaimer, 0, $tracking_id);
                
                $nextpagelink = get_permalink($post->ID); ;
            	$lastpagelink = get_permalink($post->ID); ;
                
                $paging = '';
                if($pages > 1) {
                    if($page < $pages) {
                        $paging .= '<a href="'.$nextpagelink.'?page='.($page+1).'">next page</a>';   
                    }
                    
                    $paging .= $page;
                    
                    if($page > 1 && $page <= $pages) {
                        $paging .= '<a href="'.$lastpagelink.'?page='.($page-1).'">last page</a>';   
                    }                    
                }
                    
                $resultValue .= $paging;
            }
            
            return $resultValue;
        }
    
        public function get_product_output($id, $template = 'box', $content='', $buttontype = 'notset', $field, $link = false, $elementcss = '', $containercss = '', $hidedisclaimer = false, $tracking_id='') {
                     
            $template = apply_filters('atkp_modify_template', $template);
            $template = apply_filters('atkp_modify_template_product', $template, $id, $content);
         
            $resultValue ='';
                 
            require_once ATKP_PLUGIN_DIR.'/includes/atkp_product.php';
            require_once ATKP_PLUGIN_DIR.'/includes/atkp_product_image.php'; 
            require_once ATKP_PLUGIN_DIR.'/includes/atkp_product_offer.php';
            
            if(get_post_status($id) != 'publish' && get_post_status($id) != 'draft')
                return '';
            
            $prd = atkp_product::load($id);
                    
            require_once  ATKP_PLUGIN_DIR.'/includes/helper/atkp_template_helper.php';
            $templatehelper = new atkp_template_helper();
            
            if($field != '') {
                $placeholders = ATKPCache::get_cache_by_id('placeholders_'. $id);
                
                if($placeholders == null) {        
                    $placeholders = $templatehelper->createPlaceholderArray($prd, 1, $containercss, $elementcss, $content, $buttontype, '','', $tracking_id);
                    ATKPCache::set_cache_by_id('placeholders_'. $id, $placeholders, 120);
                }
                
                foreach(array_keys($placeholders) as $key)
                    if($key == $field){
                        $resultValue = $placeholders[$key];
                        break;
                    }
                    
                    
                if($containercss != '') {
                    $resultValue = '<div class="'.$containercss.'">'.$resultValue.'</div>';   
                }
             } else                                                
                $resultValue =  $templatehelper->createOutput(array($prd), $content, $template, $containercss, $elementcss, $buttontype, '', $hidedisclaimer, 0, $tracking_id);
            
            if($link == true) {
                $placeholders = $templatehelper->createPlaceholderArray($prd, 1, $containercss, $elementcss, $content, $buttontype, '','', $tracking_id);
                        
                $link = $placeholders['link'];
           
                if($field != '') 
                    $content = $resultValue;
                else if($content == '')
                    $content = $prd->title;
                    
                if(ATKPSettings::$access_mark_links == 1 && strpos($content,'img src') == false)
                        $content .= '*';
                        
                if(ATKPSettings::$access_mark_links == 1 && strpos($content,'img src') == true) {
                    $capt =  __('Advertising', ATKP_PLUGIN_PREFIX);
                    
                    $resultValue = '<div class="'.$containercss.'"><div class="atkp-affiliateimage atkp-clearfix"><a '. $link . ' >'.$content.'</a><div style="margin-top:3px">'. $capt .'</div></div></div>';
                } else
                    $resultValue = '<a '. $link . ' >'.$content.'</a>'; 
                
            }
            
                    
            return $resultValue;
            
        }
    
        public function get_css_url() {
            return plugins_url('/css/style.css', ATKP_PLUGIN_FILE);   
        }
        
        public function get_js_url() {
            return plugins_url('/js/library.js', ATKP_PLUGIN_FILE);   
        }
    
        public function get_css_output() {
         
            $selectedbutton = get_option(ATKP_PLUGIN_PREFIX.'_buttonstyle');
            
            $custom_css = '';                
            
            switch($selectedbutton) {
                default:
                case 1:
                    $custom_css = file_get_contents(ATKP_PLUGIN_DIR.'/css/button_classic.css');
                    break;
                case 2:
                    $btn_background_top =  get_option(ATKP_PLUGIN_PREFIX.'_btn_color_background_top', '#FFB22A');
                    $btn_background_bottom =  get_option(ATKP_PLUGIN_PREFIX.'_btn_color_background_bottom', '#ffab23');
                    $btn_foreground =  get_option(ATKP_PLUGIN_PREFIX.'_btn_color_foreground', '#333333');
                    $btn_border =  get_option(ATKP_PLUGIN_PREFIX.'_btn_color_border', '#ffaa22');
                    
                    $btn_background_top_2 =  get_option(ATKP_PLUGIN_PREFIX.'_btn_color_background_top_2', '#FFB22A');
                    $btn_background_bottom_2 =  get_option(ATKP_PLUGIN_PREFIX.'_btn_color_background_bottom_2', '#ffab23');
                    $btn_foreground_2 =  get_option(ATKP_PLUGIN_PREFIX.'_btn_color_foreground_2', '#333333');
                    $btn_border_2 =  get_option(ATKP_PLUGIN_PREFIX.'_btn_color_border_2', '#ffaa22');
                    
                    
                    $custom_css = file_get_contents(ATKP_PLUGIN_DIR.'/css/button_classic_custom.css');
                     
                    $custom_css = str_replace('%background_color%', $btn_background_top, $custom_css);
                    $custom_css = str_replace('%background2_color%', $btn_background_bottom, $custom_css);
                    $custom_css = str_replace('%foreground_color%', $btn_foreground, $custom_css);
                    $custom_css = str_replace('%border_color%', $btn_border, $custom_css);
                    
                    $custom_css = str_replace('%background_color_2%', $btn_background_top_2, $custom_css);
                    $custom_css = str_replace('%background2_color_2%', $btn_background_bottom_2, $custom_css);
                    $custom_css = str_replace('%foreground_color_2%', $btn_foreground_2, $custom_css);
                    $custom_css = str_replace('%border_color_2%', $btn_border_2, $custom_css);
                    
                    break;
                case 3:
                    $custom_css = file_get_contents(ATKP_PLUGIN_DIR.'/css/button_amazon.css');
                    break;
                case 10:
                    $custom_css = file_get_contents(ATKP_PLUGIN_DIR.'/css/button_flat.css');
                    break;
                case 11:
                    $btn_background_top =  get_option(ATKP_PLUGIN_PREFIX.'_btn_color_background_top', '#FFB22A');
                    $btn_foreground =  get_option(ATKP_PLUGIN_PREFIX.'_btn_color_foreground', '#fff');
                    
                    $btn_background_top_2 =  get_option(ATKP_PLUGIN_PREFIX.'_btn_color_background_top_2', '#FFB22A');
                    $btn_foreground_2 =  get_option(ATKP_PLUGIN_PREFIX.'_btn_color_foreground_2', '#fff');
                    
                    $custom_css = file_get_contents(ATKP_PLUGIN_DIR.'/css/button_flat_custom.css');
                    $custom_css = str_replace('%background_color%', $btn_background_top, $custom_css);
                    $custom_css = str_replace('%foreground_color%', $btn_foreground, $custom_css);
                    
                    $custom_css = str_replace('%background_color_2%', $btn_background_top_2, $custom_css);
                    $custom_css = str_replace('%foreground_color_2%', $btn_foreground_2, $custom_css);
                    break;
                case 20:
                    break;
            }
            
            $custom_css2 = '';
            
            $selectedbox = get_option(ATKP_PLUGIN_PREFIX.'_boxstyle');
            
            switch($selectedbox) {
                default:
                case 1:
                    $custom_css2 = file_get_contents(ATKP_PLUGIN_DIR.'/css/box_classic.css');
                    break;
                case 2:
                    $custom_css2 = file_get_contents(ATKP_PLUGIN_DIR.'/css/box_flat.css');
                    break;
                case 3:
                    $custom_css2 = file_get_contents(ATKP_PLUGIN_DIR.'/css/box_flat_withoutborder.css');
                    break;
              
               
            }
            
            $custom_css3 = '';
            
            if(ATKP_PLUGIN_VERSION >= 30) {
                global $post;
                $args = array( 'post_type' => ATKP_TEMPLATE_POSTTYPE, 'posts_per_page'   => 300, 'post_status'      => array('publish', 'draft'));
                $posts_array = get_posts($args);
                
                foreach ( $posts_array as $prd ) { 
                  $css =  ATKPTools::get_post_setting($prd->ID, ATKP_TEMPLATE_POSTTYPE.'_css', true );
                    
                    if($css != '')
                        $custom_css3 .=  $css ."\r\n" ;
                 };
            }
            
            $custom_css4 = '';
            $predicate1_color = get_option(ATKP_PLUGIN_PREFIX.'_predicate1_color');
            $predicate1_highlightcolor = get_option(ATKP_PLUGIN_PREFIX.'_predicate1_highlightcolor');
            if($predicate1_color != '') {            
                $custom_css4 .='.atkp-producttable-highlight1 {';
                $custom_css4 .='        border-right: 1px solid '.$predicate1_color.' !important;';
                $custom_css4 .='        border-left: 1px solid '.$predicate1_color.' !important;';
                $custom_css4 .='        background-color:'.$predicate1_highlightcolor.'; ';
                $custom_css4 .='}';
                $custom_css4 .='.atkp-producttable-bottom-highlight1 {';
                $custom_css4 .='        border-bottom: 1px solid '.$predicate1_color.' !important;';
                $custom_css4 .='}';
                
                $custom_css4 .='.atkp-predicate-highlight1 {';
                $custom_css4 .='        height:35px;background-color:'.$predicate1_color.';';
                $custom_css4 .='}';
            }
            $predicate2_color = get_option(ATKP_PLUGIN_PREFIX.'_predicate2_color');
            $predicate2_highlightcolor = get_option(ATKP_PLUGIN_PREFIX.'_predicate2_highlightcolor');
            if($predicate2_color != '') {            
                $custom_css4 .='.atkp-producttable-highlight2 {';
                $custom_css4 .='        border-right: 1px solid '.$predicate2_color.' !important;';
                $custom_css4 .='        border-left: 1px solid '.$predicate2_color.' !important;';
                $custom_css4 .='        background-color:'.$predicate2_highlightcolor.'; ';
                $custom_css4 .='}';
                $custom_css4 .='.atkp-producttable-bottom-highlight2 {';
                $custom_css4 .='        border-bottom: 1px solid '.$predicate2_color.' !important;';
                $custom_css4 .='}';
                
                $custom_css4 .='.atkp-predicate-highlight2 {';
                $custom_css4 .='        height:35px;background-color:'.$predicate2_color.';';
                $custom_css4 .='}';
            }
            $predicate3_color = get_option(ATKP_PLUGIN_PREFIX.'_predicate3_color');
            $predicate3_highlightcolor = get_option(ATKP_PLUGIN_PREFIX.'_predicate3_highlightcolor');
            if($predicate3_color != '') {            
                $custom_css4 .='.atkp-producttable-highlight3 {';
                $custom_css4 .='        border-right: 1px solid '.$predicate3_color.' !important;';
                $custom_css4 .='        border-left: 1px solid '.$predicate3_color.' !important;';
                $custom_css4 .='        background-color:'.$predicate3_highlightcolor.'; ';
                $custom_css4 .='}';
                $custom_css4 .='.atkp-producttable-bottom-highlight3 {';
                $custom_css4 .='        border-bottom: 1px solid '.$predicate3_color.' !important;';
                $custom_css4 .='}';
                
                $custom_css4 .='.atkp-predicate-highlight3 {';
                $custom_css4 .='        height:35px;background-color:'.$predicate3_color.';';
                $custom_css4 .='}';
            }
            
            return $custom_css."\r\n". $custom_css2."\r\n".$custom_css3."\r\n".$custom_css4;          
        }
        
        function mixColors($basecolor, $mixcolor, $ratio, $addHash = true) {
            $baseComponentOffset = strlen($basecolor) == 7 ? 1 : 0;
            $baseComponentRed = hexdec(substr($basecolor, $baseComponentOffset, 2));
            $baseComponentGreen = hexdec(substr($basecolor, $baseComponentOffset+2, 2));
            $baseComponentBlue = hexdec(substr($basecolor, $baseComponentOffset+4, 2));
        
            $mixComponentOffset = strlen($mixcolor) == 7 ? 1 : 0;
            $mixComponentRed = hexdec(substr($mixcolor, $mixComponentOffset, 2));
            $mixComponentGreen = hexdec(substr($mixcolor, $mixComponentOffset+2, 2));
            $mixComponentBlue = hexdec(substr($mixcolor, $mixComponentOffset+4, 2));
        
            $Rsum = $baseComponentRed+$mixComponentRed;
            $Gsum = $baseComponentGreen+$mixComponentGreen;
            $Bsum = $baseComponentBlue+$mixComponentBlue;
        
            $R = ($baseComponentRed*(100-$ratio) + $mixComponentRed*$ratio) / 100;
            $G = ($baseComponentGreen*(100-$ratio) + $mixComponentGreen*$ratio) / 100;
            $B = ($baseComponentBlue*(100-$ratio) + $mixComponentBlue*$ratio) / 100;
        
            $redPercentage = max($R, $G, $B) > 255 ? $R/max($Rsum, $Gsum, $Bsum) : $R/255;
            $greenPercentage = max($R, $G, $B) > 255 ? $G/max($Rsum, $Gsum, $Bsum) : $G/255;
            $bluePercentage = max($R, $G, $B) > 255 ? $B/max($Rsum, $Gsum, $Bsum) : $B/255;
        
            $redRGB = floor(255*$redPercentage);
            $greenRGB = floor(255*$greenPercentage);
            $blueRGB = floor(255*$bluePercentage);
        
            $color = sprintf("%02X%02X%02X", $redRGB, $greenRGB, $blueRGB);
            return $addHash ? '#'.$color : $color;
        }
    
    }
    
    
?>