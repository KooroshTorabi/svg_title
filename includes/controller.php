<?php

add_action('wp_enqueue_scripts', 'svgt_do_enqueue_scripts', 10, 0);

function svgt_do_enqueue_scripts() {
	svgt_enqueue_scripts();
	svgt_enqueue_styles();
}

function svgt_enqueue_scripts() {
	wp_enqueue_script('svgt', svgt_plugin_url('includes/js/scripts.js'), array('jquery'), SVGT_VERSION, true);
	$svgt = array();
}

function svgt_enqueue_styles() {
	wp_enqueue_style('svgt', svgt_plugin_url('includes/css/styles.css'), array(), SVGT_VERSION, 'all');
}
