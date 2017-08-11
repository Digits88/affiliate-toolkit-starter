<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class atkp_product{
        public $data = array();

        function __construct()
        {
            require_once ATKP_PLUGIN_DIR.'/includes/atkp_product_image.php'; 
            require_once ATKP_PLUGIN_DIR.'/includes/atkp_product_offer.php';
            
            $this->productid =0;
            $this->asin ='';
            $this->ean='';
            $this->shopid='';
            
            $this->isbn='';
            $this->brand='';
            $this->productgroup='';
            
            $this->addtocarturl ='';
	        $this->producturl ='';
	        $this->customerreviewurl ='';
	        	        
	        $this->smallimageurl = '';
	        $this->mediumimageurl ='';
	        $this->largeimageurl ='';
	        $this->manufacturer ='';
	        $this->author='';
	        $this->numberofpages=0;
	        $this->features='';
	        //$this->thumbimagesurl ='';
	        //$this->imagesurl ='';
	        $this->images = array();
	        $this->offers = array();
	        
	        $this->refreshreviewinforegulary ='';
	        $this->refreshpriceinforegulary ='';
	        $this->refreshproducturlregulary ='';
	        $this->refreshmoreoffersregulary = false;
	        
	        $this->isownreview ='';
	        $this->rating =0;
	        $this->reviewcount =0;
	        $this->reviewsurl = '';
	        
	        //interne postid
	        $this->productid='';
	        $this->title ='';     
	        $this->description ='';
	        
	        $this->listprice = '';
	        $this->amountsaved = '';
	        $this->saleprice = '';
	                
	        $this->listpricefloat = (float)0;
	        $this->amountsavedfloat=(float)0;
	        $this->percentagesaved = '';
	        $this->salepricefloat =(float)0;
	        $this->shippingfloat =(float)0;
	        	        	       
	        $this->availability ='';
	        $this->shipping ='';
	        $this->isprime = 0;
	        
	        //wird nicht verwendet:
	        $this->lowestnewprice ='';
	        $this->totalnew =0;	       
	        $this->disablehoverlink = 0;
	        
	        $this->predicate ='';
	        $this->testresult ='';
	        
	        $this->pro ='';
	        $this->contra ='';
	        
	        $this->postids = '';
	        
	        $this->outputashtml = false;
        }
                
        public static function loadbyasin($asin) {
            /*
            * 'publish' - a published post or page
            * 'pending' - post is pending review
            * 'draft' - a post in draft status
            * 'auto-draft' - a newly created post, with no content
            * 'future' - a post to publish in the future
            * 'private' - not visible to users who are not logged in
            * 'inherit' - a revision. see get_children.
            * 'trash' - post is in trashbin. added with Version 2.9. 
            */
            
            $args = array(
                'meta_key' => ATKP_PRODUCT_POSTTYPE.'_asin',
                'meta_value' => $asin,
                'post_type' => ATKP_PRODUCT_POSTTYPE,
                'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'inherit'),
                'posts_per_page' => -1
            );
            $posts = get_posts($args);
            
            if(count($posts) == 0)
                return null;
            else
                return atkp_product::load($posts[0]->ID);
        }
        
        public static function idbyasin($asin) {
            /*
            * 'publish' - a published post or page
            * 'pending' - post is pending review
            * 'draft' - a post in draft status
            * 'auto-draft' - a newly created post, with no content
            * 'future' - a post to publish in the future
            * 'private' - not visible to users who are not logged in
            * 'inherit' - a revision. see get_children.
            * 'trash' - post is in trashbin. added with Version 2.9. 
            */
            
            $args = array(
                'meta_key' => ATKP_PRODUCT_POSTTYPE.'_asin',
                'meta_value' => $asin,
                'post_type' => ATKP_PRODUCT_POSTTYPE,
                'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'inherit'),
                'posts_per_page' => -1
            );
            $posts = get_posts($args);
            
            if(count($posts) == 0)
                return null;
            else
                return $posts[0]->ID;
        }

        public static function idbyean($asin) {
            /*
            * 'publish' - a published post or page
            * 'pending' - post is pending review
            * 'draft' - a post in draft status
            * 'auto-draft' - a newly created post, with no content
            * 'future' - a post to publish in the future
            * 'private' - not visible to users who are not logged in
            * 'inherit' - a revision. see get_children.
            * 'trash' - post is in trashbin. added with Version 2.9. 
            */
            
            $args = array(
                'meta_key' => ATKP_PRODUCT_POSTTYPE.'_ean',
                'meta_value' => $asin,
                'meta_compare' => 'LIKE',
                'post_type' => ATKP_PRODUCT_POSTTYPE,
                'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'inherit'),
                'posts_per_page' => -1
            );
            $posts = get_posts($args);
            
            if(count($posts) == 0)
                return null;
            else
                return $posts[0]->ID;
        }
        
        public static function idbyname($name) {
            /*
            * 'publish' - a published post or page
            * 'pending' - post is pending review
            * 'draft' - a post in draft status
            * 'auto-draft' - a newly created post, with no content
            * 'future' - a post to publish in the future
            * 'private' - not visible to users who are not logged in
            * 'inherit' - a revision. see get_children.
            * 'trash' - post is in trashbin. added with Version 2.9. 
            */
            
            $args = array(
                'title' => $name,
                'post_type' => ATKP_PRODUCT_POSTTYPE,
                'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'inherit'),
                'posts_per_page' => 2
            );
            $posts = get_posts($args);
            
            if(count($posts) == 0)
                return null;
            else
                return $posts[0]->ID;
        }
        
        public static function exists($post_id) {
        
            $product = get_post( $post_id ); 
    
            if(!isset($product) || $product == null)
                return false;
            if($product->post_type != ATKP_PRODUCT_POSTTYPE)
                return false;
            
            return true;
        }

        public static function load($post_id) {
         
            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product_image.php';
        
            $product = get_post( $post_id ); 
    
            if(!isset($product) || $product == null)
                throw new Exception( 'product not found: '.$post_id);
            if($product->post_type != ATKP_PRODUCT_POSTTYPE)
                 throw new Exception('invalid post_type: '.$product->post_type. ', $post_id: '.$post_id);
            
            $prd = new atkp_product();
            
            //$prd->title = $product->post_title;   
            //$prd->description = $product->post_content;
            $prd->productid = $post_id;
            $prd->shopid= ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_shopid');
            $prd->title = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_title');
            $prd->description = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_description');
            
            $prd->productid=$post_id; //ATKP_PRODUCT_POSTTYPE.'_updatedon'
            $prd->updatedon =ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_updatedon');
            
             $prd->predicate = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_predicate');
             
             $prd->pro = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_pro');
             $prd->contra = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_contra');
             
             
             $prd->testresult = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_testresult');
             $prd->testrating = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_testrating');
             $prd->testdate = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_testdate');
            
            $prd->asin = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_asin');
            $prd->ean = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_ean');
            
            $prd->isbn=ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_isbn');
            $prd->brand=ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_brand');
            $prd->productgroup=ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_productgroup');
            
            $prd->disablehoverlink =  ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_disablehoverlink');
            
            $prd->addtocarturl =  ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_addtocarturl');
            $prd->producturl = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_producturl');
            $prd->customerreviewurl = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_customerreviewsurl');
            
            
            $prd->smallimageurl = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_smallimageurl');
            $prd->mediumimageurl = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_mediumimageurl');
            $prd->largeimageurl = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_largeimageurl');
            $prd->manufacturer = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_manufacturer');
            $prd->author = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_author');
            $prd->numberofpages = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_numberofpages');
            $prd->features = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_features');
            
            $prd->images = atkp_product_image::load_images($post_id); 
            $prd->offers = atkp_product_offer::load_offers($post_id); 
            //$prd->imagesurl = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_imagesurl');
        	//$prd->thumbimagesurl = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_thumbimagesurl');
        	    
            $prd->refreshreviewinforegulary = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshreviewinforegulary');
            $prd->refreshpriceinforegulary = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshpriceinforegulary');
        	$prd->refreshproducturlregulary = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshproducturlregulary');
        	
            $prd->isownreview = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_isownreview');
            $prd->rating = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_rating');
            $prd->reviewcount = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_reviewcount');
            $prd->reviewsurl = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_reviewsurl');
            
            $prd->listprice = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_listprice');
            $prd->amountsaved = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_amountsaved');
            $prd->percentagesaved = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_percentagesaved');
            $prd->saleprice = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_saleprice');
            $prd->availability = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_availability');
            $prd->shipping = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_shipping');
            $prd->isprime = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_isprime');
            
            $prd->listpricefloat = (float)ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_listpricefloat');
            $prd->amountsavedfloat=(float)ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_amountsavedfloat');
            $prd->salepricefloat =(float)ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_salepricefloat');
            $prd->shippingfloat =(float)ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_shippingfloat');
            
            $prd->refreshmoreoffersregulary = ATKPTools::get_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshmoreoffersregulary');
            
            $prd->postids = ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_postid');
            
            $prd->outputashtml = ATKPTools::get_post_setting($post_id, ATKP_PRODUCT_POSTTYPE.'_outputashtml');
            
            
            
            
            return $prd;
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
        
        protected static function price_to_float($s) {
    		$s = str_replace(',', '.', $s);
    
    		// remove everything except numbers and dot "."
    		$s = preg_replace("/[^0-9\.]/", "", $s);
    
    		// remove all seperators from first part and keep the end
    		$s = str_replace('.', '',substr($s, 0, -3)) . substr($s, -3);
    
    		// return float
    		return round((float)$s, 2);
	    }
    }
    
    
    
    
?>