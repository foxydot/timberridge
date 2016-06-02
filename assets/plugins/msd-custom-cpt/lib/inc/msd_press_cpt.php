<?php
/**
 * @package MSD Publication CPT
 * @version 0.1
 */

class MSDPressCPT {

    //Properties
    var $cpt = 'msd_press';
	/**
    * PHP 4 Compatible Constructor
    */
    public function MSDPressCPT(){$this->__construct();}

    /**
     * PHP 5 Constructor
     */
    function __construct(){
        global $current_screen;
        //"Constants" setup
        $this->plugin_url = plugin_dir_url('msd-custom-cpt/msd-custom-cpt.php');
        $this->plugin_path = plugin_dir_path('msd-custom-cpt/msd-custom-cpt.php');
        
        //Actions
        add_action( 'init', array(&$this,'add_metaboxes') );
        add_action( 'init', array(&$this,'register_cpt_press') );
        add_action('admin_enqueue_scripts', array(&$this,'add_admin_styles') );
        
        //Filters
        
        //Shortcodes
        add_shortcode( 'press-kit', array(&$this,'list_press_stories') );
    }
        
        
    function add_metaboxes(){
        global $press_info,$wpalchemy_media_access;
        $press_info = new WPAlchemy_MetaBox(array
        (
            'id' => '_press_info',
            'title' => 'Press Info',
            'types' => array($this->cpt),
            'context' => 'normal',
            'priority' => 'high',
            'template' => WP_PLUGIN_DIR.'/'.plugin_dir_path('msd-custom-cpt/msd-custom-cpt.php').'lib/template/press-info.php',
            'autosave' => TRUE,
            'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
            'prefix' => '_press_' // defaults to NULL
        ));
    }
	
	function register_cpt_press() {
	
	    $labels = array( 
	        'name' => _x( 'Press Kit Item', 'press' ),
	        'singular_name' => _x( 'Press Kit Item', 'press' ),
	        'add_new' => _x( 'Add New', 'press' ),
	        'add_new_item' => _x( 'Add New Press Kit Item', 'press' ),
	        'edit_item' => _x( 'Edit Press Kit Item', 'press' ),
	        'new_item' => _x( 'New Press Kit Item', 'press' ),
	        'view_item' => _x( 'View Press Kit Item', 'press' ),
	        'search_items' => _x( 'Search Press Kit Items', 'press' ),
	        'not_found' => _x( 'No press kit items found', 'press' ),
	        'not_found_in_trash' => _x( 'No press kit items found in Trash', 'press' ),
	        'parent_item_colon' => _x( 'Parent Press Item:', 'press' ),
	        'menu_name' => _x( 'Press Kit', 'press' ),
	    );
	
	    $args = array( 
	        'labels' => $labels,
	        'hierarchical' => false,
	        'description' => 'Customer Press Items',
	        'supports' => array( 'title', 'author', ),
	        'public' => true,
	        'show_ui' => true,
	        'show_in_menu' => true,
	        'menu_position' => 20,
	        
	        'show_in_nav_menus' => true,
	        'publicly_queryable' => true,
	        'exclude_from_search' => false,
	        'has_archive' => false,
	        'query_var' => true,
	        'can_export' => true,
	        'rewrite' => array('slug'=>'press','with_front'=>false),
	        'capability_type' => 'press',
	        'capabilities' => array(
                'publish_posts' => 'publish_press',
                'edit_posts' => 'edit_press',
                'edit_others_posts' => 'edit_others_press',
                'delete_posts' => 'delete_press',
                'delete_others_posts' => 'delete_others_press',
                'read_private_posts' => 'read_private_press',
                'edit_post' => 'edit_press',
                'delete_post' => 'delete_press',
                'read_post' => 'read_press',
            ),
	    );
	
	    register_post_type( $this->cpt, $args );
	    flush_rewrite_rules();
	}
		
	function list_press_stories( $atts ) {
	    global $press_info;
		extract( shortcode_atts( array(
		), $atts ) );
		
		$args = array( 'post_type' => $this->cpt, 'numberposts' => 0, );

		$items = get_posts($args);
	    foreach($items AS $item){
	        $press_info->the_meta($item->ID);
	        $title = $press_info->get_the_value('pdf-press-label')!=''?$press_info->get_the_value('pdf-press-label'):$item->post_title;
            if($press_info->get_the_value('pdf-press')!=''){
                $title = '<a href="'.$press_info->get_the_value('pdf-press').'">'.$title.'</a>';
            }
	     	$publication_list .= '
	     	<li>
	     		<h4><strong>'.$title.'</strong></h4>
			</li>';
	
	     }
		
		return '<ul class="publication-list press-items">'.$publication_list.'</ul><div class="clear"></div>';
	}	


        function add_admin_styles() {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                wp_enqueue_style('thickbox');
                wp_enqueue_style('custom_meta_css',plugin_dir_url(dirname(__FILE__)).'/css/meta.css');
                wp_enqueue_style('jqueryui_smoothness','//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');
            }
        }  

}