<?php 
if (!class_exists('MSDBrandCPT')) {
    class MSDBrandCPT {
        //Properties
        var $cpt = 'brand';
        //Methods
        /**
        * PHP 4 Compatible Constructor
        */
        public function MSDBrandCPT(){$this->__construct();}
    
        /**
         * PHP 5 Constructor
         */
        function __construct(){
            global $current_screen;
            //"Constants" setup
            $this->plugin_url = plugin_dir_url('msd-custom-cpt/msd-custom-cpt.php');
            $this->plugin_path = plugin_dir_path('msd-custom-cpt/msd-custom-cpt.php');
            //Actions
            add_action( 'init', array(&$this,'register_cpt_brand') );           
            add_action( 'init', array(&$this,'register_taxonomy_market_sector') );
            add_action('admin_head', array(&$this,'plugin_header'));
            add_action('admin_print_scripts', array(&$this,'add_admin_scripts') );
            add_action('admin_print_styles', array(&$this,'add_admin_styles') );
            add_action('admin_footer',array(&$this,'info_footer_hook') );
            // important: note the priority of 99, the js needs to be placed after tinymce loads
            //add_action('print_footer_scripts',array(&$this,'print_footer_scripts'), 99);
            
            add_action('wp_enqueue_scripts', array(&$this,'add_scripts') );
            //Filters
            add_filter( 'pre_get_posts', array(&$this,'custom_query') );
            add_filter( 'enter_title_here', array(&$this,'change_default_title') );
            
            add_shortcode('logo_gallery', array(&$this,'logo_gallery'));
            add_image_size('logo',300,400,false);
        }


        
        function register_cpt_brand() {
        
            $labels = array( 
                'name' => _x( 'Brands', 'brand' ),
                'singular_name' => _x( 'Brand', 'brand' ),
                'add_new' => _x( 'Add New', 'brand' ),
                'add_new_item' => _x( 'Add New Brand', 'brand' ),
                'edit_item' => _x( 'Edit Brand', 'brand' ),
                'new_item' => _x( 'New Brand', 'brand' ),
                'view_item' => _x( 'View Brand', 'brand' ),
                'search_items' => _x( 'Search Brand', 'brand' ),
                'not_found' => _x( 'No brand found', 'brand' ),
                'not_found_in_trash' => _x( 'No brand found in Trash', 'brand' ),
                'parent_item_colon' => _x( 'Parent Brand:', 'brand' ),
                'menu_name' => _x( 'Brand', 'brand' ),
            );
        
            $args = array( 
                'labels' => $labels,
                'hierarchical' => false,
                'description' => 'Brand',
                'supports' => array( 'title', 'thumbnail' ),
                'taxonomies' => array( 'market_sector' ),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 20,
                
                'show_in_nav_menus' => true,
                'publicly_queryable' => true,
                'exclude_from_search' => true,
                'has_archive' => false,
                'query_var' => true,
                'can_export' => true,
                'rewrite' => array('slug'=>'brand','with_front'=>false),
                'capability_type' => 'post'
            );
        
            register_post_type( $this->cpt, $args );
        }
        
        function register_taxonomy_market_sector(){
            
            $labels = array( 
                'name' => _x( 'Market sectors', 'market-sectors' ),
                'singular_name' => _x( 'Market sector', 'market-sectors' ),
                'search_items' => _x( 'Search market sectors', 'market-sectors' ),
                'popular_items' => _x( 'Popular market sectors', 'market-sectors' ),
                'all_items' => _x( 'All market sectors', 'market-sectors' ),
                'parent_item' => _x( 'Parent market sector', 'market-sectors' ),
                'parent_item_colon' => _x( 'Parent market sector:', 'market-sectors' ),
                'edit_item' => _x( 'Edit market sector', 'market-sectors' ),
                'update_item' => _x( 'Update market sector', 'market-sectors' ),
                'add_new_item' => _x( 'Add new market sector', 'market-sectors' ),
                'new_item_name' => _x( 'New market sector name', 'market-sectors' ),
                'separate_items_with_commas' => _x( 'Separate market sectors with commas', 'market-sectors' ),
                'add_or_remove_items' => _x( 'Add or remove market sectors', 'market-sectors' ),
                'choose_from_most_used' => _x( 'Choose from the most used market sectors', 'market-sectors' ),
                'menu_name' => _x( 'Market sectors', 'market-sectors' ),
            );
        
            $args = array( 
                'labels' => $labels,
                'public' => true,
                'show_in_nav_menus' => true,
                'show_ui' => true,
                'show_tagcloud' => false,
                'hierarchical' => true, //we want a "category" style taxonomy, but may have to restrict selection via a dropdown or something.
        
                'rewrite' => array('slug'=>'market-sector','with_front'=>false),
                'query_var' => true
            );
        
            register_taxonomy( 'market_sector', array($this->cpt), $args );
        }
        function plugin_header() {
            global $post_type;
            ?>
            <?php
        }
         
        function add_admin_scripts() {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                wp_enqueue_script('media-upload');
                wp_enqueue_script('thickbox');
                //wp_register_script('my-upload', plugin_dir_url(dirname(__FILE__)).'/js/msd-upload-file.js', array('jquery','media-upload','thickbox'),FALSE,TRUE);
                wp_enqueue_script('my-upload');
            }
        }
        function add_scripts() {
            if(is_front_page()){
                wp_enqueue_script('msd-img-loader',plugin_dir_url(dirname(__FILE__)).'/js/jquery.loadimg.js',array('jquery'),1,TRUE);
            }
        }
        
        function add_admin_styles() {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                wp_enqueue_style('thickbox');
                wp_enqueue_style('custom_meta_css',plugin_dir_url(dirname(__FILE__)).'/css/meta.css');
            }
        }   
            
        function print_footer_scripts()
        {
            if(is_front_page()){
                print '<script>
                (function($) {
                    $.fn.load_bkg = function(opts) {
                        // default configuration
                        var config = $.extend({}, {
                            opt1: null
                        }, opts);
                    
                        // main function
                        function loadit(e) {
                      var bkg = img_array[0];
                      img_array.shift();
                      e.css(\'background-image\',\'url("\'+bkg+\'")\').fadeIn(1000).delay(5000).fadeOut(1000,function(){
                        e.trigger(\'click\');
                      });
                      img_array.push(bkg);
                        }
                
                        // initialize every element
                        this.each(function() {
                            loadit($(this));
                        });
                
                        return this;
                    };
                })(jQuery);
                </script>';
              }
        }
        function change_default_title( $title ){
            global $current_screen;
            if  ( $current_screen->post_type == $this->cpt ) {
                return __('Brand Name','brand');
            } else {
                return $title;
            }
        }
        
        function info_footer_hook()
        {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                ?><script type="text/javascript">
                        jQuery('#titlediv').after(jQuery('#postimagediv'));
                        jQuery('#postimagediv h3.hndle span').html('Logo Image');
                        jQuery('#postimagediv #set-post-thumbnail').html(function(){
                            if($(this).html == "Set featured image"){
                                return 'Set logo image';
                            } else {
                                return $(this).html();
                            }
                        }).attr('title',function(){
                            if($(this).html == "Set featured image"){
                                return 'Set logo image';
                            } else {
                                return $(this).html();
                            }
                        });
                    </script><?php
            }
        }
        

        function custom_query( $query ) {
            if(!is_admin()){
                //$is_brand = ($query->query_vars['brand_type'])?TRUE:FALSE;
                if($query->is_main_query() && $query->is_search){
                    $searchterm = $query->query_vars['s'];
                    // we have to remove the "s" parameter from the query, because it will prevent the posts from being found
                    //$query->query_vars['s'] = "";
                    
                    if ($searchterm != "") {
                        $query->set('meta_value',$searchterm);
                        $query->set('meta_compare','LIKE');
                    };
                    $posttypes = $query->query_vars['post_type'];
                    $posttypes[] = $this->cpt;
                    $query->set( 'post_type', $posttypes );
                }
            }
        }      
        
              /**
         * Logo gallery JS: Support for anaimating logos displaying on a randomized timeline. Replaces logo_gallery.
         * @param array $atts WordPress shortcode attributes
         * @return string $ret Display string
         */
        function logo_gallery_js($atts){
            extract( shortcode_atts( array(
                'rows' => 4,
                'columns' => 4,
                'fade_in' => 'random',
                'animate' => false,
                'item_height' => '120px',
            ), $atts ) );
            
            $args = array(
                'post_type' => $this->cpt,
                'orderby' => rand,
            );
            $grid_cells_count = $rows * $columns;
            $i = 1;
            while($i <= $grid_cells_count){
                $grid .= '<div class="col-md-'. 12/$columns .' col-sm-1 item-wrapper"><div class="item item-'.$i.'"></div></div>';
                $i++;
            }
            
            
            
            switch($animate){
                case true:
                case 'random':
                    $args['posts_per_page'] = -1;
                    break;
                case false:
                    $args['posts_per_page'] = $grid_cells_count;
                    break;
            }
            $brands = get_posts($args);
            $ret = false;
            foreach($brands AS $brand){
                $logo_image = wp_get_attachment_image_src( get_post_thumbnail_id($brand->ID), 'logo' );
                $logo_array[] = $logo_image[0];
            }
            $ret = '<div class="msdlab_logo_gallery">'.$grid.'</div>';
            $ret .= '
            <style>
                .msdlab_logo_gallery .item-wrapper {
                    height: 120px;
                    padding: 2rem 4rem;
                }
                .msdlab_logo_gallery .item-wrapper .item {
                    display: none;
                    background-size: contain;
                    background-repeat: no-repeat;
                    background-position: center center;
                    height: 100%;
                    width: 100%;
                }
            </style>
            <script>
                var img_array = '.json_encode($logo_array).';
                console.log(img_array);
                jQuery(document).ready(function($) {
                  //initial load
                  $(".msdlab_logo_gallery .item-wrapper .item").each(function(){
                    var faderate = Math.floor(Math.random() * 5000) + 1000;
                    $(this).delay(faderate).load_bkg();
                  });
                  $(".msdlab_logo_gallery .item-wrapper .item").click(function(){
                    $(this).load_bkg();
                  });
                });
            </script>';
            return $ret;
        }     
        
        
        
        /**
         * Logo gallery CSS: Support for non-naimating logos displaying on a randomized timeline. Legacy function supplanted by logo_gallery_js.
         * @param array $atts WordPress shortcode attributes
         * @return string $ret Display string
         */
        function logo_gallery($atts){
            extract( shortcode_atts( array(
                'rows' => 4,
                'columns' => 4,
                'fade_in' => 'random',
                'animate' => false,
                'item_height' => '120px',
            ), $atts ) );
            
            $args = array(
                'post_type' => $this->cpt,
                'orderby' => rand,
            );
            switch($animate){
                case true:
                case 'random':
                    $args['posts_per_page'] = -1;
                    break;
                case false:
                    $args['posts_per_page'] = $rows * $columns;
                    break;
            }
            $brands = get_posts($args);
            $ret = false;
            foreach($brands AS $brand){
                $logo_image = wp_get_attachment_image_src( get_post_thumbnail_id($brand->ID), 'logo' );
                $logo_url = $logo_image[0];
                $terms = wp_get_post_terms($brand->ID,'market_sector');
                $market_sector = array();
                $market_sectors = false;
                if(count($terms)>0){
                    foreach($terms AS $term){
                        $market_sector[] = $term->slug;
                    }
                    $market_sectors = implode(' ', $market_sector);
                }
                switch($fade_in){
                    case 'rand':
                    case 'random':
                        $fade = rand(1,50).'00ms';
                        break;
                    case is_numeric($fade_in):
                        $fade = $fade_in.'ms';
                        break;
                    case true:
                        $fade = '2000ms';
                        break;
                    case false:
                    default:
                        $fade = '1ms';
                        break;
                }
                $ret .= '<div class="col-md-'. 12/$columns .' col-xs-6 item-wrapper '.$market_sectors.'"><div class="item" style="background-image:url('.$logo_url.');"></div></div>';
                $i++;
            }
           $filters[] = '<a href="#" data-filter="*" class="active button">View All</a>';
           $terms = get_terms('market_sector',array('orderby'=>'slug','order'=>'ASC'));
           foreach($terms AS $term){
               $filters[] = '<a href="#" class="button" data-filter=".'.$term->slug.'">'.$term->name.'</a>';
           }
           $menu = $allbutton.'<div id="filters">'.implode(' ', $filters).'</div>';
            $ret = $menu.'<div id="msdlab_logo_gallery" class="msdlab_logo_gallery">'.$ret.'</div>';
            $ret .= '
            <style>
                .msdlab_logo_gallery .item-wrapper {
                    padding: 2rem 4rem;
                }
                .msdlab_logo_gallery .item-wrapper .item {
                    background-size: contain;
                    background-repeat: no-repeat;
                    background-position: center center;
                    min-height: '.$item_height.';
                    min-width: '.$item_height.';
                }
                @media only screen and (max-width: 1023px) {
                    .msdlab_logo_gallery .item-wrapper .item {
                        min-height: 15vw;
                        min-width: 15vw;
                    }
                }
            </style>
            <script>
                jQuery(document).ready(function($) {   
                    $(".msdlab_logo_gallery .item-wrapper").css("opacity",1);
                });
                jQuery(window).load(function() {
                    jQuery(".msdlab_logo_gallery").isotope({
                      itemSelector : ".item-wrapper",
                      layoutMode: "fitRows",
                    }); 
                    
                    // filter items when filter link is clicked
                    jQuery("#filters a").click(function(){
                      jQuery("#filters a").removeClass("active");
                      jQuery(this).addClass("active");
                      var selector = jQuery(this).attr("data-filter");
                      jQuery(".msdlab_logo_gallery").isotope({
                          itemSelector : ".item-wrapper",
                          layoutMode : "fitRows",
                          filter: selector
                        }); 
                      return false;
                    });   
                    jQuery( window ).scroll(function() {
                        jQuery(".msdlab_logo_gallery").isotope();
                    });
                } );
            </script>';
            return $ret;
        }     
  } //End Class
} //End if class exists statement