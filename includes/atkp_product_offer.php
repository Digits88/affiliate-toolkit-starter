<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class atkp_product_offer{
        public $data = array();

        function __construct()
        {
            $this->id ='';
            $this->shipping ='';
            $this->availability ='';
            $this->shipping ='';
            $this->price ='';
            $this->pricefloat =(float)0;
            
            $this->shopid ='';
            $this->type ='';
            $this->number ='';
            $this->link ='';
            $this->hideoffer = false;
            $this->holdontop = false;
            
            $this->title='';
        }
        
        public static function load_offers($productid) {
         

            $offers = ATKPTools::get_post_setting($productid, ATKP_PRODUCT_POSTTYPE.'_offers');
         
         if(!is_array($offers))
         $offers = array();
         
            return $offers;
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