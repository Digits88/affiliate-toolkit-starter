<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_template_detailoffers extends atkp_default_template_base
    {   
        public function __construct() 
        { 
            
        }         
        
        public function get_header() {
            $result = '<table style="width:100%" class="atkp-pricecompare">';        

            return $result;
        }
                
        public function get_detail() {
            $result = '<tr>';

            $result .= '<td style="vertical-align: middle;">';
            $result .= '%shoplogo%';
            $result .= '</td><td>';
            $result .= '%price_text%<br />%shipping_text%<br />%availability_text%';
            $result .= '</td>';
            $result .= '<td style="vertical-align: middle; text-align:right">';
            $result .= '<a %productlink% class="atkp-button">%linktext%%linkmark%</a>';
            $result .= '</td>';
            $result .= '</tr>';

            return $result;   
        }
        
        public function get_footer() {
            $result = '</table>';

            return $result;
        }
        
        public function get_includemainoffer() {
            return true;   
        }
        
        public function get_templatetype() {
            return 2;   
        }
        
        
    }
    
?>