<div class="hlsocials-form">
	<div class="wrap">
		<h2><?php echo $hlsocials->get_form()->get_menu_title(); ?></h2>

		<p><?php _e('Please, at least select a start date in the future.', 'hlsocials'); ?></p>

		<form method="post" action="options.php">
			<?php
			settings_fields( $hlsocials->get_form()->get_menu_id() );
			do_settings_sections( $hlsocials->get_form()->get_menu_id() );
			submit_button(__('Schedule Socials', 'hlsocials'));
			?>
		</form>
		<script defer="defer">
			(function($){
				$(document).ready(function() {

						// Function to clean the content
						function cleanContent(content) {
								// Remove matches based on the regex
								content = content.replace(/("|\d+\.? )/g, '');

								// Remove empty lines
								content = content.replace(/^\s*[\r\n]/gm, '');

								return content;
						}

						// Detect paste event on the textarea with ID "hlsocials_entries"
						$('#hlsocials_entries').on('paste', function() {
								// Use a setTimeout to ensure we get the pasted content after it has been pasted
								setTimeout(() => {
										let content = $(this).val();
										$(this).val(cleanContent(content));
								}, 0);
						});

						// Run the clean up function before form submission
						$('form[action="options.php"]').on('submit', function() {
								let content = $('#hlsocials_entries').val();
								$('#hlsocials_entries').val(cleanContent(content));
						});
				});
			})(window.jQuery)
		</script>
	</div>
</div>
