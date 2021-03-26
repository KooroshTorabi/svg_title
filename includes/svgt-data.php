<?php

class SVGT_Data {

	const post_type = 'svgt_data';

	private static $found_items = 0;
	private static $current = null;

	private $id;
	private $title;
	private $locale;
	private $properties = array();

	public static function count() {
		return self::$found_items;
	}

	public static function get_current() {
		return self::$current;
	}

	public static function register_post_type() {
		register_post_type(self::post_type, array(
			'labels' => array(
				'name' => __('SVGT Data', 'svg-title'),
				'singular_name' => __('SVGT Data', 'svg-title'),
			),
			'rewrite' => false,
			'query_var' => false,
			'public' => false,
			'capability_type' => 'page',
			'capabilities' => array(
				'edit_post' => 'publish_pages',
				'read_post' => 'edit_posts',
				'delete_post' => 'publish_pages',
				'edit_posts' => 'publish_pages',
				'edit_others_posts' => 'publish_pages',
				'publish_posts' => 'publish_pages',
				'read_private_posts' => 'publish_pages',
			),
		));
	}

	public static function find($args = '') {
		$defaults = array(
			'post_status' => 'any',
			'posts_per_page' => -1,
			'offset' => 0,
			'orderby' => 'ID',
			'order' => 'ASC',
		);

		$args = wp_parse_args($args, $defaults);

		$args['post_type'] = self::post_type;

		$q = new WP_Query();
		$posts = $q->query($args);

		self::$found_items = $q->found_posts;

		$objs = array();

		foreach ((array) $posts as $post) {
			$objs[] = new self($post);
		}

		return $objs;
	}

	public static function get_template($args = '') {
		global $l10n;

		$defaults = array('locale' => null, 'title' => '');
		$args = wp_parse_args($args, $defaults);

		$locale = $args['locale'];
		$title = $args['title'];

		if ($locale) {
			$mo_orig = $l10n['svg-title'];
			svgt_load_textdomain($locale);
		}

		self::$current = $svgt = new self;
		$svgt->title =
			($title ? $title : __('Untitled', 'svg-title'));
		$svgt->locale = ($locale ? $locale : get_user_locale());

		$properties = $svgt->get_properties();

		if (isset($mo_orig)) {
			$l10n['svg-title'] = $mo_orig;
		}

		return $svgt;
	}

	public static function get_instance($post) {
		$post = get_post($post);

		if (!$post
		or self::post_type != get_post_type($post)) {
			return false;
		}

		return self::$current = new self($post);
	}

	private function __construct($post = null) {
		$post = get_post($post);

		if ($post
		and self::post_type == get_post_type($post)) {
			$this->id = $post->ID;
			$this->title = $post->post_title;
			$this->locale = get_post_meta($post->ID, '_locale', true);

			$properties = $this->get_properties();

			foreach ($properties as $key => $value) {
				if (metadata_exists('post', $post->ID, '_' . $key)) {
					$properties[$key] = get_post_meta($post->ID, '_' . $key, true);
				} elseif (metadata_exists('post', $post->ID, $key)) {
					$properties[$key] = get_post_meta($post->ID, $key, true);
				}
			}

			$this->properties = $properties;
		}

		do_action('SVGT_Data', $this);
	}

	public function __get($name) {
		$message = __('<code>%1$s</code> property of a <code>Title</code> object is <strong>no longer accessible</strong>. Use <code>%2$s</code> method instead.', 'svg-title');

		if ($name == 'id') {
			return $this->id;
		} elseif ($name == 'title') {
			return $this->title;
		} elseif ($prop = $this->prop($name)) {
			return $prop;
		}
	}

	public function initial() {
		return empty($this->id);
	}

	public function prop($name) {
		$props = $this->get_properties();
		return isset($props[$name]) ? $props[$name] : null;
	}

	public function get_properties() {
		$properties = (array)$this->properties;

		$properties = wp_parse_args($properties, array(
			'font' => '',
			'variant' => '',
			'size' => '',
			'aspeed' => array(),
			'strokew' => '',
			'colors' => array(),
			'data' => '',
		));
		return $properties;
	}

	public function set_properties($properties) {
		$defaults = $this->get_properties();

		$properties = wp_parse_args($properties, $defaults);
		$properties = array_intersect_key($properties, $defaults);

		$this->properties = $properties;
	}

	public function id() {
		return $this->id;
	}

	public function title() {
		return $this->title;
	}

	public function set_title($title) {
		$title = strip_tags($title);
		$title = trim($title);

		if ($title === '') {
			$title = 'Untitled';
		}

		$this->title = $title;
	}

	public function locale() {
		if (svgt_is_valid_locale($this->locale)) {
			return $this->locale;
		} else {
			return '';
		}
	}

	public function set_locale($locale) {
		$locale = trim($locale);

		if (svgt_is_valid_locale($locale)) {
			$this->locale = $locale;
		} else {
			$this->locale = 'en_US';
		}
	}

	public function svg_data() {

		$data['before'] = '<p><span class="svgt-wrapper">';
		$data['svg'] = $this->data;
		$data['after'] = '</span></p>';

		$res = apply_filters('svg_data_get', $data);

		return sprintf($res['before'] . '%1$s' . $res['after'], $res['svg']);
	}

	/* Save */

	public function save() {
		$props = $this->get_properties();

		$post_content = isset($props['data']) ? $props['data'] : '';

		if ($this->initial()) {
			$post_id = wp_insert_post( array(
				'post_type' => self::post_type,
				'post_status' => 'publish',
				'post_title' => $this->title,
				'post_content' => trim($post_content),
			));
		} else {
			$post_id = wp_update_post(array(
				'ID' => (int)$this->id,
				'post_status' => 'publish',
				'post_title' => $this->title,
				'post_content' => trim($post_content),
			));
		}

		if ($post_id) {
			foreach ($props as $prop => $value) {
				update_post_meta($post_id, '_' . $prop, $value);
			}

			if (svgt_is_valid_locale($this->locale)) {
				update_post_meta($post_id, '_locale', $this->locale);
			}
		}

		return $post_id;
	}

	public function copy() {
		$new = new self;
		$new->title = $this->title . '_copy';
		$new->locale = $this->locale;
		$new->properties = $this->properties;

		return $new;
	}

	public function delete() {
		if ($this->initial()) {
			return false;
		} else {
			if (wp_delete_post($this->id, true)) {
				$this->id = 0;
				return true;
			} else {
				return false;
			}
		}
	}

	public function shortcode() {

		$shortcode = sprintf('[svgt id="%1$d" title="%2$.16s"]', $this->id, $this->title);

		return $shortcode;
	}
}
