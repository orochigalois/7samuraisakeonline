<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'themerex_template_form_1_theme_setup' ) ) {
	add_action( 'themerex_action_before_init_theme', 'themerex_template_form_1_theme_setup', 1 );
	function themerex_template_form_1_theme_setup() {
		themerex_add_template(array(
			'layout' => 'form_1',
			'mode'   => 'forms',
			'title'  => esc_html__('Contact Form 1', 'wineshop')
			));
	}
}

// Template output
if ( !function_exists( 'themerex_template_form_1_output' ) ) {
	function themerex_template_form_1_output($post_options, $post_data) {
        static $cnt = 0;
        $cnt++;
        $privacy = trx_utils_get_privacy_text();
        global $THEMEREX_GLOBALS;
        ?>
		<form <?php themerex_show_layout($post_options['id'] ? ' id="'.esc_attr($post_options['id']).'"' : ''); ?> data-formtype="<?php echo esc_attr($post_options['layout']); ?>" method="post" action="<?php echo esc_url($post_options['action'] ? $post_options['action'] : $THEMEREX_GLOBALS['ajax_url']); ?>">
			<div class="sc_form_info">
				<div class="sc_form_item sc_form_field label_over"><label class="required" for="sc_form_username"><?php esc_html_e('Name', 'wineshop'); ?></label><input id="sc_form_username" type="text" name="username" placeholder="<?php  esc_attr_e('Name *', 'wineshop'); ?>"></div>
				<div class="sc_form_item sc_form_field label_over"><label class="required" for="sc_form_email"><?php esc_html_e('E-mail', 'wineshop'); ?></label><input id="sc_form_email" type="text" name="email" placeholder="<?php  esc_attr_e('E-mail *', 'wineshop'); ?>"></div>
				<div class="sc_form_item sc_form_field label_over"><label class="required" for="sc_form_subj"><?php esc_html_e('Subject', 'wineshop'); ?></label><input id="sc_form_subj" type="text" name="subject" placeholder="<?php  esc_attr_e('Subject', 'wineshop'); ?>"></div>
			</div>
			<div class="sc_form_item sc_form_message label_over"><label class="required" for="sc_form_message"><?php esc_html_e('Message', 'wineshop'); ?></label><textarea id="sc_form_message" name="message" placeholder="<?php  esc_attr_e('Message', 'wineshop'); ?>"></textarea></div>
			<div class="sc_form_item sc_form_button"><button><?php esc_html_e('Send Message', 'wineshop'); ?></button></div>
            <?php if (!empty($privacy)) { ?>
                <input type="checkbox" id="i_agree_privacy_policy_sc_form_<?php echo esc_attr($cnt); ?>" name="i_agree_privacy_policy" class="sc_form_privacy_checkbox" value="1">

                <label for="i_agree_privacy_policy_sc_form_<?php echo esc_attr($cnt); ?>"><?php trx_utils_show_layout($privacy); ?></label>
                <?php
            }
            ?>
			<div class="result sc_infobox"></div>
		</form>
		<?php
	}
}
?>