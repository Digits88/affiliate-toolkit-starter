<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class atkp_shortcodes_asa1
    {   
        /**
         * Construct the plugin object
         */
        public function __construct($pluginbase)
        {
            $isactive = get_option(ATKP_PLUGIN_PREFIX.'_asa_activate',0);
            
            if($isactive) {
                add_shortcode('asa', array(&$this, 'shortcode_product'));
                add_shortcode('asa_collection', array(&$this, 'shortcode_list'));
            }
        }
         
        function shortcode_product($atts, $content=null, $code="") {
            try {                 
                $template ='';//sidebar_item
                $comment ='';
                $class ='';
                $asin = trim($content);
                
                $params = $this->parseParams($atts);
                
                foreach ($params as $param) {
                    $param = trim($param);
                    
                    if (!strstr($param, '=')) {
                        $template = $param;
                    } else {
                        
                        if (strstr($param, 'comment=')) {
                            // the comment feature
                            $comment = str_replace('comment=', '', $param);
                        } elseif (strstr($param, 'class=')) {
                            // the comment feature
                            $class = str_replace('class=', '', $param);
                        } else {
                            //nichts was uns interessieren könnte
                        }    
                    }
                }
                
                $template =  $this->modifyasatemplate($template); 
                
                 require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product.php';
        
                $prd = atkp_product::loadbyasin($asin);
                
                if($prd == null)
                    return 'product not found: '.$asin;
              
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_output.php';
                
                $output = new atkp_output();
                    
                return $output->get_product_output($prd->productid, $template, $comment, 'notset', '', false, $class, '', false);
            
            } catch(Exception $e) { 
                return 'Exception: '. $e->getMessage();
            }
        }
        
        function modifyasatemplate($template) {
            
            $found = false;
            for ($i = 1; $i <= 5; $i++) {
                    $asatemplate =    get_option(ATKP_PLUGIN_PREFIX.'_asa_templatename'.$i);
                    $toolkittemplate=    get_option(ATKP_PLUGIN_PREFIX.'_asa_templateid'.$i);
                
                if($template == $asatemplate)
                {
                    $template = $toolkittemplate;
                    $found = true;
                    break;   
                }
           }
           
           //wenn das template numerisch ist, dann versucht er es aus den eigenen vorlagne zu laden und zeigt ggf. nichts an
           if(!$found && is_numeric($template))
                $template = '';
            
            return $template;   
        }
        
        function shortcode_list($atts, $content=null, $code="") {
            try {
                $template ='';//sidebar_item
                $itemsCount =10;
                $type =''; 
                
                $params = $this->parseParams($atts);
                    
                foreach ($params as $param) {
                    $param = trim($param);
                    
                    if (!strstr($param, '=')) {
                        $template = $param;
                    } else {
                        
                        if (strstr($param, 'items=')) {
                            // the items count                            
                            $itemsCount = str_replace('items=', '', $param);
                        } elseif (strstr($param, 'type=')) {
                            // the type
                            $type = str_replace('type=', '', $param);
                        } else {
                            //nichts was uns interessieren könnte
                        }    
                    }
                }
                
                $randomsort = $type == 'random';
            
                $template =  $this->modifyasatemplate($template);      
        
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_list.php';            
               
                $list = atkp_list::loadbyname('ASA:'.$content);
               
               if($list == null)
                    return 'list not found: '.$content;
                
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_output.php';
                
                $output = new atkp_output();
                return $output->get_list_output($list->listid, $template, '', 'notset', '', '', $itemsCount, $randomsort, false);
                 
            } catch(Exception $e) { 
                return 'Exception: '. $e->getMessage();
            }
        }
        
        private function parseParams($atts) {
            $params_text = '';
                
            if($atts != null) {
                foreach($atts as $att => $value) {
                    if($att == '' || is_numeric($att))
                        $params_text .= ' '.$value;   
                    else
                        $params_text .= ','.$att.'='.$value;   
                }
                
                $params_text = str_replace(',,',',',$params_text);
                
                $parse_params   = array();
            }
            
            $params = array();
            $params         = explode(',', $params_text);
            $params         = array_map('trim', $params);
            
            return $params;
        }
        
        
 
    }
    
    
?>