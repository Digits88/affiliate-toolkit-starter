<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class atkp_shop_provider_base 
{ 
    //das ist die basis klasse für alle shop provider
    
    public function __construct() 
    { 
        
    } 
    
    
    public function check_offer($offer) {
                
         return $offer;   
    }
    
    public function bulk_offers_supported() {
        return false;
    }
    
    public function check_offers($offers) {
                
         return $offers;   
    }
    
    public function get_defaultlogo() {
        return '';   
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
	
	public function read_subshop_settings($subshops, $shopprefix) {
	 
    	foreach($subshops as $subshop) {
            $checked = ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid, 'bool');
            $subshop->enabled = $checked == 1;
                
            $subshop->customtitle = ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid.'_customtitle', 'string');
            $subshop->customsmalllogourl = ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid.'_customsmalllogourl', 'url');
            $subshop->customlogourl = ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid.'_customlogourl', 'url');
            $subshop->customfield1 = ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid.'_customfield1', 'html');
            $subshop->customfield2 = ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid.'_customfield2', 'html');
            $subshop->customfield3 = ATKPTools::get_post_parameter(ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid.'_customfield3', 'html');
            
        }
	    
	}
	
	public function echo_subshop_settings($subshops, $shopprefix, $locked = false) {
	    	    
	    ?> <ul> <?php
	    foreach($subshops as $subshop) {
        ?>
            <li>
            <?php if(!$locked) { ?>
                <input <?php echo $locked ? 'disabled' : '' ?> type="checkbox" id="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>" name="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>" value="1" <?php echo checked(1, $subshop->enabled); ?>>
                <label for="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>">
                    <?php echo $subshop->title ?>
                </label>
                <?php } ?>
                <table style="width:100%">
                
                <tr>
                    <td style="padding: 0px 0px;">&nbsp; </td>
                    <th scope="row"  style="padding: 0px 0px;vertical-align: middle;">
                        <label for="">
                            <?php _e('Title', ATKP_PLUGIN_PREFIX) ?>:
                        </label> 
                    </th>
                    <td style="padding: 0px 0px;">
                        <input  style="width:40%" type="text" id="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>_customtitle" name="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>_customtitle" value="<?php echo esc_attr($subshop->customtitle == '' ? $subshop->title : $subshop->customtitle); ?>">
                    </td>
                </tr>
                
                <tr>
                    <td style="padding: 0px 0px;">&nbsp; </td>
                    <th scope="row"  style="padding: 0px 0px;vertical-align: middle;">
                        <label for="">
                            <?php _e('Shop Logo Small', ATKP_PLUGIN_PREFIX) ?>:
                        </label> 
                    </th>
                    <td style="padding: 0px 0px;">
                        <input  style="width:40%" type="url" id="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>_customsmalllogourl" name="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>_customsmalllogourl" value="<?php echo esc_attr($subshop->customsmalllogourl == '' ? $subshop->smalllogourl : $subshop->customsmalllogourl); ?>">
                    </td>
                </tr>
                
                <tr>
                    <td style="padding: 0px 0px;">&nbsp; </td>
                    <th scope="row"  style="padding: 0px 0px;vertical-align: middle;">
                        <label for="">
                            <?php _e('Shop Logo Large', ATKP_PLUGIN_PREFIX) ?>:
                        </label> 
                    </th>
                    <td style="padding: 0px 0px;">
                        <input  style="width:40%" type="url" id="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>_customlogourl" name="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>_customlogourl" value="<?php echo esc_attr($subshop->customlogourl == '' ? $subshop->logourl : $subshop->customlogourl); ?>">
                    </td>
                </tr>
                
                <tr>
                    <td style="padding: 0px 0px;">&nbsp; </td>
                    <th scope="row"  style="padding: 0px 0px;vertical-align: middle;">
                        <label for="">
                            <?php _e('Custom Field 1', ATKP_PLUGIN_PREFIX) ?>:
                        </label> 
                    </th>
                    <td style="padding: 0px 0px;">
                        <input  style="width:40%" type="text" id="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>_customfield1" name="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>_customfield1" value="<?php echo esc_attr($subshop->customfield1); ?>">
                    </td>
                </tr>
                
                <tr>
                    <td style="padding: 0px 0px;">&nbsp; </td>
                    <th scope="row"  style="padding: 0px 0px;vertical-align: middle;">
                        <label for="">
                            <?php _e('Custom Field 2', ATKP_PLUGIN_PREFIX) ?>:
                        </label> 
                    </th>
                    <td style="padding: 0px 0px;">
                        <input  style="width:40%" type="text" id="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>_customfield2" name="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>_customfield2" value="<?php echo esc_attr($subshop->customfield2); ?>">
                    </td>
                </tr>
                
                <tr>
                    <td style="padding: 0px 0px;">&nbsp; </td>
                    <th scope="row"  style="padding: 0px 0px;vertical-align: middle;">
                        <label for="">
                            <?php _e('Custom Field 3', ATKP_PLUGIN_PREFIX) ?>:
                        </label> 
                    </th>
                    <td style="padding: 0px 0px;">
                        <input  style="width:40%" type="text" id="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>_customfield3" name="<?php echo ATKP_SHOP_POSTTYPE.'_'.$shopprefix.'_subshop'.$subshop->shopid ?>_customfield3" value="<?php echo esc_attr($subshop->customfield3); ?>">
                    </td>
                </tr>
                
                </table>
                
            <li>
        <?php } ?>   
	    </ul><?php
	}
    
    public function get_defaultbtn1_text() {
        return __('Buy now at %s!', ATKP_PLUGIN_PREFIX);   
    }
    
    public function get_defaultbtn2_text() {
        return __('Buy now at %s!', ATKP_PLUGIN_PREFIX);   
    }
            
    public $displayshoplogo;
    public $enablepricecomparison;
    public $buyat;
    public $addtocart;
    
    public function load_basicsettings( $shopid) {
        $this->displayshoplogo = ATKPTools::get_post_setting( $shopid, ATKP_SHOP_POSTTYPE.'_displayshoplogo');  
        $this->enablepricecomparison = ATKPTools::get_post_setting( $shopid, ATKP_SHOP_POSTTYPE.'_enablepricecomparison');   
        $this->buyat = ATKPTools::get_post_setting( $shopid, ATKP_SHOP_POSTTYPE.'_text_buyat');   
        $this->addtocart = ATKPTools::get_post_setting( $shopid, ATKP_SHOP_POSTTYPE.'_text_addtocart');   
    }
    
    
    public static function retrieve_provider($id) {
     
     switch($id) {
          case '1':
              require_once ATKP_PLUGIN_DIR.'/includes/shopproviders/atkp_shop_provider_amazon.php';
			return new atkp_shop_provider_amazon();
              break;       
     }
     }
    
    public static function retrieve_providers() {
     
         $providers = array();
         
        require_once ATKP_PLUGIN_DIR.'/includes/shopproviders/atkp_shop_provider_amazon.php';
       
         
        $providers['1'] = new atkp_shop_provider_amazon();
            
        return $providers;
    }
    
    public function get_caption() {
        return 'base';
    }
    
    public function check_configuration($post_id) {
        return '';
    }    
    
    public function set_configuration($post_id) {
        
        
    }
    
    public function get_configuration($post) {
        
        
    }
    
    public function get_shops($post_id, $allshops= false) {
        return array();
    }
    
    public function checklogon($shop) {
        
    }
    
    public function quick_search($keyword, $searchType, $page=1) {
        
    }
    
    public function retrieve_browsenodes($keyword) {
        
    }
    
    public function retrieve_departments() {
        
    }
    
    public function retrieve_filters() {
        
    }

    public function retrieve_product($asin, $id_type = 'ASIN') {
        
    }
    
    public function retrieve_products($asins) {
        
    }
    
    public function get_supportedlistsources() {
        
    }
    
    public function retrieve_list($requestType, $nodeid, $keyword, $asin, $maxCount, $sortOrder, $filter) {
        
    }
    
    public function replace_trackingid($shopId, $url, $trackingId) {
        return $url;   
    }
    
    
} 

class subshop
{
    public $data = array();

        function __construct()
        {
            $this->logourl ='';
            $this->shopid ='';
            $this->programid ='';
            $this->title ='';
            $this->enabled = false;
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