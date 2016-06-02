<?php
/**
 * @package MSD Publication CPT
 * @version 0.1
 */

class MSDNewsCPT {

	/**
    * PHP 4 Compatible Constructor
    */
    public function MSDNewsCPT(){$this->__construct();}

    /**
     * PHP 5 Constructor
     */
    function __construct(){
        global $current_screen;
        //"Constants" setup
        $this->plugin_url = plugin_dir_url('msd-custom-cpt/msd-custom-cpt.php');
        $this->plugin_path = plugin_dir_path('msd-custom-cpt/msd-custom-cpt.php');
        
        //Actions
        add_action( 'init', array(&$this,'register_cpt_news') );
        
        //Filters
        
        //Shortcodes
        add_shortcode( 'news-items', array(&$this,'list_news_stories') );
    }
        
	
	function register_cpt_news() {
	
	    $labels = array( 
	        'name' => _x( 'News Items', 'news' ),
	        'singular_name' => _x( 'News Item', 'news' ),
	        'add_new' => _x( 'Add New', 'news' ),
	        'add_new_item' => _x( 'Add New News Item', 'news' ),
	        'edit_item' => _x( 'Edit News Item', 'news' ),
	        'new_item' => _x( 'New News Item', 'news' ),
	        'view_item' => _x( 'View News Item', 'news' ),
	        'search_items' => _x( 'Search News Items', 'news' ),
	        'not_found' => _x( 'No news items found', 'news' ),
	        'not_found_in_trash' => _x( 'No news items found in Trash', 'news' ),
	        'parent_item_colon' => _x( 'Parent News Item:', 'news' ),
	        'menu_name' => _x( 'News Items', 'news' ),
	    );
	
	    $args = array( 
	        'labels' => $labels,
	        'hierarchical' => false,
	        'description' => 'Customer News Items',
	        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail'),
	        'taxonomies' => array( 'genre' ),
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
	        'rewrite' => array('slug'=>'news','with_front'=>false),
	        'capability_type' => 'news',
	        'capabilities' => array(
                'publish_posts' => 'publish_news',
                'edit_posts' => 'edit_news',
                'edit_others_posts' => 'edit_others_news',
                'delete_posts' => 'delete_news',
                'delete_others_posts' => 'delete_others_news',
                'read_private_posts' => 'read_private_news',
                'edit_post' => 'edit_news',
                'delete_post' => 'delete_news',
                'read_post' => 'read_news',
            ),
	    );
	
	    register_post_type( 'msd_news', $args );
	    flush_rewrite_rules();
	}
		
	function list_news_stories( $atts ) {
		extract( shortcode_atts( array(
		), $atts ) );
		
		$args = array( 'post_type' => 'msd_news', 'numberposts' => 0, );

		$items = get_posts($args);
	    foreach($items AS $item){ 
	    	$excerpt = $item->post_excerpt?$item->post_excerpt:msd_trim_headline($item->post_content);
	     	$publication_list .= '
	     	<li>
	     		<h4><strong>'.$item->post_title.'</strong></h4>
				<div class="news-entry">'.$item->post_content.'</div>
			</li>';
	
	     }
		
		return '<ul class="publication-list news-items">'.$publication_list.'</ul><div class="clear"></div>';
	}	

        function get_news_items_for_team_member($team_id){
            global $news;
            $args = array( 
                'post_type' => 'msd_news', 
                'numberposts' => -1,
                'order' => 'ASC',
                'orderby' => 'menu_order',
                'meta_query' => array(
                   array(
                       'key' => '_news_team_members',
                       'value' => '"'.$team_id.'"',
                       'compare' => 'LIKE',
                   )
               )
            );
            $the_news = get_posts($args);
            return($the_news);
        }
}