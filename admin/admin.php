<?php

require_once SVGT_PLUGIN_DIR . '/admin/includes/admin-functions.php';
require_once SVGT_PLUGIN_DIR . '/admin/includes/help-tabs.php';
require_once SVGT_PLUGIN_DIR . '/admin/includes/welcome-panel.php';

add_action('admin_menu', 'svgt_admin_menu', 9, 0);

function svgt_admin_menu() {
	global $_wp_last_object_menu;

	$_wp_last_object_menu++;

	add_menu_page(esc_html(__('SVG Title', 'svg-title')),
		esc_html(__('SVG Title', 'svg-title')),
		'edit_posts', 'svgt',
		'svgt_admin_management_page', 'dashicons-art',
		$_wp_last_object_menu);

	$edit = add_submenu_page('svgt',
		esc_html(__('Edit SVG Title', 'svg-title')),
		esc_html(__('SVG Titles', 'svg-title')),
		'edit_posts', 'svgt',
		'svgt_admin_management_page');

	add_action('load-' . $edit, 'svgt_load_admin', 10, 0);

	$addnew = add_submenu_page('svgt',
		esc_html(__('Add New', 'svg-title')),
		esc_html(__('Add New', 'svg-title')),
		'publish_pages', 'svgt-new',
		'svgt_admin_add_new_page');

	add_action('load-' . $addnew, 'svgt_load_admin', 10, 0);
}

add_action('admin_enqueue_scripts', 'svgt_admin_enqueue_scripts', 10, 1);

function svgt_admin_enqueue_scripts($hook_suffix) {
	if (strpos($hook_suffix, 'svgt') !== false) {

		wp_enqueue_style('svgt-admin', svgt_plugin_url('admin/css/styles.css'), array(), SVGT_VERSION, 'all');

		wp_enqueue_script('svgt-admin-maker', "https://maker.js.org/target/js/browser.maker.js", array(), "0.17.0", false);
		wp_enqueue_script('svgt-admin-bezier', svgt_plugin_url('admin/js/bezier.js'), array(), "1.0", false);
		wp_enqueue_script('svgt-admin-opentype', "https://cdn.jsdelivr.net/npm/opentype.js@latest/dist/opentype.min.js", array(), "1.3.3", false);

		wp_enqueue_script('svgt-apikey', svgt_plugin_url('admin/js/config.js'), array('jquery'), SVGT_VERSION, true);
		wp_enqueue_script('svgt-admin', svgt_plugin_url('admin/js/scripts.js'), array('jquery', 'wp-color-picker'), SVGT_VERSION, true);

	}
}

add_filter('set-screen-option', 'svgt_screen_options', 10, 3);

function svgt_screen_options($result, $option, $value) {
	$svgts = array(
		'svgt_per_page',
	);

	if (in_array($option, $svgts)) {
		$result = $value;
	}

	return $result;
}

