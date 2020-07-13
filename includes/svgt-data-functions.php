<?php

function SVGT_data($id) {
	return SVGT_Data::get_instance($id);
}

function svgt_get_by_title($title) {
	$page = get_page_by_title($title, OBJECT, SVGT_Data::post_type);

	if ($page) {
		return SVGT_data($page->ID);
	}

	return null;
}

function svgt_get_current_title() {
	if ($current = SVGT_Data::get_current()) {
		return $current;
	}
}

function svgt_shortcode_func($atts, $content = null, $code = '') {
	if (is_feed()) {
		return '[svgt]';
	}

	if ('svgt' == $code) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
				'title' => '',
			),
			$atts, 'svgt'
		);

		$id = intval($atts['id']);
		$title = trim($atts['title']);

		if (!$svgt = SVGT_data($id)) {
			$svgt = svgt_get_by_title($title);
		}
	}

	if (!$svgt) {
		return '[svg title 404 "Not Found"]';
	}

	return $svgt->svg_data();
}

function svgt_save_svg_data($args = '', $context = 'save') {
	$args = wp_parse_args($args, array(
		'id' => -1,
		'title' => null,
		'locale' => null,
		'font' => null,
		'variant' => null,
		'size' => null,
		'aspeed' => null,
		'strokew' => null,
		'colors' => null,
		'data' => null,
	));

	$args = wp_unslash($args);

	$args['id'] = (int)$args['id'];

	if ($args['id'] == -1) {
		$svgt = SVGT_Data::get_template();
	} else {
		$svgt = SVGT_data($args['id']);
	}

	if (empty($svgt)) {
		return false;
	}

	if ($args['title'] !== null) {
		$svgt->set_title($args['title']);
	}

	if ($args['locale'] !== null) {
		$svgt->set_locale($args['locale']);
	}

	$properties = array();

	if ($args['font'] !== null) {
		$properties['font'] = $args['font'];
	}

	if ($args['variant'] !== null) {
		$properties['variant'] = $args['variant'];
	}

	if ($args['size'] !== null) {
		$properties['size'] = $args['size'];
	}

	if ($args['aspeed'] !== null) {
		$properties['aspeed'] = $args['aspeed'];
	}

	if ($args['strokew'] !== null) {
		$properties['strokew'] = $args['strokew'];
	}

	if ($args['colors'] !== null) {
		$properties['colors'] = $args['colors'];
	}

	if ($args['data'] !== null) {
		$properties['data'] = $args['data'];
	}

	$svgt->set_properties($properties);

	//do_action('svgt_save_svg_data', $svgt, $args, $context);

	if ($context == 'save') {
		$svgt->save();
	}

	return $svgt;
}