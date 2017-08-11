<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('ATKP_EVENT', strtolower(ATKP_PLUGIN_PREFIX).'_event');
define('ATKP_CHECK', strtolower(ATKP_PLUGIN_PREFIX).'_check');
define('ATKP_CSVIMPORT', strtolower(ATKP_PLUGIN_PREFIX).'_csvimport');

define('ATKP_SHOP_POSTTYPE', strtolower(ATKP_PLUGIN_PREFIX).'_shop');
define('ATKP_LIST_POSTTYPE', strtolower(ATKP_PLUGIN_PREFIX).'_list');
define('ATKP_PRODUCT_POSTTYPE', strtolower(ATKP_PLUGIN_PREFIX).'_product');
define('ATKP_TEMPLATE_POSTTYPE', strtolower(ATKP_PLUGIN_PREFIX).'_template');

define('ATKP_SHORTCODE', strtolower(ATKP_PLUGIN_PREFIX).'_shortcode');
define('ATKP_LIST_SHORTCODE', strtolower(ATKP_PLUGIN_PREFIX).'_list');
define('ATKP_PRODUCT_SHORTCODE', strtolower(ATKP_PLUGIN_PREFIX).'_product');
define('ATKP_FILTERLIST_SHORTCODE', strtolower(ATKP_PLUGIN_PREFIX).'_filterlist');
define('ATKP_WIDGET', strtolower(ATKP_PLUGIN_PREFIX).'_widget');

define('ATKP_LOGFILE', ATKP_PLUGIN_DIR.'/log/log.txt');
define('ATKP_TEMPLATEDIR', ATKP_PLUGIN_DIR.'/templates');


define('ATKP_SUBSHOPTYPE', '-1');

//** Credits im Kopf erzeugen **//
if(ATKP_PLUGIN_VERSION < 40) {
    add_action( 'wp_head', 'my_affiliate_toolkit_tags' );

    function my_affiliate_toolkit_tags() {
        echo "\r\n".'<!-- '.__('This page uses Affiliate Toolkit', ATKP_PLUGIN_PREFIX).' / https://www.affiliate-toolkit.com -->'."\r\n\r\n";
    }
}

class ATKPSettings {
    //Plugin-Prefix: Affiliate Toolkit Plugin (atkp)
  
    
    public static $access_csv_intervall;
  
    /*public static $access_key;
    public static $access_secret_key;
    public static $access_message;
    public static $access_website;
    public static $access_tracking_id;*/
    public static $access_cache_duration;
    public static $access_mark_links;
    public static $access_show_disclaimer;
    public static $access_disclaimer_text;
    /*public static $load_customer_reviews;*/
	public static $add_to_cart;
	public static $open_window;
	
	public static $enable_ssl;
	
	public static $show_linkinfo;
	public static $linkinfo_template;
	
	public static $check_enabled;
	public static $notification_interval;
	public static $email_recipient;
    public static $short_title_length;
    
    public static $show_moreoffers;
    public static $moreoffers_template;
    
    public static $list_default_count;
    public static $feature_count;
    public static $description_length;
    public static $boxcontent;
  
    public static $boxstyle;
    public static $bestsellerribbon;
    public static $showprice;
    public static $showpricediscount;
    public static $showstarrating;
    public static $showrating;
    
    public static $jslink;
    public static $linktracking;
    public static $linkprime;
  
        /**
     * Returns current plugin version.
     * 
     * @return string Plugin version
     */
    public static function plugin_get_version() {
    
    	$plugin_data = get_plugin_data(ATKP_PLUGIN_FILE);
        $plugin_version = $plugin_data['Version'];
        return $plugin_version;
    }
  
