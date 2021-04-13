<?php

function svgt_plugin_url($path = '') {
	$url = plugins_url($path, SVGT_PLUGIN);

	if (is_ssl()
	and 'http:' == substr($url, 0, 5)) {
		$url = 'https:' . substr($url, 5);
	}

	return $url;
}

function svgt_format_atts($atts) {
	$html = '';

	$prioritized_atts = array('type', 'name', 'value', 'target');

	foreach ($prioritized_atts as $att) {
		if (isset($atts[$att])) {
			$value = trim($atts[$att]);
			$html .= sprintf(' %s="%s"', $att, esc_attr($value));
			unset($atts[$att]);
		}
	}

	foreach ($atts as $key => $value) {
		$key = strtolower(trim( $key));

		if (!preg_match('/^[a-z_:][a-z_:.0-9-]*$/', $key)) {
			continue;
		}

		$value = trim($value);

		if ($value !== '') {
			$html .= sprintf(' %s="%s"', $key, esc_attr($value));
		}
	}

	$html = trim($html);

	return $html;
}

function svgt_link($url, $anchor_text, $args = '') {
	$defaults = array(
		'id' => '',
		'class' => '',
		'target' => '',
	);

	$args = wp_parse_args($args, $defaults);
	$args = array_intersect_key($args, $defaults);
	$atts = svgt_format_atts($args);

	$link = sprintf('<a href="%1$s"%3$s>%2$s</a>',
		esc_url($url),
		esc_html($anchor_text),
		$atts ? (' ' . $atts) : '');

	return $link;
}

function svgt_register_post_types() {
	if (class_exists('SVGT_Data')) {
		SVGT_Data::register_post_type();
		return true;
	} else {
		return false;
	}
}

add_filter('widget_title', 'filter_function_svgt', 10, 3);
function filter_function_svgt($title, $instance, $id_base = ''){
	if (is_singular() && in_the_loop()) {
		if (strpos($title, "[svgt") !== false) {
			$title = str_replace("&quot;", '"', $title);
			$title = do_shortcode($title);

		}
	} else {
		$start = strpos($title, 'title="') + 7;
		$end = strpos($title, '"]');
		$title = substr($title, $start, $end - $start);
	}
	return $title;
}
add_filter('widget_custom_html_content', 'filter_function_svgt', 10, 3);
if (!is_admin()) {
	add_filter('the_title', 'filter_function_svgt', 10, 3);
}
