<?php
/**
 * Manages and renders a text settings field.
 * /includes/field-date.php
 *
 * @uses HLSocials_Field
 */
class HLSocials_Field_Date extends HLSocials_Field {

	/**
	 * Render this field.
	 *
	 * @access public
	 */
	public function render() {
		?>
		<input name="<?php echo $this->get_id(); ?>" id="<?php echo $this->get_id(); ?>" type="date" value="<?php echo esc_attr($this->get_value()); ?>" class="regular-date" <?php echo $this->render_required(); ?> />
		<?php
		$this->render_help();
	}

}
