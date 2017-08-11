<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_asa2_helper {

        const DB_COLL         = 'asa2_collections';
        const DB_COLL_ITEM    = 'asa2_collections_items';
        
        protected $bb_regex_collection = '#\[asa2_collection(.[^\]]*|)\]([\w-\s]+)\[/asa2_collection\]#Usi';
        protected $bb_regex = '#\[asa2(.[^\]]*|)\]([\w-]+)\[/asa2\]#Usi';
        protected $bb_regex_textlink = '#\[asa2_textlink(.[^\]]*|)\]([\w-\s]+)\[/asa2_textlink\]#Usi';
        
        protected $_regex_param_separator = '/(,)(?=(?:[^"]|"[^"]*")*$)/m';    
        
        public function getCollectionId ($collection_label)
        {
            global $wpdb;
            
            $sql = '
                SELECT id
                FROM `'. $wpdb->prefix . atkp_asa2_helper::DB_COLL .'`
                WHERE name = "'. esc_sql($collection_label) .'"
            ';
            
            return $wpdb->get_var($sql);
        }  
        
        public function getCollectionLabel ($collection_id)
        {
            global $wpdb;
            
            $sql = '
                SELECT name
                FROM `'. $wpdb->prefix . atkp_asa2_helper::DB_COLL .'`
                WHERE id = "'. esc_sql($collection_id) .'"
            ';
            
            return $wpdb->get_var($sql);
        }  
        
        public function getCollectionLabels()
        {
            global $wpdb;
            
            $sql = '
                SELECT name
                FROM `'. $wpdb->prefix . atkp_asa2_helper::DB_COLL .'`
            ';
            
            return $wpdb->get_results($sql);
        }
        
        public function getCollectionItems ($collection_id)
        {           
            global $wpdb;
            
            $sql = '
                SELECT asin
                FROM `'. $wpdb->prefix . atkp_asa2_helper::DB_COLL_ITEM .'`
                WHERE collection_id = "'. esc_sql($collection_id) .'"
                ORDER by timestamp DESC
            ';
            
            $result =  $wpdb->get_results($sql);
                        
            return $result;
        }        
        
        public function createRepoProducts(&$messages) {
            $shopid = get_option( ATKP_PLUGIN_PREFIX.'_asa2_shopid') ;
            $poststatus = get_option( ATKP_PLUGIN_PREFIX.'_asa2_poststatus', 'publish') ;
            
            //posttype: asa2_repo
            
            $args = array(
                    'post_type'    => array( 'asa2_repo' ),
                    'post_status'=>'publish', 
                    'posts_per_page'=>-1
                );
            
            $posts = new WP_Query ( $args );
            
            global $post;
            while($posts->have_posts()) : 
                $posts->the_post();
                 
                $title = get_the_title();
                $asin =  ATKPTools::get_post_setting( $post->ID, 'asa2_internal_asin');
                $description =  get_the_content();
        
                $this->createProduct($asin, $shopid, $poststatus, $title, $description);
                
            endwhile;
             
            wp_reset_postdata();
          
        }
        
        public function createProductsFromPost ($id, $content, &$messages)
        {
            $shopid = get_option( ATKP_PLUGIN_PREFIX.'_asa2_shopid') ;
            $poststatus = get_option( ATKP_PLUGIN_PREFIX.'_asa2_poststatus', 'publish') ;
            
            $matches         = array();
    
            // single items
            preg_match_all($this->bb_regex, $content, $matches);
    
            if ($matches && count($matches[0]) > 0) {
                    
                for ($i=0; $i<count($matches[0]); $i++) {
                    
                    try {
                    
                        $asin = $matches[2][$i]; 
                        
                        $created = $this->createProduct($asin, $shopid, $poststatus);
        
                        array_push($messages, 'ID: '.$id.' Product '.$asin.' productid: '.$created);
                    } catch(Exception $e) { 
                        array_push($messages, 'ID: '.$id.' Exception: '. $e->getMessage());
                    }
                    
                }
            } else {
                 array_push($messages, 'ID: '.$id.' [asa2] skipped');
            }
        }
    
        public function createProductsFromPostTextLinks ($id, $content, &$messages)
        {
            $shopid = get_option( ATKP_PLUGIN_PREFIX.'_asa2_shopid') ;
            $poststatus = get_option( ATKP_PLUGIN_PREFIX.'_asa2_poststatus', 'publish') ;
            
            $matches         = array();
    
            // single items
            preg_match_all($this->bb_regex_textlink, $content, $matches);
                    
            if ($matches && count($matches[0]) > 0) {
                    
                for ($i=0; $i<count($matches[0]); $i++) {
                    
                    try {
                        $asin = '';
                        $params = shortcode_parse_atts($matches[1][$i]);
                        
                        if (isset($params['asin']) && !empty($params['asin'])) 
                            $asin = $params['asin'];
                            
                        if($asin == '') 
                            throw new Exception('asin empty');
                        
                        $created = $this->createProduct($asin, $shopid, $poststatus);
        
                        array_push($messages, 'ID: '.$id.' Product '.$asin.' productid: '.$created);
                    } catch(Exception $e) { 
                        array_push($messages, 'ID: '.$id.' Exception: '. $e->getMessage());
                    }
                    
                }
            } else {
                 array_push($messages, 'ID: '.$id.' [asa2_textlink] skipped');
            }           
        }
        
        private function createList($name, $asins) {
            
            $args = array(
                'title' => 'ASA2:'.$name,
                'post_type' => ATKP_LIST_POSTTYPE,
                'post_status' => 'publish',
                'posts_per_page' => -1
            );
            $posts = get_posts($args);
            
            if(count($posts) == 0) {            
                $shopid = get_option( ATKP_PLUGIN_PREFIX.'_asa2_shopid') ;
                $poststatus = get_option( ATKP_PLUGIN_PREFIX.'_asa2_poststatus', 'publish') ;
				
				if($shopid == '' || $shopid == null)
					throw new exception('ASA default shop is empty');
                            
                global $user_ID;
                $new_post = array(
                    'post_title' => 'ASA2:'.trim($name),
                    'post_status' => 'publish',
                    'post_author' => $user_ID,
                    'post_type' => ATKP_LIST_POSTTYPE,
                );
                $post_id = wp_insert_post($new_post);
                
                ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_shopid', '');
                                
                $products = '';  
                
                foreach($asins as $asin) {
                    $productid = $this->createProduct($asin, $shopid, $poststatus);
                    
                    if($products =='')
        			 $products = $productid;
        			 else
        			 $products .= "\n".$productid;
                    
                }
                
                ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_products', $products);
                            	
            	$cronjob = new atkp_cronjob(array());
                $cronjob->update_list($post_id);   
                return $post_id;
            } else
                return $posts[0]->ID;
            
        }
        
        private function createProduct($asin, $shopid, $post_status = 'publish', $title = '', $description = '') {
            
            
            $args = array(
                'meta_key' => ATKP_PRODUCT_POSTTYPE.'_asin',
                'meta_value' => $asin,
                'post_type' => ATKP_PRODUCT_POSTTYPE,
                'post_status' => 'publish',
                'posts_per_page' => -1
            );
            $posts = get_posts($args);
            
            if(count($posts) == 0) {            
                				
				if($shopid == '' || $shopid == null)
					throw new exception('ASA default shop is empty');
                            
                global $user_ID;
                $new_post = array(
                    'post_title' => '',
                    'post_status' => $post_status,
                    'post_author' => $user_ID,
                    'post_type' => ATKP_PRODUCT_POSTTYPE,
                );
                $post_id = wp_insert_post($new_post);
                
                ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_shopid', $shopid);
                ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_asin', $asin);
                ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshreviewinforegulary', 1);
                ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshpriceinforegulary', 1);
            	ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshproducturlregulary', 1);
            	ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshimagesregulary', 1);
            	ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshmoreoffersregulary', 1);   
            	
            	$cronjob = new atkp_cronjob(array());
                $cronjob->update_product($post_id, 1, 1, 1, 1, 1, 0, false);                
                
                //correct post title with asa prefix
                
                $newtitle = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_title');
                         
                if($title != '')
                    $newtitle = $title;
                                
                global $wpdb;
                $wpdb->update( $wpdb->posts, array( 'post_title' => $newtitle, 'post_name' => sanitize_title($newtitle)  ),  array( 'ID' => $post_id ) );
                
                
                if($description != '') {
                    $descriptionfield = get_option(ATKP_PLUGIN_PREFIX.'_asa2_descriptionfield', ATKP_PRODUCT_POSTTYPE.'_description');
          
                    ATKPTools::set_post_setting( $post_id, $descriptionfield, $description);    
                }
                
                sleep(1);
                
                return $post_id;
            } else {
                return $posts[0]->ID;
            }
        }
        
        public function createAllLists(&$messages) {
            
            
            $collectionlabels = $this->getCollectionLabels();
            
            foreach($collectionlabels as $collection_entry) {
                $asins = array();
                $collection_label = $collection_entry->name;
                
                $collection_id = $this->getCollectionId($collection_label);                
                
                $collection = $this->getCollectionItems($collection_id);
                                
                foreach($collection as $entry) {
                                            
                    array_push($asins, $entry->asin);
                }
                
                if(count($asins) == 0) {
                    array_push($messages, 'List ASA2:'.$collection_label.' not created. Empty!');
                } else {                
                    $created = $this->createList($collection_label, $asins);
                    
                    array_push($messages, 'List ASA2:'.$collection_label.' listid: '.$created);
                }
            }
        }
        
        public function createListsFromPost ($id, $content, &$messages)
        {
            $lists = array();
            
            $matches_coll     = array();    
    
            // collections
            preg_match_all($this->bb_regex_collection, $content, $matches_coll);
            
            if ($matches_coll && count($matches_coll[0]) > 0) {
    
                for ($i=0; $i<count($matches_coll[0]); $i++) {
                    try {
                    
                    $coll_label    = $matches_coll[2][$i];
                    
                    $asins = array();
    
                    if (!empty($coll_label)) {
                                     
                        if(is_numeric($coll_label)) {
                            $collection_id = intval($coll_label);
                            //info: funktioniert nicht, weil sonst das mapping vom shortcode fehlt
                            //$coll_label = $this->getCollectionLabel($collection_id);
                        }
                        else
                            $collection_id = $this->getCollectionId($coll_label);
    
                        $collection = $this->getCollectionItems($collection_id);
                            
                        foreach($collection as $entry) {
                                                    
                            array_push($asins, $entry->asin);
                        }
                        
                        $created = $this->createList($coll_label, $asins);
                        
                        array_push($messages, 'ID: '.$id.' List ASA2:'.$coll_label.' listid: '.$created);
                    }  
                                        
                    //TODO: generate list
                    
                    } catch(Exception $e) { 
                        array_push($messages, 'ID: '.$id.' Exception: '. $e->getMessage());
                    }
                    
                }
            } else {
                 array_push($messages, 'ID: '.$id.' [asa2_collection] skipped');
            }
            
            return $lists;
        }
    }