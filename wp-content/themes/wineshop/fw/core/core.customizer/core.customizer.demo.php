<div class="to_demo_wrap">
	<a href="" class="to_demo_pin iconadmin-pin" title="<?php  esc_attr_e('Pin/Unpin demo-block by the right side of the window', 'wineshop'); ?>"></a>
	<div class="to_demo_body_wrap">
		<div class="to_demo_body">
			<h1 class="to_demo_header"><?php echo esc_html__('Header with','wineshop'); ?> <span class="to_demo_header_link"><?php echo esc_html__('inner link' ,'wineshop');?></span> <?php echo esc_html__('and it','wineshop');?> <span class="to_demo_header_hover"><?php echo esc_html__('hovered state','wineshop');?></span></h1>
			<p class="to_demo_info wineshop_font_size"><?php echo esc_html__('Posted','wineshop');?> <span class="to_demo_info_link"><?php echo esc_html__('12 May, 2015','wineshop');?></span> <?php echo esc_html__('by','wineshop');?> <span class="to_demo_info_hover"><?php echo esc_html__('Author name hovered','wineshop');?></span>.</p>
			<p class="to_demo_text"><?php echo esc_html__('This is default post content. Colors of each text element are set based on the color you choose below.','wineshop');?></p>
			<p class="to_demo_text"><span class="to_demo_text_link"><?php echo esc_html__('link example','wineshop');?></span> <?php echo esc_html__('and','wineshop')?> <span class="to_demo_text_hover"><?php echo esc_html__('hovered link','wineshop')?></span></p>

			<?php
			if (is_array($THEMEREX_GLOBALS['custom_colors']) && count($THEMEREX_GLOBALS['custom_colors']) > 0) {
				foreach ($THEMEREX_GLOBALS['custom_colors'] as $slug=>$scheme) {
					?>
					<h3 class="to_demo_header">Accent colors</h3>
					<?php if (isset($scheme['accent1'])) { ?>
						<div class="to_demo_columns3"><p class="to_demo_text"><span class="to_demo_accent1"><?php echo esc_html__('accent1 example','wineshop');?></span> <?php echo esc_html__('and','wineshop');?> <span class="to_demo_accent1_hover"><?php echo esc_html__('hovered accent1','wineshop')?></span></p></div>
					<?php } ?>
					<?php if (isset($scheme['accent2'])) { ?>
						<div class="to_demo_columns3"><p class="to_demo_text"><span class="to_demo_accent2"><?php echo esc_html__('accent2 example','wineshop')?></span> <?php echo esc_html__('and','wineshop')?> <span class="to_demo_accent2_hover"><?php echo esc_html__('hovered accent2','wineshop')?></span></p></div>
					<?php } ?>
					<?php if (isset($scheme['accent3'])) { ?>
						<div class="to_demo_columns3"><p class="to_demo_text"><span class="to_demo_accent3"><?php echo esc_html__('accent3 example','wineshop')?></span> <?php echo esc_html__('and','wineshop')?> <span class="to_demo_accent3_hover"><?php echo esc_html__('hovered accent3','wineshop')?></span></p></div>
					<?php } ?>
		
					<h3 class="to_demo_header"><?php echo esc_html__('Inverse colors (on accented backgrounds)','wineshop')?></h3>
					<?php if (isset($scheme['accent1'])) { ?>
						<div class="to_demo_columns3 to_demo_accent1_bg">
							<h4 class="to_demo_accent1_hover_bg to_demo_inverse_dark wineshop_extra_class_1"><?php echo esc_html__('Accented block header','wineshop')?></h4>
							<div class="wineshop_extra_class_padding">
								<p class="to_demo_inverse_light wineshop_extra_class_padding_2"><?php echo esc_html__('Posted','wineshop')?> <span class="to_demo_inverse_link"><?php echo esc_html__('12 May, 2015','wineshop')?></span> <?php echo esc_html__('by','wineshop')?> <span class="to_demo_inverse_hover"><?php echo esc_html__('Author name hovered','wineshop')?></span>.</p>
								<p class="to_demo_inverse_text"><?php echo esc_html__('This is a inversed colors example for the normal text','wineshop')?></p>
								<p class="to_demo_inverse_text"><span class="to_demo_inverse_link"><?php echo esc_html__('link example','wineshop')?></span> <?php echo esc_html__('and','wineshop')?> <span class="to_demo_inverse_hover"><?php echo esc_html__('hovered link','wineshop')?></span></p>
							</div>
						</div>
					<?php } ?>
					<?php if (isset($scheme['accent2'])) { ?>
						<div class="to_demo_columns3 to_demo_accent2_bg">
							<h4 class="to_demo_accent2_hover_bg to_demo_inverse_dark wineshop_extra_class_1"><?php echo esc_html__('Accented block header','wineshop')?></h4>
							<div class="wineshop_extra_class_padding">
								<p class="to_demo_inverse_light wineshop_extra_class_padding_2"><?php echo esc_html__('Posted','wineshop')?> <span class="to_demo_inverse_link"><?php echo esc_html__('12 May, 2015','wineshop')?></span> <?php echo esc_html__('by','wineshop')?> <span class="to_demo_inverse_hover"><?php echo esc_html__('Author name hovered','wineshop')?></span>.</p>
								<p class="to_demo_inverse_text"><?php echo esc_html__('This is a inversed colors example for the normal text','wineshop')?></p>
								<p class="to_demo_inverse_text"><span class="to_demo_inverse_link"><?php echo esc_html__('link example','wineshop')?></span> <?php echo esc_html__('and','wineshop')?> <span class="to_demo_inverse_hover"><?php echo esc_html__('hovered link','wineshop')?></span></p>
							</div>
						</div>
					<?php } ?>
					<?php if (isset($scheme['accent3'])) { ?>
						<div class="to_demo_columns3 to_demo_accent3_bg">
							<h4 class="to_demo_accent3_hover_bg to_demo_inverse_dark wineshop_extra_class_1"><?php echo esc_html__('Accented block header','wineshop')?></h4>
							<div class="wineshop_extra_class_padding">
								<p class="to_demo_inverse_light wineshop_extra_class_padding_2"><?php echo esc_html__('Posted','wineshop')?> <span class="to_demo_inverse_link"><?php echo esc_html__('12 May, 2015','wineshop')?></span> <?php echo esc_html__('by','wineshop')?> <span class="to_demo_inverse_hover"><?php echo esc_html__('Author name hovered','wineshop')?></span>.</p>
								<p class="to_demo_inverse_text"><?php echo esc_html__('This is a inversed colors example for the normal text','wineshop')?></p>
								<p class="to_demo_inverse_text"><span class="to_demo_inverse_link"><?php echo esc_html__('link example','wineshop')?></span> <?php echo esc_html__('and','wineshop')?> <span class="to_demo_inverse_hover"><?php echo esc_html__('hovered link','wineshop')?></span></p>
							</div>
						</div>
					<?php } ?>
					<?php
					break;
				}
			}
			?>
	
			<h3 class="to_demo_header"><?php echo esc_html__('Alternative colors used to decorate highlight blocks and form fields','wineshop')?></h3>
			<div class="to_demo_columns2">
				<div class="to_demo_alter_block wineshop_extra_class_padding_3">
					<h4 class="to_demo_alter_header wineshop_extra_class_margin_top"><?php echo esc_html__('Highlight block header','wineshop')?></h4>
					<p class="to_demo_alter_text"><?php echo esc_html__('This is a plain text in the highlight block. This is a plain text in the highlight block.','wineshop')?></p>
					<p class="to_demo_alter_text"><span class="to_demo_alter_link"><?php echo esc_html__('link example','wineshop')?></span> <?php echo esc_html__('and','wineshop')?> <span class="to_demo_alter_hover"><?php echo esc_html__('hovered link','wineshop')?></span></p>
				</div>
			</div>
			<div class="to_demo_columns2">
				<h4 class="to_demo_header wineshop_extra_class_margin_top"><?php echo esc_html__('Form field','wineshop')?></h4>
				<input type="text" class="to_demo_field" value="<?php echo esc_attr__('Input field example','wineshop')?>">
				<h4 class="to_demo_header"><?php echo esc_html__('Form field focused','wineshop')?></h4>
				<input type="text" class="to_demo_field_focused" value="<?php echo esc_attr__('Focused field example','wineshop')?>">
			</div>
		</div>
	</div>
</div>
