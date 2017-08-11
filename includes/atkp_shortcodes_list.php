<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_shortcodes_list
    {   
        /**
         * Construct the plugin object
         */
        public function __construct($pluginbase)
        {
            add_shortcode(ATKP_LIST_SHORTCODE, array(&$this, 'shortcode'));
        }
        
        
        function shortcode($atts, $content = "") {
            try {
           
                $a = shortcode_atts( array(
                    'id' => '',
                    'template' => '',
                    'elementcss' => '',
                    'containercss'=>'',
                    'buttontype' => '',
                    'limit' => 0,
                    'randomsort' =>'no',
                    'hidedisclaimer' => 'no',
                    'tracking_id' => '',
                ), $atts );
                
                $id = '';
                $template = 'wide';
                $buttontype = 'notset';
                $elementcss = '';
                $containercss = '';
                $field ='';
                $limit = ATKPSettings::$list_default_count == '' || ATKPSettings::$list_default_count == '0'  ? 10 : ATKPSettings::$list_default_count;
                $randomsort = false;
                $hidedisclaimer = false;
                $tracking_id = '';
                
                if (isset($a['id'])) 
                    $id = $a['id'];
                if (isset($a['template']) && !empty($a['template'])) 
                    $template = $a['template'];
                    
                if (isset($a['elementcss']) && !empty($a['elementcss'])) 
                    $elementcss = $a['elementcss'];
                if (isset($a['containercss']) && !empty($a['containercss'])) 
                    $containercss = $a['containercss'];
                
                 if (isset($a['buttontype']) && !empty($a['buttontype'])) 
                    $buttontype = $a['buttontype'];
                        
                if (isset($a['randomsort']) && !empty($a['randomsort'])) 
                    if($a['randomsort'] == 'yes')
                        $randomsort = true;
                    else if($a['randomsort'] == 'no')
                        $randomsort =false;
                        
                if (isset($a['hidedisclaimer']) && !empty($a['hidedisclaimer'])) 
                    if($a['hidedisclaimer'] == 'yes')
                        $hidedisclaimer =true;
                    else if($a['hidedisclaimer'] == 'no')
                        $hidedisclaimer =false;
                        
                if (isset($a['limit']) && $a['limit'] > 0) 
                    $limit = intval($a['limit']);
                    
                if (isset($a['tracking_id']) && !empty($a['tracking_id'])) 
                    $tracking_id = $a['tracking_id'];
                
                //wenn der Name der Liste übergeben wurde, dann ist es kein numerischer wert
                if(!is_numeric($id)) {                 
                    require_once ATKP_PLUGIN_DIR.'/includes/atkp_list.php';
                    
                    $id2 = atkp_list::idbyname($id);
                    
                    if($id2 == null)
                        throw new Exception('list (name) not found: '.$id);
                    else
                        $id = $id2;
                }
                
                //validation der liste
                $list = get_post( $id ); 
    
                if(!isset($list) || $list == null)
                    throw new Exception( 'list not found: '.$id);
                if($list->post_type != ATKP_LIST_POSTTYPE)
                     throw new Exception('invalid post_type: '.$list->post_type );              
                if($list->post_status != 'publish' && $list->post_status != 'draft')
                    throw new Exception( 'list not available: '.$id);
                    
                
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_output.php';
                
                $output = new atkp_output();
                return $output->get_list_output($id, $template, $content, $buttontype, $elementcss, $containercss, $limit, $randomsort, $hidedisclaimer, $tracking_id);
            
            } catch(Exception $e) {
                return 'Exception: '. $e->getMessage();
            }
        }
    }
    
    
?>