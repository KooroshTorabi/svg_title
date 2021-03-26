<?php

if (!defined('ABSPATH')) {
	die('-1');
}

function svgt_admin_save_button($post_id) {
	static $button = '';

	if (!empty($button)) {
		echo $button;
		return;
	}

	$nonce = wp_create_nonce('svgt-save-data_' . $post_id);

	$onclick = sprintf("this.form._wpnonce.value = '%s';"
		. " this.form.action.value = 'save';"
		. " return true;", $nonce);

	$button = sprintf('<input type="submit" class="button-primary" name="svgt-save" value="%1$s" onclick="%2$s" />',
		esc_attr(__('Save', 'svg-title')), $onclick);

	echo $button;
}
?><div class="wrap" id="svgt-editor">

	<h1 class="wp-heading-inline"><?php
		if ($post->initial()) {
			echo esc_html(__('Add New Title', 'svg-title'));
		} else {
			echo esc_html(__('Edit Title', 'svg-title'));
		}
	?></h1>

	<?php if (!$post->initial() && current_user_can('publish_pages')) {
		echo svgt_link(
			menu_page_url('svgt-new', false),
			__('Add New', 'svg-title'),
			array('class' => 'page-title-action')
		);
	} ?>

	<hr class="wp-header-end">

	<?php do_action('svgt_admin_warnings',
		$post->initial() ? 'svgt-new' : 'svgt',
		svgt_current_action(),
		$post);

	do_action('svgt_admin_notices',
		$post->initial() ? 'svgt-new' : 'svgt',
		svgt_current_action(),
		$post);
	if ($post) {
		if (current_user_can('publish_pages', $post_id)) {
			$disabled = '';
		} else {
			$disabled = ' disabled="disabled"';
		} ?>

	<form method="post" action="<?php echo esc_url(add_query_arg(array('post' => $post_id), menu_page_url('svgt', false))); ?>" id="svgt-admin-form-element">
	<?php if (current_user_can('publish_pages', $post_id)) {
			wp_nonce_field('svgt-save-data_' . $post_id);
		} ?>
		<input type="hidden" id="post_ID" name="post_ID" value="<?php echo (int)$post_id; ?>" />
		<input type="hidden" id="svgt-locale" name="svgt-locale" value="<?php echo esc_attr($post->locale()); ?>" />
		<input type="hidden" id="hiddenaction" name="action" value="save" />

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div id="titlediv">
						<div id="titlewrap">
							<label class="screen-reader-text" id="title-prompt-text" for="title"><?php echo esc_html(__('Enter title here', 'svg-title')); ?></label>
						<?php $posttitle_atts = array(
								'type' => 'text',
								'name' => 'post_title',
								'size' => 30,
								'value' => $post->initial() ? '' : $post->title(),
								'id' => 'title',
								'spellcheck' => 'true',
								'autocomplete' => 'off',
								'disabled' =>
									current_user_can('publish_pages', $post_id) ? '' : 'disabled',
							);
							echo sprintf('<input %s />', svgt_format_atts($posttitle_atts)); ?>
						</div>

						<div class="inside">
						<?php if (!$post->initial()) { ?>
							<p class="description">
							<label for="svgt-shortcode"><?php echo esc_html(__("Copy this shortcode and paste it into your post, page, or text widget content:", 'svg-title')); ?></label>
							<span class="shortcode"><input type="text" id="svgt-shortcode" onfocus="this.select();" readonly="readonly" class="large-text code" value="<?php echo esc_attr($post->shortcode()); ?>" /></span>
							</p>
						<?php }	?>
						</div>
					</div>
				</div>

				<div id="postbox-container-1" class="postbox-container">
					<?php if (current_user_can('publish_pages', $post_id)) { ?>
					<div id="submitdiv" class="postbox">
						<h3><?php echo esc_html(__('Status', 'svg-title'));
							if (!$post->initial()) {
								echo ': ' . esc_html(__('Published', 'svg-title'));
							} else {
								echo ': ' . esc_html(__('New', 'svg-title'));
							}
						?></h3>
						<div class="inside">
							<div class="submitbox" id="submitpost">
								<div id="minor-publishing-actions">
									<div class="hidden">
										<input type="submit" class="button-primary" name="svgt-save" value="<?php echo esc_attr(__('Save', 'svg-title')); ?>" />
									</div>
								<?php if (!$post->initial()) {
										$copy_nonce = wp_create_nonce('svgt-copy-svg-data_' . $post_id); ?>
									<input type="submit" name="svgt-copy" class="copy button" value="<?php echo esc_attr(__('Duplicate', 'svg-title')); ?>" <?php echo "onclick=\"this.form._wpnonce.value = '$copy_nonce'; this.form.action.value = 'copy'; return true;\""; ?> />
								<?php } ?>
								</div>

								<div id="major-publishing-actions">
									<?php if (!$post->initial()) {
											$delete_nonce = wp_create_nonce('svgt-delete-svg-data_' . $post_id); ?>
									<div id="delete-action">
										<input type="submit" name="svgt-delete" class="delete submitdelete" value="<?php echo esc_attr(__('Delete', 'svg-title')); ?>" <?php echo "onclick=\"if (confirm('" . esc_js(__("You are about to delete this title.\n  'Cancel' to stop, 'OK' to delete.", 'svg-title')) . "')) {this.form._wpnonce.value = '$delete_nonce'; this.form.action.value = 'delete'; return true;} return false;\""; ?> />
									</div>
									<?php } ?>

									<div id="publishing-action">
										<span class="spinner"></span>
										<?php svgt_admin_save_button($post_id); ?>
									</div>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
					<div id="informationdiv" class="postbox">
						<h3><?php echo esc_html(__("Do you need help?", 'svg-title')); ?></h3>
						<div class="inside">
							<p><?php echo esc_html(__("Here are some available options for help.", 'svg-title')); ?></p>
							<ol>
								<li><?php echo svgt_link(
									__('https://fonts.google.com/', 'svg-title'),
									__('Google Fonts', 'svg-title'),
									array("target" => "_blank")
								); ?></li>
								<li><?php echo svgt_link(
									__('https://svgt.netlify.app/', 'svg-title'),
									__('SVG titles demo', 'svg-title'),
									array("target" => "_blank")
								); ?></li>
							</ol>
						</div>
					</div>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<div id="svgt-editor">
					<?php $editor = new SVGT_Editor($post);
						$editor->display(); ?>
					</div>
				</div>
			</div>
			<br class="clear" />
		</div>
	</form>
<?php } ?>
</div>
