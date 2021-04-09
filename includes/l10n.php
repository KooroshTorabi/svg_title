<?php

function svgt_is_valid_locale($locale) {
	$pattern = '/^[a-z]{2,3}(?:_[a-zA-Z_]{2,})?$/';
	return (bool)preg_match($pattern, $locale);
}

function svgt_load_textdomain() {
	global $l10n;

	$domain = 'svg-title';

	$locale = get_locale();

	if (empty($locale)) {
		if (is_textdomain_loaded($domain)) {
			return true;
		} else {
			return load_plugin_textdomain($domain, false, $domain . '/languages');
		}
	} else {
		$mo_orig = $l10n[$domain];
		unload_textdomain($domain);

		$mofile = $locale . '.mo';
		$path = WP_PLUGIN_DIR . '/' . $domain . '/languages';
		if (!is_dir($path)) {
			$path = WP_PLUGIN_DIR . '/svg_title/languages';
			if (!is_dir($path)) {
				$path = WP_PLUGIN_DIR . '/svg_title-master/languages';
			}
		}
		if ($loaded = load_textdomain($domain, $path . '/'. $mofile)) {
			return $loaded;
		} else {
			$mofile = WP_LANG_DIR . '/plugins/' . $mofile;
			return load_textdomain($domain, $mofile);
		}
		$l10n[$domain] = $mo_orig;
	}
	return false;
}
