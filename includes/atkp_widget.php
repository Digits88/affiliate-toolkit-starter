<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Creating the widget 
class atkp_widget extends WP_Widget {
    
    function __construct() {
        parent::__construct(
        // Base ID of your widget
        ATKP_WIDGET, 
        
        // Widget name will appear in UI
        __('Affiliate Toolkit Widget', ATKP_PLUGIN_PREFIX), 
        
        // Widget description
        array( 'description' => __( 'Shows a product or a list.', ATKP_PLUGIN_PREFIX), ) 
        );
    }
    
    // Creating widget front-end
    // This is where the action happens
    public function widget( $args, $instance ) {
        
        $title = apply_filters( 'widget_title', isset($instance[ATKP_WIDGET.'_title']) ? $instance[ATKP_WIDGET.'_title'] : '', $instance );
        // before and after widget arguments are defined by themes
    
        $product = 0;
    	$list = 0;
        $template = 'box';
        $addintocart = 'notset';
        $containercssclass = '';
    	$elementcssclass = '';
        $limit = 10;
        $randomsort = false;
        $usemainproduct = false;
        $content ='';
        
        if (isset($instance[ATKP_WIDGET.'_product'])) 
            $product = intval($instance[ATKP_WIDGET.'_product']);
    	if (isset($instance[ATKP_WIDGET.'_list'])) 
            $list = intval($instance[ATKP_WIDGET.'_list']);
        if (isset($instance[ATKP_WIDGET.'_template']) && !empty($instance[ATKP_WIDGET.'_template'])) 
            $template = $instance[ATKP_WIDGET.'_template'];
            
        if (isset($instance[ATKP_WIDGET.'_containercssclass']) && !empty($instance[ATKP_WIDGET.'_containercssclass'])) 
            $containercssclass = $instance[ATKP_WIDGET.'_containercssclass'];
    	if (isset($instance[ATKP_WIDGET.'_elementcssclass']) && !empty($instance[ATKP_WIDGET.'_elementcssclass'])) 
            $elementcssclass = $instance[ATKP_WIDGET.'_elementcssclass'];
    	if (isset($instance[ATKP_WIDGET.'_limit']) && !empty($instance[ATKP_WIDGET.'_limit'])) 
            $limit = intval($instance[ATKP_WIDGET.'_limit']);
    	if (isset($instance[ATKP_WIDGET.'_random']) && !empty($instance[ATKP_WIDGET.'_random'])) 
            $randomsort = (bool)$instance[ATKP_WIDGET.'_random'];
            
        if (isset($instance[ATKP_WIDGET.'_usemainproduct']) && !empty($instance[ATKP_WIDGET.'_usemainproduct'])) 
            $usemainproduct = (bool)$instance[ATKP_WIDGET.'_usemainproduct'];
    
        if (isset($instance[ATKP_WIDGET.'_content']) && !empty($instance[ATKP_WIDGET.'_content'])) 
            $content = $instance[ATKP_WIDGET.'_content'];
    
        $elementcssclass  = $elementcssclass.' atkp-widget';
        
        if($template == '' || ATKP_PLUGIN_VERSION < 30)
            $template = 'box';
        
        require_once  ATKP_PLUGIN_DIR.'/includes/atkp_output.php';
            
        $output = new atkp_output();
    	
    	if($usemainproduct) {
    
            if(get_post_type() == 'product') {
                $woo_id = get_the_ID(); 
                $hidedisclaimer =true; 
                //woocommerce
                //INFO: Auch in external_featuredimage in verwendung
                //INFO: Auch in atkp_tools_woo in columns in verwendung!!
               
                $eanfield = get_option(ATKP_PLUGIN_PREFIX.'_woo_ean_field', '');
                $keytype = get_option(ATKP_PLUGIN_PREFIX.'_woo_keytype', 'ean');
                
                if($eanfield == '' || $eanfield == 'sku')
                    $ean =  ATKPTools::get_post_setting( $woo_id, '_sku'); 
                else
                    $ean = ATKPTools::get_post_setting( $woo_id, $eanfield);
                    
                require_once  ATKP_PLUGIN_DIR.'/includes/atkp_product.php';    
                    
                if($keytype == 'id') {
                    $exists = atkp_product::exists($ean);
                    
                    if($exists)
                        $product = $ean;
                }
                else
                    $product =  atkp_product::idbyean($ean);
            } else {    
    	        $queried_object = get_queried_object();
    
                if ( $queried_object ) {
                    $post_id = $queried_object->ID;
                    $product = ATKPTools::get_post_setting( $post_id, ATKP_PLUGIN_PREFIX.'_product');
                }
            }
    	}
    	
    	if($product != '' && $product != 0) {
    	    echo $args['before_widget'];
            if ( ! empty( $title ) )
                echo $args['before_title'] . $title . $args['after_title'];
    	    
    	    try {
        	    echo $output->get_product_output($product, $template, $content, 'notset', '', false, $elementcssclass, $containercssclass, false);    	    
    	    } catch(Exception $e) { 
                echo 'Exception: '. $e->getMessage();
            }	
    	    
        	echo $args['after_widget'];
    	}	else	if($list != '' && $list != 0) {
    	    echo $args['before_widget'];
            if ( ! empty( $title ) )
                echo $args['before_title'] . $title . $args['after_title'];
                
    	    //create list and output
    		try {
    	        echo $output->get_list_output($list, $template, $content, 'notset', $elementcssclass, $containercssclass, $limit, $randomsort, false);  
    		} catch(Exception $e) { 
                echo 'Exception: '. $e->getMessage();
            }
        	echo $args['after_widget'];             
    	} 
       
    	
    }
    		