    public static function load_settings() {
        ATKPSettings::$linktracking = get_option(ATKP_PLUGIN_PREFIX.'_link_click_tracking', 0);
        
        ATKPSettings::$access_cache_duration = get_option(ATKP_PLUGIN_PREFIX.'_cache_duration', 1440);
        ATKPSettings::$access_mark_links = get_option(ATKP_PLUGIN_PREFIX.'_mark_links', 1);
                     
        ATKPSettings::$access_show_disclaimer = get_option(ATKP_PLUGIN_PREFIX.'_show_disclaimer', 0);
        ATKPSettings::$access_disclaimer_text= get_option(ATKP_PLUGIN_PREFIX.'_disclaimer_text');        
        
        ATKPSettings::$add_to_cart = get_option(ATKP_PLUGIN_PREFIX.'_add_to_cart', 0);
        ATKPSettings::$open_window = get_option(ATKP_PLUGIN_PREFIX.'_open_window', 1);
        
        ATKPSettings::$show_linkinfo = get_option(ATKP_PLUGIN_PREFIX.'_show_linkinfo', 0);
        ATKPSettings::$linkinfo_template = get_option(ATKP_PLUGIN_PREFIX.'_linkinfo_template');
        
        
        ATKPSettings::$access_csv_intervall = get_option(ATKP_PLUGIN_PREFIX.'_access_csv_intervall', 1440);
        
        ATKPSettings::$check_enabled = get_option(ATKP_PLUGIN_PREFIX.'_check_enabled');
        ATKPSettings::$notification_interval = get_option(ATKP_PLUGIN_PREFIX.'_notification_interval', 4320);
        ATKPSettings::$email_recipient = get_option(ATKP_PLUGIN_PREFIX.'_email_recipient');
        
        ATKPSettings::$short_title_length = get_option(ATKP_PLUGIN_PREFIX.'_short_title_length',0);
        
        ATKPSettings::$show_moreoffers = get_option(ATKP_PLUGIN_PREFIX.'_show_moreoffers',0);
        ATKPSettings::$moreoffers_template = get_option(ATKP_PLUGIN_PREFIX.'_moreoffers_template','');
     
     
        ATKPSettings::$list_default_count = get_option(ATKP_PLUGIN_PREFIX.'_list_default_count',0);
        ATKPSettings::$feature_count = get_option(ATKP_PLUGIN_PREFIX.'_feature_count',0);
        ATKPSettings::$description_length = get_option(ATKP_PLUGIN_PREFIX.'_description_length',0);
        ATKPSettings::$boxcontent = get_option(ATKP_PLUGIN_PREFIX.'_boxcontent','');
     
        ATKPSettings::$boxstyle = get_option(ATKP_PLUGIN_PREFIX.'_boxstyle',1);
        ATKPSettings::$bestsellerribbon = get_option(ATKP_PLUGIN_PREFIX.'_bestsellerribbon',1);
        ATKPSettings::$showprice = get_option(ATKP_PLUGIN_PREFIX.'_showprice',1);
        ATKPSettings::$linkprime = get_option(ATKP_PLUGIN_PREFIX.'_linkprime',0);
        ATKPSettings::$jslink = get_option(ATKP_PLUGIN_PREFIX.'_jslink',0);
        
        
        
        
        ATKPSettings::$showpricediscount = get_option(ATKP_PLUGIN_PREFIX.'_showpricediscount',1);
        ATKPSettings::$showstarrating = get_option(ATKP_PLUGIN_PREFIX.'_showstarrating',1);
        ATKPSettings::$showrating = get_option(ATKP_PLUGIN_PREFIX.'_showrating',1);
        
        $loglevel = get_option(ATKP_PLUGIN_PREFIX.'_loglevel','off');
        
        ATKPLog::Init(ATKP_LOGFILE, $loglevel);
        
        
        $crontype = get_option(ATKP_PLUGIN_PREFIX.'_crontype', 'wpcron');
            
        switch($crontype) {
            default:
            case 'wpcron':
                if(!ATKPTools::exists_cron_job(ATKP_EVENT, false)) {
                    ATKPTools::show_notice(sprintf(__('<span style="font-weight:bold">affiliate-toolkit:</span> cronjob is not running. Check the <a href="%s">settings page</a>.', ATKP_PLUGIN_PREFIX), admin_url('admin.php?page=ATKP_affiliate_toolkit-plugin')), 'error');
                }
                break;
            case 'external':
            case 'externaloutput':
                //nothing todo...
                break;                
        }  
        
        
    }
  
}

class ATKPTools {
    
    public static function has_subshops($shoptype) {
        return $shoptype == '2' || $shoptype == '3' || $shoptype == '5' || $shoptype== '8';
    }
    
    public static function has_eanpricecompare($shoptype) {
        return $shoptype != '8';
    }
    
    public static function create_list($title,$shopid,$listtype,$searchterm, $department ='', $sortby ='', $loadmoreoffers =false) 
    {
        //throw new exception($listtype);
        global $user_ID;
        $new_post = array(
            'post_title' => $title,
            'post_status' => 'publish',
            'post_author' => $user_ID,
            'post_type' => ATKP_LIST_POSTTYPE,
        );
        $post_id = wp_insert_post($new_post);
        
        ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_shopid', $shopid);
        ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_source', $listtype);
        
        
        ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_preferlocalproduct', true);
        ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_loadmoreoffers', $loadmoreoffers);
        ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_search_department', $department == '' ? 'All' : $department);
        if($listtype == '10' || $listtype == '11') {
            ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_node_id', $searchterm);
            ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_search_limit', 10);            
            
        }
        else {
            ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_search_keyword', $searchterm);
            ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_search_limit', 20);
        }
        
        if($sortby != '')
            ATKPTools::set_post_setting( $post_id, ATKP_LIST_POSTTYPE.'_search_orderby', $sortby);
        
        return $post_id;
    }    
        
