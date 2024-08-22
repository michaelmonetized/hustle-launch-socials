<?php
/**
 * Plugin Name: Hustle Launch Socials
 * Plugin URI: https://hustlelaunch.com/plugins/hustle-launch-socials/
 * Description: A handy tool to quickly schedule social posts.
 * Version: 0.0.1
 * Author: Hustle Launch
 * Author URI: https://www.hustlelaunch.com/
 * Plugin URI: https://www.hustlelaunch.com/plugins/hustle-launch-socials/
 * License: GPL2
 * Requires at least: 3.0.1
 * Tested up to: 6.2
 */

/**
 * Main plugin class.
 */
class HLSocials {

	/**
	 * Path to the plugin.
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $plugin_path;

	/**
	 * Form object.
	 *
	 * @access protected
	 *
	 * @var HLSocials_Form
	 */
	protected $form;

	/**
	 * Constructor.
	 *
	 * Hooks all of the plugin functionality.
	 *
	 * @access public
	 */
	public function __construct() {

		// set the path to the plugin main directory
		$this->set_plugin_path(dirname(__FILE__));

		// include all plugin files
		$this->include_files();

		// initialize the admin form
		$this->set_form( new HLSocials_Form() );

		add_action('init', array($this, 'register_socials_post_type'));
	}

	public function register_socials_post_type() {
			if (post_type_exists('socials')) {
					return;
			}

			$args = [
					'labels' => [
						'name'               => _x('Socials', 'post type general name', 'your-plugin-textdomain'),
						'singular_name'      => _x('Social Post', 'post type singular name', 'your-plugin-textdomain'),
						'menu_name'          => _x('Socials', 'admin menu', 'your-plugin-textdomain'),
						'name_admin_bar'     => _x('Social Post', 'add new on admin bar', 'your-plugin-textdomain'),
						'add_new'            => _x('Add New', 'social', 'your-plugin-textdomain'),
						'add_new_item'       => __('Add New Social Post', 'your-plugin-textdomain'),
						'new_item'           => __('New Social Post', 'your-plugin-textdomain'),
						'edit_item'          => __('Edit Social Post', 'your-plugin-textdomain'),
						'view_item'          => __('View Social Post', 'your-plugin-textdomain'),
						'all_items'          => __('All Socials', 'your-plugin-textdomain'),
						'search_items'       => __('Search Socials', 'your-plugin-textdomain'),
						'parent_item_colon'  => __('Parent Socials:', 'your-plugin-textdomain'),
						'not_found'          => __('No socials found.', 'your-plugin-textdomain'),
						'not_found_in_trash' => __('No socials found in Trash.', 'your-plugin-textdomain'),
					],
					'public'             => true,
					'publicly_queryable' => true,
					'show_ui'            => true,
					'show_in_menu'       => true,
					'query_var'          => true,
					'rewrite'            => array('slug' => 'socials'),
					'capability_type'    => 'post',
					'has_archive'        => true,
					'hierarchical'       => false,
					'menu_position'      => null,
					'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
			];

			register_post_type('socials', $args);
	}

	/**
	 * Load the plugin classes and libraries.
	 *
	 * @access protected
	 */
	protected function include_files() {
		require_once($this->get_plugin_path() . '/includes/hierarchy.php');
		require_once($this->get_plugin_path() . '/includes/posts.php');
		require_once($this->get_plugin_path() . '/includes/field.php');
		require_once($this->get_plugin_path() . '/includes/field-text.php');
		require_once($this->get_plugin_path() . '/includes/field-date.php');
		require_once($this->get_plugin_path() . '/includes/field-textarea.php');
		require_once($this->get_plugin_path() . '/includes/field-select.php');
		require_once($this->get_plugin_path() . '/includes/form.php');
	}

	/**
	 * Retrieve the path to the main plugin directory.
	 *
	 * @access public
	 *
	 * @return string $plugin_path The path to the main plugin directory.
	 */
	public function get_plugin_path() {
		return $this->plugin_path;
	}

	/**
	 * Modify the path to the main plugin directory.
	 *
	 * @access protected
	 *
	 * @param string $plugin_path The new path to the main plugin directory.
	 */
	protected function set_plugin_path($plugin_path) {
		$this->plugin_path = $plugin_path;
	}

	/**
	 * Retrieve the form object.
	 *
	 * @access public
	 *
	 * @return string $form The form object.
	 */
	public function get_form() {
		return $this->form;
	}

	/**
	 * Modify the form object.
	 *
	 * @access protected
	 *
	 * @param string $form The new form object.
	 */
	protected function set_form($form) {
		$this->form = $form;
	}

}

// initialize the plugin
global $hlsocials;
$hlsocials = new HLSocials();
