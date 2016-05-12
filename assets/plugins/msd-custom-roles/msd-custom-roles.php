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
                'edit_pages' => true,
                'edit_others_pages' => true,
                'edit_published_pages' => true,
            );
            remove_role('marketing');
            remove_role('human_resources');
            add_role( 'marketing', 'Marketing', $capabilities );
            add_role( 'human_resources', 'Human Resources', $capabilities );
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