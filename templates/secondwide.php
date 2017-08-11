<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_template_secondwide extends atkp_default_template_base
    {   
        public function __construct() 
        { 
            
        }         
        
        public function get_detail() {
           $result = '<div class="atkp-box atkp-secondbox atkp-clearfix %cssclass%">';
           $result .= '        <a class="atkp-title" %titlelink%>%short_title%%titlelinkmark%</a>';
           
           
            $result .= '    <div class="atkp-thumb">';

            if(get_option(ATKP_PLUGIN_PREFIX.'_linkimage',0))
                $result .= '        <a %titlelink%><img class="atkp-image" src="%mediumimageurl%" alt="%title%" /></a>';
            else
                $result .= '        <img class="atkp-image" src="%mediumimageurl%" alt="%title%" />';
            
            
            $result .= '    </div>';

            $result .= '    <div class="atkp-bottom">';
           
            $result .= '        <span class="atkp-price atkp-saleprice">%price_text% %prime_icon% </span>';
            
             $rating = '';
            
            if(ATKPSettings::$showrating) {
                $rating = '(%reviewcount%)';
                
                if(get_option(ATKP_PLUGIN_PREFIX.'_linkrating',0))
                    $rating = '(<a %reviewslink%>%reviewcount%%markrating%</a>)';
            }
            
            $result .= '        <div class="atkp-rating">%star_rating% <span>'.$rating.'</span></div>';
            $result .= '        <div class="atkp-reviews"></div>';
            
            $result .= '        <a %link% class="atkp-button">%linktext%%linkmark%</a> ';
            $result .= '        <div class="atkp-container"></div><span class="atkp-priceinfo">%priceinfo_text%</span>';
            
            
            $result .= '        <div class="atkp-shoplogo">%shoplogo%</div>';
            $result .= '    </div>'; 

            
            $result .= '</div>'; 
            

            return $result;
        }
        
        
    }
    
?>