<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_endpoints
    {   
        /**
         * Construct the plugin object
         */
        public function __construct($pluginbase)
        {            
            add_action( 'wp_ajax_atkp_export_template',  array(&$this, 'atkp_export_template') );
            add_action( 'wp_ajax_atkp_search_departments',  array(&$this, 'atkp_search_departments') );
            add_action( 'wp_ajax_atkp_search_products',  array(&$this, 'atkp_search_products') );
            add_action( 'wp_ajax_atkp_search_browsenodes',  array(&$this, 'atkp_search_browsenodes') );
            add_action( 'wp_ajax_atkp_search_filters',  array(&$this, 'atkp_search_filters') );
            
            add_action( 'wp_ajax_atkp_get_object',  array(&$this, 'atkp_get_object') );
            add_action( 'wp_ajax_atkp_import_product',  array(&$this, 'atkp_import_product') );
            add_action( 'wp_ajax_atkp_create_list',  array(&$this, 'atkp_create_list') );
            
            add_action( 'wp_ajax_atkp_clear_logfile',  array(&$this, 'atkp_clear_logfile') );
            add_action( 'wp_ajax_atkp_download_logfile',  array(&$this, 'atkp_download_logfile') );
            
            add_action( 'wp_ajax_atkp_search_local_products',  array(&$this, 'atkp_search_local_products') );
        }
        
        function atkp_export_template() {
            try {
                $nounce =  ATKPTools::get_get_parameter('request_nonce', 'string');
            	
            	if(!wp_verify_nonce($nounce, 'atkp-export-template', false))
            	    throw new Exception('Nonce invalid'); 
            	
				//if ( !wp_verify_nonce( $nounce, 'atkp-export-template' ) )
				//	throw new Exception('Nonce invalid');        
    					
                $templateid = ATKPTools::get_get_parameter( 'templateid', 'int');
                
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_template.php';
               
                $atkp_template = atkp_template::load($templateid);
               
                $string =  serialize($atkp_template);
                $name = sanitize_title($atkp_template->title);
                
                # send the file to the browser as a download
                
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"".$name.".txt\"");
                header("Content-Transfer-Encoding: utf-8");
                header("Content-Length: ".strlen($string));
                
                echo $string;
                
                exit;
            } catch(Exception $e) {
                $gif_data[] = array(
                        'error'  => 'An error has occurred.',
                        'message' => $e->getMessage(),
                    );
                    
                wp_send_json( $gif_data );
            }
        }
        
        function atkp_clear_logfile() {
            try {
                $nounce =  ATKPTools::get_get_parameter('request_nonce', 'string');
            	
            	if(!wp_verify_nonce($nounce, 'atkp-download-log', false))
            	    throw new Exception('Nonce invalid');       

    		    if(file_exists(ATKP_LOGFILE)) 
                    unlink (ATKP_LOGFILE);
                
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
               
            } catch(Exception $e) {
                $gif_data[] = array(
                        'error'  => 'An error has occurred.',
                        'message' => $e->getMessage(),
                    );
                    
                wp_send_json( $gif_data );
            }
        }
        
        function atkp_download_logfile() {
            try {
                $nounce =  ATKPTools::get_get_parameter('request_nonce', 'string');
            	
            	if(!wp_verify_nonce($nounce, 'atkp-download-log', false))
            	    throw new Exception('Nonce invalid');  
            	    
    			$string ='';
    		    if(file_exists(ATKP_LOGFILE)) 
                    $string = file_get_contents(ATKP_LOGFILE);
               
                $name = sanitize_title('affiliate-toolkit-log.txt');
                
                # send the file to the browser as a download
                
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-type: text/plain");
                header("Content-Disposition: attachment; filename=\"".$name.".txt\"");
                header("Content-Transfer-Encoding: utf-8");
                header("Content-Length: ".strlen($string));
                
                echo $string;
                
                exit;
            } catch(Exception $e) {
                $gif_data[] = array(
                        'error'  => 'An error has occurred.',
                        'message' => $e->getMessage(),
                    );
                    
                wp_send_json( $gif_data );
            }
        }
        
        function atkp_create_list() {
            try {
                $nounce =  ATKPTools::get_post_parameter('request_nonce', 'string');
            	
            	if(!wp_verify_nonce($nounce, 'atkp-import-nonce', false))
            	    throw new Exception('Nonce invalid');  
            	    
				$shopid = ATKPTools::get_post_parameter('shop', 'string');
                $searchterm =  ATKPTools::get_post_parameter('searchterm', 'string');
                $listtype =  ATKPTools::get_post_parameter('listtype', 'string');
                $title =  ATKPTools::get_post_parameter('title', 'string');
                
                $department =  ATKPTools::get_post_parameter('department', 'string');
                $sortby =  ATKPTools::get_post_parameter('sortby', 'string');
                $loadmoreoffers =  ATKPTools::get_post_parameter('loadmoreoffers', 'bool');
                
                //shop: shop, title: title, department: department, sortby:sortby, browsenode:browsenodeid, searchterm: keyword, listtype: source, loadmoreoffers: loadmoreoffers
            	
            	$post_id = ATKPTools::create_list($title, $shopid, $listtype, $searchterm, $department, $sortby, $loadmoreoffers);
            	
            	$cronjob = new atkp_cronjob(array());
                $cronjob->update_list($post_id, false);      
            	
            	$gif_data[] = array(
                        'postid'  => $post_id,
                        'title' => get_the_title($post_id),
                        'edit_url' => get_edit_post_link($post_id),
                    );
                    
                wp_send_json( $gif_data );  
    			
            } catch(Exception $e) {
                $gif_data[] = array(
                        'error'  => 'An error has occurred.',
                        'message' => $e->getMessage(),
                    );
                    
                wp_send_json( $gif_data );
            }
        }
        
        function atkp_import_product() {
             try {
                $nounce =  ATKPTools::get_post_parameter('request_nonce', 'string');
            			
				if ( !wp_verify_nonce( $nounce, 'atkp-import-nonce' ) )
					throw new Exception('Nonce invalid');        
				
				$shopid = ATKPTools::get_post_parameter('shop', 'string');
                $asin =  ATKPTools::get_post_parameter('asin', 'string');
                $asintype =  ATKPTools::get_post_parameter('asintype', 'string');
                $title =  ATKPTools::get_post_parameter('title', 'string');
                $status =  ATKPTools::get_post_parameter('status', 'string');
                
                if($shopid == '')       
                    throw new Exception('shop required');
                if($asin == '')       
                    throw new Exception('asin required');
                    
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_udfield.php';
                $newfields = atkp_udfield::load_fields();
                $taxonomies = atkp_udtaxonomy::load_taxonomies();
            	 
            	$post_id = ATKPTools::create_product($title,$shopid,$asin, $status, $asintype);
            	
            	foreach ($taxonomies as $taxonomy ) {
                    if(!$taxonomy->showui)
                        continue;
                        
                    $parametername = str_replace('-', '_', 'atkp-tax-'.$taxonomy->name);
                    $val = ATKPTools::get_post_parameter($parametername, 'string');
                    
                    if($val != null)
                        ATKPTools::check_taxonomy($post_id, $taxonomy->name, $val, false);                           
                }
                
                foreach ($newfields as $newfield ) {
                   //html controls überspringen
                    if($newfield->type == 5)
                        continue;
                        
                    $fieldvalue= null;
                    $parametername = str_replace('-', '_', 'atkp-field-'.$newfield->name);
                        
                    switch($newfield->type) {
                                      
                         case 1:
                             //Text
                            $type  ='text';
                            switch($newfield->format) {
                                default:
                                case 'text':
                                    $fieldvalue =  ATKPTools::get_post_parameter($parametername, 'string');
                                    break;
                                case 'number':
                                    $fieldvalue =  ATKPTools::get_post_parameter($parametername, 'int');
                                    break;
                                case 'url':
                                    $fieldvalue =  ATKPTools::get_post_parameter($parametername, 'url');
                                    break;
                            }
                            
                             
                             break;
                         case 2:
                             //multiline
                             $fieldvalue =  ATKPTools::get_post_parameter($parametername, 'multistring');                      
                             break;
                         case 3:
                             //dropdown
                              $fieldvalue =  ATKPTools::get_post_parameter($parametername, 'string');
                             break;
                         case 4:
                             //yesno
                             $fieldvalue =  ATKPTools::get_post_parameter($parametername, 'string');    
                             
                             switch($fieldvalue) {
                                 default:
                                case '':
                                    $fieldvalue = null;
                                    break;
                                case '0':
                                    $fieldvalue = '0';
                                    break;
                                case '1';
                                    $fieldvalue = '1';
                                    break;
                                 
                             }
                             break;
                         case 5:
                             //html
                             $fieldvalue =  ATKPTools::get_post_parameter($parametername, 'html');
                             break;
                         default:
                             throw new exception('unknown newfield->type: '. $newfield->type);
                        }
                    
                    $fieldname = 'customfield_'.$newfield->name; 
                    
                    if($fieldvalue != null)
                        ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_'.$fieldname, $fieldvalue);
                }
            	
            	
            	$cronjob = new atkp_cronjob(array());
            	
            	//lade beim hinzufügen keine preisvergleiche
            	$refreshpricecompare = false;
            	
                $cronjob->update_product($post_id, 1, 1, 1, 1, 1, $refreshpricecompare, false);                
                
                //correct post title 
                if($title != '') {
                    ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_title', $title);

                    global $wpdb;
                    $wpdb->update( $wpdb->posts, array( 'post_title' => $title, 'post_name' => sanitize_title($title)  ),  array( 'ID' => $post_id ) );
                }
                
                $gif_data[] = array(
                        'postid'  => $post_id,
                        'title' => get_the_title($post_id),
                        'edit_url' => get_edit_post_link($post_id),
                    );
                    
                wp_send_json( $gif_data );
           
             } catch(Exception $e) {
                $gif_data[] = array(
                        'error'  => 'An error has occurred.',
                        'message' => $e->getMessage(),
                    );
                    
                wp_send_json( $gif_data );
            }
        }
        function atkp_get_object() {
             try {
                $nounce =  ATKPTools::get_post_parameter('request_nonce', 'string');
            			
				if ( !wp_verify_nonce( $nounce, 'atkp-get-nonce' ) )
					throw new Exception('Nonce invalid');        
				
				$post_type = ATKPTools::get_post_parameter('post_type', 'string');
                $id =  ATKPTools::get_post_parameter('post_id', 'string');
                
                if($post_type == '')       
                    throw new Exception('post_type required');
                if($id == '')       
                    throw new Exception('id required');
                    
                $gif_data[] = array();
                    
                switch($post_type) {
                    case ATKP_PRODUCT_POSTTYPE:
                    case ATKP_LIST_POSTTYPE:
                        $gif_data['post_id'] = $id;
                        $gif_data['post_type'] = $post_type;
                        $gif_data['title'] = get_the_title($id);
                        $gif_data['edit_url'] = get_edit_post_link($id);
                        break;     
                    default:
                        throw new exception('unknown posttype: '.$post_type);
                }
            	                    
                wp_send_json( $gif_data );
           
             } catch(Exception $e) {
                $gif_data[] = array(
                        'error'  => 'An error has occurred.',
                        'message' => $e->getMessage(),
                    );
                    
                wp_send_json( $gif_data );
            }
        }
        
        function atkp_search_local_products() {
            
            try {
                $azproducts = $this->localSearch();

                if($azproducts != null) {
                    wp_send_json( $azproducts);
                }
            } catch(Exception $e) {
                $gif_data[] = array(
                        'error'  => 'An error has occurred.',
                        'message' => $e->getMessage(),
                    );
                    
                wp_send_json( $gif_data );
            }
            
        }
        
        function atkp_search_products() {
            try {
                $azproducts = $this->quickSearch('product');

                if($azproducts != null) {
                    wp_send_json( $azproducts);
                }
            } catch(Exception $e) {
                $gif_data[] = array(
                        'error'  => 'An error has occurred.',
                        'message' => $e->getMessage(),
                    );
                    
                wp_send_json( $gif_data );
            }
        }
        
        function atkp_search_filters() {
            try {
                $azproducts = $this->quickSearch('filter');
                
                if($azproducts != null) {
                    wp_send_json( $azproducts);
                }
            } catch(Exception $e) {
                $gif_data[] = array(
                        'error'  => 'An error has occurred.',
                        'message' => $e->getMessage(),
                    );
                    
                wp_send_json( $gif_data );
            }
        }
        
        function atkp_search_departments() {
            try {
                $azproducts = $this->quickSearch('department');
                
                if($azproducts != null) {
                    wp_send_json( $azproducts);
                }
            } catch(Exception $e) {
                $gif_data[] = array(
                        'error'  => 'An error has occurred.',
                        'message' => $e->getMessage(),
                    );
                    
                wp_send_json( $gif_data );
            }
        }
        
        function atkp_search_browsenodes() {
            try {
                $aznodes = $this->quickSearch('browsenode');
                
                if($aznodes != null) {
                    wp_send_json( $aznodes);
                }
            } catch(Exception $e) {
                $gif_data[] = array(
                        'error'  => 'An error has occurred.',
                        'message' => $e->getMessage(),
                    );
                    
                wp_send_json( $gif_data );
            }
        }
                
        function localSearch() {
            $nounce =  ATKPTools::get_post_parameter('request_nonce', 'string');
            			
            if ( !wp_verify_nonce( $nounce, 'atkp-search-nonce' ) )
                throw new Exception('Nonce invalid');     
			
			require_once ATKP_PLUGIN_DIR.'/includes/atkp_shop.php';
			
            $type =  ATKPTools::get_post_parameter('type', 'string');
            $keyword =  ATKPTools::get_post_parameter('keyword', 'string');
            
            if($type == '')       
                throw new Exception('type required');
                
            $products = array();
            $args = array(
    			'post_type' => array( $type ),
    			's' => $keyword,
    			'post_status' => array('draft', 'publish'),
    			'paged' => 1,
    			'posts_per_page' => 20,			
    		);
    		
    		$the_query = new WP_Query( $args );
    		
    		while ( $the_query->have_posts() ) {
    			try {
        			$the_query->the_post();
        			
        			$prd = $the_query->post;
        			
        		
        			$product = array();
                    //info: je nach anbieter wird entweder small oder large zurückgeliefert?!
                                
                    $product['id'] = $post_id = $prd->ID;
                    $product['title'] = $prd->post_title;
                    $product['editurl'] = get_edit_post_link($prd->ID);
                    
                    switch($type) {
                        case ATKP_PRODUCT_POSTTYPE:  
                            $imageurl = ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_smallimageurl', true );
        					if($imageurl == '')
        					    $imageurl = ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_mediumimageurl', true );
        					if($imageurl == '')
        					    $imageurl = ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_largeimageurl', true );
        					    
        					$product['imageurl'] = $imageurl;
        					
        					$selectedshopid = ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_shopid', 0); 
                            
                            if($selectedshopid != '')
                                $shps = atkp_shop::load($selectedshopid, false);
                            
                            if(!isset($shps) || $shps == null)
                                $product['shop'] =  __('Manual product', ATKP_PLUGIN_PREFIX);
                            else
                                $product['shop'] =  __('Shop', ATKP_PLUGIN_PREFIX).': '.$shps->title;
                            break;
                        case ATKP_LIST_POSTTYPE:
                            
                            $selectedshopid = ATKPTools::get_post_setting($post_id, ATKP_LIST_POSTTYPE.'_shopid', 0); 
                            
                            if($selectedshopid != '')
                                $shps = atkp_shop::load($selectedshopid, false);
                            
                            if(!isset($shps) || $shps == null)
                                $product['shop'] = __('Manual list', ATKP_PLUGIN_PREFIX);
                            else
                                $product['shop'] = __('Shop', ATKP_PLUGIN_PREFIX).': '.$shps->title;
                            break;
                        default:
                            throw new exception('unknown type: '.$type);
                    }
                    
                    array_push($products, $product);  
    			} catch(Exception $e) {
    			    
    			    
    			}
    		}
            return $products;   
        }
                
        function quickSearch($searchType) {
            
			$nounce =  ATKPTools::get_post_parameter('request_nonce', 'string');
            			
            if ( !wp_verify_nonce( $nounce, 'atkp-search-nonce' ) )
                throw new Exception('Nonce invalid');     
			
            $shopid =  ATKPTools::get_post_parameter('shop', 'string');
            $keyword =  ATKPTools::get_post_parameter('keyword', 'string');
            
            if($shopid == '')       
                throw new Exception('shop required');
            if($keyword == '' && $searchType != 'department' && $searchType != 'filter')       
                throw new Exception('keyword required');
            
            require_once ATKP_PLUGIN_DIR.'/includes/atkp_shop.php';
            
            $shop = atkp_shop::load($shopid);
            
            
            
            if($shop != null && $shop->provider != null) {   
                $shop->provider->checklogon($shop);
                
                if($searchType == 'department')
                    return $shop->provider->retrieve_departments();
                else if($searchType == 'filter')
                    return $shop->provider->retrieve_filters();
                else if($searchType == 'browsenode')
                    return $shop->provider->retrieve_browsenodes($keyword);
                else {
                    $products = $shop->provider->quick_search($keyword, $searchType);
                    
                    require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product.php';
                    
                    $newproducts = array();
                    
                    if(isset($products) && is_array($products)) {
                        foreach($products as $product){ 
                            if(isset($product['asin'])) { 
                                $id = atkp_product::idbyasin($product['asin']);
                                
                                if($id == null)
                                    $product['productid'] = 'null';
                                else 
                                    $product['productid'] = $id;
                            } else
                                $product['productid'] = 'null';
                                
                            array_push($newproducts, $product);
                        }
                    }
                    
                    return $newproducts;
                }
            }
        
        }
            
    }
    
?>