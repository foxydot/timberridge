<?php 
if (!class_exists('MSDTestimonialCPT')) {
	class MSDTestimonialCPT {
		//Properties
		var $cpt = 'testimonial';
		//Methods
	    /**
	    * PHP 4 Compatible Constructor
	    */
		public function MSDTestimonialCPT(){$this->__construct();}
	
		/**
		 * PHP 5 Constructor
		 */
		function __construct(){
			global $current_screen;
        	//"Constants" setup
        	$this->plugin_url = plugin_dir_url('msd-custom-cpt/msd-custom-cpt.php');
        	$this->plugin_path = plugin_dir_path('msd-custom-cpt/msd-custom-cpt.php');
			//Actions
            add_action( 'init', array(&$this,'register_cpt_testimonial') );
            add_action( 'init', array( &$this, 'add_metaboxes' ) );
            
            if(class_exists('MSD_Widget_Random_Testimonial')){
                add_action('widgets_init',array('MSD_Widget_Random_Testimonial','init'),10);
            }
			add_action('admin_head', array(&$this,'plugin_header'));
			add_action('admin_print_scripts', array(&$this,'add_admin_scripts') );
			add_action('admin_print_styles', array(&$this,'add_admin_styles') );
			// important: note the priority of 99, the js needs to be placed after tinymce loads
			add_action('admin_print_footer_scripts',array(&$this,'print_footer_scripts'),99);
			
			//Filters
			//add_filter( 'pre_get_posts', array(&$this,'custom_query') );
            add_shortcode('testimonial',array(&$this,'testimonial_shortcode_handler'));
            add_shortcode('testimonials',array(&$this,'testimonial_shortcode_handler'));
		}
		
		function register_cpt_testimonial() {
		
		    $labels = array( 
		        'name' => _x( 'Testimonials', 'testimonial' ),
		        'singular_name' => _x( 'Testimonial', 'testimonial' ),
		        'add_new' => _x( 'Add New', 'testimonial' ),
		        'add_new_item' => _x( 'Add New Testimonial', 'testimonial' ),
		        'edit_item' => _x( 'Edit Testimonial', 'testimonial' ),
		        'new_item' => _x( 'New Testimonial', 'testimonial' ),
		        'view_item' => _x( 'View Testimonial', 'testimonial' ),
		        'search_items' => _x( 'Search Testimonial', 'testimonial' ),
		        'not_found' => _x( 'No testimonial found', 'testimonial' ),
		        'not_found_in_trash' => _x( 'No testimonial found in Trash', 'testimonial' ),
		        'parent_item_colon' => _x( 'Parent Testimonial:', 'testimonial' ),
		        'menu_name' => _x( 'Testimonial', 'testimonial' ),
		    );
		
		    $args = array( 
		        'labels' => $labels,
		        'hierarchical' => false,
		        'description' => 'Testimonial',
		        'supports' => array( 'title', 'author' ,'genesis-cpt-archives-settings'),
		        'taxonomies' => array(),
		        'public' => true,
		        'show_ui' => true,
		        'show_in_menu' => true,
		        'menu_position' => 20,
		        
		        'show_in_nav_menus' => true,
		        'publicly_queryable' => true,
		        'exclude_from_search' => true,
		        'has_archive' => true,
		        'query_var' => true,
		        'can_export' => true,
		        'rewrite' => array('slug'=>'testimonial','with_front'=>false),
		        'capability_type' => 'post'
		    );
		
		    register_post_type( $this->cpt, $args );
        
		}
		
		function plugin_header() {
			global $post_type;
		}
		 
		function add_admin_scripts() {
			global $current_screen;
			if($current_screen->post_type == $this->cpt){
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-datepicker');
                wp_enqueue_script('jquery-timepicker',plugin_dir_url(dirname(__FILE__)).'js/jquery.timepicker.min.js',array('jquery'));
                wp_enqueue_script('media-upload');
                wp_enqueue_script('thickbox');
			}
		}

        function add_admin_styles() {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                wp_enqueue_style('thickbox');
                wp_enqueue_style('jquery-ui-style','http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css');
                wp_enqueue_style('custom_meta_css',plugin_dir_url(dirname(__FILE__)).'css/meta.css');
            }
        }   
			
		function print_footer_scripts()
		{
			global $current_screen;
			if($current_screen->post_type == $this->cpt){
				?><script type="text/javascript">
                    jQuery(function($){
                        $( ".datepicker" ).datepicker({
                        onSelect : function(dateText, inst)
                        {
                            var epoch = $.datepicker.formatDate('@', $(this).datepicker('getDate')) / 1000;
                            $('.datestamp').val(epoch);
                        }
                        });
                        $('.timepicker').timepicker({ 'scrollDefaultNow': true });
                        $("#postdivrich").after($("#_testimonial_info_metabox"));
                    });
                 </script><?php
			}
		}
		

		function custom_query( $query ) {
			if(!is_admin()){
				if($query->is_main_query() && $query->is_search){
					$searchterm = $query->query_vars['s'];
					// we have to remove the "s" parameter from the query, because it will prtestimonial the posts from being found
					$query->query_vars['s'] = "";
					
					if ($searchterm != "") {
						$query->set('meta_value',$searchterm);
						$query->set('meta_compare','LIKE');
					};
					$query->set( 'post_type', array('post','page',$this->cpt) );
				}
			}
		}	
        
        function testimonial_shortcode_handler($atts){
            extract( shortcode_atts( array(
                'rows' => 1,
                'columns' => 1,
                'link' => false,
            ), $atts ) );
            global $testimonial_info;
            $args = array(
                'post_type' => $this->cpt,
                'orderby' => rand,
                'posts_per_page' => $rows * $columns,
            );
            $testimonials = get_posts($args);
            $ret = false;
            foreach($testimonials AS $testimonial){
                $testimonial_info->the_meta($testimonial->ID);
                $title = $testimonial->post_title;
                $quote = apply_filters('the_content',$testimonial_info->get_the_value('quote'));
                $name = $testimonial_info->get_the_value('attribution')!=''?'<span class="name">'.$testimonial_info->get_the_value('attribution').',</span> ':'';
                $position = $testimonial_info->get_the_value('position')!=''?'<span class="position">'.$testimonial_info->get_the_value('position').',</span> ':'';
                $company = $testimonial_info->get_the_value('company')!=''?'<span class="company">'.$testimonial_info->get_the_value('company').'</span> ':'';
                $ret .= '<div class="col-md-'. 12/$columns .' col-sm-1 item-wrapper">
                <div class="title"><h3>'.$title.'</h3></div>
                <div class="quote">'.$quote.'</div>
                <div class="attribution">'.$name.$position.$company.'</div>
                </div>';
            }
            if($link){
                $link_text = is_string($link)?$link:'Read More Testimonials';
                $ret .= '<div class="link-wrapper"><a href="'.get_post_type_archive_link($this->cpt).'">'.$link_text.'</a></div>';
            }
            $ret = '<div class="msdlab_testimonial_gallery">'.$ret.'</div>';
            
            return $ret;
        } 

        function add_metaboxes(){
                global $post,$wpalchemy_media_access,$testimonial_info;
                $testimonial_info = new WPAlchemy_MetaBox(array
                    (
                        'id' => '_testimonial_info',
                        'title' => 'Testimonial Info',
                        'types' => array('testimonial'),
                        'context' => 'normal',
                        'priority' => 'high',
                        'template' => WP_PLUGIN_DIR.'/'.plugin_dir_path('msd-custom-cpt/msd-custom-cpt.php').'lib/template/testimonial-information.php',
                        'autosave' => TRUE,
                        'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
                        'prefix' => '_testimonial_' // defaults to NULL
                    ));
            }
            
  } //End Class
} //End if class exists statement

class MSD_Widget_Random_Testimonial extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'widget_random_testimonial', 'description' => __('Displays a random testimonial.'));
        parent::__construct('widget_random_testimonial', __('Random Testimonial'), $widget_ops, $control_ops);
    }
    function widget( $args, $instance ) {
        $cpt = new MSDTestimonialCPT();
        extract($args);
        echo $before_widget; 
        print '<h4 class="widget-title widgettitle">'.$post->post_title.'</h4>';
        print '<div class="wrap">';
        print $cpt->testimonial_shortcode_handler(); 
        print '
        <div class="clearfix"></div>
        </div>';
        echo $after_widget;
    }
    function init() {
        if ( !is_blog_installed() )
            return;
        register_widget('MSD_Widget_Random_Testimonial');
    }  
}