    public static function create_product($title,$shopid,$asin, $status, $asintype ='ASIN')
    {
        global $user_ID;
        $new_post = array(
            'post_title' => $title,
            'post_status' => $status == 'publish' ? 'publish' : 'draft',
            'post_author' => $user_ID,
            'post_type' => ATKP_PRODUCT_POSTTYPE,
        );
        $post_id = wp_insert_post($new_post);
        
        if($status == 'woo') 
            ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_iswoocommerce', 1);
                
        ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_shopid', $shopid);
        ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshmoreoffersregulary', 1);
        
        if($shopid == '') {
            
        } else {
            ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_asin', $asin);            
            ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_asintype', $asintype);
            ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshreviewinforegulary', 1);
            ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshpriceinforegulary', 1);
        	ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshproducturlregulary', 1);
        	ATKPTools::set_post_setting( $post_id, ATKP_PRODUCT_POSTTYPE.'_refreshimagesregulary', 1);        	
        }
        
        return $post_id;
    }
    
    public static function delete_image_attachment($attach_id){
        //wenn thumbnail vorhanden dann nicht setzen        
        if($attach_id != '') {
            delete_post_thumbnail($attach_id); 
            wp_delete_attachment($attach_id, true);
        }
    }
    
    public static function upload_image( $image_url, $image_name, $post_id, $idx = 1){
        if(ATKPLog::$logenabled) {
            ATKPLog::LogDebug('*** upload_image started ('.$image_url.' / '. $image_name.' / '.$post_id.') ***');
        } 
        
        if(!function_exists('file_get_contents')) {
            if(ATKPLog::$logenabled) {
                ATKPLog::LogDebug('file_get_contents not exists');
            } 
            return false;
        }
    
        $ext = substr(strrchr($image_url,'.'),1);
        //dateiendung hat nur 3 stellen
        $ext = strlen($ext) <= 3 ? $ext : '';
        
        if($ext == '' || $ext == ' ')
            $ext = 'jpg';
        
        $image_name = strlen($image_name) > 30 ? substr($image_name, 0,30) : $image_name;
        
        
        
        // Add Featured Image to Post
        $upload_dir       = wp_upload_dir(); // Set upload folder
        
        $context = stream_context_create(
            array(
                'http' => array(
                    'method'=>"GET",
                    'header'=>"Accept-language: en\r\n" .
                              "Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
                              "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad
                )
            )
        );
        
        $image_data       = file_get_contents($image_url, false, $context); // Get image data
       
        //wenn fehler beim lesen auftritt, wird false zurück gegeben
        if(!$image_data) {
            if(ATKPLog::$logenabled) {
                ATKPLog::LogDebug('$image_data is empty: '.$image_url);
            } 
        
            return false;
        }
        
        $unique_file_name = sanitize_file_name($post_id.'-'.$idx.'-'.$image_name.'.'.$ext); //wp_unique_filename( $upload_dir['path'], $image_name.'.'.$ext ); // Generate unique name
                
        $filename         = basename( $unique_file_name ); // Create image file name
        
        // Check folder permission and define file location
        if( wp_mkdir_p( $upload_dir['path'] ) ) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }
        
        $alreadyUploaded = false;
        if(file_exists($file)) {
            //bild wurde schon mal hochgeladen
            //jetzt überschreiben
            unlink ($file);            
            
            $alreadyUploaded = true;
        }
        
        // Create the image  file on the server
        if(!file_put_contents( $file, $image_data )) {
            if(ATKPLog::$logenabled) {
                ATKPLog::LogDebug('image cannot be saved: '.$file);
            } 
            return false;
        }
        
        if($alreadyUploaded) {
            //find attachmentid
            $args = array(
                'post_per_page' => 1,
                'post_type'     => 'attachment',
                'name'          => $filename,
            );
            $posts_array = get_posts( $args );
    
            if ( isset($posts_array[0] )) {
                $attachmentid =  $posts_array[0]->ID;  
                
                // Include image.php
                require_once(ABSPATH . 'wp-admin/includes/image.php');

                wp_update_attachment_metadata( $attachmentid, wp_generate_attachment_metadata( $attachmentid, $file ) );
                
                return $attachmentid;
            }            
        } 
                
        // Check image file type
        $wp_filetype = wp_check_filetype( $filename, null );
        
