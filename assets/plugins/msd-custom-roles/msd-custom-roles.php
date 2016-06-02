<?php
/*
Plugin Name: MSD Custom Roles
Description: Custom plugin for MSDLAB
Author: Catherine Sandrick
Version: 0.0.1
Author URI: http://msdlab.com
*/

if(!class_exists('WPAlchemy_MetaBox')){
    if(!include_once (WP_CONTENT_DIR.'/wpalchemy/MetaBox.php'))
    include_once (plugin_dir_path(__FILE__).'/lib/wpalchemy/MetaBox.php');
}
global $msd_roles;

/*
 * Pull in some stuff from other files
*/
if(!function_exists('requireDir')){
    function requireDir($dir){
        $dh = @opendir($dir);

        if (!$dh) {
            throw new Exception("Cannot open directory $dir");
        } else {
            while($file = readdir($dh)){
                $files[] = $file;
            }
            closedir($dh);
            sort($files); //ensure alpha order
            foreach($files AS $file){
                if ($file != '.' && $file != '..') {
                    $requiredFile = $dir . DIRECTORY_SEPARATOR . $file;
                    if ('.php' === substr($file, strlen($file) - 4)) {
                        require_once $requiredFile;
                    } elseif (is_dir($requiredFile)) {
                        requireDir($requiredFile);
                    }
                }
            }
        }
        unset($dh, $dir, $file, $requiredFile);
    }
}
if (!class_exists('MSDCustomRoles')) {
    class MSDCustomRoles {
        //Properites
        /**
         * @var string The plugin version
         */
        var $version = '0.0.1';
        
        /**
         * @var string The options string name for this plugin
         */
        var $optionsName = 'msd_custom_roles_options';
        
        /**
         * @var string $nonce String used for nonce security
         */
        var $nonce = 'msd_custom_roles-update-options';
        
        /**
         * @var string $localizationDomain Domain used for localization
         */
        var $localizationDomain = "msd_custom_roles";
        
        /**
         * @var string $pluginurl The path to this plugin
         */
        var $plugin_url = '';
        /**
         * @var string $pluginurlpath The path to this plugin
         */
        var $plugin_path = '';
        
        /**
         * @var array $options Stores the options for this plugin
         */
        var $options = array();
        //Methods
        /**
        * PHP 4 Compatible Constructor
        */
        function MSDCustomRoles(){$this->__construct();}
        
        /**
        * PHP 5 Constructor
        */        
        function __construct(){
            //"Constants" setup
            $this->plugin_url = plugin_dir_url(__FILE__).'/';
            $this->plugin_path = plugin_dir_path(__FILE__).'/';
            //Initialize the options
            $this->get_options();
            //check requirements
            register_activation_hook(__FILE__, array(&$this,'check_requirements'));
            //get sub-packages
            //requireDir(plugin_dir_path(__FILE__).'/lib/inc');
            add_action('init', array(&$this,'create_roles'));
        }
        
        function create_roles(){
            $capabilities = array(
                'read' => true,
            );
            remove_role('marketing');
            add_role( 'marketing', 'Marketing', $capabilities );
            $this->events_capabilites('marketing',1,1,1);
            $this->news_capabilites('marketing');
            
            remove_role('human_resources');
            add_role( 'human_resources', 'Human Resources', $capabilities );
            $this->jobs_capabilities('human_resources');
        }
        
        function restrict_pages(){
            if(is_page()){
                if(current_user_can('marketing') || current_user_can('human_resources')){
                    add_action('',array(&$this,'filter_page')); //add to front edit admin bar
                    add_action('',array(&$this,'filter_page')); //add to page menu
                    add_action('',array(&$this,'filter_page')); //add to backend editor
                }
            }
        }
        
        function filter_page(){
            global $post;
            if(current_user_can('marketing')){
                $allowed = array(); //ids of pages allowed by marketing
            } elseif(current_user_can('human_resources')){
                $allowed = array(); //ids of pages allowed by hr
            }
            if(in_array($post->ID, $allowed)){
                //do something.
            }
        }

        function events_capabilites($role,$events = false,$venue = false,$organizers = false){
            if(class_exists('Tribe__Events__Main')){
                $role = get_role($role);
                $event_caps = array(
                'edit_tribe_event',
                'read_tribe_event',
                'delete_tribe_event',
                'delete_tribe_events',
                'edit_tribe_events',
                'edit_others_tribe_events',
                'delete_others_tribe_events',
                'publish_tribe_events',
                'edit_published_tribe_events',
                'delete_published_tribe_events',
                'delete_private_tribe_events',
                'edit_private_tribe_events',
                'read_private_tribe_events',
                );
                $venue_caps = array(
                'edit_tribe_venue',
                'read_tribe_venue',
                'delete_tribe_venue',
                'delete_tribe_venues',
                'edit_tribe_venues',
                'edit_others_tribe_venues',
                'delete_others_tribe_venues',
                'publish_tribe_venues',
                'edit_published_tribe_venues',
                'delete_published_tribe_venues',
                'delete_private_tribe_venues',
                'edit_private_tribe_venues',
                'read_private_tribe_venues',
                );
                $organizer_caps = array(
                'edit_tribe_organizer',
                'read_tribe_organizer',
                'delete_tribe_organizer',
                'delete_tribe_organizers',
                'edit_tribe_organizers',
                'edit_others_tribe_organizers',
                'delete_others_tribe_organizers',
                'publish_tribe_organizers',
                'edit_published_tribe_organizers',
                'delete_published_tribe_organizers',
                'delete_private_tribe_organizers',
                'edit_private_tribe_organizers',
                'read_private_tribe_organizers',
                );
                if($events){
                    foreach($event_caps AS $ec){
                        $role->add_cap($ec);
                    }
                }
                if($venue){
                    foreach($venue_caps AS $vc){
                        $role->add_cap($vc);
                    }
                }
                if($organizers){
                    foreach($organizer_caps AS $oc){
                        $role->add_cap($oc);
                    }
                }
            }
        }

        function jobs_capabilities($role){
            if(class_exists('WP_Job_Manager')){
                $role = get_role($role);
                $job_caps = array(
                "edit_job_listing",
                "read_job_listing",
                "delete_job_listing",
                "edit_job_listings",
                "edit_others_job_listings",
                "publish_job_listings",
                "read_private_job_listings",
                "delete_job_listings",
                "delete_private_job_listings",
                "delete_published_job_listings",
                "delete_others_job_listings",
                "edit_private_job_listings",
                "edit_published_job_listings",
                "manage_job_listing_terms",
                "edit_job_listing_terms",
                "delete_job_listing_terms",
                "assign_job_listing_terms",
                'manage_job_listings',
                );
                foreach($job_caps AS $jc){
                    $role->add_cap($jc);
                }
            }
        }

        function news_capabilites($role){
            if(class_exists('MSDNewsCPT')){
                $role = get_role($role);
                $news_caps = array(
                'edit_news',
                'read_news',
                'delete_news',
                'delete_news',
                'edit_news',
                'edit_others_news',
                'delete_others_news',
                'publish_news',
                'edit_published_news',
                'delete_published_news',
                'delete_private_news',
                'edit_private_news',
                'read_private_news',
                );
                foreach($news_caps AS $nc){
                    $role->add_cap($nc);
                }
            }
        }
        /**
         * @desc Loads the options. Responsible for handling upgrades and default option values.
         * @return array
         */
        function check_options() {
            $options = null;
            if (!$options = get_option($this->optionsName)) {
                // default options for a clean install
                $options = array(
                        'version' => $this->version,
                        'reset' => true
                );
                update_option($this->optionsName, $options);
            }
            else {
                // check for upgrades
                if (isset($options['version'])) {
                    if ($options['version'] < $this->version) {
                        // post v1.0 upgrade logic goes here
                    }
                }
                else {
                    // pre v1.0 updates
                    if (isset($options['admin'])) {
                        unset($options['admin']);
                        $options['version'] = $this->version;
                        $options['reset'] = true;
                        update_option($this->optionsName, $options);
                    }
                }
            }
            return $options;
        }
        
        /**
         * @desc Retrieves the plugin options from the database.
         */
        function get_options() {
            $options = $this->check_options();
            $this->options = $options;
        }
        /**
         * @desc Check to see if requirements are met
         */
        function check_requirements(){
            
        }
        /**
         * @desc Checks to see if the given plugin is active.
         * @return boolean
         */
        function is_plugin_active($plugin) {
            return in_array($plugin, (array) get_option('active_plugins', array()));
        }
        /***************************/
  } //End Class
} //End if class exists statement

//instantiate
$msd_roles = new MSDCustomRoles();