function svgt_load_admin() {
	global $plugin_page;

	$action = svgt_current_action();

	if ($action == 'save') {
		$id = isset($_POST['post_ID']) ? sanitize_text_field($_POST['post_ID']) : '-1';
		check_admin_referer('svgt-save-data_' . $id);

		if (!current_user_can('publish_pages', $id)) {
			wp_die(esc_html(__('You are not allowed to edit this item.', 'svg-title')));
		}

		$args['id'] = $id;

		$args['title'] = isset($_POST['post_title'])
			? sanitize_text_field($_POST['post_title']) : null;

		$args['locale'] = isset($_POST['svgt-locale'])
			? sanitize_text_field($_POST['svgt-locale']) : null;

		$args['font'] = isset($_POST['svgt-font'])
			? sanitize_text_field($_POST['svgt-font']) : '';

		$args['variant'] = isset($_POST['svgt-variant'])
			? sanitize_text_field($_POST['svgt-variant']) : '';

		$args['size'] = isset($_POST['svgt-size'])
			? (float)sanitize_text_field($_POST['svgt-size']) : '';

		$args['aspeed'] = isset($_POST['svgt-aspeed'])
			? sanitize_aspeed($_POST['svgt-aspeed']) : array();

		$args['strokew'] = isset($_POST['svgt-strokew'])
			? (float)sanitize_text_field($_POST['svgt-strokew']) : '';

		$args['colors'] = isset($_POST['svgt-colors'])
			? sanitize_colors($_POST['svgt-colors']) : array();

		$args['data'] = isset($_POST['svgt-data'])
			? sanitize_data($_POST['svgt-data']) : '';

		$svgt = svgt_save_svg_data($args);

		$query = array(
			'post' => $svgt ? $svgt->id() : 0,
		);

		if (!$svgt) {
			$query['message'] = 'failed';
		} elseif (-1 == $id) {
			$query['message'] = 'created';
		} else {
			$query['message'] = 'saved';
		}

		$redirect_to = add_query_arg($query, menu_page_url('svgt', false));
		wp_safe_redirect($redirect_to);
		exit();
	}

	if ($action == 'copy') {
		$id = empty($_POST['post_ID']) ? absint(sanitize_text_field($_REQUEST['post'])) : absint(sanitize_text_field($_POST['post_ID']));

		check_admin_referer('svgt-copy-svg-data_' . $id);

		if (!current_user_can('publish_pages', $id)) {
			wp_die(esc_html(__('You are not allowed to edit this item.', 'svg-title')));
		}

		$query = array();

		if ($svgt = SVGT_data($id)) {
			$new_svgt = $svgt->copy();
			$new_svgt->save();

			$query['post'] = $new_svgt->id();
			$query['message'] = 'created';
		}

		$redirect_to = add_query_arg($query, menu_page_url('svgt', false));

		wp_safe_redirect($redirect_to);
		exit();
	}

	if ($action == 'delete') {
		if (!empty($_POST['post_ID'])) {
			check_admin_referer('svgt-delete-svg-data_' . sanitize_text_field($_POST['post_ID']));
		} elseif (!is_array( $_REQUEST['post'])) {
			check_admin_referer('svgt-delete-svg-data_' . sanitize_text_field($_REQUEST['post']));
		} else {
			check_admin_referer('bulk-posts');
		}

		$deleted = 0;

		$posts = empty($_POST['post_ID']) ? array_map('sanitize_text_field', (array)$_REQUEST['post']) : (array)sanitize_text_field($_POST['post_ID']);

		foreach ($posts as $post) {
			$post = SVGT_Data::get_instance($post);

			if (empty($post)) {
				continue;
			}

			if (!current_user_can('publish_pages', $post->id())) {
				wp_die(esc_html(__('You are not allowed to delete this item.', 'svg-title')));
			}

			if (!$post->delete()) {
				wp_die(esc_html(__('Error in deleting.', 'svg-title')));
			}

			$deleted += 1;
		}

		$query = array();

		if (!empty($deleted)) {
			$query['message'] = 'deleted';
		}

		$redirect_to = add_query_arg($query, menu_page_url('svgt', false));

		wp_safe_redirect($redirect_to);
		exit();
	}

	$post = null;

	if ($plugin_page == 'svgt-new') {
		$post = SVGT_Data::get_template(array(
			'locale' => isset($_GET['locale']) ? sanitize_text_field($_GET['locale']) : null,
		));
	} elseif (!empty($_GET['post'])) {
		$post = SVGT_Data::get_instance(sanitize_text_field($_GET['post']));
	}

	$current_screen = get_current_screen();

	$help_tabs = new SVGT_Help_Tabs($current_screen);

	if ($post && current_user_can('publish_pages', $post->id())) {
		$help_tabs->set_help_tabs('edit');
	} else {
		$help_tabs->set_help_tabs('list');

		if (!class_exists('SVGT_List_Table')) {
			require_once SVGT_PLUGIN_DIR . '/admin/includes/svgt-list-table.php';
		}

		add_filter('manage_' . $current_screen->id . '_columns', array('SVGT_List_Table', 'define_columns'), 10, 0);

		add_screen_option('per_page', array(
			'default' => 20,
			'option' => 'svgt_per_page',
		));
	}
}

function sanitize_aspeed($aspeed) {
	$res = array();
	if (is_array($aspeed)) {
		foreach($aspeed as $a) {
			$res[] = (float)$a;
		}
	}
	return $res;
}

function sanitize_colors($colors) {
	$res = array();
	if (is_array($colors)) {
		foreach($colors as $c) {
			$res[] = sanitize_hex_color($c);
		}
	}
	return $res;
}

