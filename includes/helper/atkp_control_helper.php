<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class atkp_control_helper {
    
    public function create_control($newfield, $controlname, $value) {
        $result = '';        
        
        switch($newfield->type) {
             case 1:
                 //Text
                $type  ='text';
                $attributes = '';
                switch($newfield->format) {
                    case 'text':
                        $type = 'text';
                        break;
                    case 'number':
                        $type = 'number';
                        $attributes = ' step="0.01" ';
                        break;
                    case 'url':
                        $type = 'url';
                        break;
                }
                 
                 
                $result = '<input style="width:100%" type="'.$type.'" '.$attributes.' id="'. $controlname .'" name="'. $controlname .'" value="'.esc_attr($value).'"> ';                            
            
                 break;
             case 2:
                 //multiline
                  
                $result = '<textarea style="width:100%;height:100px" id="'. $controlname .'" name="'. $controlname .'">'.esc_textarea($value).'</textarea>';
		          
                 break;
             case 3:
                 //dropdown
                 $values = explode(';', $newfield->format);
                 
                 $result = '<select id="'. $controlname .'" name="'. $controlname .'" style="width:300px">  ';                          
                                
                $result .= '<option value="" '.($value == '' ? 'selected' : '').'>' . __('None', ATKP_PLUGIN_PREFIX) . '</option>';
                
                foreach($values as $value2) {
                    $value2 = trim($value2);
                    if($value2 != '')
                        $result .= '<option value="'.$value2.'" '.($value == $value2 ? 'selected' : '').'>' . esc_attr($value2) . '</option>';
                }
                
                $result .= '</select>';
                 
                 break;
             case 4:
                 //yesno
                 
                 $result .= '<select id="'. $controlname .'" name="'. $controlname .'" style="width:300px"> ';                           
                                                                
                $result .= '<option value="" '.($value == '' || $value == null ? 'selected' : '').'>' . __('None', ATKP_PLUGIN_PREFIX) . '</option>';
                $result .= '<option value="1" '.($value == '1' ? 'selected' : '').'>' . __('Yes', ATKP_PLUGIN_PREFIX) . '</option>';
                $result .= '<option value="0" '.($value == '0' ? 'selected' : '').'>' . __('No', ATKP_PLUGIN_PREFIX) . '</option>';
                                
                $result .= '</select>';
                 
                 break;
             case 5:
                 //html
                
                ob_start();
                 
                wp_editor($value, $controlname, array(
                    'media_buttons' => false,
                    'textarea_name' => $controlname,
                    'textarea_rows' => 5,
                ));
                
                $result = ob_get_contents();
                
                ob_end_clean();
                 break;
                    
        }   
        echo $result;    
    }
 
    public function create_frontendcontrol($newfield, $taxonomy, $controlname, $value) {
        $result = '';        
        
        if($taxonomy != null) {
            $result .= '<select id="'.$controlname.'" name="'.$controlname.'" style="width:300px">';
            $result .= '<option value="">'. esc_attr(__('None', ATKP_PLUGIN_PREFIX)) .'</option>';
            
                $categories = get_categories('orderby=name&hide_empty=1&taxonomy='.$taxonomy->name);
                foreach ($categories as $category) {
                    $option = '<option value="'.$category->cat_name.'">';
                    $option .= $category->cat_name;
                    //$option .= ' ('.$category->category_count.')';
                    $option .= '</option>';
                    $result .=  $option;
                }
            
            $result .= '</select>';
            
        } else if($newfield != null) {        
            switch($newfield->type) {
                 case 1:
                     //Text
                    $type  ='text';
                    switch($newfield->format) {
                        case 'text':
                            $type = 'text';
                            break;
                        case 'number':
                            $type = 'number';
                            break;
                        case 'url':
                            $type = 'url';
                            break;
                    }
                     
                     
                    //$result = '<input style="width:100%" type="'.$type.'" id="'. $controlname .'" name="'. $controlname .'" value="'.esc_attr($value).'"> ';                            
                
                    $result = '<div class="atkp-rangeslider"></div>';
                
                     break;
                 case 2:
                     //multiline
                      
                    $result = '<textarea style="width:100%;height:100px" id="'. $controlname .'" name="'. $controlname .'">'.esc_textarea($value).'</textarea>';
    		          
                     break;
                 case 3:
                     //dropdown
                     $values = explode(';', $newfield->format);
                     
                     $result = '<select id="'. $controlname .'" name="'. $controlname .'" style="width:300px">  ';                          
                                    
                    $result .= '<option value="" '.($value == '' ? 'selected' : '').'>' . __('None', ATKP_PLUGIN_PREFIX) . '</option>';
                    
                    foreach($values as $value2) {
                        $value2 = trim($value2);
                        if($value2 != '')
                            $result .= '<option value="'.$value2.'" '.($value == $value2 ? 'selected' : '').'>' . esc_attr($value2) . '</option>';
                    }
                    
                    $result .= '</select>';
                     
                     break;
                 case 4:
                     //yesno
                     
                     $result .= '<select id="'. $controlname .'" name="'. $controlname .'" style="width:300px"> ';                           
                                                                    
                    $result .= '<option value="" '.($value == '' || $value == null ? 'selected' : '').'>' . __('None', ATKP_PLUGIN_PREFIX) . '</option>';
                    $result .= '<option value="1" '.($value == '1' ? 'selected' : '').'>' . __('Yes', ATKP_PLUGIN_PREFIX) . '</option>';
                    $result .= '<option value="0" '.($value == '0' ? 'selected' : '').'>' . __('No', ATKP_PLUGIN_PREFIX) . '</option>';
                                    
                    $result .= '</select>';
                     
                     break;
                 case 5:
                     //html
                    
                    ob_start();
                     
                    wp_editor($value, $controlname, array(
                        'media_buttons' => false,
                        'textarea_name' => $controlname,
                        'textarea_rows' => 5,
                    ));
                    
                    $result = ob_get_contents();
                    
                    ob_end_clean();
                     break;
                        
            }   
        } else {
            //product dropdown
            
            if($controlname == 'orderby') {
                //neuheiten
                //bewertungen
                //preis auf bzw. absteigend
                //produktname auf bzw. absteigend
            }
            
        }
        return $result;    
    }   
}


?>