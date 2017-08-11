<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class atkp_template{
        public $data = array();

        function __construct()
        {
            $this->title= '';
            $this->template_type= '';
            $this->header = '';
            $this->body = '';
            $this->footer ='';
            $this->disableddisclaimer =0;
            $this->extendedview = 0;
            $this->bodyheader = '';
            $this->bodyfooter = '';
            
            $this->detailheader = '';       
            $this->detailfooter = '';
            
            $this->comparevalues = array();

            $this->css = '';
          
        }

        public static function load($post_id) {
         
            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_template_comparevalue.php';
          
            $product = get_post( $post_id ); 
    
            if(!isset($product) || $product == null)
                throw new Exception( 'template not found: '.$post_id);
            if($product->post_type != ATKP_TEMPLATE_POSTTYPE)
                 throw new Exception('invalid post_type: '.$product->post_type. ', $post_id: '.$post_id);
            
            $prd = new atkp_template();
            
            $prd->title= $product->post_title;
            $prd->template_type= ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_template_type');
            $prd->header = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_header');
            $prd->body = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_body');
            $prd->footer = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_footer');
            $prd->disableddisclaimer = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_disabledisclaimer');
            $prd->extendedview = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_extendedview');
            $prd->bodyheader = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_body_header');
            $prd->bodyfooter = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_body_footer');
            
            $prd->detailheader = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_detail_header');            
            $prd->detailfooter = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_detail_footer');
            
            $prd->css = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_css');
  
            $prd->comparevalues = atkp_template_comparevalue::load_comparevalues($post_id);
            
            $prd->horizontalscrollbars = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_horizontalscrollbars');
            $prd->hideheaders = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_hideheaders');
            $prd->maxmobileproducts = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_maxmobileproducts');
            $prd->maxproducts = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_maxproducts');
            $prd->viewtype = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_viewtype');
            $prd->mobilebody = ATKPTools::get_post_setting( $post_id, ATKP_TEMPLATE_POSTTYPE.'_mobilebody');
  
     
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
    }
    
    
?>