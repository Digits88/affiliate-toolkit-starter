<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_amazon_helper_de {
     
     public function get_departments() {
         $departments = array();
         
            $departments['All'] = array(
               'caption' => __('Alle Kategorien', ATKP_PLUGIN_PREFIX)
              );
       $departments['UnboxVideo'] = array(
               'caption' => __('Amazon Instant Video', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   'date-desc-rank' => __('date-desc-rank', ATKP_PLUGIN_PREFIX), 
                   'popularity-rank' => __('popularity-rank', ATKP_PLUGIN_PREFIX), 
                   'price-asc-rank' => __('price-asc-rank', ATKP_PLUGIN_PREFIX), 
                   'price-desc-rank' => __('price-desc-rank', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'review-rank' => __('review-rank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Pantry'] = array(
               'caption' => __('Amazon Pantry', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['MobileApps'] = array(
               'caption' => __('Apps & Spiele', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'pmrank' => __('pmrank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Automotive'] = array(
               'caption' => __('Auto & Motorrad', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Baby'] = array(
               'caption' => __('Baby', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'psrank' => __('psrank', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Tools'] = array(
               'caption' => __('Baumarkt', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'featured' => __('featured', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Beauty'] = array(
               'caption' => __('Beauty', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Apparel'] = array(
               'caption' => __('Bekleidung', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Lighting'] = array(
               'caption' => __('Beleuchtung', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Books'] = array(
               'caption' => __('Bücher', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-pubdate' => __('-pubdate', ATKP_PLUGIN_PREFIX), 
                   '-publication_date' => __('-publication_date', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   '-unit-sales' => __('-unit-sales', ATKP_PLUGIN_PREFIX), 
                   'inverse-pricerank' => __('inverse-pricerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'pricerank' => __('pricerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['OfficeProducts'] = array(
               'caption' => __('Bürobedarf & Schreibwaren', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['PCHardware'] = array(
               'caption' => __('Computer & Zubehör', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'launch_date' => __('launch_date', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'psrank' => __('psrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['DVD'] = array(
               'caption' => __('DVD & Blu-ray', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['HealthPersonalCare'] = array(
               'caption' => __('Drogerie & Körperpflege', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Appliances'] = array(
               'caption' => __('Elektro-Großgeräte', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Electronics'] = array(
               'caption' => __('Elektronik & Foto', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['ForeignBooks'] = array(
               'caption' => __('Fremdsprachige Bücher', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-pubdate' => __('-pubdate', ATKP_PLUGIN_PREFIX), 
                   '-publication_date' => __('-publication_date', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   '-unit-sales' => __('-unit-sales', ATKP_PLUGIN_PREFIX), 
                   'inverse-pricerank' => __('inverse-pricerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'pricerank' => __('pricerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['VideoGames'] = array(
               'caption' => __('Games', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-date' => __('-date', ATKP_PLUGIN_PREFIX), 
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['HomeGarden'] = array(
               'caption' => __('Garten', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['GiftCards'] = array(
               'caption' => __('Geschenkgutscheine', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'date-desc-rank' => __('date-desc-rank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['PetSupplies'] = array(
               'caption' => __('Haustier', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-price-new-bin' => __('-price-new-bin', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'price-new-bin' => __('price-new-bin', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Photo'] = array(
               'caption' => __('Kamera & Foto', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['KindleStore'] = array(
               'caption' => __('Kindle-Shop', ATKP_PLUGIN_PREFIX),
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
       $departments['Classical'] = array(
               'caption' => __('Klassik', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-pubdate' => __('-pubdate', ATKP_PLUGIN_PREFIX), 
                   '-publication_date' => __('-publication_date', ATKP_PLUGIN_PREFIX), 
                   '-releasedate' => __('-releasedate', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'pubdate' => __('pubdate', ATKP_PLUGIN_PREFIX), 
                   'publication_date' => __('publication_date', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Luggage'] = array(
               'caption' => __('Koffer, Rucksäcke & Taschen ', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'date-desc-rank' => __('date-desc-rank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Kitchen'] = array(
               'caption' => __('Küche & Haushalt', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Grocery'] = array(
               'caption' => __('Lebensmittel & Getränke', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Music'] = array(
               'caption' => __('Musik-CDs & Vinyl', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-pubdate' => __('-pubdate', ATKP_PLUGIN_PREFIX), 
                   '-publication_date' => __('-publication_date', ATKP_PLUGIN_PREFIX), 
                   '-releasedate' => __('-releasedate', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'pubdate' => __('pubdate', ATKP_PLUGIN_PREFIX), 
                   'publication_date' => __('publication_date', ATKP_PLUGIN_PREFIX), 
                   'releasedate' => __('releasedate', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['MP3Downloads'] = array(
               'caption' => __('Musik-Downloads', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-albumrank' => __('-albumrank', ATKP_PLUGIN_PREFIX), 
                   '-artistalbumrank' => __('-artistalbumrank', ATKP_PLUGIN_PREFIX), 
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-releasedate' => __('-releasedate', ATKP_PLUGIN_PREFIX), 
                   '-runtime' => __('-runtime', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'albumrank' => __('albumrank', ATKP_PLUGIN_PREFIX), 
                   'artistalbumrank' => __('artistalbumrank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'runtime' => __('runtime', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['MusicalInstruments'] = array(
               'caption' => __('Musikinstrumente & DJ-Equipment', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank_authority' => __('reviewrank_authority', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Jewelry'] = array(
               'caption' => __('Schmuck', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Shoes'] = array(
               'caption' => __('Schuhe & Handtaschen', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-launch-date' => __('-launch-date', ATKP_PLUGIN_PREFIX), 
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Software'] = array(
               'caption' => __('Software', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-date' => __('-date', ATKP_PLUGIN_PREFIX), 
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Toys'] = array(
               'caption' => __('Spielzeug', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-date' => __('-date', ATKP_PLUGIN_PREFIX), 
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['SportingGoods'] = array(
               'caption' => __('Sport & Freizeit', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-release-date' => __('-release-date', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'release-date' => __('release-date', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Industrial'] = array(
               'caption' => __('Technik & Wissenschaft', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   'featured' => __('featured', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Watches'] = array(
               'caption' => __('Uhren', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       $departments['Magazines'] = array(
               'caption' => __('Zeitschriften', ATKP_PLUGIN_PREFIX),
               'sortvalues' => array(
                   '-price' => __('-price', ATKP_PLUGIN_PREFIX), 
                   '-titlerank' => __('-titlerank', ATKP_PLUGIN_PREFIX), 
                   '-unit-sales' => __('-unit-sales', ATKP_PLUGIN_PREFIX), 
                   'price' => __('price', ATKP_PLUGIN_PREFIX), 
                   'relevancerank' => __('relevancerank', ATKP_PLUGIN_PREFIX), 
                   'reviewrank' => __('reviewrank', ATKP_PLUGIN_PREFIX), 
                   'salesrank' => __('salesrank', ATKP_PLUGIN_PREFIX), 
                   'titlerank' => __('titlerank', ATKP_PLUGIN_PREFIX), 
                   )
              );
       
       return $departments;
     }
        
    }
?>