        // Set attachment data
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => $filename,
            'post_content'   => $image_name,
            'post_status'    => 'inherit'
        );
        
        // Create the attachment
        $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
        
        // Include image.php
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        
        // Assign metadata to attachment
        wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $file ) );
        
            
            
        if(ATKPLog::$logenabled) {
            ATKPLog::LogDebug('*** upload_image finished ('.$attach_id.') ***');
        } 
        return $attach_id;
    }
    
    public static function set_featured_image( $image_url, $image_name, $post_id){
        $thumbnail = get_post_thumbnail_id($post_id);
        
        $attach_id = ATKPTools::upload_image($image_url, $image_name, $post_id, 1);
        
        //lösche das alte thumbnail
        if($thumbnail != $attach_id && $thumbnail != '') {
           ATKPTools::delete_image_attachment($thumbnail); 
        }
        
        // And finally assign featured image to post
        set_post_thumbnail( $post_id, $attach_id );
    }
    
    public static function create_subshop($title,$shopid, $programid, $parentid, $subshop)
    {   
        $args = array(
			'post_type' => array( ATKP_SHOP_POSTTYPE ),
			'post_parent' => $parentid,
			'post_status' => array('publish', 'draft'),
        	'meta_query' => array(
        		'relation' => 'AND',
        		array(
        			'key'     => ATKP_SHOP_POSTTYPE.'_shopid',
        			'value'   => $shopid,
        			'compare' => '='
        		),
        		array(
        			'key'     => ATKP_SHOP_POSTTYPE.'_programid',
        			'value'   => $programid,
        			'compare' => '='
        		)
        	)
		);
		
		$the_query = new WP_Query( $args );
		
		if ( $the_query->have_posts() ) {   
		    $the_query->the_post();
            $post_id = $the_query->post->ID;
            wp_reset_postdata();
            
            $my_post = array(
                  'ID'           => $post_id,
                  'post_title' => $title,
                  'post_status' => 'publish',
            );
            
            // Update the post into the database
            wp_update_post( $my_post );
            
                        
            return $post_id;         
		}                    
        
        global $user_ID;
        $new_post = array(
            'post_title' => $title,
            'post_status' => 'publish',
            'post_author' => $user_ID,
            'post_type' => ATKP_SHOP_POSTTYPE,
            'post_parent' => $parentid,
        );
        $post_id = wp_insert_post($new_post);
                        
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_shopid', $shopid);
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_programid', $programid);
        
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_access_webservice', ATKP_SUBSHOPTYPE);
        
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_customtitle', $subshop->customtitle);
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_customsmalllogourl', $subshop->customsmalllogourl);
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_customlogourl', $subshop->customlogourl);
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_customfield1', $subshop->customfield1);
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_customfield2', $subshop->customfield2);
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_customfield3', $subshop->customfield3);
        
        $subshops = array();
        array_push($subshops, $subshop);
        
        ATKPTools::set_post_setting( $post_id, ATKP_SHOP_POSTTYPE.'_default_shops',$subshops);
        
                
        return $post_id;
    }
    
    public static function exists_cron_job($hookParam = ATKP_EVENT, $output = false)
    {
        //$cron = _get_cron_array();
        //$hook = wp_get_schedule( $hookParam );
        
        $crons  = _get_cron_array();
    	$events = array();
    
		foreach ( $crons as $time => $cron ) {
			foreach ( $cron as $hook => $dings ) {
				foreach ( $dings as $sig => $data ) {
                    if($hook == $hookParam) {
        			
        				if (  $data['schedule'] ) {
        					
        					if($output) {
                				$localtime = get_date_from_gmt( date( 'Y-m-d H:i:s', $time ), get_option( 'time_format' ));
                				$localdate = get_date_from_gmt( date( 'Y-m-d H:i:s', $time ), get_option( 'date_format' ));
                				
                				$text = '';
                				$text .= ' '.sprintf(__('Cronjob next execution: %s %s', ATKP_PLUGIN_PREFIX), $localdate, $localtime);
                				$text .= ' ('.self::time_since( time(), $time ).')';
                				$text .= ' Interval: '. self::interval(isset( $data['interval'] ) ? $data['interval'] : null );
                				
                				echo $text;
        					}
                				
                			return true;
        				}
                    }
				}
			}
		}
		
		if($output) {
		    echo __('cronjob is not running! Click the "Save Changes" button.', ATKP_PLUGIN_PREFIX);   
		}
		
		return false;
    }

	public static function time_since( $older_date, $newer_date ) {
		return self::interval( $newer_date - $older_date );
	}

	public static function interval( $since ) {
	    __('%s years', ATKP_PLUGIN_PREFIX);
	    __('%s months', ATKP_PLUGIN_PREFIX);
	    __('%s weeks', ATKP_PLUGIN_PREFIX);
	    __('%s days', ATKP_PLUGIN_PREFIX);
	    __('%s hours', ATKP_PLUGIN_PREFIX);
	    __('%s minutes', ATKP_PLUGIN_PREFIX);
	    __('%s seconds', ATKP_PLUGIN_PREFIX);
	    
		// array of time period chunks
		$chunks = array(
			array( 60 * 60 * 24 * 365, _n_noop( '%s year', '%s years', ATKP_PLUGIN_PREFIX) ),
			array( 60 * 60 * 24 * 30, _n_noop( '%s month', '%s months', ATKP_PLUGIN_PREFIX ) ),
			array( 60 * 60 * 24 * 7, _n_noop( '%s week', '%s weeks', ATKP_PLUGIN_PREFIX ) ),
			array( 60 * 60 * 24, _n_noop( '%s day', '%s days', ATKP_PLUGIN_PREFIX ) ),
			array( 60 * 60, _n_noop( '%s hour', '%s hours', ATKP_PLUGIN_PREFIX ) ),
			array( 60, _n_noop( '%s minute', '%s minutes', ATKP_PLUGIN_PREFIX ) ),
			array( 1, _n_noop( '%s second', '%s seconds', ATKP_PLUGIN_PREFIX ) ),
		);

		if ( $since <= 0 ) {
			return __( 'now', ATKP_PLUGIN_PREFIX );
		}

		// we only want to output two chunks of time here, eg:
		// x years, xx months
		// x days, xx hours
		// so there's only two bits of calculation below:

		// step one: the first chunk
		for ( $i = 0, $j = count( $chunks ); $i < $j; $i++ ) {
			$seconds = $chunks[ $i ][0];
			$name = $chunks[ $i ][1];

			// finding the biggest chunk (if the chunk fits, break)
			if ( ( $count = floor( $since / $seconds ) ) != 0 ) {
				break;
			}
		}

		// set output var
		$output = sprintf( translate_nooped_plural( $name, $count, ATKP_PLUGIN_PREFIX ), $count );

		// step two: the second chunk
		if ( $i + 1 < $j ) {
			$seconds2 = $chunks[ $i + 1 ][0];
			$name2 = $chunks[ $i + 1 ][1];

			if ( ( $count2 = floor( ( $since - ( $seconds * $count ) ) / $seconds2 ) ) != 0 ) {
				// add to output var
				$output .= ' ' . sprintf( translate_nooped_plural( $name2, $count2, ATKP_PLUGIN_PREFIX ), $count2 );
			}
		}

		return $output;
	}
    
    
    
    public static function mix_colors($basecolor, $mixcolor, $ratio, $addHash = true) {
        if($basecolor == '' || $basecolor == null)
            return $basecolor;
        
        
        $baseComponentOffset = strlen($basecolor) == 7 ? 1 : 0;
        $baseComponentRed = hexdec(substr($basecolor, $baseComponentOffset, 2));
        $baseComponentGreen = hexdec(substr($basecolor, $baseComponentOffset+2, 2));
        $baseComponentBlue = hexdec(substr($basecolor, $baseComponentOffset+4, 2));
    
        $mixComponentOffset = strlen($mixcolor) == 7 ? 1 : 0;
        $mixComponentRed = hexdec(substr($mixcolor, $mixComponentOffset, 2));
        $mixComponentGreen = hexdec(substr($mixcolor, $mixComponentOffset+2, 2));
        $mixComponentBlue = hexdec(substr($mixcolor, $mixComponentOffset+4, 2));
    
        $Rsum = $baseComponentRed+$mixComponentRed;
        $Gsum = $baseComponentGreen+$mixComponentGreen;
        $Bsum = $baseComponentBlue+$mixComponentBlue;
    
        $R = ($baseComponentRed*(100-$ratio) + $mixComponentRed*$ratio) / 100;
        $G = ($baseComponentGreen*(100-$ratio) + $mixComponentGreen*$ratio) / 100;
        $B = ($baseComponentBlue*(100-$ratio) + $mixComponentBlue*$ratio) / 100;
    
        $redPercentage = max($R, $G, $B) > 255 ? $R/max($Rsum, $Gsum, $Bsum) : $R/255;
        $greenPercentage = max($R, $G, $B) > 255 ? $G/max($Rsum, $Gsum, $Bsum) : $G/255;
        $bluePercentage = max($R, $G, $B) > 255 ? $B/max($Rsum, $Gsum, $Bsum) : $B/255;
    
        $redRGB = floor(255*$redPercentage);
        $greenRGB = floor(255*$greenPercentage);
        $blueRGB = floor(255*$bluePercentage);
    
        $color = sprintf("%02X%02X%02X", $redRGB, $greenRGB, $blueRGB);
        return $addHash ? '#'.$color : $color;
    }
    
    public static function check_taxonomy($post_id, $manufacturerType, $manufacturer2, $splitmode = true) {
        
        if($manufacturer2 == '' || $manufacturer2 == null) {
            wp_set_object_terms( $post_id, null, $manufacturerType );
            return;
        }
        
        $manufacturers = array();
        
        if($splitmode) {
            $manufacturers = explode(',', $manufacturer2);            
        } else
        {
            array_push($manufacturers, $manufacturer2);   
        }
        
        $manufacturers = array_map('trim', $manufacturers);
        
        $product_terms = wp_get_object_terms( $post_id,  $manufacturerType );
        $found = 0;
        
        foreach($manufacturers as $manufacturer)
            foreach($product_terms as $term) {
                   if($term->name == $manufacturer)
                   {
                        $found++;
                        break;   
                   }
            }
        
        if($found == count($manufacturers))
            $found = true;
        else
            $found = false;
            
        if(!$found) {
            $cat_ids = array();
             
            foreach($manufacturers as $manufacturer) {
                $termid = -1;
                
                if (!term_exists($manufacturer, $manufacturerType)) {
                    $term = wp_insert_term(
                      $manufacturer, // the term 
                      $manufacturerType // the taxonomy                      
                    );
                    
                    $termid  = intval( $term['term_id']);
                } else { 
                    $term = get_term_by('name', $manufacturer, $manufacturerType);
                    $termid = $term->term_id; //term_taxonomy_id
                    
                }
                
                if($termid != -1)
                    array_push($cat_ids, $termid);
            }
           
            $cat_ids = array_map( 'intval', $cat_ids );
            $cat_ids = array_unique( $cat_ids );            
            
            $term_taxonomy_ids =  wp_set_object_terms( $post_id, $cat_ids, $manufacturerType, false );
            
            if ( is_wp_error( $term_taxonomy_ids ) ) {
            	// There was an error somewhere and the terms couldn't be set.
                //TODO: logging
            } else {
            	// Success! These categories were added to the post.
            }	
                       
        }   
    }
    
    
    public static function add_global_styles($name) {

            require_once  ATKP_PLUGIN_DIR.'/includes/atkp_output.php';
            $output = new atkp_output();
            $custom_css = $output->get_css_output();
            
            wp_add_inline_style($name, $custom_css );
        }
    
    public static function add_column( $post_types, $label, $callback, $priority = 10) {
		if ( !is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}
		foreach ( $post_types as $post_type ) {
			$filter_name = 'manage_'.$post_type.'_posts_columns';
			
			add_filter( $filter_name , function ( $columns ) use ( $label, $priority ) {
					$key = sanitize_title( $label );
					$col = array( $key => $label );
					if ( $priority < 0 ) {
						return array_merge( $col, $columns );
					} else if ( $priority > count( $columns ) ) {
						return array_merge( $columns, $col );
					} else {
						$offset = $priority;
						$sorted = array_slice( $columns, 0, $offset, true ) + $col + array_slice( $columns, $offset, NULL, true );
						return $sorted;
					}
				}, $priority );
				
			add_action( 'manage_'.$post_type.'_posts_custom_column', function( $col, $pid ) use ( $label, $callback ) {
					$key = sanitize_title( $label );
					if ( $col == $key ) {
						$callback( $pid );
					}
				}, $priority, 2 );
		}
	}
	
	public static function show_notice( $text, $class = 'updated' ) {
		if ( $class == 'yellow' ) {
			$class = 'updated';
		}
		if ( $class == 'red' ) {
			$class = 'error';
		}
		add_action( 'admin_notices', function() use ( $text, $class ) {
				echo '<div class="'.$class.'"><p>'.$text.'</p></div>';
			}, 1 );
	}
    
    
    public static function get_siteurl() {
        
        $url = 'unknown';
        
        if(is_multisite())
            $url = network_site_url();
        else
            $url = site_url();
        
        return $url;         
     }
     
     public static function get_endpointurl() {
        
        $url = admin_url('admin-ajax.php');
        
        return $url;         
     }
	 
	 public static function exists_get_parameter($key) {
		 return isset($_GET[$key]);
	 }
	 
	 public static function get_get_parameter($key, $type) {
		$parametervalue = null;
                      
        if(isset($_GET[$key])) {
            $parametervalue = $_GET[$key];
        }
		
		return ATKPTools::get_casted_value($parametervalue, $type);
	 }
     
	 public static function exists_post_parameter($key) {
		 return isset($_POST[$key]);
	 }
	 
	 public static function get_post_parameter($key, $type) {
		$parametervalue = null;
                      
        if(isset($_POST[$key])) {
            $parametervalue = $_POST[$key];
        }
		
		return ATKPTools::get_casted_value($parametervalue, $type);
	 }

     public static function get_casted_value($parametervalue, $type) {
        
        switch($type) {
            case 'bool':
                if($parametervalue == null || $parametervalue == '')
                    return false;
                else {
                    //hack for older versions than 5.5
                    if(function_exists('boolval'))
                        return boolval($parametervalue);   
                    else
                        return (bool)$parametervalue;
                }
                break;
            case 'int':
                if($parametervalue == null || $parametervalue == '')
                    return 0;
                else
                    return intval($parametervalue);
                break;
            case 'double':
                if($parametervalue == null || $parametervalue == '')
                    return 0;
                else
                    return floatval($parametervalue);
                break;
            case 'string':
                if($parametervalue == null || $parametervalue == '')
                    return '';
                else
                    return sanitize_text_field($parametervalue);
                break;
            case 'multistring2':
            case 'multistring':
                if($parametervalue == null || $parametervalue == '')
                    return '';
                else
                    return implode( "\n", array_map( 'sanitize_text_field', (array) explode( "\n", (string)$parametervalue) ));                
                break;            
            case 'allhtml':
                if($parametervalue == null || $parametervalue == '')
                    return '';
                else
                    return ($parametervalue);          
                break;
            case 'html':
                if($parametervalue == null || $parametervalue == '')
                    return '';
                else
                    return wp_kses_post($parametervalue);          
                
                break;
            case 'url':
                if($parametervalue == null || $parametervalue == '')
                    return '';
                else
                    return sanitize_text_field($parametervalue);
                break;
            default:
                throw new exception('type unkown: '.$type);
        }
     }
     
    

    
     public static function get_post_setting($post_id, $key) {
      $value = get_post_meta($post_id, $key);
      
      if(isset($value) && is_array($value) && count($value) > 0) {
        return $value[0];
      }
      else {
        return '';
      }
     }
     public static function set_post_setting($post_id, $key, $value) {
         
        delete_post_meta($post_id, $key);
        if($value != null)
        add_post_meta($post_id, $key, $value);
         
     }
     
    public static function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
    
    public static function price_to_float($s) {
		$s = str_replace(',', '.', $s);

		// remove everything except numbers and dot "."
		$s = preg_replace("/[^0-9\.]/", "", $s);

		// remove all seperators from first part and keep the end
		$s = str_replace('.', '',substr($s, 0, -3)) . substr($s, -3);

		// return float
		return round((float)$s, 2);
	}
}

