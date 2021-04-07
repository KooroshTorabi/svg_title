<?php

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class SVGT_List_Table extends WP_List_Table {

	public static function define_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => esc_html(__('Title', 'svg-title')),
			'shortcode' => esc_html(__('Shortcode', 'svg-title')),
			'author' => esc_html(__('Author', 'svg-title')),
			'date' => esc_html(__('Date', 'svg-title')),
		);

		return $columns;
	}

	public function __construct() {
		parent::__construct(array(
			'singular' => 'post',
			'plural' => 'posts',
			'ajax' => false,
		));
	}

	public function prepare_items() {
		$current_screen = get_current_screen();
		$per_page = $this->get_items_per_page('svgt_per_page');

		$args = array(
			'posts_per_page' => $per_page,
			'orderby' => 'title',
			'order' => 'ASC',
			'offset' => ($this->get_pagenum() - 1) * $per_page,
		);

		if (!empty($_REQUEST['s'])) {
			$args['s'] = sanitize_text_field($_REQUEST['s']);
		}

		if (!empty($_REQUEST['orderby'])) {
			if ('title' == $_REQUEST['orderby']) {
				$args['orderby'] = 'title';
			} elseif ('author' == $_REQUEST['orderby']) {
				$args['orderby'] = 'author';
			} elseif ('date' == $_REQUEST['orderby']) {
				$args['orderby'] = 'date';
			}
		}

		if (isset($_REQUEST['order'])) {
			if ('asc' == strtolower($_REQUEST['order'])) {
				$args['order'] = 'ASC';
			} elseif ('desc' == strtolower($_REQUEST['order'])) {
				$args['order'] = 'DESC';
			}
		}

		$this->items = SVGT_Data::find($args);

		$total_items = SVGT_Data::count();
		$total_pages = ceil($total_items / $per_page);

		$this->set_pagination_args(array(
			'total_items' => $total_items,
			'total_pages' => $total_pages,
			'per_page' => $per_page,
		));
	}

	public function get_columns() {
		return get_column_headers(get_current_screen());
	}

	protected function get_sortable_columns() {
		$columns = array(
			'title' => array('title', true),
			'author' => array('author', false),
			'date' => array('date', false),
		);

		return $columns;
	}

	protected function get_bulk_actions() {
		$actions = array(
			'delete' => esc_html(__('Delete', 'svg-title')),
		);

		return $actions;
	}

	protected function column_default($item, $column_name) {
		return '';
	}

	public function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],
			$item->id()
		);
	}

	public function column_title($item) {
		$edit_link = add_query_arg(
			array(
				'post' => absint($item->id()),
				'action' => 'edit',
			),
			menu_page_url('svgt', false)
		);

		$output = sprintf(
			'<a class="row-title" href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url($edit_link),
			esc_attr(sprintf(
				__('Edit &#8220;%s&#8221;', 'svg-title'),
				$item->title()
			)),
			esc_html($item->title())
		);

		$output = sprintf('<strong>%s</strong>', $output);

		return $output;
	}

	protected function handle_row_actions($item, $column_name, $primary) {
		if ($column_name !== $primary) {
			return '';
		}

		$edit_link = add_query_arg(
			array(
				'post' => absint($item->id()),
				'action' => 'edit',
			),
			menu_page_url('svgt', false)
		);

		$actions = array(
			'edit' => svgt_link($edit_link, __('Edit', 'svg-title')),
		);

		if (current_user_can('publish_pages', $item->id())) {
			$copy_link = add_query_arg(
				array(
					'post' => absint($item->id()),
					'action' => 'copy',
				),
				menu_page_url('svgt', false)
			);

			$copy_link = wp_nonce_url(
				$copy_link,
				'svgt-copy-svg-data_' . absint($item->id())
			);

			$actions = array_merge($actions, array(
				'copy' => svgt_link($copy_link, __('Duplicate', 'svg-title')),
			));
		}

		return $this->row_actions($actions);
	}

	public function column_author($item) {
		$post = get_post($item->id());

		if (!$post) {
			return;
		}

		$author = get_userdata($post->post_author);

		if ($author === false) {
			return;
		}

		return esc_html($author->display_name);
	}

	public function column_shortcode($item) {
		$shortcodes = array($item->shortcode());

		$output = '';

		foreach ($shortcodes as $shortcode) {
			$output .= "\n" . '<span class="shortcode"><input type="text"'
				. ' onfocus="this.select();" readonly="readonly"'
				. ' value="' . esc_attr($shortcode) . '"'
				. ' class="large-text code" /></span>';
		}

		return trim($output);
	}

	public function column_date($item) {
		$post = get_post($item->id());

		if (!$post) {
			return;
		}

		$t_time = mysql2date(__('Y/m/d g:i:s A', 'svg-title'), $post->post_date, true);
		$m_time = $post->post_date;
		$time = mysql2date('G', $post->post_date) - get_option('gmt_offset') * 3600;

		$time_diff = time() - $time;

		if ($time_diff > 0 && $time_diff < 24 * 60 * 60) {
			$h_time = sprintf(__('%s ago', 'svg-title'), human_time_diff($time));
		} else {
			$h_time = mysql2date(__('Y/m/d', 'svg-title'), $m_time);
		}

		return sprintf('<abbr title="%2$s">%1$s</abbr>', esc_html($h_time), esc_attr($t_time));
	}
}