    // Widget Backend 
    public function form( $instance ) {
        
        // Widget admin form
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' , ATKP_PLUGIN_PREFIX); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( ATKP_WIDGET.'_title' ); ?>" type="text" value="<?php echo  isset( $instance[ATKP_WIDGET.'_title']) ? esc_attr($instance[ ATKP_WIDGET.'_title' ]) : ''; ?>" />
        </p>
        
        <p>
        <input type="checkbox" id="<?php echo $this->get_field_id( ATKP_WIDGET.'_usemainproduct' ); ?>" name="<?php echo $this->get_field_name( ATKP_WIDGET.'_usemainproduct' ); ?>" value="1" <?php echo checked(1, isset($instance[ ATKP_WIDGET.'_usemainproduct' ]) ? $instance[ ATKP_WIDGET.'_usemainproduct' ] : false, true); ?>>
                                    
                                     <label for="<?php echo $this->get_field_id( ATKP_WIDGET.'_usemainproduct' ); ?>">
                                            <?php _e('Use main product', ATKP_PLUGIN_PREFIX) ?>
                                        </label> 
        </p>
        
        <p>
        <label for="<?php echo $this->get_field_id( ATKP_WIDGET.'_product' ); ?>"><?php _e( 'Product:' , ATKP_PLUGIN_PREFIX); ?></label> 
        <select class="widefat" id="<?php echo $this->get_field_id( ATKP_WIDGET.'_product' ); ?>" name="<?php echo $this->get_field_name( ATKP_WIDGET.'_product' ); ?>">
                                        <?php
                                        echo '<option value="">'.__('no product', ATKP_PLUGIN_PREFIX).'</option>';
                                        
                                        global $post;
        $args = array( 'post_type' => ATKP_PRODUCT_POSTTYPE, 'posts_per_page'   => 300, 'post_status'      => 'publish');
        $posts_array = get_posts($args);
        foreach ( $posts_array as $prd ) { 
        
                                            if (isset($instance[ ATKP_WIDGET.'_product' ]) && $prd->ID == $instance[ ATKP_WIDGET.'_product' ]) 
                                                $sel = ' selected'; 
                                            else 
                                                $sel = '';
                                                
                                                echo '<option value="' .$prd->ID . '"' . $sel . '>' . $prd->post_title.' ('.$prd->ID.')' . '</option>';
         };
         ?>
        </select>
        </p>
        
        <p>
        <label for="<?php echo $this->get_field_id( ATKP_WIDGET.'_list' ); ?>"><?php _e( 'List:' , ATKP_PLUGIN_PREFIX); ?></label> 
        <select class="widefat" id="<?php echo $this->get_field_id( ATKP_WIDGET.'_list' ); ?>" name="<?php echo $this->get_field_name( ATKP_WIDGET.'_list' ); ?>">
                                        <?php
                                        echo '<option value="">'.__('no list', ATKP_PLUGIN_PREFIX).'</option>';
                                        
                                        global $post;
        $args = array( 'post_type' => ATKP_LIST_POSTTYPE, 'posts_per_page'   => 300, 'post_status'      => 'publish');
        $posts_array = get_posts($args);
        foreach ( $posts_array as $prd ) { 
        
                                            if (isset($instance[ ATKP_WIDGET.'_list' ]) && $prd->ID == $instance[ ATKP_WIDGET.'_list' ]) 
                                                $sel = ' selected'; 
                                            else 
                                                $sel = '';
                                                
                                                echo '<option value="' .$prd->ID . '"' . $sel . '>' . $prd->post_title.' ('.$prd->ID.')' . '</option>';
         };
         ?>
        </select></p>
        
        <p>
        <label for="<?php echo $this->get_field_id( ATKP_WIDGET.'_template' ); ?>"><?php _e( 'Template:' , ATKP_PLUGIN_PREFIX); ?></label> 
        <select class="widefat" <?php  if(ATKP_PLUGIN_VERSION < 30) { echo 'disabled'; } ?> id="<?php echo $this->get_field_id( ATKP_WIDGET.'_template' ); ?>" name="<?php echo $this->get_field_name( ATKP_WIDGET.'_template' ); ?>">
                                        <?php
                                        echo '<option value="">'.__('default', ATKP_PLUGIN_PREFIX).'</option>';
                                        
                                        if(ATKP_PLUGIN_VERSION >= 30) {
                                        global $post;
        $args = array( 'post_type' => ATKP_TEMPLATE_POSTTYPE, 'posts_per_page'   => 300, 'post_status'      => 'publish');
        $posts_array = get_posts($args);
        foreach ( $posts_array as $prd ) { 
        
                                            if (isset($instance[ ATKP_WIDGET.'_template' ]) && $prd->ID == $instance[ ATKP_WIDGET.'_template' ]) 
                                                $sel = ' selected'; 
                                            else 
                                                $sel = '';
                                                
                                                echo '<option value="' .$prd->ID . '"' . $sel . '>' . $prd->post_title.' ('.$prd->ID.')' . '</option>';
         };
         }?>
        </select>
        </p>
        
        <p>
        <label for="<?php echo $this->get_field_id( ATKP_WIDGET.'_containercssclass' ); ?>"><?php _e( 'Container CSS Class:' , ATKP_PLUGIN_PREFIX); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( ATKP_WIDGET.'_containercssclass' ); ?>" name="<?php echo $this->get_field_name( ATKP_WIDGET.'_containercssclass' ); ?>" type="text" value="<?php echo esc_attr( isset($instance[ ATKP_WIDGET.'_containercssclass' ]) ? $instance[ ATKP_WIDGET.'_containercssclass' ] : '' ); ?>" />
        </p>
        
        <p>
        <label for="<?php echo $this->get_field_id( ATKP_WIDGET.'_elementcssclass' ); ?>"><?php _e( 'Element CSS Class:' , ATKP_PLUGIN_PREFIX); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( ATKP_WIDGET.'_elementcssclass' ); ?>" name="<?php echo $this->get_field_name( ATKP_WIDGET.'_elementcssclass' ); ?>" type="text" value="<?php echo esc_attr( isset($instance[ ATKP_WIDGET.'_elementcssclass' ]) ? $instance[ ATKP_WIDGET.'_elementcssclass' ] : '' ); ?>" />
        </p>
        
        <p>
        <label for="<?php echo $this->get_field_id( ATKP_WIDGET.'_limit' ); ?>"><?php _e( 'Limit:' , ATKP_PLUGIN_PREFIX); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( ATKP_WIDGET.'_limit' ); ?>" name="<?php echo $this->get_field_name( ATKP_WIDGET.'_limit' ); ?>" type="number" min="1" max="10" value="<?php echo esc_attr( isset($instance[ ATKP_WIDGET.'_limit' ]) ? $instance[ ATKP_WIDGET.'_limit' ] : '3' ); ?>" />
        </p>
        
        <p>
        <label for="<?php echo $this->get_field_id( ATKP_WIDGET.'_content' ); ?>"><?php _e( 'Content:' , ATKP_PLUGIN_PREFIX); ?></label> 
        <input class="widefat" <?php  if(ATKP_PLUGIN_VERSION < 30) { echo 'disabled'; } ?> id="<?php echo $this->get_field_id( ATKP_WIDGET.'_content' ); ?>" name="<?php echo $this->get_field_name( ATKP_WIDGET.'_content' ); ?>" type="text" value="<?php echo esc_attr( isset($instance[ ATKP_WIDGET.'_content' ]) ? $instance[ ATKP_WIDGET.'_content' ] : '' ); ?>" />
        </p>
        
        <p>
        <input type="checkbox" id="<?php echo $this->get_field_id( ATKP_WIDGET.'_random' ); ?>" name="<?php echo $this->get_field_name( ATKP_WIDGET.'_random' ); ?>" value="1" <?php echo checked(1, isset($instance[ ATKP_WIDGET.'_random' ]) ? $instance[ ATKP_WIDGET.'_random' ] : false, true); ?>>
                                    
                                     <label for="<?php echo $this->get_field_id( ATKP_WIDGET.'_random' ); ?>">
                                            <?php _e('Random sort (List)', ATKP_PLUGIN_PREFIX) ?>
                                        </label> 
        </p>
        
        <?php 
        }
        	
