<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_template_wide extends atkp_default_template_base
    {   
        public function __construct() 
        { 
            
        }         
        
        public function get_detail() {
            $result = '<div class="atkp-box atkp-clearfix %cssclass%">';
            $result .= '    <div class="atkp-thumb">';

            if(get_option(ATKP_PLUGIN_PREFIX.'_linkimage',0))
                $result .= '        <a %titlelink%><img class="atkp-image" src="%mediumimageurl%" alt="%title%" /></a>';
            else
                $result .= '        <img class="atkp-image" src="%mediumimageurl%" alt="%title%" />';
            
            $result .= '        <div class="atkp-rating">%star_rating%</div>';
            if(get_option(ATKP_PLUGIN_PREFIX.'_linkrating',0))
                $result .= '        <div class="atkp-reviews"><a %reviewslink%>%reviewcount%%markrating%</a></div>';
            else
                $result .= '        <div class="atkp-reviews">%reviewcount%</div>';
            $result .= '        <div class="atkp-shoplogo">%shoplogo%</div>';
            $result .= '    </div>';

            $result .= '    <div class="atkp-content">';
            $result .= '        <a class="atkp-title" %titlelink%>%short_title%%titlelinkmark%</a>';
            $result .= '        <div class="atkp-author">%by_text%</div>';
			$result .= '        <div class="atkp-description">%info_text%</div>';
            $result .= '    </div>'; 
            $result .= '    <div class="atkp-bottom">';
           
            $result .= '        <span class="atkp-price atkp-listprice">%listprice_text%</span>';
            $result .= '        <span class="atkp-price atkp-savedamount">%save_text%</span>';
            $result .= '        <span class="atkp-price atkp-saleprice">%prime_icon%&nbsp;%price_text%</span>';
            
            $result .= '        <a %link% class="atkp-button">%linktext%%linkmark%</a> ';
            $result .= '        <div class="atkp-container"></div><span class="atkp-priceinfo">%priceinfo_text%</span>';
            $result .= '    </div>'; 

            
            $result .= '</div>'; 
            

            return $result;
        }
        
        
    }
    
?>