<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class atkp_export_provider_woo extends atkp_export_provider_base
{ 
    //das ist die basis klasse für alle shop provider
    
    public function __construct() 
    { 
        
    } 
        
    public function check() {
         try {
            $apikey = get_option(ATKP_PLUGIN_PREFIX.'_woo_access_key');
			$apisecretkey = get_option(ATKP_PLUGIN_PREFIX.'_woo_access_secret_key');
            
            $message = '';
            if ($apikey != '' && $apisecretkey != '') {
                
                    if (!class_exists('WC_API_Client'))
                        require_once  ATKP_PLUGIN_DIR.'/lib/woocommerce/woocommerce-api.php';
                    
                    $options = array(
                        'ssl_verify'      => false,
                    );
                    
                    $home = get_option('home');
                    $api = new WC_API_Client($home, $apikey, $apisecretkey, $options );
                                
                    $categories = $api->products->get_categories();
                  
                    if(ATKPLog::$logenabled) {
                        ATKPLog::LogDebug('*** check_configuration woo export ***');
                        ATKPLog::LogDebug(serialize($categories));
                        ATKPLog::LogDebug('*** check_configuration woo export ***');
                    }
            } else {
                //wenn zugangscodes gelöscht werden muss message auch geleert werden
                 $message = 'Credientials are empty';  
            }
            return $message;
        } catch(Exception $e) {
	        return $e->getMessage();
	    }
    }
    
    private $helper = null;
    
    public function checklogon() {
        if (!class_exists('WC_API_Client'))
                require_once  ATKP_PLUGIN_DIR.'/lib/woocommerce/woocommerce-api.php';
        
        $apikey = get_option(ATKP_PLUGIN_PREFIX.'_woo_access_key');
		$apisecretkey = get_option(ATKP_PLUGIN_PREFIX.'_woo_access_secret_key');
        
        
        $options = array(
            'ssl_verify'      => false,
        );
        
        $home = get_option('home');
        $this->helper = new WC_API_Client($home, $apikey, $apisecretkey, $options );
        
        if($apikey == '' || $apisecretkey =='') {
            throw new Exception('Credientials are empty');   
        }
        
    }
    
    public function update_key_product($wooproductid) {     
        $eanfield = get_option(ATKP_PLUGIN_PREFIX.'_woo_ean_field', '');
        $keytype = get_option(ATKP_PLUGIN_PREFIX.'_woo_keytype', 'ean');
        
        if($eanfield == '' || $eanfield == 'sku') {
            $ean = ATKPTools::get_post_setting( $wooproductid, '_sku');
        }
        else
            $ean = ATKPTools::get_post_setting( $wooproductid, $eanfield);
        
        if($ean == null || $ean== '')
            return 'ean is null';
        
        $post_id = null;
        $exists = true;
        
        //wenn keytype gleich id dann ist die EAN in den Daten
                
        if($keytype == 'ean') {
            //wenn keytype gleich ean dann sind die produkte mit ID des produktes gespeichert
            $exists = atkp_product::exists($ean);
            
            if($exists)
                $post_id = $ean;
        }
        else if($keytype == 'id')
            $post_id =  atkp_product::idbyean($ean);
         
        
        //wenn affiliate-toolkit produkt nicht gefunden wurde dann überspringen
        if($post_id == null || !$exists) { 
            return 'AT Produkt not found: '.$ean;            
        }       
        
        $newvalue = null;
        if($keytype == 'ean') {
            //setze die ean in das woo produkt
            $ean =ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_ean');
            
            if($ean == null ||$ean== '')
                return 'ean is empty';
            
            $eans = explode(',', $ean);
                        
            $newvalue = $eans[0];
            
        } else if($keytype == 'id')  {
            //setze die id in das woo produkt
            $newvalue = $post_id;
        }   
        
        if($newvalue != null) {
            if($eanfield == '' || $eanfield == 'sku') { 
                ATKPTools::set_post_setting( $wooproductid, '_sku', $newvalue);
            } else {
                ATKPTools::set_post_setting( $wooproductid, $eanfield, $newvalue);
            }
        }
    }
    
    public function import_product($wooproductid) {
        $WC_Product = wc_get_product( $wooproductid);
        
        if($WC_Product == null) 
            return;
        
        $eanfield = get_option(ATKP_PLUGIN_PREFIX.'_woo_ean_field', '');
        $keytype = get_option(ATKP_PLUGIN_PREFIX.'_woo_keytype', 'ean');
        
        if($eanfield == '' || $eanfield == 'sku')
            $ean = $WC_Product->get_sku();    
        else
            $ean = ATKPTools::get_post_setting( $wooproductid, $eanfield);
        
        if($ean == null || $ean== '')
            return;
        
        if(($keytype == 'ean') && strlen($ean) < 8)
            return;
        
        $post_id = null;
        $exists = true;
        
        if($keytype == 'id') {
            $exists = atkp_product::exists($ean);
            
            if($exists)
                $post_id = $ean;
        }
        else
            $post_id =  atkp_product::idbyean($ean);
            
        $created = false;
        
        if($post_id == null) {
            $post_id = ATKPTools::create_product($WC_Product->get_title(),'','', 'woo');
            $created = true;
            
            if(!$exists && $eanfield != '' && $eanfield != 'sku')
                ATKPTools::set_post_setting( $wooproductid, $eanfield, $post_id);
        }
        
        if($keytype == 'ean')
            ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_ean', $ean);    

        $shopid = ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_shopid');
                       
        //schreibe die Produktinfos von woo nur rein wenn manuelles produkt         
        if($shopid == '') {
            ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_title', $WC_Product->get_title());   
            ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_producturl', $WC_Product->get_permalink()); 
            
            if(get_option(ATKP_PLUGIN_PREFIX.'_woo_exportdescription', 0))
                ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_description', $WC_Product->get_post_data()->post_content);
            if(get_option(ATKP_PLUGIN_PREFIX.'_woo_exportshortdescription', 0))
    		    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_features', $WC_Product->get_post_data()->post_excerpt);
            
            $imageid = $WC_Product->get_image_id();
            if($imageid != 0) {
                ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_smallimageurl', wp_get_attachment_image_src($imageid, 'thumbnail')[0]);
                ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_mediumimageurl', wp_get_attachment_image_src($imageid, 'medium')[0]);
                ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_largeimageurl', wp_get_attachment_image_src($imageid, 'large')[0]);
            }        
        }
        
        if($created) {
            $cronjob = new atkp_cronjob(array());
            $cronjob->update_product($post_id, 0, 0, 0, 0, 0, 1, false);
            
        }
            
        //wir sind so mutig.. wir schreiben gleich mal die preise ins woo zurück
        $this->export_product($post_id);
        
        return $post_id;
    }
    
    public function export_product($productid) {
        
        $product = atkp_product::load($productid);
        
        $keytype = get_option(ATKP_PLUGIN_PREFIX.'_woo_keytype', 'ean');
        
        $woo_product = null;
        $eans = array();
        
        if($keytype == 'id') {
            array_push($eans, $productid);
        } else {
            $eans = explode(',', $product->ean);
        
            if($product->ean == '')
                return;
        }
        
        if(ATKPLog::$logenabled) {
            ATKPLog::LogDebug('export_product woo productid: '. $productid );
            ATKPLog::LogDebug('export_product woo ean exists: '. (!($product->ean == '' || count($eans) == 0)) );
        }
        
              
        if(count($eans) == 0)
            return;
            
        $eanfield = get_option(ATKP_PLUGIN_PREFIX.'_woo_ean_field', '');        
        
        foreach($eans as $ean) {
            try {
                
                if($eanfield == '' || $eanfield == 'sku') {                    
                    $woo_product = $this->helper->products->get_by_sku($ean);
                } else {
                    $args = array(
            			'post_type' => array( 'product' ),
            			'meta_key'     => $eanfield,
                    	'meta_value'   => $ean,
                    	'meta_compare' => '=',		
            		);
            		
            		$the_query = new WP_Query( $args );
            		
            		while ( $the_query->have_posts() ) {   
            		    $the_query->the_post();
			            
			            $woo_product = $this->helper->products->get($the_query->post->ID);            
            		}                    
                }
                
                
            } catch ( WC_API_Client_Exception $e ) { 
            
                //error code ungleich not found dann fehler schmeißen
                if($e->getCode() != 404)
                    throw $e;
            }
        }       
        
        $mode = get_option(ATKP_PLUGIN_PREFIX.'_woo_mode', '');
            
        
        $result = null;
        if($mode != null) {
            if($woo_product == null) {
                //create   
                if( $mode == 'at-woo') {
                    $arr = $this->prepareProductArray($product, $eans[0], true);
                    
                    $result = $this->helper->products->create($arr ) ;
                    
                    if(isset($result->product->id)) {
                        if($eanfield != '' && $eanfield != 'sku')
                            ATKPTools::set_post_setting( $result->product->id, $eanfield, $eans[0]);
                        
                        $this->updateafter($result->product->id, $product, $arr);

                    }
                }
            } else {
                //update
                $arr = $this->prepareProductArray($product, $eans[0], false);
                
                $result = $this->helper->products->update( 
                    $woo_product->product->id, 
                    $arr) ;
                    
                $this->updateafter( $woo_product->product->id, $product, $arr);
                
            }
        }
        
        if(ATKPLog::$logenabled) {
            ATKPLog::LogDebug('*** export_product woo export ***');
            ATKPLog::LogDebug('created: '. ($woo_product == null) );
            ATKPLog::LogDebug('mode: '. serialize($mode) );
            
            ATKPLog::LogDebug(serialize($result));
            ATKPLog::LogDebug('*** export_product woo export ***');
        }
        
        
    }
    
    private function updateafter($productid, $product, $arr) {
        //manuelles update der produkturl weil diese falsch encoded wird
        if(isset($arr['product_url']) && $arr['product_url'] != '')
            ATKPTools::set_post_setting( $productid, '_product_url', $arr['product_url']);
         
        $importimagemode = get_option(ATKP_PLUGIN_PREFIX.'_woo_importimage',1);
        $importgallerymode = get_option(ATKP_PLUGIN_PREFIX.'_woo_imagegallery', 0);
        
        switch($importimagemode) {
            case 0:
                //mache nichts
                break;
            case 1:
                //importiert das bild in die bibliothek
                
                $imageurl = $product->largeimageurl;
                if($imageurl == '')
                    $imageurl = $product->mediumimageurl;
                if($imageurl == '')
                    $imageurl = $product->smallimageurl;
                            
                if($imageurl != '' && $product->title != '') {
                    ATKPTools::set_featured_image($imageurl, $product->title, $productid);
                }
                break;
            case 2:
                //verlinkt das bild mit dem externen bild
                require_once ATKP_PLUGIN_DIR.'/includes/atkp_external_featuredimage.php';                        
                $featuredimage = new atkp_external_featuredimage();
                $featuredimage->set_external_postimage($productid);
                break;
            default:
                throw new exception('unknown imagemode: '.$importimagemode);                
        }
        
        switch($importgallerymode) {
            case 0:
            case 1:
                //entweder nichts oder external image
                break;
            case 2:
                //importiert die gallerie
                $oldlist_id = get_post_meta($productid,'_product_image_gallery', true);
               
               $oldids = array();
               $newids = array();
                if(isset($oldlist_id) && $oldlist_id != '') {
                    $oldids = array_unique (explode(',', $oldlist_id));
                    /* 
                    foreach($oldids as $oldid)
                        ATKPTools::delete_image_attachment($oldid);
                        */
                }
                
                $list_id = '';
                $idx = 2;
                if(is_array($product->images)) {
                    foreach($product->images as $image) {
                        $image_url = $image->largeimageurl;
                        if($image_url == '')
                            $image_url = $image->mediumimageurl;
                        if($image_url == '')
                            $image_url = $image->smallimageurl;
                            
                        $oldattachid = '';
                        if(isset($oldids[$idx-2])) {
                            $oldattachid =$oldids[$idx-2]; 
                        }
                        $attachmentid = ATKPTools::upload_image( $image_url, $product->title, $productid, $idx);
                        
                        array_push($newids, $attachmentid);
                        $idx++;
                        
                        if($list_id == '')
                            $list_id .= $attachmentid;
                        else
                            $list_id .= ','.$attachmentid;
                    }
                }
                
                //lösche die alten anhänge
                foreach($oldids as $oldid) {
                    if(!in_array($oldid, $newids)) {
                        ATKPTools::delete_image_attachment($oldid);
                    }
                    
                }
                //delete $oldids[0]
                
                //echo $list_id; exit;
                
                //echo serialize($oldids);
                //echo serialize($list_id);
               //exit;
                
                //sollte die bilder mit der galerie verknüpfen:
                //$list_id = "444,555";
                update_post_meta($productid,'_product_image_gallery',$list_id);  
                break;
            default:
                throw new exception('unknown $importgallerymode: '.$importgallerymode);                
            
        }
        
 
        
        
    }
    
    private function sortPrice($a, $b)
    {
        if ($a->pricefloat == $b->pricefloat) {
            return 0;
        }
        return ($a->pricefloat < $b->pricefloat) ? -1 : 1;
    }
    
    private function prepareProductArray($product, $ean, $creating) {
        $mode = get_option(ATKP_PLUGIN_PREFIX.'_woo_mode', '');
        //$importimagemode = get_option(ATKP_PLUGIN_PREFIX.'_woo_importimage',1);
        $array = array();
        
        if( $mode == 'at-woo') {
            
            
        //} else {
            $array = array(  
                'title' => $product->title, 
    			'type' => 'external' );   
    				
    		if(get_option(ATKP_PLUGIN_PREFIX.'_woo_exportdescription', 0))
    		    $array['description'] = $product->description; 
    		if(get_option(ATKP_PLUGIN_PREFIX.'_woo_exportshortdescription', 0))
    		    $array['short_description'] = $product->features; 
		
    		$imagesarray = array();
    		
    		//mainimage
    		if($product->largeimageurl != '')
        		array_push($imagesarray, array(
                        'src' => $product->largeimageurl, 
                        'position' => 0, 
                        'title' => $product->title
                    )
                );
    					
    		//if($creating && $importimagemode == 1)
    		//    $array['images'] = $imagesarray;
    		
		}
		
		$eanfield = get_option(ATKP_PLUGIN_PREFIX.'_woo_ean_field', '');
            
        if($eanfield == '' || $eanfield == 'sku')
            $array['sku'] = $ean;    
		    
		$myproduct = $product;
		    
		if(!is_array($myproduct->offers))
            $offers = array();
        else
            $offers = $myproduct->offers;
        
        if($myproduct->shopid != '') {
            $offer = new atkp_product_offer();
            $offer->link = $myproduct->producturl;
            $offer->price = $myproduct->saleprice;
            $offer->pricefloat =  $myproduct->salepricefloat;
                
            array_push($offers, $offer);
        }
            
        usort($offers, array(&$this, "sortPrice"));
        
        $offercount = 0;
        $minprice = null;
        $maxprice = null;
        foreach($offers as $offer) {
            if($offer->pricefloat == (float)0 || $offer->hideoffer)
                continue;
            
            if($offercount == 0)
                $minprice = $offer;
            
            $maxprice = $offer;
                
            $offercount++;
        }
        
        //wenn keine angebote gefunden wurden, fallback auf hauptprodukt
        if($minprice == null)
            $minprice = $offer;
        if($maxprice == null)
            $maxprice = $offer;
              
        		
		switch(get_option(ATKP_PLUGIN_PREFIX.'_woo_targeturl', '')) {
		    case 'min_price':
		        $array['product_url'] = $minprice->link;
		        break;
		    case 'max_price':
		        $array['product_url'] = $maxprice->link;
		        break;
		    default:
		        $array['product_url'] = $product->producturl; 
    		    break;
		}
		
		switch(get_option(ATKP_PLUGIN_PREFIX.'_woo_price_field', '')) {
		    case 'min_price':
		        $array['regular_price'] = $minprice->pricefloat;
		        break;
		    case 'max_price':
		        $array['regular_price'] = $maxprice->pricefloat;
		        break;
		    default:
		        if($product->listprice != '' && $product->listpricefloat != 0 && $myproduct->listpricefloat > $myproduct->salepricefloat) {
        	        $array['regular_price'] = $product->listpricefloat; 
        	    } else {
        	        $array['regular_price'] = $product->salepricefloat; 
        	    }
    		    break;
		}
		
		switch(get_option(ATKP_PLUGIN_PREFIX.'_woo_saleprice_field', '')) {
		    case 'min_price':
		        $array['sale_price'] = $minprice->pricefloat;
		        break;
		    case 'max_price':
		        $array['sale_price'] = $maxprice->pricefloat;
		        break;
		    default:
		        if($product->listprice != '' && $product->listpricefloat != 0 && $myproduct->listpricefloat > $myproduct->salepricefloat) {
        			$array['sale_price'] = $product->salepricefloat;
        	    } else {
        			$array['sale_price'] = 0;	        
        	    }
    		    break;
		}
		
		if(isset($array['sale_price']) && $array['sale_price'] == 0)
		    $array['sale_price'] = '';
		if(isset($array['regular_price']) && $array['regular_price'] == 0)
		    $array['regular_price'] = '';
	
	    
	
				
	    return $array;
    }
    
}