function sanitize_data($data) {
	$res = '';
	$res = @wp_kses($data, array(
		'svg' => array('width' => true, 'height' => true, 'viewbox' => true, 'xmlns' => true, 'id' => true, 'data-aspeed' => true),
		'g' => array('stroke-linecap' => true, 'fill-rule' => true, 'stroke' => true),
		'path' => array('d' => true, 'vector-effect' => true , 'stroke-width' => true, 'stroke' => true, 'fill' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'fill-opacity' => true),
		'animate' => array('id' => true, 'attributename' => true, 'begin' => true, 'values' => true, 'dur' => true, 'repeatcount' => true, 'fill' =>true, 'calcmode' => true),
		'title' => true,
		'desc' => true
		)
	);
	return $res;
}

function svgt_admin_management_page() {
	if ($post = svgt_get_current_title()) {
		$post_id = $post->initial() ? -1 : $post->id();

		require_once SVGT_PLUGIN_DIR . '/admin/includes/editor.php';
		require_once SVGT_PLUGIN_DIR . '/admin/edit-svg-title.php';
		return;
	}

	$list_table = new SVGT_List_Table();
	$list_table->prepare_items();
?>
<div class="wrap" id="svgt-list-table">

<h1 class="wp-heading-inline"><?php	echo esc_html(__('SVG Titles', 'svg-title')); ?></h1>

<?php if (current_user_can('publish_pages')) {
		echo svgt_link(
			menu_page_url('svgt-new', false),
			__('Add New', 'svg-title'),
			array('class' => 'page-title-action')
		);
	}

	if (!empty($_REQUEST['s'])) {
		echo sprintf('<span class="subtitle">'
			. esc_html(__('Search results for &#8220;%s&#8221;', 'svg-title'))
			. '</span>', sanitize_text_field($_REQUEST['s'])
		);
	}
?>

<hr class="wp-header-end">

<?php do_action('svgt_admin_warnings', 'svgt', svgt_current_action(), null);
	svgt_welcome_panel();
	do_action('svgt_admin_notices', 'svgt', svgt_current_action(), null);
?>

<form method="get" action="">
	<input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
	<?php $list_table->search_box(esc_html(__('Search Title', 'svg-title')), 'svgt-search'); ?>
	<?php $list_table->display(); ?>
</form>

</div>
<?php
}

function svgt_admin_add_new_page() {
	$post = svgt_get_current_title();

	if (!$post) {
		$post = SVGT_Data::get_template();
	}

	$post_id = -1;

	require_once SVGT_PLUGIN_DIR . '/admin/includes/editor.php';
	require_once SVGT_PLUGIN_DIR . '/admin/edit-svg-title.php';
}

add_action('svgt_admin_notices', 'svgt_admin_updated_message', 10, 3);

function svgt_admin_updated_message($page, $action, $object) {
	if (!in_array($page, array('svgt', 'svgt-new'))) {
		return;
	}

	if (empty($_REQUEST['message'])) {
		return;
	}

	if ($_REQUEST['message'] == 'created') {
		$updated_message = esc_html(__("Title created.", 'svg-title'));
	} elseif ($_REQUEST['message'] == 'saved') {
		$updated_message = esc_html(__("Title saved.", 'svg-title'));
	} elseif ($_REQUEST['message'] == 'deleted') {
		$updated_message = esc_html(__("Title deleted.", 'svg-title'));
	}

	if (!empty($updated_message)) {
		echo sprintf('<div id="message" class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html($updated_message));
		return;
	}

	if ($_REQUEST['message'] == 'failed') {
		$updated_message = esc_html(__("There was an error saving the title.", 'svg-title'));

		echo sprintf('<div id="message" class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html($updated_message));
		return;
	}
}

add_filter('plugin_action_links', 'svgt_plugin_action_links', 10, 2);

function svgt_plugin_action_links($links, $file) {
	if ($file != SVGT_PLUGIN_BASENAME) {
		return $links;
	}

	if (!current_user_can('edit_posts')) {
		return $links;
	}

	$settings_link = svgt_link(
		menu_page_url('svgt', false),
		__('Settings', 'svg-title')
	);

	array_unshift($links, $settings_link);

	return $links;
}

add_action('svgt_admin_warnings', 'svgt_not_allowed_to_edit', 10, 3);

function svgt_not_allowed_to_edit($page, $action, $object) {
	if ($object instanceof SVGT_Data) {
		$svgt = $object;
	} else {
		return;
	}

	if (current_user_can('publish_pages', $svgt->id())) {
		return;
	}

	$message = esc_html(__("You are not allowed to edit this title.", 'svg-title'));

	echo sprintf('<div class="notice notice-warning"><p>%s</p></div>', esc_html($message));
}