class ATKPCache {
    public static function get_cache_by_id($postid) {			
	return get_transient( ATKP_PLUGIN_PREFIX.'_' . $postid );		
    }

    public static function set_cache_by_id($postid, $data, $expiration = 0) {
    	if (!isset($data) || empty($data) || sizeof($data)==0) {
    	    delete_transient(ATKP_PLUGIN_PREFIX.'_' . $postid);
    	    return;
    	}

    	set_transient( ATKP_PLUGIN_PREFIX.'_' . $postid, $data, $expiration);
    }
    
   public static function GetCache($category, $keyword, $count) {			
	return get_transient( ATKP_PLUGIN_PREFIX.'_' . md5($category .'-'. $keyword .'-'. $count) );		
    }

    public static function SetCache($category, $keyword, $count, $data) {
    	if (!isset($data) || empty($data) || sizeof($data)==0) {
    	    delete_transient(ATKP_PLUGIN_PREFIX.'_' . md5($category .'-'. $keyword .'-'. $count));
    	    return;
    	}

    	set_transient( ATKP_PLUGIN_PREFIX.'_' . md5($category .'-'. $keyword .'-'. $count) , $data, ATKPSettings::$access_cache_duration * 60 );
    }

    public static function ClearCache() {
        global $wpdb;

        $result = $wpdb->get_results("SELECT * FROM $wpdb->options WHERE option_name LIKE '_transient_".ATKP_PLUGIN_PREFIX."_%'");
    		foreach ( $result as $row ) {
    			delete_transient(str_replace('_transient_', '',$row->option_name), true);
    		}		 
        }    
    }

