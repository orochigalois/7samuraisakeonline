<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'themerex_template_team_3_theme_setup' ) ) {
	add_action( 'themerex_action_before_init_theme', 'themerex_template_team_3_theme_setup', 1 );
	function themerex_template_team_3_theme_setup() {
		themerex_add_template(array(
			'layout' => 'team-3',
			'template' => 'team-3',
			'mode'   => 'team',
			'title'  => esc_html__('Team /Style 3/', 'wineshop'),
			'thumb_title'  => esc_html__('Medium square image (crop)', 'wineshop'),
			'w' => 390,
			'h' => 390
		));
	}
}

// Template output
if ( !function_exists( 'themerex_template_team_3_output' ) ) {
	function themerex_template_team_3_output($post_options, $post_data) {
		$show_title = true;
		$parts = explode('_', $post_options['layout']);
		$style = $parts[0];
		$columns = max(1, min(12, empty($parts[1]) ? (!empty($post_options['columns_count']) ? $post_options['columns_count'] : 1) : (int) $parts[1]));
		if (themerex_param_is_on($post_options['slider'])) {
			?><div class="swiper-slide" data-style="<?php echo esc_attr($post_options['tag_css_wh']); ?>" style="<?php echo esc_attr($post_options['tag_css_wh']); ?>"><?php
		} else if ($columns > 1) {
			?><div class="column-1_<?php echo esc_attr($columns); ?> column_padding_bottom"><?php
		}
		?>
			<div<?php themerex_show_layout($post_options['tag_id'] ? ' id="'.esc_attr($post_options['tag_id']).'"' : ''); ?>
				class="sc_team_item sc_team_item_<?php echo esc_attr($post_options['number']) . ($post_options['number'] % 2 == 1 ? ' odd' : ' even') . ($post_options['number'] == 1 ? ' first' : '') . (!empty($post_options['tag_class']) ? ' '.esc_attr($post_options['tag_class']) : ''); ?>"
				<?php themerex_show_layout($post_options['tag_css']!='' ? ' style="'.esc_attr($post_options['tag_css']).'"' : ''
					. (!themerex_param_is_off($post_options['tag_animation']) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($post_options['tag_animation'])).'"' : '')); ?>>
				<div class="sc_team_item_avatar"><?php themerex_show_layout($post_options['photo']); ?>
					<div class="sc_team_item_hover">
						<div class="sc_team_item_socials"><?php themerex_show_layout($post_options['socials']); ?></div>
					</div>
				</div>
				<div class="sc_team_item_info">
					<h5 class="sc_team_item_title">
                        <?php
                        if ($post_options['position'] != '') ?>
                            <a href="<?php themerex_show_layout($post_options['link']) ?>"><?php themerex_show_layout($post_data['post_title'])?></a>



                       </h5>
					<div class="sc_team_item_position"><?php themerex_show_layout($post_options['position']);?></div>
					<div class="sc_team_item_description"><?php themerex_show_layout(themerex_strshort($post_data['post_excerpt'], isset($post_options['descr']) ? 100 : themerex_get_custom_option('post_excerpt_maxlength_masonry'))); ?></div>
				</div>
			</div>
		<?php
		if (themerex_param_is_on($post_options['slider']) || $columns > 1) {
			?></div><?php
		}
	}
}
?>