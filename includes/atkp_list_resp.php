<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class atkp_list_resp {
 
 public $data = array();
        
        function __construct()
        {
            $this->listid = '';
            $this->title = '';
            $this->updatedon ='';
            $this->message='';
            
            $this->listurl = '';
        }     
        
        public $asins = array();
        
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