class ATKPHomeLinks {
    
    public static function echo_banner() {
		
		$bannerurl = plugins_url('images/468x60_buy_starter_%locale%.jpg', ATKP_PLUGIN_FILE );
		
		$str = '<a href="https://www.affiliate-toolkit.com/%locale%/gute-gruende-fuer-ein-upgrade-auf-eine-kaufversion/" target="_blank" title="'. __('Good reasons for a upgrade!', ATKP_PLUGIN_PREFIX).'"><img src="'.$bannerurl.'" alt="Affiliate-Toolkit Banner" /></a>';
		
		echo ATKPHomeLinks::CheckLink($str);
	}
    
    public static function ReplaceLinkType($str) {
        
        $str = str_replace('%link_toolkit-bestseller%', 'https://www.affiliate-toolkit.com/%locale%/toolkit-bestseller/', $str);   
        $str = str_replace('%link_get-amazon-apikey%', 'https://www.affiliate-toolkit.com/%locale%/get-amazon-apikey/', $str); 
        $str = str_replace('%link_load-amazon-customer-reviews%', 'https://www.affiliate-toolkit.com/%locale%/load-amazon-customer-reviews/', $str);
        $str = str_replace('%link_mark-affiliate-links%', 'https://www.affiliate-toolkit.com/%locale%/mark-affiliate-links/', $str);
        $str = str_replace('%link_get-amazon-search-department%', 'https://www.affiliate-toolkit.com/%locale%/get-amazon-search-department/', $str);
        
        $str = str_replace('%link_help%', 'https://www.affiliate-toolkit.com/%locale%/help/', $str);
        
        $str = str_replace('%link_support%', 'https://www.affiliate-toolkit.com/%locale%/support/', $str);
        
        $str = str_replace('%link_affiliate%', 'https://www.affiliate-toolkit.com/%locale%/affiliate/', $str);
        $str = str_replace('%link_contact%', 'https://www.affiliate-toolkit.com/%locale%/contact/', $str);
        $str = str_replace('%link_get-license-key%', 'https://www.affiliate-toolkit.com/%locale%/get-license-key/', $str);
        $str = str_replace('%link_customfields%', 'https://www.affiliate-toolkit.com/%locale%/customfields/', $str);
        

        return ATKPHomeLinks::CheckLink($str);
    }
    
