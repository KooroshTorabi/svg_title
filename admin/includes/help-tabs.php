<?php

class SVGT_Help_Tabs {

	private $screen;

	public function __construct(WP_Screen $screen) {
		$this->screen = $screen;
	}

	public function set_help_tabs($type) {
		switch ($type) {
			case 'list':
				$this->screen->add_help_tab(array(
					'id' => 'list_overview',
					'title' => __('Overview', 'svg-title'),
					'content' => $this->content('list_overview')));

				$this->screen->add_help_tab(array(
					'id' => 'list_available_actions',
					'title' => __('Available Actions', 'svg-title'),
					'content' => $this->content('list_available_actions')));
				$this->sidebar();
				break;
			case 'edit':
				$this->screen->add_help_tab(array(
					'id' => 'edit_overview',
					'title' => __('Overview', 'svg-title'),
					'content' => $this->content('edit_overview')));

				$this->sidebar();
				break;
			return;
		}
	}

	private function content($name) {
		$content = array();

		$content['list_overview'] = '<p>' . __("On this screen, you can manage your SVG titles. You can create an unlimited number of SVG titles. Each SVG title has a shortcode. To insert an SVG title into a post or a text widget, insert the shortcode into the target.", 'svg-title') . '</p>';

		$content['list_available_actions'] = '<p>' . __("Hovering over a row in the SVG titles list will display action links that allow you to manage your SVG titles. You can perform the following actions:", 'svg-title') . '</p>';
		$content['list_available_actions'] .= '<p><strong>' . __("Edit", 'svg-title') . "</strong> - " . __("navigates to the editing screen for that SVG title. You can also reach that screen by clicking on the SVG title.", 'svg-title') . '</p>';
		$content['list_available_actions'] .= '<p><strong>' . __("Duplicate", 'svg-title') . "</strong> - " . __("clones that SVG title. A cloned SVG title inherits all settings from the original, but has a different ID.", 'svg-title') . '</p>';

		$content['edit_overview'] = '<p>' . __("On this screen, you can edit an SVG title. SVG title settings consists of the following components:", 'svg-title') . '</p>';
		$content['edit_overview'] .= '<p><strong>' . __("Title", 'svg-title') . "</strong> " . __("is the title you see in SVG.", 'svg-title') . '</p>';
		$content['edit_overview'] .= '<p><strong>' . __("Font Subset", 'svg-title') . "</strong> " . __("is a font subset selector. Allows easier to select exact font by removing all other fonts in font selector without such subset.", 'svg-title') . '</p>';
		$content['edit_overview'] .= '<p><strong>' . __("Font", 'svg-title') . "</strong> " . __("is a font that will be used to render title into SVG. All fonts taken from Google Font.", 'svg-title') . '</p>';
		$content['edit_overview'] .= '<p><strong>' . __("Variant", 'svg-title') . "</strong> " . __("is one of possible variant of the selected font - italic, regular, bold, etc.", 'svg-title') . '</p>';
		$content['edit_overview'] .= '<p><strong>' . __("Size", 'svg-title') . "</strong> " . __("is letters size of the title.", 'svg-title') . '</p>';
		$content['edit_overview'] .= '<p><strong>' . __("Stroke width", 'svg-title') . "</strong> " . __("is stroke width in pixels.", 'svg-title') . '</p>';
		$content['edit_overview'] .= '<p><strong>' . __("Animation speed", 'svg-title') . "</strong> " . __("in case you wish to add animation to the title, you can add four parameters", 'svg-title') . ':<br/>';
		$content['edit_overview'] .= " - " . __("pause before outline drawing animation will start", 'svg-title') . ';<br/>';
		$content['edit_overview'] .= " - " . __("duration of the outline drawing animation", 'svg-title') . ';<br/>';
		$content['edit_overview'] .= " - " . __("pause before color filling animation will start", 'svg-title') . ';<br/>';
		$content['edit_overview'] .= " - " . __("duration of the color filling animation", 'svg-title') . ';<br/>';
		$content['edit_overview'] .= __("Animation starts if SVG title is visible.", 'svg-title') . '</p>';

		$content['edit_overview'] .= '<p><strong>' . __("Outline color", 'svg-title') . "</strong> " . __("is a color that used to paint the outline of the letters.", 'svg-title') . '</p>';
		$content['edit_overview'] .= '<p><strong>' . __("Text color", 'svg-title') . "</strong> " . __("is a color that fills letters inside.", 'svg-title') . '</p>';

		if (!empty($content[$name])) {
			return $content[$name];
		}
	}

	public function sidebar() {
		$content = '<p><strong>' . __('For more information', 'svg-title') . ':</strong></p>';
		$content .= '<p>' . svgt_link(__('https://fonts.google.com/', 'svg-title'), __('Google Fonts', 'svg-title'), array("target" => "_blank")) . '</p>';
		$content .= '<p>' . svgt_link(__('https://svgt.netlify.app/', 'svg-title'), __('SVG titles demo', 'svg-title'), array("target" => "_blank")) . '</p>';

		$this->screen->set_help_sidebar($content);
	}
}
