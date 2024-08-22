<?php
/**
 * Handles functionality, related to posts.
 * /includes/posts.php
 */
class HLSocials_Posts {

	/**
	 * Retrieve the available post types.
	 *
	 * @access public
	 * @static
	 *
	 * @return array $post_types Retrieve the available post types.
	 */
	public static function get_post_types() {
		$post_types = array();

		$all_post_types = get_post_types(array(), 'objects');
		foreach ($all_post_types as $post_type_name => $post_type_object) {
			$post_types[$post_type_name] = $post_type_object->labels->name;
		}

		return $post_types;
	}

	/**
	 * Using an array hierarchy, insert the entries hierarchy.
	 *
	 * @access public
	 * @static
	 *
	 * @param array $hierarchy Hierarchy of entries to insert.
	 * @param string $post_type Post type of the entries.
	 * @param string $post_status Post status of the entries.
	 * @param int $parent ID of the parent entry.
	 * @return int $total Number of entries that were inserted.
	 */
	public static function process_hierarchy($hierarchy = array(), $start_date = false, $post_type = 'socials', $post_status = 'future', $parent = 0) {
		$start_date = $start_date ? $start_date : date('c', strtotime('+1 days'));

		$total = 0;
		foreach ($hierarchy as $hierarchy_entry) {
			$next_time = strtotime("$start_date +$total days");
			$next_date = date('c', $next_time);

			$id = self::insert($post_type, $hierarchy_entry['title'], $post_status, $parent, $next_date);
			$total++;

			if ( !empty($hierarchy_entry['children']) ) {
				$total += self::process_hierarchy($hierarchy_entry['children'], $next_date, $post_type, $post_status, $id);
			}
		}

		return $total;
	}

	/**
	 * Insert a post of certain post type with a certain title under a specific parent.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $post_type Post type of the post.
	 * @param string $title Title of the post.
	 * @param string $post_status Post status of the post.
	 * @param int $parent ID of the parent post.
	 * @return int $id The ID of the inserted post.
	 */
	public static function insert($post_type, $title, $post_status, $parent = 0, $date = false) {
		if(!$date){
			$date = date('Y-m-d H:i:s', strtotime('+1 days'));
		}

		$content = $title;
		$title_arr = explode(' ', $title);
		$short_title = $title_arr[0].' '.$title_arr[1];

		$id = wp_insert_post(array(
			'post_type' => $post_type,
			'post_title' => $short_title,
			'post_content' => $content,
			'post_parent' => $parent,
			'post_status' => $post_status,
			'post_date' => $date,
			'post_date_gmt' => get_gmt_from_date( $date ),
		));

		return $id;
	}

}
