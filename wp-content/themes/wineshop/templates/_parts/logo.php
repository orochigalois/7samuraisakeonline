<?php
if ( !empty($THEMEREX_GLOBALS['logo']) ) {
	$attr = getimagesize($THEMEREX_GLOBALS['logo']);
}
?>
					<div class="logo">
						<a href="<?php echo esc_url(home_url('/')); ?>"><?php
							echo !empty($THEMEREX_GLOBALS['logo']) 
								? '<img src="'.esc_url($THEMEREX_GLOBALS['logo']).'" class="logo_main" alt="'.esc_attr__('Logo', 'wineshop').'"'.(!empty($attr[3]) ? ' '.esc_attr($attr[3]) : '').'>'
								: '';
                            themerex_show_layout($THEMEREX_GLOBALS['logo_text']
								? '<div class="logo_text">'.($THEMEREX_GLOBALS['logo_text']).'</div>' 
								: '');
                            themerex_show_layout($THEMEREX_GLOBALS['logo_slogan']
								? '<br><div class="logo_slogan">' . esc_html($THEMEREX_GLOBALS['logo_slogan']) . '</div>' 
								: '');
						?></a>
					</div>
