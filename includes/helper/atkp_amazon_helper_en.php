<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_amazon_helper_en {
     
     public function get_departments() {
         $departments = array();
         
                   $departments['All'] = array(
               'caption' => __('All Departments', ATKP_PLUGIN_PREFIX)
              );
       $departments['UnboxVideo'] = array(
               'caption' => __('Amazon Instant Video', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-launch-date' => __('-launch-date', ATKP_PLUGIN_PREFIX), 
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-video-release-date' => __('-video-release-date', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Appliances'] = array(
               'caption' => __('Appliances', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'pmrank' => __('pmrank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['MobileApps'] = array(
               'caption' => __('Apps & Games', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'pmrank' => __('pmrank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['ArtsAndCrafts'] = array(
               'caption' => __('Arts, Crafts & Sewing', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'pmrank' => __('pmrank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Automotive'] = array(
               'caption' => __('Automotive', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Baby'] = array(
               'caption' => __('Baby', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'psrank' => __('psrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Beauty'] = array(
               'caption' => __('Beauty', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-launch-date' => __('-launch-date', ATKP_PLUGIN_PREFIX), 
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'pmrank' => __('pmrank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'sale-flag' => __('sale-flag', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Books'] = array(
               'caption' => __('Books', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-publication_date' => __('-publication_date', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   '-unit-sales' => __('-unit-sales', ATKP_PLUGIN_PREFIX), 
                   'daterank' => __('daterank', ATKP_PLUGIN_PREFIX), 
                   'inverse-pricerank' => __('inverse-pricerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'pricerank' => __('pricerank', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Music'] = array(
               'caption' => __('CDs & Vinyl', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-orig-rel-date' => __('-orig-rel-date', ATKP_PLUGIN_PREFIX), 
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-releasedate' => __('-releasedate', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'artistrank' => __('artistrank', ATKP_PLUGIN_PREFIX), 
                   'orig-rel-date' => __('orig-rel-date', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'psrank' => __('psrank', ATKP_PLUGIN_PREFIX), 
                   'release-date' => __('release-date', ATKP_PLUGIN_PREFIX), 
                   'releasedate' => __('releasedate', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Wireless'] = array(
               'caption' => __('Cell Phones & Accessories', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'daterank' => __('daterank', ATKP_PLUGIN_PREFIX), 
                   'inverse-pricerank' => __('inverse-pricerank', ATKP_PLUGIN_PREFIX), 
                   'pricerank' => __('pricerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Fashion'] = array(
               'caption' => __('Clothing, Shoes & Jewelry', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'launch-date' => __('launch-date', ATKP_PLUGIN_PREFIX), 
                   'popularity-rank' => __('popularity-rank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['FashionBaby'] = array(
               'caption' => __('Clothing, Shoes & Jewelry - Baby', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'launch-date' => __('launch-date', ATKP_PLUGIN_PREFIX), 
                   'popularity-rank' => __('popularity-rank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['FashionBoys'] = array(
               'caption' => __('Clothing, Shoes & Jewelry - Boys', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'launch-date' => __('launch-date', ATKP_PLUGIN_PREFIX), 
                   'popularity-rank' => __('popularity-rank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['FashionGirls'] = array(
               'caption' => __('Clothing, Shoes & Jewelry - Girls', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'launch-date' => __('launch-date', ATKP_PLUGIN_PREFIX), 
                   'popularity-rank' => __('popularity-rank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['FashionMen'] = array(
               'caption' => __('Clothing, Shoes & Jewelry - Men', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'launch-date' => __('launch-date', ATKP_PLUGIN_PREFIX), 
                   'popularity-rank' => __('popularity-rank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['FashionWomen'] = array(
               'caption' => __('Clothing, Shoes & Jewelry - Women', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'launch-date' => __('launch-date', ATKP_PLUGIN_PREFIX), 
                   'popularity-rank' => __('popularity-rank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Collectibles'] = array(
               'caption' => __('Collectibles & Fine Arts', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['PCHardware'] = array(
               'caption' => __('Computers', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'psrank' => __('psrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['MP3Downloads'] = array(
               'caption' => __('Digital Music', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-releasedate' => __('-releasedate', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Electronics'] = array(
               'caption' => __('Electronics', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'pmrank' => __('pmrank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['GiftCards'] = array(
               'caption' => __('Gift Cards', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Grocery'] = array(
               'caption' => __('Grocery & Gourmet Food', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   'inverseprice' => __('inverseprice', ATKP_PLUGIN_PREFIX), 
                   'launch-date' => __('launch-date', ATKP_PLUGIN_PREFIX), 
                   'pricerank' => __('pricerank', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'sale-flag' => __('sale-flag', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['HealthPersonalCare'] = array(
               'caption' => __('Health & Personal Care', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   'inverseprice' => __('inverseprice', ATKP_PLUGIN_PREFIX), 
                   'launch-date' => __('launch-date', ATKP_PLUGIN_PREFIX), 
                   'pmrank' => __('pmrank', ATKP_PLUGIN_PREFIX), 
                   'pricerank' => __('pricerank', ATKP_PLUGIN_PREFIX), 
                   'sale-flag' => __('sale-flag', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['HomeGarden'] = array(
               'caption' => __('Home & Kitchen', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Industrial'] = array(
               'caption' => __('Industrial & Scientific', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'pmrank' => __('pmrank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['KindleStore'] = array(
               'caption' => __('Kindle Store', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-edition-sales-velocity' => __('-edition-sales-velocity', ATKP_PLUGIN_PREFIX), 
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'daterank' => __('daterank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Luggage'] = array(
               'caption' => __('Luggage & Travel Gear', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'launch-date' => __('launch-date', ATKP_PLUGIN_PREFIX), 
                   'popularity-rank' => __('popularity-rank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Magazines'] = array(
               'caption' => __('Magazine Subscriptions', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-publication_date' => __('-publication_date', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   '-unit-sales' => __('-unit-sales', ATKP_PLUGIN_PREFIX), 
                   'daterank' => __('daterank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'subslot-salesrank' => __('subslot-salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Movies'] = array(
               'caption' => __('Movies & TV', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-release-date' => __('-release-date', ATKP_PLUGIN_PREFIX), 
                   'featured' => __('featured', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['MusicalInstruments'] = array(
               'caption' => __('Musical Instruments', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-launch-date' => __('-launch-date', ATKP_PLUGIN_PREFIX), 
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'pmrank' => __('pmrank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'sale-flag' => __('sale-flag', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['OfficeProducts'] = array(
               'caption' => __('Office Products', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'pmrank' => __('pmrank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['LawnAndGarden'] = array(
               'caption' => __('Patio, Lawn & Garden', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['PetSupplies'] = array(
               'caption' => __('Pet Supplies', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevance' => __('relevance', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Pantry'] = array(
               'caption' => __('Prime Pantry', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Software'] = array(
               'caption' => __('Software', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'pmrank' => __('pmrank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['SportingGoods'] = array(
               'caption' => __('Sports & Outdoors', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'inverseprice' => __('inverseprice', ATKP_PLUGIN_PREFIX), 
                   'launch-date' => __('launch-date', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'pricerank' => __('pricerank', ATKP_PLUGIN_PREFIX), 
                   'relevance-fs-rank' => __('relevance-fs-rank', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'sale-flag' => __('sale-flag', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Tools'] = array(
               'caption' => __('Tools & Home Improvement', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'pmrank' => __('pmrank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Toys'] = array(
               'caption' => __('Toys & Games', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-age-min' => __('-age-min', ATKP_PLUGIN_PREFIX), 
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'pmrank' => __('pmrank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['VideoGames'] = array(
               'caption' => __('Video Games', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'pmrank' => __('pmrank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Wine'] = array(
               'caption' => __('Wine', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'featured' => __('featured', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewscore' => __('reviewscore', ATKP_PLUGIN_PREFIX), 
                   )
              );
     
       return $departments;
     }
        
    }
?>