<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_cronjob
    {        
        /**
         * Construct the plugin object
         */
        public function __construct($pluginbase)
        {
            add_filter( 'cron_schedules', array($this, 'add_new_intervals'));
            
            add_action(ATKP_EVENT, array(&$this, 'do_this_hourly'));
            add_action(ATKP_CHECK, array(&$this, 'do_this_weekly'));            
            add_action(ATKP_CSVIMPORT, array(&$this, 'do_this_daily'));
            
            register_activation_hook(__FILE__, 'my_activation');
            register_deactivation_hook(__FILE__, 'my_deactivation');
        }
        
        function add_new_intervals($schedules) 
        {
        	// add weekly and monthly intervals
        	$schedules[ATKP_PLUGIN_PREFIX.'_60'] = array(
        		'interval' => 60 * 60,
        		'display' => __('1 Hour', ATKP_PLUGIN_PREFIX)
        	);
        
        	$schedules[ATKP_PLUGIN_PREFIX.'_360'] = array(
        		'interval' => 360 * 60,
        		'display' => __('6 Hours', ATKP_PLUGIN_PREFIX)
        	);
        	
        	$schedules[ATKP_PLUGIN_PREFIX.'_720'] = array(
        		'interval' => 720 * 60,
        		'display' => __('12 Hours', ATKP_PLUGIN_PREFIX)
        	);        	
        	
        	$schedules[ATKP_PLUGIN_PREFIX.'_1440'] = array(
        		'interval' => 1440 * 60,
        		'display' => __('1 Day', ATKP_PLUGIN_PREFIX)
        	);
        	
        	$schedules[ATKP_PLUGIN_PREFIX.'_4320'] = array(
        		'interval' => 4320 * 60,
        		'display' => __('3 Days', ATKP_PLUGIN_PREFIX)
        	);
        	
        	$schedules[ATKP_PLUGIN_PREFIX.'_10080'] = array(
        		'interval' => 10080 * 60,
        		'display' => __('1 Week', ATKP_PLUGIN_PREFIX)
        	);
        
        	return $schedules;
        }

        
        public function my_activation() {
            $crontype = get_option(ATKP_PLUGIN_PREFIX.'_crontype', 'wpcron');
            
            switch($crontype) {
                default:
                case 'wpcron':
                    $inittime = strtotime('00:00:00');
                    
                    $key = ATKP_PLUGIN_PREFIX.'_'.(ATKPSettings::$access_cache_duration);
                    wp_schedule_event( $inittime, $key, ATKP_EVENT);
                    
                    $key = ATKP_PLUGIN_PREFIX.'_'.(ATKPSettings::$notification_interval);
                    wp_schedule_event( $inittime, $key, ATKP_CHECK);
                    
                    $key = ATKP_PLUGIN_PREFIX.'_'.(ATKPSettings::$access_csv_intervall);
                    wp_schedule_event( $inittime, $key, ATKP_CSVIMPORT);
                    break;
                case 'external':
                case 'externaloutput':
                    //external cronjob? nothing todo...
                    break;                
            }  
        }
        
        public function my_deactivation() {
           
	        wp_clear_scheduled_hook(ATKP_EVENT);
	        wp_clear_scheduled_hook(ATKP_CHECK);
	        wp_clear_scheduled_hook(ATKP_CSVIMPORT);
        }
        
        public function my_update() {
            $this->my_deactivation();
            $this->my_activation();
        }

        public function do_this_weekly() {
            try {
    	        if(ATKPLog::$logenabled) {
                    ATKPLog::LogDebug('*** cronjob for datacheck started ***');
                }  
                    
                //prüfe auf shop-warnung
                //prüfe auf listen und produktwarnung
                //prüfe ob dynamische listen leer sind
                
                if(!ATKPSettings::$check_enabled)
                    return;
                    
                $recipient = ATKPSettings::$email_recipient;
                
                if($recipient == '') 
                    $recipient = get_bloginfo('admin_email');
                
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product.php';
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_list.php';
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_shop.php';
                
                $errors = array();
            	
            	$posts_found = get_posts(array(
            	'posts_per_page'	=> -1,
            	'post_status'      =>  array('publish','draft'),
            	'post_type'			=> ATKP_SHOP_POSTTYPE	
                ));
            
                foreach ($posts_found as $prdpost) {
                    try {
                        $access_test = ATKPTools::get_post_setting($prdpost->ID, ATKP_SHOP_POSTTYPE.'_access_message');
                                    
                         if($access_test != '') 
                            array_push( $errors, 'Shop '.$prdpost->post_title . ' ('. $prdpost->ID. '): '."\n" .$access_test);
               
                    } catch(Exception $e) {
                        array_push( $errors, 'Shop '.$prdpost->post_title . ' ('. $prdpost->ID. '): '."\n".'Exception '. $e->getMessage());
                    }
                }
            	
            	
            	$posts_found = get_posts(array(
            	'posts_per_page'	=> -1,
            	'post_status'      => array('draft', 'publish'),
            	'post_type'			=> ATKP_PRODUCT_POSTTYPE	
                ));
                
                foreach ($posts_found as $prdpost) {
                    try {
                    
                    $message = ATKPTools::get_post_setting($prdpost->ID, ATKP_PRODUCT_POSTTYPE.'_message', true );
                    
                    if($message != '') 
                        array_push( $errors, 'Product '.$prdpost->post_title . ' ('. $prdpost->ID. '): '."\n" .$message);
                        
                    } catch(Exception $e) {
                        array_push( $errors, 'Product '.$prdpost->post_title . ' ('. $prdpost->ID. '): '."\n".'Exception '. $e->getMessage());
                    }
                }
                        
                $posts_found = get_posts(array(
            	'posts_per_page'	=> -1,
            	'post_status'      => array('publish','draft'),
            	'post_type'			=> ATKP_LIST_POSTTYPE	
                ));
            
                foreach ($posts_found as $prdpost) {
                    try {
                        $message = ATKPTools::get_post_setting($prdpost->ID, ATKP_LIST_POSTTYPE.'_message', true );
                        
                        if($message != '') 
                            array_push( $errors, 'List '.$prdpost->post_title . ' ('. $prdpost->ID. '): '."\n" .$message);
               
                    } catch(Exception $e) {
                        array_push( $errors, 'List '.$prdpost->post_title . ' ('. $prdpost->ID. '): '."\n".'Exception '. $e->getMessage());
                    }
                }
        
                //TODO: einschalten
                //TODO: empfänger
                //TODO: Betreff            
                if(sizeof($errors) > 0) 
                    wp_mail($recipient, __('Affiliate Toolkit Report', ATKP_PLUGIN_PREFIX), __('Following messages are currently in your records: ', ATKP_PLUGIN_PREFIX) . "\n" . implode("\n\n", $errors) );
                
                ATKPLog::LogDebug('mail sent: '.(sizeof($errors) > 0));
                
                if(ATKPLog::$logenabled) {
                    ATKPLog::LogDebug('*** cronjob for datacheck finished ***');
                }        
    	        
	        } catch (Exception $e) {
                ATKPLog::LogError($e->getMessage());
            }
        }

        public function do_this_hourly() {
	        // do something every hour
	        try {
    	        if(ATKPLog::$logenabled) {
                    ATKPLog::LogDebug('*** cronjob started ***');
                }        
    	        
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product.php';
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_list.php';
                
                $errors = array();
                
                 $posts_found = get_posts(array(
                	'posts_per_page'	=> -1,
                	'post_status'      => array('draft', 'publish'),
                	'post_type'			=> ATKP_PRODUCT_POSTTYPE	
                ));
                
                foreach ($posts_found as $prdpost) {
                    try {
                        $this->update_product($prdpost->ID, false,false,false, false,false,false, true);
                        //Delay damit nicht zu viele Request hintereinander kommen
                        sleep(1);
                    } catch(Exception $e) {
                        array_push( $errors, 'Product '. $prdpost->ID. ': '. $e->getMessage());
                        ATKPLog::LogError($e->getMessage());
                    }
                }
                
    	        if(ATKPLog::$logenabled) {
                    ATKPLog::LogDebug('*** products updated ***');
                }   
                
                $posts_found = get_posts(array(
                	'posts_per_page'	=> -1,
                	'post_status'      => array('publish','draft'),
                	'post_type'			=> ATKP_LIST_POSTTYPE	
                ));
                foreach ($posts_found as $prdpost) {
                    try {
                        $this->update_list($prdpost->ID, true);
                        //Delay damit nicht zu viele Request hintereinander kommen
                        sleep(1);
                    } catch(Exception $e) {
                        array_push( $errors, 'List '. $prdpost->ID. ': '. $e->getMessage());
                        ATKPLog::LogError($e->getMessage());
                    }
                }
                
                if(ATKPLog::$logenabled) {
                    ATKPLog::LogDebug('*** lists updated ***');
                } 
                
                if(ATKPLog::$logenabled) {
                    ATKPLog::LogDebug('*** cronjob finished ***');
                }        
    	        
	        } catch (Exception $e) {
                ATKPLog::LogError($e->getMessage());
            }
        }
    
        public function do_this_daily() {
            try {
    	        if(ATKPLog::$logenabled) {
                    ATKPLog::LogDebug('*** csv cronjob for datacheck started ***');
                }  
                
                $posts_found = get_posts(array(
                	'posts_per_page'	=> -1,
                	'post_status'      => array('publish','draft'),
                	'post_type'			=> ATKP_SHOP_POSTTYPE,
                	'meta_query' => array(
                		array(
                			'key' => ATKP_SHOP_POSTTYPE.'_access_webservice',
                			'value' => '7',
                		))
                ));
                
                if(ATKPLog::$logenabled) {
                    ATKPLog::LogDebug('csv posts found: '. serialize($posts_found));
                }  
                
                //wenn csv feeds vorhanden sind, dann initialisiere den csv cronjob.
                if($posts_found && count($posts_found) > 0) {
                    require_once ATKP_PLUGIN_DIR.'/includes/shopproviders/atkp_shop_provider_base.php';
                    require_once ATKP_PLUGIN_DIR.'/includes/shopproviders/atkp_shop_provider_csv.php';
                    
                    foreach($posts_found as $csvimport) {
                        try {
                            if(ATKPLog::$logenabled) {
                                ATKPLog::LogDebug('import csv: '. $csvimport->ID);
                            }   
                            
                            $csvprovider = new atkp_shop_provider_csv(); 
                            $csvprovider->import_csv($csvimport->ID, true);
                            
                            unset($csvprovider);
                            
                            if(ATKPLog::$logenabled) {
                                ATKPLog::LogDebug('import finished csv: '. $csvimport->ID);
                            }  
                        } catch (Exception $e) {
                            ATKPLog::LogError('import csv error ('.$csvimport->ID.') '.$e->getMessage());
                        }
                    }
                }
                
                if(ATKPLog::$logenabled) {
                    ATKPLog::LogDebug('*** csv cronjob finished ***');
                }   
            } catch (Exception $e) {
                ATKPLog::LogError($e->getMessage());
            }
        }
    
    public function update_product($post_id, $refreshproductinfo, $refreshpriceinfo, $refreshreviewinfo, $refreshimages, $refreshproducturl, $refreshmoreoffers, $iscron = false) {
        if(ATKPLog::$logenabled) {
            ATKPLog::LogDebug('*** update_product ***');
            ATKPLog::LogDebug('$post_id: '. $post_id);
            ATKPLog::LogDebug('$iscron: '. $iscron);
        }

        $refreshreviewinforegulary= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshreviewinforegulary');
        $refreshpriceinforegulary= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshpriceinforegulary');
        $refreshproducturlregulary=	ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshproducturlregulary');        
        $refreshimagesregulary= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshimagesregulary');        
        $refreshmoreoffersregulary= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshmoreoffersregulary');
        $iswoocommerce = (bool)ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_iswoocommerce');
                
        require_once  ATKP_PLUGIN_DIR.'/includes/atkp_shop.php';
        require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product.php';
        
        
        $shopid =ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_shopid');          
        $asin=  ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_asin');
        $asintype=  ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_asintype');
        
        if($asintype == null || $asintype == '')
            $asintype = 'ASIN';
        
        $manufacturer= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_manufacturer');
        $author=    ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_author');
        $brand = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_brand');
        
        
        if($iscron) {
            if(!$refreshreviewinforegulary && !$refreshpriceinforegulary && !$refreshproducturlregulary && !$refreshimagesregulary && !$refreshmoreoffersregulary)
                return;
               
            if($refreshreviewinforegulary)
                $refreshreviewinfo = true;
            if($refreshpriceinforegulary) {
                $refreshpriceinfo = true;
            }            
            if($refreshproducturlregulary) {
                $refreshproducturl = true;
            }
            
            if($refreshimagesregulary) {
                $refreshimages = true;
            }
            
            if($refreshmoreoffersregulary)
                $refreshmoreoffers = true;
            
            $refreshproductinfo = false;
            
        } else {
            if($refreshproductinfo)
                $refreshproducturl = true;
            
        }
        
        if(ATKPLog::$logenabled) {
            ATKPLog::LogDebug('$refreshproductinfo: '. $refreshproductinfo);
            ATKPLog::LogDebug('$refreshpriceinfo: '. $refreshpriceinfo);
            ATKPLog::LogDebug('$refreshreviewinfo: '. $refreshreviewinfo);
            ATKPLog::LogDebug('$refreshimages: '. $refreshimages);
            ATKPLog::LogDebug('$refreshproducturl: '. $refreshproducturl);
            ATKPLog::LogDebug('$refreshmoreoffers: '. $refreshmoreoffers);
        }
                  
        if($shopid == '' || $asin == '') {
            //Änderung: manuelles produkt wird im normalen Zyklus aktualisiert weil es auch weitereangebote enthalten kann if(!$iscron) {
            ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_updatedon', current_time('timestamp'));
            ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_message', null);
            //}   
        } else {    
            
            $title = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_title');
            $description = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_description');
            
            $ean = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_ean');
            
            $isbn = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_isbn');
            $productgroup = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_productgroup');
            
            $addtocarturl = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_addtocarturl');;
            $producturl= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_producturl');
            $customerreviewurl =  ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_customerreviewsurl');
            
            
            $smallimageurl = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_smallimageurl');
            $mediumimageurl= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_mediumimageurl');
            $largeimageurl= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_largeimageurl');
            $numberofpages=  ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_numberofpages');
            $features= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_features');
            $imagesurl= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_imagesurl');
            $thumbimagesurl=  ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_thumbimagesurl');
            
            
            
            $isownreview= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_isownreview');
            $rating= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_rating');
            $reviewcount=    ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_reviewcount');
            $reviewsurl=  ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_reviewsurl');
            
            $listprice=  ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_listprice');
            $amountsaved= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_amountsaved');
            $percentagesaved= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_percentagesaved');
            $saleprice= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_saleprice');
            $availability= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_availability');
            $shipping = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_shipping');
            $isprime=  ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_isprime');   
            
            $listpricefloat = (float)ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_listpricefloat');
            $amountsavedfloat=(float)ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_amountsavedfloat');
            $salepricefloat =(float)ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_salepricefloat');
            $shippingfloat =(float)ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_shippingfloat');
            
            $enablepricecomparison = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_enablepricecomparison');
            
           if($refreshproductinfo == true || $refreshpriceinfo == true || $refreshreviewinfo == true || $refreshimages == true || $refreshproducturl == true) {
                $shop = atkp_shop::load($shopid);
                
                if($shop == null || $shop->provider == null) {   
                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_updatedon', current_time('timestamp'));
                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_message', 'shop not found');  
                    return;
                }
                
                $message = $shop->provider->checklogon($shop);
     
      
                //wenn die Extension nicht geladen ist, kann das Plugin nicht arbeiten
                //Wenn keine Einstellungen definiert wurden um Daten zu laden, keine Liste generieren
                if($message != '') {
                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_updatedon', current_time('timestamp'));
                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_message', 'Shop connection invalid: '.$message);
                }else {
                
                    try {
                        ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_updatedon', current_time('timestamp'));
                                            
                        $product = $shop->provider->retrieve_product($asin, $asintype);
                        
                         if(isset($product) && $product != null){
            
                                ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_asin_caption', $product->title);
                                    
                                if($refreshproductinfo == true) {
                                    $post_loaded = get_post($post_id); 
                                    if($post_loaded->post_title == '') {
                                        global $wpdb;
                                        $wpdb->update( $wpdb->posts, array( 'post_title' => $product->title, 'post_name' => sanitize_title($product->title)  ),  array( 'ID' => $post_id ) );
                                    }
                                    
                                    $title =  $product->title;
                                    $description = $product->description;
                                    $ean = $product->ean;
                                     
                                    
                                    
                                    $manufacturer = $product->manufacturer;  
                                    $author = $product->author;  
                                    $numberofpages = $product->numberofpages;  
                                    $features =  $product->features;
                    				
                    				$isbn = $product->isbn;
                                    $brand = $product->brand;
                                    $productgroup = $product->productgroup; 
                                    
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_title', $title);    
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_description', $description);
                                    
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_ean', $ean);    
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_isbn', $isbn);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_brand', $brand);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_productgroup', $productgroup);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_manufacturer', $manufacturer);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_author', $author);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_numberofpages', $numberofpages);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_features', $features);
                                    
                                } 
                                
                                if($refreshimages == true) {
                                    $images = $product->images;
                                    $smallimageurl = $product->smallimageurl;  
                                    $mediumimageurl = $product->mediumimageurl;  
                                    $largeimageurl = $product->largeimageurl;  
                                    
                                  
                                 
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_smallimageurl', $smallimageurl);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_mediumimageurl', $mediumimageurl);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_largeimageurl', $largeimageurl);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_images', $images);
                                }
                                
                                
                                if($refreshproducturl == true) {
                                    $producturl = $product->producturl;
                                    $addtocarturl = $product->addtocarturl;
                                    $customerreviewurl = $product->customerreviewurl;
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_producturl', $producturl);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_addtocarturl', $addtocarturl);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_customerreviewsurl', $customerreviewurl);
                                }
                         
                                if($refreshreviewinfo == true) {                               
                                    $rating = $product->rating;
                                    $reviewcount = $product->reviewcount;
                                    
                                    //ratings werden nur mehr überschrieben wenn welche vorhanden sind
                                    
                                    if($rating != '' && $rating != '0')                                
                                        ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_rating', $rating);
                                    if($reviewcount != '' && $reviewcount != '0')
                                        ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_reviewcount', $reviewcount);                                
                                }   
                                
                                if($refreshpriceinfo == true) {
                        
                                    $listprice = $product->listprice;
                                    $amountsaved =$product->amountsaved;
                                    $percentagesaved = $product->percentagesaved;
                                    $saleprice = $product->saleprice;
                                    $availability = $product->availability;
                                    $isprime = $product->isprime;
                                    
                                    $listpricefloat  =$product->listpricefloat;
                                    $amountsavedfloat=$product->amountsavedfloat;
                                    $salepricefloat =$product->salepricefloat;
                                    $shippingfloat =$product->shippingfloat;
                                    
                                    $shipping = $product->shipping;
                                    
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_listpricefloat', $listpricefloat);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_amountsavedfloat', $amountsavedfloat);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_salepricefloat', $salepricefloat);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_shippingfloat', $shippingfloat);
                                    
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_listprice', $listprice);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_amountsaved', $amountsaved);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_percentagesaved', $percentagesaved);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_saleprice', $saleprice);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_availability', $availability);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_shipping', $shipping);
                                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_isprime', $isprime);
                                    
                                    if($saleprice=='')
                                        throw new Exception(__('saleprice is empty', ATKP_PLUGIN_PREFIX));
                                    
                                    
                                }    
                                 
                        } else  {
                            ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_asin_caption', '');
                                    
                            throw new Exception(__('product not found: ', ATKP_PLUGIN_PREFIX).$asin);
                        }
                            
                            ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_message', null);
                        } catch(Exception $e) {
                            ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_message', 'Error: '.$e->getMessage());
                            
                            
                        }
                } 
            }
        }       
        
                
        do_action('atkp_product_updated', $post_id);
    }
    
    
    
    public function update_offers($product) {
        require_once  ATKP_PLUGIN_DIR.'/includes/atkp_shop.php';
        
        //wir holen uns alle shops + eans und legen dafür eine offer row an
        //danach gehen wir die daten pro shop durch...
     
        $filterarray =array();
        $alloffers = array();
        
        if($product->offers != null && $product->offers != '') {
            foreach($product->offers as $offer) {
                if($offer->type == 2) {
                    if(!array_key_exists ($offer->shopid, $filterarray) || !is_array($filterarray[$offer->shopid]))
                        $filterarray[$offer->shopid] = array();
                        
                    array_push($filterarray[$offer->shopid], $offer);     
                     array_push($alloffers, $offer);
                }
            }
        }
         
        $eans = array();
     
        if($product->ean != '') {
            $eansex = explode(',',$product->ean);   
            
            foreach($eansex as $ean) {
                $ean = trim($ean);
                if($ean != '') {
                    array_push($eans, $ean);
                    
                }
            }
        }
        
        //TODO: implement bulk offers check: bulk_offers_supported
        //aufgrund von unzähligen Shops pro affiliate-netzwerk ist es unmöglich für jeden shop einen request abzusetzen.
         
        $shops = atkp_shop::get_list(null, true);
       
        foreach ( $shops as $subshop ) { 
            $enableofferload = $subshop->enablepricecomparison;
                      
            if(!array_key_exists ($subshop->id, $filterarray) ||!is_array($filterarray[$subshop->id]))
                $filterarray[$subshop->id] = array();
                
            if( $product->shopid != $subshop->id && $enableofferload) {
                foreach($eans as $ean) {
                    
                    $offer = new atkp_product_offer();
                    $offer->id =uniqid();
                    $offer->type = 1;
                    $offer->shopid=$subshop->id;
                    $offer->number = $ean;
                        
                    array_push($filterarray[$subshop->id], $offer);    
                    array_push($alloffers, $offer);
                }
                 
            }
            
                
            foreach($filterarray[$subshop->id] as $checkoffer) {
                try {
                    //var_dump($checkoffer);exit;
                    $checkoffer->updatedon = current_time('timestamp');
                    $checkoffer->message = '';
               
                    $checkoffer->shipping ='';
                    $checkoffer->shippingfloat = (float)0;
                    $checkoffer->availability ='';
        
                    $checkoffer->price ='';
                    $checkoffer->pricefloat = (float)0;
               
                    $message = $subshop->provider->checklogon($subshop);
                
                    if( $message != '')
                        $checkoffer->message = 'Shop connection: '.$message;
                    else                       
                        $subshop->provider->check_offer($checkoffer);
                
                }catch (Exception $e) {
                   $checkoffer->message = $e->getMessage();
                }
            }                        
        }
        
        //hideoffer von alten einträgen übernehmen
        if($product->offers != null && $product->offers != '') {
            foreach($product->offers as $offer) {
                if($offer->hideoffer) {
                 
                 foreach($alloffers as $checkoffer) {
                     
                       if($checkoffer->number == $offer->number && $checkoffer->shopid == $offer->shopid) {
                            $checkoffer->hideoffer = $offer->hideoffer;   
                            break;
                       }
                 }
                    
                }
            }
            
        }
                
        $product->offers = $alloffers;
    }
    
    private function str_contains($string, $array, $caseSensitive = true)
    {
        $stripedString = $caseSensitive ? str_replace($array, '', $string) : str_ireplace($array, '', $string);
        return strlen($stripedString) !== strlen($string);
    }
    
    public function update_list($post_id, $iscron = false) {
        if(ATKPLog::$logenabled) {
            ATKPLog::LogDebug('*** update_list ***');
            ATKPLog::LogDebug('$post_id: '. $post_id);
        }        
        
        $shopid =     ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_shopid');
        
        $productlist = array();
        
        //listtype == dynamic list
        if($shopid != '') {
            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product.php';
            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_list.php';
            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_list_resp.php';
            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_shop.php';
            
            $shop = atkp_shop::load($shopid);
                
            if($shop== null || $shop->provider == null) {   
                ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_updatedon', current_time('timestamp'));
                ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_message', 'shop not found');  
                return;
            }
                        
            $message = $shop->provider->checklogon($shop);
        
            
            if($message != '') {
                ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_updatedon', current_time('timestamp'));
                ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_message', 'Shop connection invalid: '.$message);
            }else {
                $source= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_source');
                
                $preferlocalproduct= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_preferlocalproduct');
                $loadmoreoffers= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_loadmoreoffers');
                
                $extendedsearchlimit= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_extendedsearch_limit');
                $searchlimit= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_search_limit');
                
                $searchttitlefilter = ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_search_titelfilter');
                
                $searchdepartment= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_search_department');
                $searchkeyword= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_search_keyword');
                $searchorderby= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_search_orderby');
                
                $nodeid= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_node_id');
                $keyword= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_keyword');
                $productid=  ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_productid');
                
                $filterfield1= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_filterfield1');
                $filtertext1= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_filtertext1');
                $filterfield2= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_filterfield2');
                $filtertext2= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_filtertext2');
                $filterfield3=  ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_filterfield3');
                $filtertext3= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_filtertext3');
                $filterfield4=   ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_filterfield4');
                $filtertext4= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_filtertext4');
                $filterfield5=  ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_filterfield5');
                $filtertext5= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_filtertext5');

                try {
                    ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_updatedon', current_time('timestamp'));
                    ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_node_caption', '');
                    
                    $requestType = 'Search';
                    $sortOrder = '';
                    $keyword ='';
                    $maxCount = 20;
                    $asin ='';
                    $filter = array(); 
                    $producttitle = '';
                    
                    switch($source) {
                        case 10:
                            $requestType = 'TopSellers';                            
                            break;
                        case 11:
                            $requestType = 'NewReleases';
                            break;
                        case 20:
                            $keyword = $searchkeyword; ;
                            $sortOrder = $searchorderby;
                            if($searchdepartment == '')
                                $nodeid = 'All';
                            else
                                $nodeid=$searchdepartment;
                            $maxCount = $searchlimit;
                            break;                        
                        case 30:
                            if($filterfield1 != '')
                                $filter[$filterfield1] = $filtertext1;
                            if($filterfield2 != '')
                                $filter[$filterfield2] = $filtertext2;
                            if($filterfield3 != '')
                                $filter[$filterfield3] = $filtertext3;
                            if($filterfield4 != '')
                                $filter[$filterfield4] = $filtertext4;
                            if($filterfield5 != '')
                                $filter[$filterfield5] = $filtertext5;
                            $maxCount = $extendedsearchlimit;
                            $nodeid = 'All';
                            break;        
                        case 40:
                            $requestType = 'Similarity';
                            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product.php';
                            $value = atkp_product::load($productid);
                            $asin = $value->asin;
                            $producttitle = $value->title;
                            break;
                    }
                
                
                    $mylist = $shop->provider->retrieve_list($requestType, $nodeid, $keyword, $asin, $maxCount, $sortOrder, $filter);
                    
                    $titlekeywords = null;
                    
                    if($searchttitlefilter != null) {
                        $titlekeywords = array_map('strtolower',explode("\n", $searchttitlefilter));   
                    }
                    
                                        
                    if($mylist->products  != null) {
                        foreach ($mylist->products as $azproduct) {
                            
                            if($titlekeywords != null && count($titlekeywords) > 0) {
                                
                                if(!$this->str_contains($azproduct->title,$titlekeywords, false) ) {
                                    continue;
                                }
                            }
                            
                            
                            $item = array();
                            $item['type'] = 'product';
                            $item['value'] = $azproduct;

                            
                            array_push($productlist, $item);	   
                        }
                    }
                    
                    
                    ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_listurl', $mylist->listurl);
                    ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_node_caption', $mylist->browsenodename);
                    
                    
                    $post_loaded = get_post($post_id); 
                    if($post_loaded->post_title == '') {
                        
                        $newtitle ='';
                        
                        switch($source) {
                            case 10:
                                $newtitle = __('Topseller', ATKP_PLUGIN_PREFIX) . ' ' . $mylist->browsenodename;               
                                break;
                            case 11:
                                $newtitle = __('Newreleases', ATKP_PLUGIN_PREFIX) . ' ' . $mylist->browsenodename;
                                break;
                            case 20:
                                $newtitle = __('Search for ', ATKP_PLUGIN_PREFIX) . ' ' . $keyword . __(' order by ', ATKP_PLUGIN_PREFIX) . $searchorderby . ' ('. $searchdepartment.')';
                                
                                break;                        
                            case 30:
                                $newtitle = __('Extended Search', ATKP_PLUGIN_PREFIX);
                                break;        
                            case 40:
                                $newtitle = __('Similarity Products', ATKP_PLUGIN_PREFIX). __(' for ', ATKP_PLUGIN_PREFIX) .$producttitle;
                                break;
                        }

                        global $wpdb;
                        $wpdb->update( $wpdb->posts, array( 'post_title' => $newtitle ),  array( 'ID' => $post_id ) );
                    }
                    
                                            
                     ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_message', null);
                } catch(Exception $e) {                    
                    ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_message', 'Error: '.$e->getMessage());
                }
            } 
        } else {
            try {
                ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_updatedon', current_time('timestamp'));
                
                $products= ATKPTools::get_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_products');
                        
                foreach(explode("\n", $products) as $productid) {
                    $item = array();
                    $item['type'] = 'productid';
                    $item['value'] = $productid;
                    
                    array_push($productlist, $item);	   
                }
                                
                ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_message', null);
            
            } catch(Exception $e) {                    
                ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_message', 'Error: '.$e->getMessage());
            }
        } 
    
        if(sizeof($productlist) == 0)
            ATKPCache::set_cache_by_id($post_id, null);
        else
            ATKPCache::set_cache_by_id($post_id, $productlist);  
            
        
        do_action('atkp_list_updated', $post_id);
        }        
    }