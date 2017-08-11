<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_template_moreoffers extends atkp_default_template_base
    {   
        public function __construct() 
        { 
            
        }         
        
        public function get_header() {
            $result = '<div class="atkp-offers-dropdown">';

            $result .= '      <a  class="atkp-offers-dropbtn" style="font-size:12px" >'.get_option(ATKP_PLUGIN_PREFIX.'_moreoffers_title', __('More offers »', ATKP_PLUGIN_PREFIX)).'</a>';
            $result .= '          <div class="atkp-offers-dropdown-content">';
            

            return $result; 
        }
        
        public function get_detail() {
            $result = '<div class="atkp-container atkp-clearfix">';

            $result .= '<a %productlink%>';
            $result .= '<span class="atkp-more-offers-left" style="width: 25%;">';
            $result .= '%smallshoplogo%';
            $result .= '</span>';
            $result .= '<span class="atkp-more-offers-right" style="width: 65%;">';
            $result .= '<span class="atkp-more-offers-price">%price_text%</span><br />';
            $result .= '<span class="atkp-more-offers-shipping atkp-clearfix">%shipping_text%</span>';
            $result .= '</span>';
            $result .= '</span>';
            $result .= '</a>';
            $result .= '</div>';

            return $result;  
        }
                
        public function get_footer() {
            $result = '  </div>  </div>';

            return $result; 
        }
        
        public function get_hidedisclaimer() {
            return true;   
        }
        
        public function get_templatetype() {
            return 2;   
        }
            
        
    }
    
?>