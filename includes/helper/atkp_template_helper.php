<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class atkp_template_helper 
{    
    public function formatFloat($number, $fallback, $shopid) {
        $currencysign= 1;
        
        if($shopid != '') {
            $shopids = explode('_', $shopid);
            $currencysign = ATKPTools::get_post_setting($shopids[0], ATKP_SHOP_POSTTYPE.'_currencysign');
        }
        
        $currencysymbol = 'EUR ';
        $currencysymbol2 = '';
        
        switch($currencysign) {
            default:
            case 1:
                $currencysymbol = '&euro; ';
                break;
            case 2:
                $currencysymbol = 'EUR ';
                break;
            case 3:
                $currencysymbol = '&#36 ';
                break;
            case 4:
                $currencysymbol = 'USD ';
                break;
            case 5:                
                return $fallback;
                break;
            case 6:
                $currencysymbol = ATKPTools::get_post_setting($shopids[0], ATKP_SHOP_POSTTYPE.'_currencysign_customprefix');
                $currencysymbol2 = ATKPTools::get_post_setting($shopids[0], ATKP_SHOP_POSTTYPE.'_currencysign_customsuffix');
                break;
        }
            
        if($number == (float)0 && $fallback != '') 
            $number = $this->price_to_float($fallback);
        
        return $currencysymbol. ''.number_format_i18n  ($number, 2 ) . ''.$currencysymbol2;
    }
    
    function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
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
	
	private static function replace_tracking_code($shopid, $url , $tracking_id) {
	    
	    if($tracking_id !='') {
	        
	        if($shopid =='')
 		        throw new exception('shop is empty (tracking-replace): '.$title);
 		        
 		    require_once ATKP_PLUGIN_DIR.'/includes/atkp_shop.php';
 		    
 		    $shop = atkp_shop::load($shopid);
 		    
            $url = $shop->provider->replace_trackingid($shop->settingid, $url, $tracking_id);   
	    }
	    
        return $url;
	}
	
	private static function create_external_link($url, $title, $productid, $listid, $templateid, $shopid, $linktype, $tracking_id ='') {
	    if(ATKPSettings::$open_window)
 		    $target='target="_blank"';
 		else
 		    $target='';
 		    
 		if($tracking_id !='') {
 		    //TODO: shop laden und replace mit trackingid aufrufen
 		    //TODO: Shop direkt von der übergeordneten methode übernehmen wenn vorhanden
 		    
            $url = self::replace_tracking_code($shopid, $url, $tracking_id);                      		    
 		}
 		
 		$shoptext ='';
        	   
	   if(is_numeric($shopid)) {
	       $shoptext .= get_the_title($shopid);
	   } else {
	     $shoptext =    $shopid;
	   }
	    
	    
        $link = 'href="'.$url.'" rel="nofollow" '.$target.' title="'.$title.'"';
	    
	    return $link;
	}
    
    public function createImagePlaceholderArray($myproduct,  $image, $itemIdx,$listid='',$templateid='', $tracking_id='') {
        $placeholders = array();
        
        $placeholders['title'] = $myproduct->title;
        $placeholders['listid'] = $listid;
        $placeholders['templateid'] = $templateid;
        $placeholders['shopid'] = $myproduct->shopid;
        $placeholders['productid'] = $myproduct->productid;
        
        $placeholders['smallimage'] ='<img src="'. $image->smallimageurl.'" alt="'.$myproduct->title.'" />';
        $placeholders['mediumimage'] ='<img src="'. $image->mediumimageurl.'" alt="'.$myproduct->title.'" />';
        $placeholders['image'] ='<img src="'. $image->largeimageurl.'" alt="'.$myproduct->title.'" />';
        
        $placeholders['smallimageurl'] = $image->smallimageurl;
        $placeholders['mediumimageurl'] = $image->mediumimageurl;
        $placeholders['imageurl'] = $image->largeimageurl;
            
        if(ATKPSettings::$access_mark_links == 1)
            $placeholders['mark'] =  '*';
        else 
            $placeholders['mark'] =  '';
            
        $target = '';
 		
 		if(ATKPSettings::$open_window)
 		    $target='target="_blank"';
 		else
 		    $target='';
 		    
 		$shop = $this->add_shop_info($myproduct->shopid, $placeholders);
 		
 		$placeholders['productlink'] = $this->create_external_link($myproduct->producturl, $myproduct->title, $myproduct->title, $listid, $templateid, $myproduct->shopid, 5, $tracking_id);
        
        if($myproduct->shopid != '')
            $this->add_acf_placeholder('acf_shop_', $myproduct->shopid, $placeholders);
        if($listid != '')
            $this->add_acf_placeholder('acf_list_', $listid, $placeholders);
        if($myproduct->productid != '')
            $this->add_acf_placeholder('acf_', $myproduct->productid, $placeholders);
            
        
        $placeholders = apply_filters('atkp_modify_image_placeholders', $placeholders);
            
        return $placeholders ;
    }
    
    public function createOfferPlaceholderArray($myproduct, $offer, $itemIdx,  $listid='',$templateid='') {
        $placeholders = array();
        
        $placeholders['listid'] = $listid;
        $placeholders['templateid'] = $templateid;
        $placeholders['shopid'] = $offer->shopid;
        $placeholders['productid'] = $myproduct->productid;
    
        if($offer->price == '')
            $placeholders['price'] =__('not available', ATKP_PLUGIN_PREFIX);
        else
            $placeholders['price']= sprintf(__('%s', ATKP_PLUGIN_PREFIX), $this->formatFloat($offer->pricefloat, $offer->price, $offer->shopid)); 
         
        if($offer->price == '')
            $placeholders['price_text'] =__('not available', ATKP_PLUGIN_PREFIX);
        else
            $placeholders['price_text']= sprintf(__('Price: %s', ATKP_PLUGIN_PREFIX), $this->formatFloat($offer->pricefloat, $offer->price, $offer->shopid)); 
            
        if(ATKPSettings::$access_mark_links == 1)
            $placeholders['mark'] =  '*';
        else 
            $placeholders['mark'] =  '';
            
        $target = '';
 		
 		if(ATKPSettings::$open_window)
 		    $target='target="_blank"';
 		else
 		    $target='';
 		
        if(ATKPSettings::$access_mark_links == 1)
            $placeholders['linkmark'] = '*';
        else
            $placeholders['linkmark'] = '';
            
        $placeholders['linktext'] = __('Buy now', ATKP_PLUGIN_PREFIX);
        
        $shop = $this->add_shop_info($offer->shopid, $placeholders);
                   
        $placeholders['productlink'] = $placeholders['link'] =  $this->create_external_link($offer->link, $shop->get_tooltip(), $myproduct->title, $listid, $templateid, $placeholders['shoptitle'], 2);
        
        $placeholders['producturl'] = $offer->link;
        $placeholders['producttitle'] = $offer->title;
        
        $placeholders['availability'] = $offer->availability;
        $placeholders['shipping'] = $this->formatFloat($offer->shippingfloat, $offer->shipping, $offer->shopid);
        
        $shipping = $offer->shippingfloat;
        $price = $offer->pricefloat;
        
        if($offer->shipping =='')
            $shipping = 0;
        if($offer->price == '')
            $price = 0;
        
        $placeholders['totalprice'] = $this->formatFloat($shipping + $price, $placeholders['price'], $offer->shopid);
        
        if($offer->shipping =='')
            $placeholders['shipping_text'] = __('Shipping: N/A', ATKP_PLUGIN_PREFIX);
        else
            $placeholders['shipping_text'] = sprintf(__('Shipping: %s', ATKP_PLUGIN_PREFIX), $this->formatFloat($offer->shippingfloat, $offer->shipping, $offer->shopid)); 
            
        if($offer->availability =='')
            $placeholders['availability_text'] = __('Availability: N/A', ATKP_PLUGIN_PREFIX);
        else
            $placeholders['availability_text'] = sprintf(__('Availability: %s', ATKP_PLUGIN_PREFIX), $offer->availability); 
 
        if($itemIdx == 1) {
            $placeholders['bestprice_badget'] = __('Bestprice', ATKP_PLUGIN_PREFIX);
        } else {
            $placeholders['bestprice_badget'] = '';
        }
 
        $placeholders['offerindex'] = $itemIdx;
        
        $placeholders = apply_filters('atkp_modify_offer_placeholders', $placeholders);
        
        return $placeholders ;
    }
    
    public function add_shop_info($shopId, &$placeholders) {
        require_once ATKP_PLUGIN_DIR.'/includes/atkp_shop.php';
                
        if($shopId == '' || $shopId == null) {
            $shop = null;
            
            $placeholders['shoplogo'] = '';
            $placeholders['smallshoplogo'] = '';
            $placeholders['shoptitle'] = '';
            $placeholders['shoplogourl'] ='';
            $placeholders['smallshoplogourl'] ='';
            
        } else {
            $shop = atkp_shop::load($shopId);
            
            if($shop->displayshoplogo) {
                $placeholders['shoplogo'] = '<img src="'. $shop->get_logourl().'" alt="'.$shop->get_title().'" />';
                $placeholders['smallshoplogo'] = '<img src="'. $shop->get_smalllogourl().'" alt="'.$shop->get_title().'" />';
            }
            else {
                $placeholders['shoplogo'] = '';
                $placeholders['smallshoplogo'] = '';
            }
            $placeholders['shoptitle'] = $shop->get_title();
            
            $placeholders['shoplogourl'] = $shop->get_logourl();
            $placeholders['smallshoplogourl'] =  $shop->get_smalllogourl();
            
            
            
            $this->add_acf_placeholder('acf_shop_', $shopId, $placeholders);
        }
        
        return $shop;
    }
                    
    public function createPlaceholderArray($myproduct, $itemIdx, $cssContainerClass, $cssElementClass, $content, $addtocart = 'notset', $listid='',$templateid='', $tracking_id='') {
        $placeholders = array();
        $shop = null;
        $myprovider = null;
        
        //hack für primelogo
        $amzCountry ='';
        $amzTag = '';
        
        $fallbackimage = $myproduct->smallimageurl;
        if($fallbackimage == '')
            $fallbackimage = $myproduct->mediumimageurl;
        if($fallbackimage == '')
            $fallbackimage = $myproduct->largeimageurl;
        
        if( $myproduct->smallimageurl == '')
             $myproduct->smallimageurl = $fallbackimage;
        if( $myproduct->mediumimageurl == '')
             $myproduct->mediumimageurl = $fallbackimage;
        if( $myproduct->largeimageurl == '')
             $myproduct->largeimageurl = $fallbackimage;
       
        $shop = $this->add_shop_info($myproduct->shopid, $placeholders);
       
        if($shop != null) {
            $myprovider = $shop->provider;
            $webservice = $shop->webservice;
            $amzCountry = $access_website =ATKPTools::get_post_setting($shop->settingid, ATKP_SHOP_POSTTYPE.'_access_website');
            $amzTag = $access_tracking_id =ATKPTools::get_post_setting($shop->settingid, ATKP_SHOP_POSTTYPE.'_access_tracking_id');
        }
               
               
            
         $placeholders['shopid'] = $myproduct->shopid;
         
         $placeholders['listid'] = $listid;
         $placeholders['listtitle'] = '';
         
         if($listid != '') {
             $list = get_post( $listid ); 
             if(isset($list))
                $placeholders['listtitle'] = $list->post_title;   
         }
         
         $placeholders['templateid'] = $templateid;
                
        if(ATKPSettings::$access_mark_links == 1)
            $placeholders['mark'] =  '*';
        else 
            $placeholders['mark'] =  '';
        
        $placeholders['title'] = $myproduct->title;
        
        if( ATKPSettings::$short_title_length > 0) {
            $placeholders['short_title'] = (strlen($myproduct->title) > ATKPSettings::$short_title_length) ? substr($myproduct->title,0,ATKPSettings::$short_title_length) :  $myproduct->title;
        } else {
            $placeholders['short_title'] = $myproduct->title;
        }
        
        $placeholders['detailtext'] = get_option(ATKP_PLUGIN_PREFIX.'_productpage_title', __('View Product', ATKP_PLUGIN_PREFIX));
        
        $placeholders['detailurl'] = '';
        $placeholders['detaillink'] = '';
        $placeholders['detailvisibility'] = 'visibility: collapse;';        
        
        if($myproduct->productid != null) {
            if(get_option(ATKP_PLUGIN_PREFIX.'_product_enabled', false) == true) {
                $placeholders['detailurl'] = get_permalink($myproduct->productid);
            } else {
                 $postid = ATKPTools::get_post_setting($myproduct->productid, ATKP_PRODUCT_POSTTYPE.'_postid');
                
                if($postid != null)  {
                    if(is_array($postid)) {
                        foreach($postid as $p) {
                            $placeholders['detailurl'] = get_permalink($p);
                            break;
                        }
                        
                    } else                         
                        $placeholders['detailurl'] = get_permalink($postid);
                }            
            }
        }    
        
        if($placeholders['detailurl'] != '') {
            $placeholders['detaillink'] = ' href="'.$placeholders['detailurl'].'" title="'.$myproduct->title.'"';
            $placeholders['detailvisibility'] = 'visibility: visible;';   
        }
        
        $placeholders['productid'] = $myproduct->productid;
        
        $placeholders['asin'] = $myproduct->asin;
        $placeholders['ean'] = $myproduct->ean;
        $placeholders['isbn']= $myproduct->isbn;
        $placeholders['brand']= $myproduct->brand;
        $placeholders['productgroup']= $myproduct->productgroup;
        $placeholders['availability'] = $myproduct->availability;
        $placeholders['shipping'] = $myproduct->shipping;
        
        //Offers section
        if(!is_array($myproduct->offers))
            $offers = array();
        else
            $offers = $myproduct->offers;
        
        $offer = new atkp_product_offer();
        $offer->shopid= $myproduct->shopid;
        $offer->link = $myproduct->producturl;
        $offer->price = $myproduct->saleprice;
        $offer->pricefloat =  $myproduct->salepricefloat;
            
        array_push($offers, $offer);
            
        require_once ATKP_PLUGIN_DIR.'/includes/atkp_shop.php';
        
        foreach($offers as $offer) {
            if($offer->shopid == '')
                continue;
            $shopOffer = atkp_shop::load($offer->shopid);
            
            if($shopOffer != null && $shopOffer->holdontop)
            {
                $offer->holdontop = true;
                break;    
            }
        }
            
        usort($offers, array($this, "sortPrice"));
        
        $offercount = 0;
        $minprice = null;
        $maxprice = null;
        foreach($offers as $offer) {
            if($offer->pricefloat == (float)0 || $offer->hideoffer)
                continue;
            
            if($offercount == 0)
                $minprice = $offer;
            else
                $maxprice = $offer;
                
            $offercount++;
        }
        if($offercount > 0) {
            $placeholders['offerscount'] = $offercount == 1 ? sprintf(__('%s offer', ATKP_PLUGIN_PREFIX), $offercount) : sprintf(__('%s offers', ATKP_PLUGIN_PREFIX), $offercount);   
            if($minprice != null) {
                $placeholders['minprice'] = $this->formatFloat($minprice->pricefloat, $minprice->price, $minprice->shopid);
                $placeholders['minprice_url'] = $minprice->link;
            }
            if($maxprice != null) {
                $placeholders['maxprice'] = $this->formatFloat($maxprice->pricefloat, $maxprice->price, $maxprice->shopid);            
                $placeholders['maxprice_url'] = $maxprice->link;
            }
        }
          
        //offers section
       
        
        for ($i = 1; $i <= 5; $i++) {
            $placeholders['thumbimages_'.$i] ='';
			$placeholders['mediumimages_'.$i] = '';
            $placeholders['images_'.$i] = '';
        }
        
        $idx = 1;
        if(is_array($myproduct->images))
            foreach ($myproduct->images as $newimage ) {
                
                if($myproduct->smallimageurl == '')
                    $myproduct->smallimageurl = $newimage->smallimageurl;
                if($myproduct->mediumimageurl == '')
                    $myproduct->mediumimageurl = $newimage->mediumimageurl;
                if($myproduct->largeimageurl == '')
                    $myproduct->largeimageurl = $newimage->largeimageurl;
                    
                $placeholders['thumbimages_'.$idx] ='<img src="'. $newimage->smallimageurl.'" alt="'.$myproduct->title.'" />';
                $placeholders['mediumimages_'.$idx] ='<img src="'. $newimage->mediumimageurl.'" alt="'.$myproduct->title.'" />';
                $placeholders['images_'.$idx] ='<img src="'. $newimage->largeimageurl.'" alt="'.$myproduct->title.'" />';                
                
               $idx += 1; 
            }
        
        if($myproduct->smallimageurl == '')
            $myproduct->smallimageurl = plugins_url(__('../../images/image-not-found.jpg', ATKP_PLUGIN_PREFIX), __FILE__ );
        if($myproduct->mediumimageurl == '')
            $myproduct->mediumimageurl = plugins_url(__('../../images/image-not-found.jpg', ATKP_PLUGIN_PREFIX), __FILE__ );
        if($myproduct->largeimageurl == '')
            $myproduct->largeimageurl = plugins_url(__('../../images/image-not-found.jpg', ATKP_PLUGIN_PREFIX), __FILE__ );
        
        $placeholders['smallimageurl'] = $myproduct->smallimageurl;
        $placeholders['mediumimageurl'] = $myproduct->mediumimageurl;
        $placeholders['largeimageurl'] = $myproduct->largeimageurl;
        
        $placeholders['smallimage'] = '<img src="'. $myproduct->smallimageurl.'" alt="'.$myproduct->title.'" />';
        $placeholders['mediumimage'] ='<img src="'.  $myproduct->mediumimageurl.'" alt="'.$myproduct->title.'" />';
        $placeholders['largeimage'] = '<img src="'. $myproduct->largeimageurl.'" alt="'.$myproduct->title.'" />';        
            
        if($myproduct->manufacturer != '')
            $placeholders['by_text'] = sprintf(__('by %s', ATKP_PLUGIN_PREFIX), $myproduct->manufacturer);
        else if ($myproduct->author != '')
            $placeholders['by_text'] =  sprintf(__('by %s', ATKP_PLUGIN_PREFIX),$myproduct->author);
        else
            $placeholders['by_text'] ='';
 		
 		$target = '';
 		
 		if(ATKPSettings::$open_window)
 		    $target='target="_blank"';
 		else
 		    $target='';
 		
 		$linkttitle = $myproduct->title;
 		
 		if($shop != null) {
 		    $linkttitle =  $shop->get_tooltip();
 		}
 		
 		$placeholders['productlink'] = $this->create_external_link($myproduct->producturl, $linkttitle, $myproduct->title, $listid, $templateid, $myproduct->shopid , 1, $tracking_id);
            
 		
 		    if($myproduct->addtocarturl == '')
 		        $placeholders['cartlink'] =$placeholders['productlink'];
 		    else
			    $placeholders['cartlink'] = $this->create_external_link($myproduct->addtocarturl, $linkttitle, $myproduct->title, $listid, $templateid, $myproduct->shopid , 3, $tracking_id);
            
            $placeholders['producturl'] = self::replace_tracking_code($myproduct->shopid, $myproduct->producturl, $tracking_id);          
            $placeholders['customerreviewsurl'] = self::replace_tracking_code($myproduct->shopid, $myproduct->customerreviewurl, $tracking_id);  
            
            $listurl = '';
            
            if($listid != '') {
                $listurl =  ATKPTools::get_post_setting( $listid, ATKP_LIST_POSTTYPE.'_listurl');  
             
                $this->add_acf_placeholder('acf_list_', $listid, $placeholders);
            }
            
            if($listurl != '') {
                $placeholders['hidelistlink'] ='';
                $placeholders['listlink'] = 'href="'.$listurl.'" rel="nofollow" '.$target.' title="'. __('Show me more products', ATKP_PLUGIN_PREFIX).'"';            
                $placeholders['listurl'] = $listurl;        
                $placeholders['listlinktext']       = __('Show me more products', ATKP_PLUGIN_PREFIX);                
                
            } else {
                $placeholders['hidelistlink'] = 'style="display:none"';
                $placeholders['listurl']  ='';
                $placeholders['listlink'] ='';
                 $placeholders['listlinktext'] ='';
            }
            
            
            $buttontype = get_option(ATKP_PLUGIN_PREFIX.'_add_to_cart', 'link');
                    
            
            if($addtocart != '' && $addtocart != 'notset') 
                $buttontype = $addtocart;
            
            //fallback falls cartlink leer ist
            if($buttontype == 'addtocart' && $placeholders['cartlink'] == '')
                $buttontype = 'link';
            
            //falls die produktseiten nicht aktiv sind, fallback auf produktlink
            if($buttontype == 'product' && $placeholders['detaillink'] == '')
                $buttontype = 'link';
            
            if(ATKPSettings::$access_mark_links == 1)
                $placeholders['linkmark'] = '*';
            else
                $placeholders['linkmark'] = '';
            
            switch($buttontype) {
                case '1':
                case 'addtocart':
                    $placeholders['link'] = $placeholders['cartlink'];
                    
                    if(!isset($shop))
                        $placeholders['linktext'] = __('Add to cart', ATKP_PLUGIN_PREFIX);
                    else {
                     
                        $placeholders['linktext'] = $shop->get_addtocart() != '' ? $shop->get_addtocart() : __('Add to Amazon Cart', ATKP_PLUGIN_PREFIX);
                    }
                    
                    break;
                default:
                case 'link':
                    $placeholders['link'] = $placeholders['productlink'];
                
                    if(!isset($shop))
                        $placeholders['linktext'] = __('Buy now', ATKP_PLUGIN_PREFIX);
                    else {
                     
                        $placeholders['linktext'] = $shop->get_buyat() != '' ? $shop->get_buyat() : __('Buy now at Amazon', ATKP_PLUGIN_PREFIX);
                    }
                
                    break;
                case 'product':
                    $placeholders['link'] = $placeholders['detaillink'];
                    $placeholders['linktext'] = $placeholders['detailtext'];
                    $placeholders['linkmark'] = '';
                    break;                
            }
            
            
            $titletype = get_option(ATKP_PLUGIN_PREFIX.'_title_link_type', 'link');
            
            if($titletype == 'product' && $placeholders['detaillink'] == '')
                $titletype = 'link';
            
            if(ATKPSettings::$access_mark_links == 1)
                $placeholders['titlelinkmark'] = '*';
            else
                $placeholders['titlelinkmark'] = '';
            
            switch($titletype) {
                default:
                case 'link':
                    $placeholders['titlelink'] = $placeholders['productlink'];
                    break;
                case 'product':
                    $placeholders['titlelink'] = $placeholders['detaillink'];
                    $placeholders['titlelinkmark'] = '';
                    break;                
            }
            
            
            if(($itemIdx > 3 && ATKPSettings::$bestsellerribbon == 1) || $itemIdx <= 0)
                $placeholders['bestseller_text'] = '';
            else
                $placeholders['bestseller_text'] = sprintf(__('#%s Best Seller', ATKP_PLUGIN_PREFIX), $itemIdx);
                
            if(($itemIdx > 3 && ATKPSettings::$bestsellerribbon == 1) || $itemIdx <= 0)
                $placeholders['bestseller_number'] = '';
            else
                $placeholders['bestseller_number'] = sprintf(__('#%s', ATKP_PLUGIN_PREFIX), $itemIdx);
            
             $placeholders['reviewsurl'] =$myproduct->reviewsurl;
            
            $placeholders['reviewcount2'] = '';
            
            if(ATKPSettings::$showstarrating) {
                
                if($myproduct->rating =='')
                    $myproduct->rating = 0;
                    
                if($myproduct->rating == 0 && get_option(ATKP_PLUGIN_PREFIX.'_hideemptystars',0)) {
                    $placeholders['rating'] = '';
                    $placeholders['star_rating'] = '';
                } else {                
                    $class = 'atkp-star-' . number_format($this->roundRate($myproduct->rating), 1, ' atkp-star-0', '');
                        
                    $placeholders['rating'] = sprintf(__('%s out of 5 stars', ATKP_PLUGIN_PREFIX), $myproduct->rating);
                    $placeholders['star_rating'] = '<span class="atkp-star '.$class.'" title="'.$placeholders['rating'].'"></span>';
                    $placeholders['reviewcount2'] = $placeholders['rating'];
                }
            } else {
                $placeholders['rating'] = '';
                $placeholders['star_rating'] = '';
                
            }
            
            if($myproduct->reviewcount == '')
                $myproduct->reviewcount = 0;
            
            if( $myproduct->isownreview) {
                $reviewstext = __('Show review', ATKP_PLUGIN_PREFIX);
                
                if(ATKPSettings::$showrating)
                    $placeholders['reviewcount'] =  $reviewstext;
                
                if($myproduct->reviewsurl != '' && ATKPSettings::$showrating) {                   
                    
                    $placeholders['reviewslink'] = 'href="'.$myproduct->reviewsurl.'" title="'.$reviewstext.'"';
                    $placeholders['markrating'] = '';
                } else {
                    $placeholders['reviewslink'] ='';
//                    $placeholders['reviewcount'] ='';
                    $placeholders['markrating'] = '';   
                }            
            }
            else {
                $reviewstextNull = __('Show customer reviews', ATKP_PLUGIN_PREFIX);
                $reviewstext = __('%s customer reviews', ATKP_PLUGIN_PREFIX);
			    $reviewstext2 = __('1 customer review', ATKP_PLUGIN_PREFIX);
                
                $placeholders['reviewcount'] ='';
                
                if(ATKPSettings::$showrating) {
                    if($myproduct->reviewcount == '' || $myproduct->reviewcount == 0) {
                        
                        if(get_option(ATKP_PLUGIN_PREFIX.'_hideemptyrating',0))
                            $placeholders['reviewcount'] = $reviewstextNull = '';
                        else
                            $placeholders['reviewcount'] = $reviewstextNull;
                            
                    } else {                    
						$placeholders['reviewcount'] = sprintf(_n($reviewstext2, $reviewstext, $myproduct->reviewcount, ATKP_PLUGIN_PREFIX), $myproduct->reviewcount);
						
					}
                }
                
                if($myproduct->customerreviewurl != '' && ATKPSettings::$showrating && $placeholders['reviewcount'] != '') {
                    $placeholders['reviewslink'] = $this->create_external_link($myproduct->customerreviewurl, $placeholders['reviewcount'], $myproduct->title, $listid, $templateid, $myproduct->shopid , 4, $tracking_id);
                    $placeholders['markrating'] = $placeholders['mark'] ;
                } else {         
                    $placeholders['reviewslink'] ='';
                    
                    if($placeholders['reviewcount'] == $reviewstextNull)
                        $placeholders['reviewcount'] ='';
                    $placeholders['markrating'] = '';                    
                }
                
            }
            
            if($myproduct->isprime && ATKPSettings::$showprice) {
                //special hack amazon
                //https://www.amazon.de/gp/prime/?primeCampaignId=prime_assoc_ft&tag=neunzehn90com-21&camp=4510&creative=670002&linkCode=ur1&adid=07VBBZ76N7ZKENHMQCDR
                
                $primelink = 'https://www.amazon.'.$amzCountry.'/gp/prime/?primeCampaignId=prime_assoc_ft&tag='.$amzTag.'&camp=4510&creative=670002&linkCode=ur1&adid=07VBBZ76N7ZKENHMQCDR';
                                
                $placeholders['prime_icon'] = '<img src="'.plugins_url('images/prime_amazon.png', ATKP_PLUGIN_FILE).'" alt="'.__('Prime', ATKP_PLUGIN_PREFIX).'"/>';
            
                if(ATKPSettings::$linkprime)
                    $placeholders['prime_icon'] = '<a href="'.$primelink.'" rel="nofollow" target="_blank" title="'.__('More about prime', ATKP_PLUGIN_PREFIX).'">'.$placeholders['prime_icon'].'</a>';
                
            } else {
                $placeholders['prime_icon'] = '';   
            }
            
            if($myproduct->percentagesaved == '' || $myproduct->percentagesaved == 0 || !ATKPSettings::$showpricediscount) {
                $placeholders['save_percentage'] = '';
                $placeholders['save_percentage_'] = '';
            } else {
                $placeholders['save_percentage'] = '-'.$myproduct->percentagesaved.'%';
                $placeholders['save_percentage_']  = '('.$placeholders['save_percentage'].')';
            }
            
            if($myproduct->amountsaved == '' || !ATKPSettings::$showpricediscount) {
                $placeholders['save_text'] ='';
                $placeholders['save_amount'] = '';

            }
            else {
                $placeholders['save_amount'] = $myproduct->amountsaved;

                if($myproduct->percentagesaved != '' && $myproduct->percentagesaved != '0')
                    $perc = ' (%s)';
                else
                    $perc ='';
                    
                $placeholders['save_text'] = sprintf(__('You Save: %s', ATKP_PLUGIN_PREFIX).$perc,  $this->formatFloat( $myproduct->amountsavedfloat,  $myproduct->amountsaved, $myproduct->shopid), $myproduct->percentagesaved.'%');
            }
            if($myproduct->listprice == '' || $myproduct->listpricefloat == 0 || $myproduct->listpricefloat <= $myproduct->salepricefloat || !ATKPSettings::$showprice  || !ATKPSettings::$showpricediscount)
                $placeholders['listprice_text'] ='';
            else
                $placeholders['listprice_text'] = sprintf(__('List Price: %s', ATKP_PLUGIN_PREFIX), $this->formatFloat( $myproduct->listpricefloat,  $myproduct->listprice, $myproduct->shopid)); 
         
            if($myproduct->listprice == '' || $myproduct->listpricefloat == 0 || $myproduct->listpricefloat <= $myproduct->salepricefloat || !ATKPSettings::$showprice)
                $placeholders['listprice'] ='';
            else
                $placeholders['listprice'] = sprintf(__('%s', ATKP_PLUGIN_PREFIX), $myproduct->listprice); 
                
            if($myproduct->saleprice == '' || !ATKPSettings::$showprice)
                $placeholders['price'] =__('not available', ATKP_PLUGIN_PREFIX);
            else
                $placeholders['price']= sprintf(__('%s', ATKP_PLUGIN_PREFIX),  $this->formatFloat( $myproduct->salepricefloat,  $myproduct->saleprice, $myproduct->shopid)); 
         
         if(!ATKPSettings::$showprice)
         $placeholders['price_text'] ='';
           else if($myproduct->saleprice == '')
                $placeholders['price_text'] =__('not available', ATKP_PLUGIN_PREFIX);
            else
                $placeholders['price_text']= sprintf(__('Price: %s', ATKP_PLUGIN_PREFIX),  $this->formatFloat( $myproduct->salepricefloat,  $myproduct->saleprice, $myproduct->shopid));          
         
            //if($myproduct->totalnew == '' || $myproduct->totalnew == 0)
            //    $placeholders['newfrom_text'] ='';
            //else
            //    $placeholders['newfrom_text'] = sprintf(__('%s new from %s', ATKP_PLUGIN_PREFIX), $myproduct->totalnew, $myproduct->lowestnewprice);          
         
         
             if( $myproduct->outputashtml) {
                 
                $descclean = $myproduct->description;
                $featureclean = $myproduct->features;
             }else {     
                 
                 $desclength = ATKPSettings::$description_length == '0' || ATKPSettings::$description_length == '' ? 400 : ATKPSettings::$description_length;
                 $featurecount = ATKPSettings::$feature_count == '0' || ATKPSettings::$feature_count == '' ? 10 : ATKPSettings::$feature_count;
                 
                if($myproduct->features == '')
                    $featureRows = array();
                else {         
                    $featureRows = explode('<li>', $myproduct->features);         
                    $featureRows = array_map('strip_tags', $featureRows);
                }
                        
                $descclean = strip_tags($myproduct->description);
                $descclean = (strlen($descclean) > $desclength) ? substr($descclean,0,$desclength).'...' :  $descclean;
                $featureclean = '';
                $cnt = 0;
                foreach($featureRows as $featureRow) {
                    if($featureRow == '')
                        continue;
                    
                    $featureclean .= '<li>'.$featureRow.'</li>';
                    $cnt++;
                    
                    if($cnt >= $featurecount)
                        break;
                }
                
                if(count($featureRows) <= 1)
                    $featureclean = strip_tags($myproduct->features);
                    
                if($featureclean !='')
                    if(count($featureRows) > 1)
                        $featureclean = '<ul>'.$featureclean.'</ul>';
            }        
         
            switch(ATKPSettings::$boxcontent) {
                default:
                case '1':
                    
                    if($myproduct->features == '') {
                        $placeholders['info_text'] = $descclean;
                    } else {
                         $placeholders['info_text'] = $featureclean;
                    }
                    
                    break;
                case '2':
                    $placeholders['info_text'] = $featureclean;
                    break;
                case '3':
                    $placeholders['info_text'] = $descclean;
                    break;
                
            }
            
          
            
            
            $placeholders['features_text'] = $myproduct->features;
            $placeholders['description_text'] = $myproduct->description;
            
            $placeholders['priceinfo_text'] = __('Price incl. VAT., Excl. Shipping', ATKP_PLUGIN_PREFIX);
            $placeholders['cssclass'] = $cssElementClass;
            $placeholders['content'] = $content;
            

        $this->add_acf_placeholder('acf_', $myproduct->productid, $placeholders);
            
        
        $placeholders['refresh_date'] = date_i18n( get_option( 'date_format' ), $myproduct->updatedon);
        $placeholders['refresh_time'] = date_i18n( get_option( 'time_format' ), $myproduct->updatedon);
        
        
        $placeholders['disclaimer'] = str_replace('%refresh_time%', $placeholders['refresh_time'], str_replace('%refresh_date%', $placeholders['refresh_date'], ATKPSettings::$access_disclaimer_text));
        
        $placeholders = apply_filters('atkp_modify_placeholders', $placeholders);
 
        return $placeholders;
    }
    
    public function add_acf_placeholder($prefix, $postId, &$placeholders) {
        
        
        return $placeholders;
    }
    
    public function createFilterPlaceholderArray($submitText) {
        $placeholders = array();
      
        require_once  ATKP_PLUGIN_DIR.'/includes/atkp_udfield.php';
        $newfields = atkp_udfield::load_fields();
       
        //echo '<option value="1">' . __('Text', ATKP_PLUGIN_PREFIX) . '</option>';
        //echo '<option value="2">' . __('Multiline', ATKP_PLUGIN_PREFIX) . '</option>';
        //echo '<option value="3">' . __('Dropdown', ATKP_PLUGIN_PREFIX) . '</option>';
        //echo '<option value="4">' . __('Yes/No', ATKP_PLUGIN_PREFIX) . '</option>';
        //echo '<option value="5">' . __('Html', ATKP_PLUGIN_PREFIX) . '</option>';

         require_once  ATKP_PLUGIN_DIR.'/includes/helper/atkp_control_helper.php';
        $helper = new atkp_control_helper();

        $placeholders['orderby'] = $helper->create_frontendcontrol(null, null, 'orderby', '');

        //combo mit dropdown
        $placeholders['productid1'] = $helper->create_frontendcontrol(null, null, 'productid1', '');	
        $placeholders['productid2'] = $helper->create_frontendcontrol(null, null, 'productid2', '');	
        $placeholders['productid3'] = $helper->create_frontendcontrol(null, null, 'productid3', '');	
        $placeholders['productid4'] = $helper->create_frontendcontrol(null, null, 'productid4', '');	
        
        //range
        $placeholders['rating'] ='';
        //range
        $placeholders['price'] ='';

        foreach ($newfields as $newfield ) {
            $fieldname = 'customfield_'.$newfield->name; 

            switch($newfield->type) {
                case 3:
                case 4:
                    //dropdown & yes no
                    $placeholders[$fieldname] = $helper->create_frontendcontrol($newfield, null, $fieldname, '');
                    break;
                case 1:
                    //range
                    if($newfield->format == 'number')
                        $placeholders[$fieldname] =$helper->create_frontendcontrol($newfield, null, $fieldname, '');
                    break;
            }
        }    
        
        $taxonomies = atkp_udtaxonomy::load_taxonomies();
        
        if($taxonomies != null) 
            foreach($taxonomies as $taxonomy) {
                if($taxonomy->showui) {						 
					$fieldname = 'customtaxonomy_'.$taxonomy->name; 
				} else {
					$fieldname = $taxonomy->name; 
				}
				
				//combo mit dropdown
				$placeholders[$fieldname] = $helper->create_frontendcontrol(null, $taxonomy, $fieldname, '');		   						
            }
      
        return $placeholders;  
     }
    
    public function getPlaceholders($fieldtype = '') {
        require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product.php';
        require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product_image.php';
        
        $placeholders = $this->createPlaceholderArray(new atkp_product(), 1, '', '', false);
        
        $newfields = array();
        $taxonomies = array();
         if(ATKP_PLUGIN_VERSION >= 30) {
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_udfield.php';
                $newfields = atkp_udfield::load_fields();
                
                $taxonomies = atkp_udtaxonomy::load_taxonomies();                
         }            
        
        $acfgroups = null;
        
        if(function_exists('get_fields')) {
            $args = array(
    			'posts_per_page' => -1,
    			'post_type'      => 'acf',
    		);
    		$field_types = apply_filters('wcf_acf_field_type', array('text','textarea','select','checkbox','radio','true_false' ));
    
    		$acfgroups = get_posts( $args );    
        }
        
        $myplaceholders = array();
        
        $filterplaceholders = array();
                                            
        foreach ( array_keys($placeholders) as $placeholder ) { 
            
            switch($placeholder) {
                case 'bestseller_number':
                case 'bestseller_text':
                case 'cartlink':
                case 'content':
                case 'cssclass': 
                case 'hidelistlink':
                case 'info_text':
                case 'link':
                case 'linktext':
                case 'mark':
                case 'markrating':
                case 'productid':                                                                       
                case 'productlink':
                case 'reviewslink':
                case 'listurl':
                case 'listlink':
                case 'listlinktext':
                case 'reviewcount2':
                case 'priceinfo_text':
                case 'shopid':
                case 'listid':
                case 'templateid':
                case 'moreoffers':
                case 'shoplogourl':
                case 'smallshoplogourl':
                    break;
                case 'refresh_date':
                    $myplaceholders[$placeholder] =  __('Refresh date', ATKP_PLUGIN_PREFIX);  
                    break;
                case 'refresh_time':
                    $myplaceholders[$placeholder] =  __('Refresh time', ATKP_PLUGIN_PREFIX);  
                    break;
                case 'shipping':
                    $myplaceholders[$placeholder] =  __('Shipping', ATKP_PLUGIN_PREFIX);  
                    break;
                case 'shoptitle':
                    $myplaceholders[$placeholder] =  __('Shop Title', ATKP_PLUGIN_PREFIX);  
                    break;
                case 'smallshoplogo':
                    $myplaceholders[$placeholder] =  __('Small Shop Logo', ATKP_PLUGIN_PREFIX);  
                    break;
                case 'shoplogo':
                    $myplaceholders[$placeholder] =  __('Shop Logo', ATKP_PLUGIN_PREFIX);  
                    break;
                case 'title':
                    $myplaceholders[$placeholder] =  __('Title', ATKP_PLUGIN_PREFIX);  
                    break;    
                case 'short_title':
                    $myplaceholders[$placeholder] =  __('Title Short', ATKP_PLUGIN_PREFIX);  
                    break;     
                case 'asin':
                    $myplaceholders[$placeholder] =  __('ASIN', ATKP_PLUGIN_PREFIX);  
                    break;   
                case 'isbn':
                    $myplaceholders[$placeholder] =  __('ISBN', ATKP_PLUGIN_PREFIX);  
                    break;   
                case 'ean':
                    $myplaceholders[$placeholder] =  __('EAN', ATKP_PLUGIN_PREFIX);  
                    break;
                case 'brand':
                    $myplaceholders[$placeholder] =  __('Brand', ATKP_PLUGIN_PREFIX);  
                    break;   
                case 'productgroup':
                    $myplaceholders[$placeholder] =  __('Productgroup', ATKP_PLUGIN_PREFIX);  
                    break;  
                case 'availability':
                    $myplaceholders[$placeholder] =  __('Availability', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'smallimageurl':
                    $myplaceholders[$placeholder] =  __('Small image URL', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'mediumimageurl':
                    $myplaceholders[$placeholder] =  __('Medium image URL', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'largeimageurl':
                    $myplaceholders[$placeholder] =  __('Large image URL', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'smallimage':
                    $myplaceholders[$placeholder] =  __('Small image', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'mediumimage':
                    $myplaceholders[$placeholder] =  __('Medium image', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'largeimage':
                    $myplaceholders[$placeholder] =  __('Large image', ATKP_PLUGIN_PREFIX);  
                    break; 
                     
                case 'thumbimages_1':
                case 'thumbimages_2':
                case 'thumbimages_3':
                case 'thumbimages_4':
                case 'thumbimages_5':
                case 'thumbimages_6':
                    $splitted = explode('_',$placeholder);
                    $myplaceholders[$placeholder] =  sprintf(__('Small image %s', ATKP_PLUGIN_PREFIX),$splitted[1]);  
                    break; 
                case 'mediumimages_1':
                case 'mediumimages_2':
                case 'mediumimages_3':
                case 'mediumimages_4':
                case 'mediumimages_5':
                case 'mediumimages_6':
                    $splitted = explode('_',$placeholder);
                    $myplaceholders[$placeholder] =  sprintf(__('Medium image %s', ATKP_PLUGIN_PREFIX) ,$splitted[1]); 
                    break; 
                case 'images_1':
                case 'images_2':
                case 'images_3':
                case 'images_4':
                case 'images_5':
                case 'images_6':
                    $splitted = explode('_',$placeholder);
                    $myplaceholders[$placeholder] =  sprintf(__('Large image %s', ATKP_PLUGIN_PREFIX),$splitted[1]); 
                    break; 
                case 'by_text':
                    $myplaceholders[$placeholder] =  __('"by"-Text', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'producturl':
                    $myplaceholders[$placeholder] =  __('Product page URL', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'customerreviewsurl':
                    $myplaceholders[$placeholder] =  __('Customer Reviews URL', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'reviewsurl':
                    $myplaceholders[$placeholder] =  __('Review URL', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'rating':
                    $myplaceholders[$placeholder] =  __('Rating', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'star_rating':
                    $myplaceholders[$placeholder] =  __('Star Rating', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'reviewcount':
                    $myplaceholders[$placeholder] =  __('Review count', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'prime_icon':
                    $myplaceholders[$placeholder] =  __('Is prime', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'save_percentage':
                    $myplaceholders[$placeholder] =  __('Percentage saved', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'save_percentage_':
                    $myplaceholders[$placeholder] =  __('(Percentage saved)', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'save_text':
                    $myplaceholders[$placeholder] =  __('You Save', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'save_amount':
                    $myplaceholders[$placeholder] =  __('Amount saved', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'listprice':
                    $myplaceholders[$placeholder] =  __('Listprice', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'listprice_text':
                    $myplaceholders[$placeholder] =  __('Listprice (Text)', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'price':
                    $myplaceholders[$placeholder] =  __('Price', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'price_text':
                    $myplaceholders[$placeholder] =  __('Price (Text)', ATKP_PLUGIN_PREFIX);  
                    break;
                case 'features_text':
                    $myplaceholders[$placeholder] =  __('Features', ATKP_PLUGIN_PREFIX);  
                    break; 
                case 'description_text':
                    $myplaceholders[$placeholder] =  __('Description', ATKP_PLUGIN_PREFIX);  
                    break; 
                default:
                    $myplaceholders[$placeholder] = $placeholder;  
                    
                    if($this->startsWith($placeholder, 'acf_') && function_exists('get_fields')) {
                        $newacfname = str_replace('acf_shop_', '', $placeholder);
                        $newacfname = str_replace('acf_list_', '', $newacfname);
                        $newacfname = str_replace('acf_', '', $newacfname);
                    
                        
                		if ($acfgroups) {
                			foreach ( $acfgroups as $group ) {
                				$group_fields = apply_filters('acf/field_group/get_fields', array(), $group->ID);
                				if (is_array($group_fields)) {
                					foreach ( $group_fields as $field ) {
                						if($field['name'] == $newacfname) {
                						    $myplaceholders[$placeholder] = $field['label'];
                						    break;
                						}
                						
                					}
                				}
                			}
                		}
                    
                    }
                    
                    if($newfields != null)
                       foreach ($newfields as $newfield ) {
                           if('customfield_'.$newfield->name == $placeholder) {
                                $myplaceholders[$placeholder] = $newfield->caption;
                                
                                if($fieldtype != '' && $fieldtype == 'html' && $newfield->type == 5)
                                    $filterplaceholders[$placeholder] = $newfield->caption;
                                
                                break;    
                           }
                       }
                       
					$taxonomies = atkp_udtaxonomy::load_taxonomies();
                
					   
				   if($taxonomies != null)
					   foreach ($taxonomies as $taxonomy ) {
						   $fieldname = '';
						   
						   if($taxonomy->showui) {						 
								$fieldname = 'customtaxonomy_'.$taxonomy->name; 
							} else {
								$fieldname = $taxonomy->name; 
							}
						   
						   if($fieldname == $placeholder)
								$myplaceholders[$placeholder] = $taxonomy->caption == '' ? $taxonomy->name : $taxonomy->caption;
					   }
                    break;
            }   
        }
        
        if($fieldtype != '')
            return $filterplaceholders;
        else
            return $myplaceholders;        
    }
    
    public function createOutput($products, $content='', $template='', $cssContainerClass = '', $cssElementClass = '', $addtocart = '', $listid= '', $hidedisclaimer = 0, $templatetypedefault = 0, $tracking_id= '') {
            
        $myheader ='';
        $body_header ='';
        $detail_header ='';
        $detail_footer ='';
        $customdisclaimer = null;
        
        $mytemplate = '';
        $body_footer ='';
        $myfooter ='';
        $disclaimer = '';
        $disclaimerclass ='';

        $templatetype = 1;
        $includemainoffer = false;
        $filtertarget = null;
        
        if(ATKPSettings::$access_show_disclaimer) 
            $disclaimer = ATKPSettings::$access_disclaimer_text;

        if(is_numeric($template) && ATKP_PLUGIN_VERSION >= 30) {
            
             $templatefound = get_post($template);

            if(isset($templatefound) && $templatefound != null && ($templatefound->post_status == 'publish' || $templatefound->post_status == 'draft')) {
                $templatetype = ATKPTools::get_post_setting($templatefound->ID, ATKP_TEMPLATE_POSTTYPE.'_template_type', true );
                $disabledisclaimer = ATKPTools::get_post_setting($templatefound->ID, ATKP_TEMPLATE_POSTTYPE.'_disabledisclaimer', true );
                
                $myheader = html_entity_decode(ATKPTools::get_post_setting($templatefound->ID, ATKP_TEMPLATE_POSTTYPE.'_header', true ));
                $myfooter = html_entity_decode(ATKPTools::get_post_setting($templatefound->ID, ATKP_TEMPLATE_POSTTYPE.'_footer', true ));    
                
                switch($templatetype) {
                    case 4:
                        //vergleichstabelle
                        break;
                    default:
                        
                        $detail_header = html_entity_decode(ATKPTools::get_post_setting($templatefound->ID, ATKP_TEMPLATE_POSTTYPE.'_detail_header', true ));
                        $detail_footer = html_entity_decode(ATKPTools::get_post_setting($templatefound->ID, ATKP_TEMPLATE_POSTTYPE.'_detail_footer', true ));
                        $body_header = html_entity_decode(ATKPTools::get_post_setting($templatefound->ID, ATKP_TEMPLATE_POSTTYPE.'_body_header', true ));
                        $mytemplate = html_entity_decode(ATKPTools::get_post_setting($templatefound->ID, ATKP_TEMPLATE_POSTTYPE.'_body', true ));
                        
                        $body_footer = html_entity_decode(ATKPTools::get_post_setting($templatefound->ID, ATKP_TEMPLATE_POSTTYPE.'_body_footer', true ));
                        
                        
                        if($templatetype == 2)
                            $includemainoffer = ATKPTools::get_post_setting($templatefound->ID, ATKP_TEMPLATE_POSTTYPE.'_includemainoffer', true );
                
                        if($templatetype == 5)
                            $filtertarget = get_page_uri(ATKPTools::get_post_setting($templatefound->ID, ATKP_TEMPLATE_POSTTYPE.'_filtertarget' ));
                        break;                    
                }
                                
                $disclaimerclass = 'atkp-disclaimer-'.$templatefound->ID;            
                
                if($disabledisclaimer)
                     $hidedisclaimer = true;
            } else
                return 'template not found: ' . $template;        
            
        }else {
            
            $templatepath = get_template_directory().'/atkp-templates/'.$template.'.php';
            //wenn die datei im aktuellen theme directory nicht vorhanden ist, greifen wir auf das at-verzeichnis zu
            if(!file_exists($templatepath)) 
                $templatepath = ATKP_TEMPLATEDIR.'/'.$template.'.php';
            //existiert die vorlage auch nicht im atkp-verzeichnis dann fallback auf wide
            if(!file_exists($templatepath) && file_exists(ATKP_TEMPLATEDIR.'/wide.php')) {
                $template = 'wide';
                $templatepath = ATKP_TEMPLATEDIR.'/'.$template.'.php';
            }
            
            $templateclass = 'atkp_template_'.$template;
            
            if(file_exists($templatepath)) {
                
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_default_template_base.php';
                require_once  $templatepath;
                
                $defaulttemplate = new $templateclass();
                $body_header = $defaulttemplate->get_header();
                $detail_header = $defaulttemplate->get_detail_header();
                $mytemplate = $defaulttemplate->get_detail();
                $detail_footer = $defaulttemplate->get_detail_footer();
                $myfooter =  $defaulttemplate->get_footer();
                                                
                if($defaulttemplate->get_hidedisclaimer() != null)
                    $hidedisclaimer = (bool)$defaulttemplate->get_hidedisclaimer();
                 if($defaulttemplate->get_includemainoffer() != null)
                    $includemainoffer = (bool)$defaulttemplate->get_includemainoffer();
                if($defaulttemplate->get_templatetype() != null)
                    $templatetype = intval($defaulttemplate->get_templatetype());
                if($defaulttemplate->get_customdisclaimer() != null)
                    $customdisclaimer = $defaulttemplate->get_customdisclaimer();
                                
                //special hack für die schmale box
                if($template == 'box') {
                    $disclaimerclass = 'atkp-small-disclaimer';
                        
                    if(strpos($cssElementClass, 'atkp-widget') !== false)
                        $disclaimerclass .= ' atkp-widget';
                }
            } else
                return 'template not found: ' . $template;        
        }
       
        if($templatetypedefault != 0)
            $templatetype = $templatetypedefault;
        
        //file_put_contents(ATKP_PLUGIN_DIR.'/'.$template.'.html', $myheader. $mytemplate.$myfooter);
        
        $resultValue = '<div class="atkp-container '.$cssContainerClass.'">';
        
        $firstproduct = null;
        $firstplaceholders = null;
        
        if(sizeof($products) > 0)
        {
            $firstproduct  =$products[0];
         
            //if($templatetype != 2 && $templatetype != 3)    
            //schlag mich dafür!!
            $bak = ATKPSettings::$show_moreoffers;
            ATKPSettings::$show_moreoffers = false;
            $firstplaceholders = $this->createPlaceholderArray($firstproduct, 1,  $cssContainerClass, $cssElementClass, $content, $addtocart, $listid, $template, $tracking_id);  
            ATKPSettings::$show_moreoffers = $bak;
            //else
            //    $firstplaceholders = array();
            
            $firstplaceholders['refresh_date'] = date_i18n( get_option( 'date_format' ), $firstproduct->updatedon);
            $firstplaceholders['refresh_time'] = date_i18n( get_option( 'time_format' ), $firstproduct->updatedon);
        }
            
        
        $productplaceholders = array();
        $hideoutput = false;
        
        
        $count = 1;
        foreach($products as $myproduct) {    
            
            switch($templatetype) {
                case 5:
                    
                    $placeholders = $this->createFilterPlaceholderArray('');
                    $hidedisclaimer = true;
                    break;
                default:
                case 4:
                case 1:
                    //product
                    $placeholders = $this->createPlaceholderArray($myproduct, $count, $cssContainerClass, $cssElementClass, $content, $addtocart, $listid, $template, $tracking_id);  
                    
                    array_push($productplaceholders, $placeholders);
                    $count = $count+1;
                    break;
                case 2:
                     
                    //offer
                    if(is_array($myproduct->offers) || $includemainoffer) {
                        if(!is_array($myproduct->offers))
                            $offers = array();
                        else
                            $offers = $myproduct->offers;
                        
                        if($includemainoffer && $myproduct->shopid != '') {
                         
                            $offer = new atkp_product_offer();
                            $offer->id =uniqid();
                            $offer->type = 2;
                            $offer->shopid= $myproduct->shopid;
                            $offer->number =  $myproduct->asin;
                            
                            $offer->shipping = $myproduct->shipping;
                            $offer->availability = $myproduct->availability;
                
                            $offer->price = $myproduct->saleprice;
                            $offer->pricefloat =  $myproduct->salepricefloat;
                            $offer->shippingfloat =  $myproduct->shippingfloat;
                            
                            
                            $offer->link = $myproduct->producturl;
                            $offer->title = $myproduct->title;
                                
                            array_push($offers, $offer);
                            
                        }
                        
                        require_once ATKP_PLUGIN_DIR.'/includes/atkp_shop.php';
                        
                        foreach($offers as $offer) {
                            if($offer->shopid == '')
                                continue;
                            $shopOffer = atkp_shop::load($offer->shopid);
                            
                            if($shopOffer != null && $shopOffer->holdontop)
                            {
                                $offer->holdontop = true;
                                break;    
                            }
                        }
                        
                        usort($offers, array($this, "sortPrice"));
                        
                        
                        //nur der erste shop wird angezeigt... so werden duplikate aufgrund zweier eans verhindert
                        $shopsadded = array();
                        
                        foreach($offers as $offer) {
                            if($offer->pricefloat == (float)0 || $offer->hideoffer || in_array($offer->shopid, $shopsadded))
                                continue;
                            
                            $placeholders = $this->createOfferPlaceholderArray($myproduct, $offer, $count, $listid, $template);  
                            if($placeholders != null) {
                                array_push($productplaceholders, $placeholders);
                                $count = $count+1;
                                
                                array_push($shopsadded, $offer->shopid);
                            }
                        }
                    }
                    
                    if($count <= 1)
                            $hideoutput = true;
                    break;
                case 3:
                    //image
                    if(is_array($myproduct->images)) {
                        foreach($myproduct->images as $image) {
                            $placeholders = $this->createImagePlaceholderArray($myproduct, $image, $count, $listid, $template);  
                            array_push($productplaceholders, $placeholders);
                            $count = $count+1;
                        }
                    }
                     if($count <= 1)
                            $hideoutput = true;
                    
                    break;                    
            }
        }
        
        //*** header ersetzen
        if($myheader != '' && $firstplaceholders != null) {
            foreach(array_keys($firstplaceholders) as $key){
                    $myheader = str_replace('%'.strtolower($key).'%', $firstplaceholders[$key], $myheader);
                }    
            
            $resultValue .= $myheader;
        }
        //header ersetzen ***
        
        switch($templatetype) {
            default:
                //klassische vorlagen ersetzen
                
                //*** detailheader, detail und detailfooter ersetzen
                
                $headerrows = explode('{SYS_APPEND}', $detail_header);
                $templaterows = explode('{SYS_APPEND}', $mytemplate);
                $footerrows = explode('{SYS_APPEND}', $detail_footer);
                
                $idx = 0;
                foreach($templaterows as $templaterow) {    
                    $resultrow = '';
                    foreach($productplaceholders as $placeholders) {    
                        $result = $templaterow;
        
                        // Search & Replace placeholders
                        foreach(array_keys($placeholders) as $key){
                            $result = str_replace('%'.strtolower($key).'%', $placeholders[$key], $result);
                        }
                        $resultrow .= $result;
                        
                    }
                    
                    $resultValue .= $body_header. (isset($headerrows[$idx]) ? $headerrows[$idx] : ''). $resultrow .(isset($footerrows[$idx]) ? $footerrows[$idx] : '').$body_footer;
                    
                    $idx = $idx+1;
                }
                
                //detailheader, detail und detailfooter ersetzen***                
               
                break;
            case 4:
                //vergleichstabelle generieren
                $resultValue .= $this->generateCompareTable($template, $productplaceholders);
                
                break;
            case 5:
                
                $result = $mytemplate;
                
                $result = '<form action="'.$filtertarget.'" method="get">'. $mytemplate. '</form>';
                
                foreach(array_keys($placeholders) as $key){
                    $result = str_replace('%'.strtolower($key).'%', $placeholders[$key], $result);
                }
                
                $resultValue .= $result;
                break;
                
        }
        
         //*** footer ersetzen
        if($myfooter != '' && $firstplaceholders != null) {    
            foreach(array_keys($firstplaceholders) as $key){
                    $myfooter = str_replace('%'.strtolower($key).'%', $firstplaceholders[$key], $myfooter);
                }    
            $resultValue .= $myfooter;
        }
        //footer ersetzen*** 
        
        //*** disclaimer ersetzen
        if($disclaimer != '' && $firstproduct != null && !$hidedisclaimer) {
            if($customdisclaimer != null)
                $disclaimer = $customdisclaimer;
            
            foreach(array_keys($firstplaceholders) as $key){
                        $disclaimer = str_replace('%'.strtolower($key).'%', $firstplaceholders[$key], $disclaimer);
                    }  
            
            if($customdisclaimer != null)
                $resultValue = $disclaimer;
            else
                $resultValue .= '<span class="atkp-disclaimer '.$disclaimerclass.'">'.$disclaimer.'</span>';
        }
        //disclaimer ersetzen*** 
    	    	
    	$resultValue .= '</div>';
    	
    	if($firstproduct == null)
    	    $resultValue ='';
    	
    	//remove empty a tags from output
    	
        //$resultValue=	preg_replace("/<a>(.*?)<\/a>/", "$1", $resultValue);
        	
    	if($hideoutput == true)
    	    return '';
    	else	
            return str_replace(array('<a ></a>', '()'), '', $resultValue); 
    }
    
    private function generateCompareTable($templateid, $productplaceholders) {
        require_once  ATKP_PLUGIN_DIR.'/includes/atkp_template_comparevalue.php';
        $comparevalues = atkp_template_comparevalue::load_comparevalues($templateid); 
        
        $viewtype = ATKPTools::get_post_setting($templateid, ATKP_TEMPLATE_POSTTYPE.'_viewtype');
        //$viewtype 1 == mobile & desktop + responsive | 2 == desktop || 3 == mobile
        
        $resultValue ='';
        
        if($viewtype == 1 || $viewtype == 2) {
            $maxproducts = ATKPTools::get_post_setting($templateid, ATKP_TEMPLATE_POSTTYPE.'_maxproducts');
            $horizontalscrollbars = ATKPTools::get_post_setting( $templateid, ATKP_TEMPLATE_POSTTYPE.'_horizontalscrollbars');
            $hideheaders=  ATKPTools::get_post_setting( $templateid, ATKP_TEMPLATE_POSTTYPE.'_hideheaders');
        
            $scrollbarstyle ='';
            
            //enable horizontal scrollbar
            if($horizontalscrollbars)
                $scrollbarstyle = 'class="atkp-producttable-wrapper"';
            
            
            $hideheaderclass ='';
            if($hideheaders)
                $hideheaderclass = 'atkp-producttable-leftborder';
                    
            $rowidx = 0;
            $resultValue .= '<div '.$scrollbarstyle.'><table class=" atkp-cleartable atkp-producttable '.($viewtype == 1 ? 'atkp-producttable-resp' : '').' '.$hideheaderclass.'">';
            
            foreach($comparevalues as $comparerow) {    
                if($comparerow->viewtype == 3)
                    continue;
                
                $resultrow = '';
                $borderclass = '';
                
                if($rowidx == 0)
                    $borderclass='atkp-producttable-lefttop';
                
                $caption = $comparerow->caption;
                
                if($comparerow->description != '') {
                    $caption = '<div class="atkp-tooltip">'.$comparerow->caption.'<span class="atkp-tooltiptext">'.$comparerow->description.'</span></div>';
                }
                
                //enable columnheadercaption oder style class zuweisen die links border zeigt
                if(!$hideheaders)
                    $resultrow .= '<td class="atkp-producttable-header '.$borderclass.'">'.$caption.'</td>';
                
                    
                $align = '';
                    
                switch($comparerow->align) {
                    case 1:
                        //left
                        $align= 'atkp-producttable-left';
                        break;
                    case 2:
                        //center
                        $align = 'atkp-producttable-center';
                        break;
                    case 3:
                        //right
                        $align = 'atkp-producttable-right';
                        break;
                    case 4:
                        //stretch
                        $align = 'atkp-producttable-stretch';
                        break;
                }
            
                $containspredicate = false;
                
                foreach($productplaceholders as $placeholders) 
                    if($placeholders['predicate_id'] != ''){
                        $containspredicate = true;
                        break;   
                    }
                
                $prodIdx = 0;
                foreach($productplaceholders as $placeholders) {    
                    $result = $comparerow->detail;
                    if($rowidx == 0 && $containspredicate)
                        $result = '<div class="atkp-predicateheadline atkp-predicate-highlight%predicate_id%" style="height:35px"><span>%predicate_text%</span></div>'.$result;
                    
    
                    // Search & Replace placeholders
                    foreach(array_keys($placeholders) as $key){
                        $result = str_replace('%'.strtolower($key).'%', $placeholders[$key], $result);
                    }
                    
                    $highlight ='';
                    
                    if($rowidx+1 >= count($comparevalues))
                        $highlight .= ' atkp-producttable-lastrow';
                    
                    if($placeholders['predicate_id'] != '') {
                        $highlight .= ' atkp-producttable-highlight'.$placeholders['predicate_id'];
                        if($rowidx+1 >= count($comparevalues))
                            $highlight .= ' atkp-producttable-bottom-highlight'.$placeholders['predicate_id'];
                    }
                    
                    
                    //TODO: 100 / produkteanzahl + 1 ergibt spaltenbreite
                    $resultrow .= '<td class="atkp-producttable-detail '.$align.' '.$highlight.' '.$comparerow->cssclass.'" >'.$result.'</td>';
                    $prodIdx = $prodIdx+1;
                    
                    if($maxproducts > 0 && $prodIdx >= $maxproducts)
                        break;
                }
                
                $resultValue .= '<tr  class="atkp-producttable-row">'.$resultrow.'</tr>';
                
                $rowidx = $rowidx+1;
            }   
            $resultValue .= '</table></div>';
            
        }
        
        if($viewtype == 1 || $viewtype == 3) {
            $maxmobileproducts = ATKPTools::get_post_setting($templateid, ATKP_TEMPLATE_POSTTYPE.'_maxmobileproducts');
            $body = html_entity_decode(ATKPTools::get_post_setting($templateid, ATKP_TEMPLATE_POSTTYPE.'_mobilebody'));
            
            $resultValue .= '<div class="atkp-mobileproducttable '.($viewtype == 1 ? 'atkp-mobileproducttable-resp' : '').'">';
            
            $prodIdx = 0;
            foreach($productplaceholders as $placeholders) {
                
                $mobile = '<table class="atkp-cleartable">';
            
                foreach($comparevalues as $comparerow) {  
                    
                    if($comparerow->viewtype == 2)
                        continue;
                    
                     $caption = $comparerow->caption;
                    
                    if($comparerow->description != '') {
                        $caption = '<div class="atkp-tooltip">'.$comparerow->caption.'<span class="atkp-tooltiptext">'.$comparerow->description.'</span></div>';
                    }
                    
                    $mobile .= '<tr>';
                    $mobile .= '<td class="atkp-mobileproducttable-header">'.$caption.'</td>';
                    
                     $align = '';
                    
                    switch($comparerow->align) {
                        case 1:
                            //left
                            $align= 'atkp-producttable-left';
                            break;
                        case 2:
                            //center
                            $align = 'atkp-producttable-center';
                            break;
                        case 3:
                            //right
                            $align = 'atkp-producttable-right';
                            break;
                        case 4:
                            //stretch
                            $align = 'atkp-producttable-stretch';
                            break;
                    }
                    
                    $mobile .= '<td class="atkp-mobileproducttable-detail '.$align.' '.$comparerow->cssclass.'" >'.$comparerow->detail.'</td>';
                    $mobile .= '</tr>';
                }
                
                $mobile .= '</table>';
                
                
                $result = $body;
                
                $result = str_replace('%mobiletable%', $mobile, $result);
                
                // Search & Replace placeholders
                foreach(array_keys($placeholders) as $key){
                    $result = str_replace('%'.strtolower($key).'%', $placeholders[$key], $result);
                }
                
                
                
                //$resultValue .= '<div class="atkp-box">';
                
                $resultValue .= $result;                
                
                //$resultValue .= '</div>';
                
                $prodIdx = $prodIdx+1;                
                if($maxmobileproducts > 0 && $prodIdx >= $maxmobileproducts)
                    break;
            }
            
            $resultValue .= '</div>';
        }
                       
        return $resultValue;   
    }
    
    private function sortPrice($a, $b)
    {
        if($a->holdontop || $b->holdontop)
            return $a->holdontop ? -1 : 1;
        
        if ($a->pricefloat == $b->pricefloat) {
            return 0;
        }
        return ($a->pricefloat < $b->pricefloat) ? -1 : 1;
    }

    
    private function roundRate($rate)
    {
        $rate = round(($rate * 2), 0) / 2;
        return $rate;
    }
  
    
}


?>