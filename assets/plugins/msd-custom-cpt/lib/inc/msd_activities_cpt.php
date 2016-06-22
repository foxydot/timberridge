<?php
/**
 * @package MSD Activities CPT
 * @version 0.1
 */

class MSDActivityCPT {

    //Properties
    var $cpt = 'msd_activity';
	/**
    * PHP 4 Compatible Constructor
    */
    public function MSDActivityCPT(){$this->__construct();}

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
        add_action( 'init', array(&$this,'register_cpt_activity') );
        add_action('admin_enqueue_scripts', array(&$this,'add_admin_styles') );
        
        //Filters
        
        //Shortcodes
        add_shortcode( 'activity-kit', array(&$this,'list_activity_stories') );
    }
        
        
    function add_metaboxes(){
        global $activity_info,$wpalchemy_media_access;
        $activity_info = new WPAlchemy_MetaBox(array
        (
            'id' => '_activity_info',
            'title' => 'Activity PDFs',
            'types' => array($this->cpt),
            'context' => 'normal',
            'priority' => 'high',
            'template' => WP_PLUGIN_DIR.'/'.plugin_dir_path('msd-custom-cpt/msd-custom-cpt.php').'lib/template/activity-info.php',
            'autosave' => TRUE,
            'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
            'prefix' => '_activity_' // defaults to NULL
        ));
    }
	
	function register_cpt_activity() {
	
	    $labels = array( 
	        'name' => _x( 'Activity PDF', 'activity' ),
	        'singular_name' => _x( 'Activity PDF', 'activity' ),
	        'add_new' => _x( 'Add New', 'activity' ),
	        'add_new_item' => _x( 'Add New Activity PDF', 'activity' ),
	        'edit_item' => _x( 'Edit Activity PDF', 'activity' ),
	        'new_item' => _x( 'New Activity PDF', 'activity' ),
	        'view_item' => _x( 'View Activity PDF', 'activity' ),
	        'search_items' => _x( 'Search Activity PDFs', 'activity' ),
	        'not_found' => _x( 'No activity items found', 'activity' ),
	        'not_found_in_trash' => _x( 'No activity items found in Trash', 'activity' ),
	        'parent_item_colon' => _x( 'Parent Activity PDF:', 'activity' ),
	        'menu_name' => _x( 'Activity PDFs', 'activity' ),
	    );
	
	    $args = array( 
	        'labels' => $labels,
	        'hierarchical' => false,
	        'description' => 'Activity PDFs',
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
	        'rewrite' => array('slug'=>'activity','with_front'=>false),
	        'capability_type' => 'activity',
	        'capabilities' => array(
                'publish_posts' => 'publish_activity',
                'edit_posts' => 'edit_activity',
                'edit_others_posts' => 'edit_others_activity',
                'delete_posts' => 'delete_activity',
                'delete_others_posts' => 'delete_others_activity',
                'read_private_posts' => 'read_private_activity',
                'edit_post' => 'edit_activity',
                'delete_post' => 'delete_activity',
                'read_post' => 'read_activity',
            ),
	    );
	
	    register_post_type( $this->cpt, $args );
	    flush_rewrite_rules();
	}
		
	function list_activity_stories( $atts ) {
	    global $activity_info;
		extract( shortcode_atts( array(
		), $atts ) );
		
		$args = array( 'post_type' => $this->cpt, 'numberposts' => 0, );

		$items = get_posts($args);
	    foreach($items AS $item){
	        $activity_info->the_meta($item->ID);
	        $title = $activity_info->get_the_value('pdf-activity-label')!=''?$activity_info->get_the_value('pdf-activity-label'):$item->post_title;
            if($activity_info->get_the_value('pdf-activity')!=''){
                $title = '<a href="'.$activity_info->get_the_value('pdf-activity').'">'.$title.'</a>';
            }
	     	$publication_list .= '
	     	<li>
	     		<h4><strong>'.$title.'</strong></h4>
			</li>';
	
	     }
		
		return '<ul class="publication-list activity-items">'.$publication_list.'</ul><div class="clear"></div>';
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