<?php

class SVGT_Editor {

	private $svgt;

	public function __construct(SVGT_Data $svgt) {
		$this->svgt = $svgt;
	}

	public function display() {?>
		<div class="col-swrap">
			<div class="scol">
				<div class="swrap">
					<label for="subset"><?php echo esc_html(__('Font Subset', 'svg-title')); ?>:</label>
					<select id="subset" name="svgt-subset" class="widefat"></select>
					<input type="hidden" id="saved-subset" value="<?php echo $this->svgt->subset ? esc_attr($this->svgt->subset) : "0"; ?>" />
				</div>
				<div class="swrap">
					<label for="font"><?php echo esc_html(__('Font', 'svg-title')); ?>:</label>
					<select id="font" name="svgt-font" class="widefat"></select>
					<input type="hidden" id="saved-font" value="<?php echo $this->svgt->font ? esc_attr($this->svgt->font) : "0"; ?>" />
				</div>
				<div class="swrap">
					<label for="variant"><?php echo esc_html(__('Variant', 'svg-title')); ?>:</label>
					<select id="variant" name="svgt-variant" class="widefat"></select>
					<input type="hidden" id="saved-variant" value="<?php echo $this->svgt->variant ? esc_attr($this->svgt->variant) : "0"; ?>" />
				</div>
			</div>

			<div class="scol">
				<div class="swrap">
					<label for="size"><?php echo esc_html(__('Size', 'svg-title')); ?>:</label>
					<input type="text" id="size" name="svgt-size" value="<?php echo $this->svgt->size ? esc_attr($this->svgt->size) : "32"; ?>" class="widefat"/>
				</div>
				<div class="swrap">
					<label for="strokew"><?php echo esc_html(__('Stroke width', 'svg-title')); ?>:</label>
					<input type="text" id="strokew" name="svgt-strokew" value="<?php echo $this->svgt->strokew ? esc_attr($this->svgt->strokew) : "1"; ?>" class="widefat"/>
				</div>
				<div class="swrap">
					<label for="aspeed"><?php echo esc_html(__('Animation speed', 'svg-title')); ?>:</label>
					<input type="text" id="aspeed1" name="svgt-aspeed[]" value="<?php echo $this->svgt->aspeed[0] ? esc_attr($this->svgt->aspeed[0]) : "0"; ?>" class="aspeed"/>
					<input type="text" id="aspeed2" name="svgt-aspeed[]" value="<?php echo $this->svgt->aspeed[1] ? esc_attr($this->svgt->aspeed[1]) : "0"; ?>" class="aspeed"/>
					<input type="text" id="aspeed3" name="svgt-aspeed[]" value="<?php echo $this->svgt->aspeed[2] ? esc_attr($this->svgt->aspeed[2]) : "0"; ?>" class="aspeed"/>
					<input type="text" id="aspeed4" name="svgt-aspeed[]" value="<?php echo $this->svgt->aspeed[3] ? esc_attr($this->svgt->aspeed[3]) : "0"; ?>" class="aspeed"/>
				</div>
				
			</div>
		</div>
		<div class="clear"></div>
		<div class="swrap">
			<div class="col-swrap">
				<div class="color-swrap">
					<label for="color1"><?php echo esc_html(__('Outline color', 'svg-title')); ?>:</label>&nbsp;
					<input type="text" id="color1" name="svgt-colors[]" value="<?php echo $this->svgt->colors[0] ? esc_attr($this->svgt->colors[0]) : '#000000'; ?>" class="color-field" data-defaultColor="<?php echo $this->svgt->colors[0] ? esc_attr($this->svgt->colors[0]) : '#000000'; ?>"/>
				</div>
				<div class="color-swrap">
					<label for="color2"><?php echo esc_html(__('Text color', 'svg-title')); ?>:</label>&nbsp;
					<input type="text" id="color2" name="svgt-colors[]" value="<?php echo $this->svgt->colors[1] ? esc_attr($this->svgt->colors[1]) : 'transparent'; ?>" class="color-field" data-defaultColor="<?php echo $this->svgt->colors[1] ? esc_attr($this->svgt->colors[1]) : 'transparent'; ?>"/>
				</div>
			</div>
		</div>
		<div class="swrap" style="display: none;">
			<label for="data"><?php echo esc_html(__('SVG Data', 'svg-title')); ?>:</label>
			<textarea id="data" name="svgt-data" readonly="readonly"><?php echo esc_attr($svgt->data); ?></textarea>
		</div>
		<div class="swrap">
			<label for="result"><?php echo esc_html(__('Result', 'svg-title')); ?>:</label>
			<div id="svg-render"></div>
		</div>
		
	<?php }

}