        // Updating widget replacing old instances with new
        public function update( $new_instance, $old_instance ) {
        $instance = array();
        
       $instance[ATKP_WIDGET.'_title'] = ( ! empty( $new_instance[ATKP_WIDGET.'_title'] ) ) ? strip_tags( $new_instance[ATKP_WIDGET.'_title'] ) : '';
        $instance[ATKP_WIDGET.'_product'] = ( ! empty( $new_instance[ATKP_WIDGET.'_product'] ) ) ?  $new_instance[ATKP_WIDGET.'_product'] : '';
        $instance[ATKP_WIDGET.'_list'] = ( ! empty( $new_instance[ATKP_WIDGET.'_list'] ) ) ?  $new_instance[ATKP_WIDGET.'_list'] : '';
        $instance[ATKP_WIDGET.'_template'] = ( ! empty( $new_instance[ATKP_WIDGET.'_template'] ) ) ?  $new_instance[ATKP_WIDGET.'_template'] : '';
        $instance[ATKP_WIDGET.'_containercssclass'] = ( ! empty( $new_instance[ATKP_WIDGET.'_containercssclass'] ) ) ?  $new_instance[ATKP_WIDGET.'_containercssclass'] : '';
        $instance[ATKP_WIDGET.'_elementcssclass'] = ( ! empty( $new_instance[ATKP_WIDGET.'_elementcssclass'] ) ) ?  $new_instance[ATKP_WIDGET.'_elementcssclass'] : '';
        $instance[ATKP_WIDGET.'_limit'] = ( ! empty( $new_instance[ATKP_WIDGET.'_limit'] ) ) ?  $new_instance[ATKP_WIDGET.'_limit'] : '';
        $instance[ATKP_WIDGET.'_random'] = ( ! empty( $new_instance[ATKP_WIDGET.'_random'] ) ) ?  $new_instance[ATKP_WIDGET.'_random'] : '';
        $instance[ATKP_WIDGET.'_usemainproduct'] = ( ! empty( $new_instance[ATKP_WIDGET.'_usemainproduct'] ) ) ?  $new_instance[ATKP_WIDGET.'_usemainproduct'] : '';
        $instance[ATKP_WIDGET.'_content'] = ( ! empty( $new_instance[ATKP_WIDGET.'_content'] ) ) ?  $new_instance[ATKP_WIDGET.'_content'] : '';
        return $instance;
    }
} // Class wpb_widget ends here

// Register and load the widget
function atkp_load_widget() {
	register_widget( ATKP_WIDGET );
}
add_action( 'widgets_init', 'atkp_load_widget' );

?>