<?php
/**
 * Class for setting up the settings page with the main form.
 * /includes/form.php
 */
class HLSocials_Form {

	/**
	 * Constructor.
	 *
	 * Initializes the admin form functionality.
	 *
	 * @access public
	 */
	public function __construct() {
		// hook the main plugin page
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );

		// register settings fields & sections
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// handle the form submission
		add_action( 'add_option_hlsocials_entries', array( $this, 'handle' ), 10, 2 );
		add_action( 'update_option_hlsocials_entries', array( $this, 'handle' ), 10, 2 );

		// display the plugin's notices
		add_action( 'admin_notices', array($this, 'notices') );
	}

	/**
	 * Get the title of the submenu item page.
	 *
	 * @access public
	 *
	 * @return string $menu_title The title of the submenu item.
	 */
	public function get_menu_title() {
		// allow filtering the title of the submenu page
		$menu_title = apply_filters('hlsocials_menu_item_title', __('Social Post Scheduler', 'hlsocials'));

		return $menu_title;
	}

	/**
	 * Get the ID (slug) of the submenu item page.
	 *
	 * @access public
	 *
	 * @return string $menu_id The ID (slug) of the submenu item.
	 */
	public function get_menu_id() {
		return 'hlsocials';
	}

	/**
	 * Register the main plugin submenu page.
	 *
	 * @access public
	 */
	public function add_submenu_page() {
		$menu_title = $this->get_menu_title();
		$menu_id = $this->get_menu_id();

		// register the submenu page - child of the Settings parent menu item
		add_submenu_page(
			'tools.php',
			$menu_title,
			$menu_title,
			'publish_posts',
			$menu_id,
			array($this, 'render')
		);

		// register settings section
		add_settings_section(
			$menu_id,
			'',
			'',
			$menu_id
		);

	}

	/**
	 * Get field data. Defines and describes the fields that will be registered.
	 *
	 * @access public
	 *
	 * @return array $fields The fields and their data.
	 */
	public function get_field_data() {
		return array(
			'start_date' => array(
				'type' => 'date',
				'title' => __('Start Date', 'hlsocials'),
				'default' => date('Y-m-d', strtotime('+1 days')),
				'help' => __('The date you want to start scheduling from', 'hlsocials'),
				'required' => true,
			),
			'entries' => array(
				'type' => 'textarea',
				'title' => __('Captions (one per line)', 'hlsocials'),
				'default' => '',
				'help' =>
					'<h1>' .
					__('Make sure you have removed " marks and GPT line numbering', 'hlsocials') .
					'</h1><br /><br />' .
					__('A hierarchical list of your entries.', 'hlsocials') .
					'<br /><br />' .

					__('Example 1: This will create 3 posts with the corresponding titles:', 'hlsocials') . '<br />' .
					'<strong style="padding-left: 18px; display: block;">Post 1<br />Post 2<br />Post 3</strong>' .
					 '<br />' .

					__('Example 2: This will create 5 pages with the corresponding titles in the corresponding hierarchy:', 'hlsocials') . '<br />' .
					'<strong style="padding-left: 18px; display: block;">Post X<br />* Post X1<br />** Post X1a<br />* Post X2<br />Post Y</strong>' .
					__('Post X1 is a child of X, while X1a is a child of X1 (considering that the asterisk is used as hierarchy indentation character).', 'hlsocials'),
				'required' => true,
			),
			'post_type' => array(
				'type' => 'select',
				'title' => __('Post Type', 'hlsocials'),
				'default' => 'socials',
				'help' => __('The post type that you want to bulk insert entries into.', 'hlsocials'),
				'options' => HLSocials_Posts::get_post_types(),
				'required' => true,
			),
			'post_status' => array(
				'type' => 'select',
				'title' => __('Post Status', 'hlsocials'),
				'default' => 'future',
				'help' => __('The post status that you want to bulk insert entries into.', 'hlsocials'),
				'options' => array(
					'future' => __('Scheduled', 'hlsocials'),
					'publish' => __('Published', 'hlsocials'),
					'draft' => __('Draft', 'hlsocials'),
					'pending' => __('Pending', 'hlsocials'),
					'private' => __('Private', 'hlsocials'),
					'trash' => __('Trash', 'hlsocials'),
				),
				'required' => true,
			),
			'hierarchy_indent_character' => array(
				'type' => 'text',
				'title' => __('Hierarchy Indent Character', 'hlsocials'),
				'default' => '*',
				'help' => __('You can use this character at the beginning of your entry to specify hierarchy indentation.', 'hlsocials'),
				'required' => true,
			),
		);
	}

	/**
	 * Register the settings sections and fields.
	 *
	 * @access public
	 */
	public function register_settings() {
		// register fields
		$field_data = $this->get_field_data();
		foreach ($field_data as $field_id => $field) {
			$field_object = HLSocials_Field::factory($field['type'], 'hlsocials_' . $field_id, $field['title'], $this->get_menu_id(), $this->get_menu_id());
			if (isset($field['options'])) {
				$field_object->set_options($field['options']);
			}
			$this->fields[] = $field_object;
		}
	}

	/**
	 * Render the settings page with the form.
	 *
	 * @access public
	 */
	public function render() {
		global $hlsocials;

		// determine the form template
		$template = $hlsocials->get_plugin_path() . '/templates/form.php';
		$template = apply_filters('hlsocials_main_template', $template);

		// render the form template
		include_once($template);
	}

	/**
	 * Display the errors/notices of this plugin.
	 *
	 * @access public
	 */
	public function notices() {
		settings_errors( 'hlsocials' );
	}

	/**
	 * Handle the form submission.
	 * Should be hooked on the update_option of the last form field.
	 *
	 * @param string $placeholder Either an option name or the old option value.
	 * @param string $entries_raw The new entries.
	 * @access public
	 */
	public function handle($placeholder, $entries_raw) {

		// prevent recursion
		remove_action( 'update_option_hlsocials_entries', array( $this, 'handle' ) );

		// get the entries
		$entries_raw = get_option('hlsocials_entries');

		// generate the entries hierachy
		$hierarchy = new HLSocials_Hierarchy();
		$hierarchy->set_character( get_option('hlsocials_hierarchy_indent_character') );
		$hierarchy->set_text( $entries_raw );
		$hierarchy->build();

		// determine post type
		$post_type = get_option('hlsocials_post_type');
		if (!$post_type) {
			$post_type = 'socials';
		}

		// determine post status
		$post_status = get_option('hlsocials_post_status');
		if (!$post_status) {
			$post_status = 'future';
		}

		// determine start_date
		$start_date = get_option('hlsocials_start_date');
		if (!$start_date) {
			$start_date = date('c', strtotime('+1 days'));
		}

		// insert the entries hierarchy
		$total_entries = HLSocials_Posts::process_hierarchy($hierarchy->get_hierarchy(), $start_date, $post_type, $post_status);

		// empty the entries field
		update_option('hlsocials_entries', '');

		// add success notice
		$notice = sprintf( _n('1 entry inserted.', '%s entries inserted.', $total_entries, 'hlsocials'), $total_entries );
		add_settings_error('hlsocials', 'settings_updated', $notice, 'updated');
	}

}
