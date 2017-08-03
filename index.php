<?php
/*
Plugin Name: Post thumbnail in Post admin list
Plugin URI: https://wordpress.org/plugins/mhm-list-postthumbnail/
Description: Adds a new column to the WordPress admin post list view, containing a thumbnail-sized preview of the post thumbnail (where available).
Version: 1.2.2.1
Author: Mark Howells-Mead
Author URI: http://permanenttourist.ch/
Donate link: https://www.paypal.me/mhmli
Licence: GPL3
Text Domain: mhm-list-postthumbnail
Domain Path: /languages
*/

class MHMListPostThumbnail
{
    public $version = '1.2.2.1';
    public $wpversion = '4.0';

    public function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'check_version'));
        add_action('admin_init', array($this, 'check_version'));

        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_filter('manage_posts_columns', array($this, 'add_post_column'), 20);
        add_action('manage_posts_custom_column', array($this, 'custom_post_column'), 5, 2);
    }

    public function check_version()
    {
        // Check that this plugin is compatible with the current version of WordPress
        if (!$this->compatible_version()) {
            if (is_plugin_active(plugin_basename(__FILE__))) {
                deactivate_plugins(plugin_basename(__FILE__));
                add_action('admin_notices', array($this, 'disabled_notice'));
                if (isset($_GET['activate'])) {
                    unset($_GET['activate']);
                }
            }
        }
    }

    public function disabled_notice()
    {
        echo '<div class="notice notice-error is-dismissible">
            <p>'.sprintf(
                _x('The plugin “%1$s” requires WordPress %2$s or higher!', 'Compatibility warning message on activation', 'mhm-list-postthumbnail'),
                _x('Post thumbnail in Post admin list', 'Plugin name in compatibility warning message', 'mhm-list-postthumbnail'),
                $this->wpversion
            ).'</p>
        </div>';
    }

    private function compatible_version()
    {
        if (version_compare($GLOBALS['wp_version'], $this->wpversion, '<')) {
            return false;
        }

        return true;
    }

    public function load_textdomain()
    {
        load_plugin_textdomain('mhm-list-postthumbnail', false, plugin_basename(dirname(__FILE__)).'/languages');
    }

    public function add_post_column($cols)
    {
        // Add column and header
        $cols['mhm-list-postthumbnail'] = __('Thumbnail', 'The column header', 'mhm-list-postthumbnail');

        return $cols;
    }

    public function custom_post_column($column_name, $post_id)
    {
        //  show content for each row
        switch ($column_name) {
            case 'mhm-list-postthumbnail':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, 'thumbnail');
                } else {
                    echo __('None', 'Text or HTML which is displayed in the list view if there is no thumbnail available', 'mhm-list-postthumbnail');
                }
                break;
        }
    }
}

new MHMListPostThumbnail();
