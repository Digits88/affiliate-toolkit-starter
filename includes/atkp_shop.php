<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class atkp_shop{
        public $data = array();

        function __construct()
        {
            $this->provider =null;
            $this->settingid = '';
            $this->webservice = '';
            
            $this->displayshoplogo = false;  
            $this->enablepricecomparison = false;   
            $this->buyat = '';   
            $this->addtocart = ''; 
            $this->tooltip = ''; 
            
            $this->customtitle = '';
            $this->customsmalllogourl = '';
            $this->customlogourl = '';
            $this->customfield1 = '';
            $this->customfield2 = '';
            $this->customfield3 = '';
            
            $this->holdontop = false;
            
            $this->oldid = '';
        }
        
        public function get_addtocart() {
            return sprintf($this->addtocart == '' ? __('Buy now at %s', ATKP_PLUGIN_PREFIX) : $this->addtocart, $this->get_title());
        }
        
        public function get_buyat() {
            return sprintf($this->buyat == '' ? __('Buy now at %s', ATKP_PLUGIN_PREFIX) : $this->buyat, $this->get_title());
        }
        
        public function get_tooltip() {
            return sprintf($this->tooltip == '' ? __('Buy now at %s', ATKP_PLUGIN_PREFIX) : $this->tooltip, $this->get_title());
        }
        
        public function get_title() {
            return $this->customtitle == '' ? $this->outputtitle : $this->customtitle;
        }
        
        public function get_logourl() {
            return $this->customlogourl == '' ? $this->logourl : $this->customlogourl;
        }
        
        public function get_smalllogourl() {
            return $this->customsmalllogourl == '' ? $this->smalllogourl : $this->customsmalllogourl;
        }

        public static function get_list($saved_shopid, $full_load = false) {
            $shops = array();
            $subshopids = explode('_', $saved_shopid);
            
            $posts_array = get_posts(array( 'post_type' => ATKP_SHOP_POSTTYPE, 'posts_per_page'   => -1, 'post_status'      => array('publish', 'draft')));
            
            if(count($subshopids) > 1) {
                //gespeicherter subshop ist veraltet (unterteilt mit _)
                
                
            } 
            $savedshop_found = false;
            
            foreach ( $posts_array as $prd ) { 
                $children = get_posts(array( 'post_type' => ATKP_SHOP_POSTTYPE, 'posts_per_page'   => -1, 'post_parent' => $prd->ID, 'post_status'      => array('publish', 'draft')));
                
                //überspringen aller netzwerk-shops (hier sind untergeordnete Shops vorhanden)
                if(count($children) > 0)
                    continue;
                
                $webservice = ATKPTools::get_post_setting( $prd->ID, ATKP_SHOP_POSTTYPE.'_access_webservice');
                
                if($webservice == ATKP_SUBSHOPTYPE) {
                    $shp = atkp_shop::load($prd->ID, $full_load);
                    
                    if($shp != null) {
                        //laden des untergeordnete subshops
                        array_push($shops, $shp);
                        
                        if ($saved_shopid == $shp->id)    
                            $shp->selected = $savedshop_found = true;
                        else 
                            $shp->selected = false;
                    }
                    
                } else {                
                    //laden aller netzwerke wie amazon oder ebay    
                    
                    require_once ATKP_PLUGIN_DIR.'/includes/shopproviders/atkp_shop_provider_base.php';
                    
                    $myprovider = atkp_shop_provider_base::retrieve_provider($webservice);
                    
                    if(isset($myprovider)) {      
                        $subshops = $myprovider->get_shops($prd->ID);
                                                          
                        foreach ($subshops  as $subshop ) { 
                            if(!$subshop->enabled)
                                continue;
                            
                            $shp = atkp_shop::load($subshop->shopid, $full_load);
                            
                            if($shp != null) {
                                if ($saved_shopid == $shp->id)    
                                    $shp->selected = $savedshop_found = true;
                                else
                                    $shp->selected = false;
                  
                                array_push($shops, $shp);
                            }
                        }
                    }       
                }
             }
            
            //kompatibilitätsmodus
            if(!$savedshop_found) {                
                foreach($shops as $shp) {
                    
                    if($shp->oldid == $saved_shopid) {
                        
                        $shp->selected = $savedshop_found = true;
                        break;   
                    }
                }
            }
            
            //if(!$savedshop_found && $saved_shopid != '') {
            //    $shp = atkp_shop::load($saved_shopid);
            //    if($shp != null)
            //        array_push($shops, $shp);
            //}            
            
            //lädt alle verfügbaren shops inkl subshops nach neuem muster und ermittelt den aktuell gespeicherten shop aufgrund der id (egal ob alter subshop oder neuer)
            
            return $shops;
        }

        public static function load($post_id, $full_load = true) {
    
            if($post_id == null || $post_id == '')
                throw new Exception( 'post_id is empty');
    
            require_once ATKP_PLUGIN_DIR.'/includes/shopproviders/atkp_shop_provider_base.php';
    
                    
            //ermittelt entweder den neuen subshop (postid) oder den alten subshop und stellt alle notwendigen informationen zur verfügung
         
            //alte shopids sind mit '_' getrennt -> postid_programid_shopid
            $subshopids = explode('_', $post_id);
            
            $shopid = $subshopids[0];
         
            $shop = get_post( $shopid ); 
    
            if(!isset($shop) || $shop == null)
                throw new Exception( 'shop not found: '.$post_id);
            if($shop->post_type != ATKP_SHOP_POSTTYPE)
                 throw new Exception('invalid shop post_type: '.$shop->post_type. ', $post_id: '.$post_id);
            
            $shp = new atkp_shop();
            $shp->id = $post_id; 
            
            $webservice = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_access_webservice');
            $shp->holdontop =  ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_holdshopontop');
                
            if($webservice == ATKP_SUBSHOPTYPE) {
                $parentid = wp_get_post_parent_id($shopid);
                               
                $parentshop = get_post($parentid); 
                $webservice = ATKPTools::get_post_setting($parentid, ATKP_SHOP_POSTTYPE.'_access_webservice');
                
                //neuer subshop
                $shp->title = $shop->post_title .' ('. $parentshop->post_title.', '.$shop->ID.')';     
                
                $myprovider = atkp_shop_provider_base::retrieve_provider($webservice);
                    
                $shp->provider = $myprovider;
                $shp->provider->load_basicsettings($parentid); 
                
                $shp->settingid = $parentid;
                $shp->webservice = $webservice;
                
                $shp->programid = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_programid');
                $shp->shopid = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_shopid');
                $shp->oldid = $parentid.'_'.$shp->programid.'_'.$shp->shopid;
                
                if($full_load) {
                    
                    $shp->displayshoplogo = (bool)ATKPTools::get_post_setting( $parentid, ATKP_SHOP_POSTTYPE.'_displayshoplogo');  
                    $shp->enablepricecomparison = (bool)ATKPTools::get_post_setting( $parentid, ATKP_SHOP_POSTTYPE.'_enableofferload');   
                    $shp->buyat = ATKPTools::get_post_setting( $parentid, ATKP_SHOP_POSTTYPE.'_text_buyat');   
                    $shp->addtocart = ATKPTools::get_post_setting( $parentid, ATKP_SHOP_POSTTYPE.'_text_addtocart');
                    $shp->tooltip = ATKPTools::get_post_setting( $parentid, ATKP_SHOP_POSTTYPE.'_text_tooltip'); 
                    
                    $shp->customtitle = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_customtitle');
                    $shp->customsmalllogourl = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_customsmalllogourl');
                    $shp->customlogourl = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_customlogourl');
                    $shp->customfield1 = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_customfield1');
                    $shp->customfield2 = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_customfield2');
                    $shp->customfield3 = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_customfield3');
                                        
                    $subshops=  ATKPTools::get_post_setting( $shop->ID, ATKP_SHOP_POSTTYPE.'_default_shops');
         
                    if(is_array($subshops)) {
                        $subshop = $subshops[0];
                    
                        $shp->outputtitle = $subshop->title;
                        $shp->logourl = $subshop->logourl;
                        $shp->smalllogourl = $subshop->smalllogourl;                    
                    }
                }
                    
            } else {
                $myprovider = atkp_shop_provider_base::retrieve_provider($webservice);
                
                if(isset($myprovider)) {       
                    
                    $subshops = $myprovider->get_shops($shopid, true);
                    $found = false;                              
                    foreach ($subshops  as $subshop ) { 
                        if($subshop->shopid == $post_id) {
                            //dann ist es ein "alter" subshop
                            $found = true;                   
                            if(!is_numeric($shp->id))
                                $shp->title = $subshop->title .' ('.$shop->post_title.', '.$shop->ID.')';    
                            else
                                $shp->title = $shop->post_title .' ('.$shop->ID.')';  
                                
                            $shp->provider = $myprovider;
                            $shp->provider->load_basicsettings($shop->ID); 
                                
                            $shp->settingid = $shop->ID;
                            $shp->webservice = $webservice;
                            
                            $shp->programid = $subshop->programid;
                            
                            
                            
                            if($subshop->shopid2 != '')
                                $shp->shopid = $subshop->shopid2;
                            else
                                $shp->shopid = $subshop->shopid;
                                
                            $shp->oldid = $subshop->shopid;
                                                       
                            if($full_load) {
                            
                                $shp->displayshoplogo = (bool) ATKPTools::get_post_setting( $shop->ID, ATKP_SHOP_POSTTYPE.'_displayshoplogo');  
                                $shp->enablepricecomparison = (bool)ATKPTools::get_post_setting( $shop->ID, ATKP_SHOP_POSTTYPE.'_enableofferload');   
                                $shp->buyat = ATKPTools::get_post_setting( $shop->ID, ATKP_SHOP_POSTTYPE.'_text_buyat');   
                                $shp->addtocart = ATKPTools::get_post_setting( $shop->ID, ATKP_SHOP_POSTTYPE.'_text_addtocart'); 
                                $shp->tooltip = ATKPTools::get_post_setting( $shop->ID, ATKP_SHOP_POSTTYPE.'_text_tooltip'); 
                                                                    
                                $shp->customtitle = $subshop->customtitle;
                                $shp->customsmalllogourl = $subshop->customsmalllogourl;
                                $shp->customlogourl = $subshop->customlogourl;
                                $shp->customfield1 = $subshop->customfield1;
                                $shp->customfield2 = $subshop->customfield2;
                                $shp->customfield3 = $subshop->customfield3;
                                
                                
                                $shp->outputtitle = $subshop->title;
                                $shp->logourl =  $subshop->logourl;
                                $shp->smalllogourl = $subshop->smalllogourl;
                                
                                if(!is_numeric($shp->id)) {
                                    
                                    $args = array(
                            			'post_type' => array( ATKP_SHOP_POSTTYPE ),
                            			'post_parent' => $post_id,
                            			'post_status' => array('publish', 'draft'),
                                    	'meta_query' => array(
                                    		'relation' => 'AND',
                                    		array(
                                    			'key'     => ATKP_SHOP_POSTTYPE.'_shopid',
                                    			'value'   => $shp->shopid,
                                    			'compare' => '='
                                    		),
                                    		array(
                                    			'key'     => ATKP_SHOP_POSTTYPE.'_programid',
                                    			'value'   => $shp->programid,
                                    			'compare' => '='
                                    		)
                                    	)
                            		);
                            		
                            		$the_query = new WP_Query( $args );
                            		
                            		if ( $the_query->have_posts() ) {   
                            		    $the_query->the_post();
                                        $post_id = $the_query->post->ID;
                                        
                                        $shp->id = $post_id; 
                                        
                                        $shp->customtitle = ATKPTools::get_post_setting($shp->id, ATKP_SHOP_POSTTYPE.'_customtitle');
                                        $shp->customsmalllogourl = ATKPTools::get_post_setting($shp->id, ATKP_SHOP_POSTTYPE.'_customsmalllogourl');
                                        $shp->customlogourl = ATKPTools::get_post_setting($shp->id, ATKP_SHOP_POSTTYPE.'_customlogourl');
                                        $shp->customfield1 = ATKPTools::get_post_setting($shp->id, ATKP_SHOP_POSTTYPE.'_customfield1');
                                        $shp->customfield2 = ATKPTools::get_post_setting($shp->id, ATKP_SHOP_POSTTYPE.'_customfield2');
                                        $shp->customfield3 = ATKPTools::get_post_setting($shp->id, ATKP_SHOP_POSTTYPE.'_customfield3');
                                        
                            		}      
                                } else {
                                    $shp->customtitle = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_customtitle');
                                    $shp->customsmalllogourl = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_customsmalllogourl');
                                    $shp->customlogourl = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_customlogourl');
                                    $shp->customfield1 = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_customfield1');
                                    $shp->customfield2 = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_customfield2');
                                    $shp->customfield3 = ATKPTools::get_post_setting($shop->ID, ATKP_SHOP_POSTTYPE.'_customfield3');
                                }
                            }
                            
                            break;
                        }
                    }
                    if(!$found)
                        throw new Exception( 'subshop not found: '.$post_id);
                } 
            }
            
                
            
            //$shp->buyattext = ATKPTools::get_post_setting( $shopid, ATKP_SHOP_POSTTYPE.'_text_buyat');
            //$shp->addtocarttext =ATKPTools::get_post_setting( $shopid, ATKP_SHOP_POSTTYPE.'_text_addtocart');
            
            
            return $shp;
        }

        public function __get($member) {
            if (isset($this->data[$member])) {
                return $this->data[$member];
            }
        }

        public function __set($member, $value) {            
           // if (isset($this->data[$member])) {
                $this->data[$member] = $value;
            //}
        }
    }
    
    
?>