    public static function CheckLink($link) {
  
    if(get_locale() == 'de_DE' || get_locale() == 'de_AT')
        $locale = "de";
    else
        $locale = "en";
    
    return str_replace('%locale%', $locale, $link);   
     
 }
    
}

class ATKPLog {
    private static $log;
    public static $logenabled;
    
    public static function Init($filepath, $priority) {
        ATKPLog::$logenabled = false;        
        
        if($priority != 'off' && $priority != '') {     
            if (!class_exists('KLogger')) 
    			require_once  ATKP_PLUGIN_DIR.'/lib/klogger.php';
			
			$logpriority = KLogger::OFF;
            
			switch($priority) {
				case 'debug':
					ATKPLog::$logenabled = true;
					$logpriority = KLogger::DEBUG;
					break;
				case 'error':
					ATKPLog::$logenabled = true;
					$logpriority = KLogger::ERROR;
					break;
			}
			
            ATKPLog::$log = new KLogger ( $filepath, $logpriority );   
        }
    }
    
    public static function LogInfo($line)
	{
	    if(!ATKPLog::$logenabled)
	        return;	    
	    
		ATKPLog::$log->LogInfo( $line);
	}
	
	public static function LogDebug($line, $context = null)
	{
	    if(!ATKPLog::$logenabled)
	        return;
	        
		ATKPLog::$log->LogDebug( $line );
		
		if($context != null) {
		    ATKPLog::$log->LogDebug( ATKPLog::contextToString($context));
		}
	}
	
	/**
     * Takes the given context and coverts it to a string.
     *
     * @param  array $context The Context
     * @return string
     */
    protected static function contextToString($context)
    {
        $export = '';
        foreach ($context as $key => $value) {
            $export .= "{$key}: ";
            $export .= preg_replace(array(
                '/=>\s+([a-zA-Z])/im',
                '/array\(\s+\)/im',
                '/^  |\G  /m'
            ), array(
                '=> $1',
                'array()',
                '    '
            ), str_replace('array (', 'array(', var_export($value, true)));
            $export .= PHP_EOL;
        }
        return str_replace(array('\\\\', '\\\''), array('\\', '\''), rtrim($export));
    }
	
	public static function LogWarn($line)
	{
	    if(!ATKPLog::$logenabled)
	        return;
	        
		ATKPLog::$log->LogWarn( $line);	
	}
	
	public static function LogError($line)
	{
	    if(!ATKPLog::$logenabled)
	        return;
	        
		ATKPLog::$log->LogError( $line  );		
	}
    
}


?>