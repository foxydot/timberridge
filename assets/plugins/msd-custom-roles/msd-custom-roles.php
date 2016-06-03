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
            add_action('init', array(&$this,'tweak_plugins'), 20);
        }
        
        function create_roles(){
            $capabilities = array(
                'read' => true,
                'upload_files' => true,
                'gravityforms_view_entries' => true,
                'gravityforms_view_entry_notes' => true,
                'gravityforms_edit_entry_notes' => true,
                'gravityforms_export_entries' => true,
            );
            remove_role('marketing');
            add_role( 'marketing', 'Marketing', $capabilities );
            $this->events_capabilites('marketing',1,1,1);
            $this->news_capabilites('marketing');
            $this->press_capabilites('marketing');
            
            $this->news_capabilites('administrator');
            $this->press_capabilites('administrator');
            
            remove_role('human_resources');
            add_role( 'human_resources', 'Human Resources', $capabilities );
            $this->jobs_capabilities('human_resources');
            
            
        }
        
        function tweak_plugins(){
            if(class_exists('Tribe__Events__Main')){
                remove_post_type_support( 'tribe_events', 'thumbnail' );
                remove_post_type_support( 'tribe_events', 'excerpt' );
                remove_post_type_support( 'tribe_events', 'trackbacks' );
                remove_post_type_support( 'tribe_events', 'comments' );
                remove_post_type_support( 'tribe_events', 'page-attributes' );
            }
            if(current_user_can('marketing') || current_user_can('human_resources')){
                add_filter('upload_mimes', array(&$this,'restrict_uploads'), 1, 1);
            }
        }
        
        function restrict_uploads($mime_types){
            $mime_types = array(
                'txt|asc|c|cc|h|srt' => 'text/plain',
                'csv' => 'text/csv',
                'tsv' => 'text/tab-separated-values',
                'ics' => 'text/calendar',
                'rtx' => 'text/richtext',
                'css' => 'text/css',
                'vtt' => 'text/vtt',
                'dfxp' => 'application/ttaf+xml',
                'rtf' => 'application/rtf',
                'js' => 'application/javascript',
                'pdf' => 'application/pdf',
                'class' => 'application/java',
                'tar' => 'application/x-tar',
                'zip' => 'application/zip',
                'gz|gzip' => 'application/x-gzip',
                'rar' => 'application/rar',
                '7z' => 'application/x-7z-compressed',
                'psd' => 'application/octet-stream',
                'xcf' => 'application/octet-stream',
                'doc' => 'application/msword',
                'pot|pps|ppt' => 'application/vnd.ms-powerpoint',
                'wri' => 'application/vnd.ms-write',
                'xla|xls|xlt|xlw' => 'application/vnd.ms-excel',
                'mdb' => 'application/vnd.ms-access',
                'mpp' => 'application/vnd.ms-project',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
                'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
                'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
                'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
                'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
                'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
                'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
                'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
                'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
                'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
                'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
                'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
                'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
                'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
                'sldm' => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
                'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote',
                'oxps' => 'application/oxps',
                'xps' => 'application/vnd.ms-xpsdocument',
                'odt' => 'application/vnd.oasis.opendocument.text',
                'odp' => 'application/vnd.oasis.opendocument.presentation',
                'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
                'odg' => 'application/vnd.oasis.opendocument.graphics',
                'odc' => 'application/vnd.oasis.opendocument.chart',
                'odb' => 'application/vnd.oasis.opendocument.database',
                'odf' => 'application/vnd.oasis.opendocument.formula',
                'wp|wpd' => 'application/wordperfect',
                'key' => 'application/vnd.apple.keynote',
                'numbers' => 'application/vnd.apple.numbers',
                'pages' => 'application/vnd.apple.pages',
            );
            return $mime_types;
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
        
        function press_capabilites($role){
            if(class_exists('MSDPressCPT')){
                $role = get_role($role);
                $press_caps = array(
                'edit_press',
                'read_press',
                'delete_press',
                'delete_press',
                'edit_press',
                'edit_others_press',
                'delete_others_press',
                'publish_press',
                'edit_published_press',
                'delete_published_press',
                'delete_private_press',
                'edit_private_press',
                'read_private_press',
                );
                foreach($press_caps AS $pc){
                    $role->add_cap($pc);
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