<?php
/**
** A base module for [select] and [select*]
**/
/* form_tag handler */
add_action( 'wpcf7_init', 'wpcf7_add_form_tag_select_custom' );
function wpcf7_add_form_tag_select_custom() {
	wpcf7_add_form_tag( array( 'select_custom', 'select_custom*' ),
		'wpcf7_select_form_tag_handler_custom',
		array(
			'name-attr' => true,
			'selectable-values' => true,
		)
	);
}
function wpcf7_select_form_tag_handler_custom( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
	$custom_values = $tag->raw_values; 
	$validation_error = wpcf7_get_validation_error( $tag->name );
	$class = wpcf7_form_controls_class( $tag->type );
	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}
	$atts = array();
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );
	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}
	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
	$multiple = $tag->has_option( 'multiple' );
	$include_blank = $tag->has_option( 'include_blank' );
	$first_as_label = $tag->has_option( 'first_as_label' );
	if ( $tag->has_option( 'size' ) ) {
		$size = $tag->get_option( 'size', 'int', true );
		if ( $size ) {
			$atts['size'] = $size;
		} elseif ( $multiple ) {
			$atts['size'] = 4;
		} else {
			$atts['size'] = 1;
		}
	}
	$values = $tag->values;
	$labels = $tag->labels;
	if ( $data = (array) $tag->get_data_option() ) {
		$values = array_merge( $values, array_values( $data ) );
		$labels = array_merge( $labels, array_values( $data ) );
	}
	$defaults = array();
	$default_choice = $tag->get_default_option( null, array(
		'multiple' => $multiple,
		'shifted' => $include_blank,
	) );
	if ( $matches = $tag->get_first_match_option( '/^default:([0-9_]+)$/' ) ) {
		$defaults = array_merge( $defaults, explode( '_', $matches[1] ) );
	}
	$defaults = array_unique( $defaults );
	$shifted = false;
	if ( $include_blank || empty( $values ) ) {
		$values = array_merge(array('--'=>'---'),$values);
		$shifted = true;
		array_unshift($custom_values," |---");
	} elseif ( $first_as_label ) {
		$values[0] = '';
	}
	$html = '';
	$hangover = wpcf7_get_hangover( $tag->name );
	$i=0;
	foreach ( $values as $key => $value ) {
		$selected = false;
		$custom_data = explode("|",$custom_values[$i]);
		$label = trim($custom_data[1]);
		$value = trim($custom_data[0]);
		if ( $hangover ) {
			$selected = in_array( $value, (array) $hangover, true );
		} else {
			$selected = in_array( $value, (array) $default_choice, true );
		}
		$item_atts = array(
			'value' => $value,
			'selected' => $selected ? 'selected' : '',
		);
		$item_atts = wpcf7_format_atts( $item_atts );
		//$label = isset( $labels[$key] ) ? $labels[$key] : $value;
		$html .= sprintf( '<option %1$s>%2$s</option>',
			$item_atts, esc_html( $label ) );
		$i++;
	}
	if ( $multiple ) {
		$atts['multiple'] = 'multiple';
	}
	$atts['name'] = $tag->name . ( $multiple ? '[]' : '' );
	$atts = wpcf7_format_atts( $atts );
	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
		sanitize_html_class( $tag->name ), $atts, $html, $validation_error );
	return $html;
}
/* Validation filter */
add_filter( 'wpcf7_validate_select_custom', 'wpcf7_select_validation_filter_custom', 10, 2 );
add_filter( 'wpcf7_validate_select_custom*', 'wpcf7_select_validation_filter_custom', 10, 2 );
function wpcf7_select_validation_filter_custom( $result, $tag ) {
	$name = $tag->name;
	if ( isset( $_POST[$name] ) && is_array( $_POST[$name] ) ) {
		foreach ( $_POST[$name] as $key => $value ) {
			if ( '' === $value ) {
				unset( $_POST[$name][$key] );
			}
		}
	}
	$empty = ! isset( $_POST[$name] ) || empty( $_POST[$name] ) && '0' !== $_POST[$name];
	if ( $tag->is_required() && $empty ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	}
	return $result;
}
/* Tag generator */
add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_menu_custom', 98 );
function wpcf7_add_tag_generator_menu_custom() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'select_custom', __( 'drop-down menu price', 'contact-form-7-cost-calculator' ),
		'wpcf7_tag_generator_menu_custom' );
}
function wpcf7_tag_generator_menu_custom( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$description = __( "Generate a form-tag for a drop-down menu. For more details, see %s.", 'contact-form-7-cost-calculator' );
	$desc_link = wpcf7_link( __( 'https://contactform7.com/checkboxes-radio-buttons-and-menus/', 'contact-form-7-cost-calculator' ), __( 'Checkboxes, Radio Buttons and Menus', 'contact-form-7-cost-calculator' ) );
?>
<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>
<table class="form-table">
<tbody>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7-cost-calculator' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7-cost-calculator' ) ); ?></legend>
		<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7-cost-calculator' ) ); ?></label>
		</fieldset>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7-cost-calculator' ) ); ?></label></th>
	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
	</tr>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Options', 'contact-form-7-cost-calculator' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Options', 'contact-form-7-cost-calculator' ) ); ?></legend>
		<textarea name="values" class="values" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"></textarea>
		<?php echo  __( "One option per line (number|text): Ex: <strong>10|Blue $10</strong>", 'contact-form-7-cost-calculator'  ); ?></span></label><br />
		<label><input type="checkbox" name="include_blank" class="option" /> <?php echo esc_html( __( 'Insert a blank item as the first option', 'contact-form-7-cost-calculator' ) ); ?></label>
		</fieldset>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7-cost-calculator' ) ); ?></label></th>
	<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
	</tr>
	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7-cost-calculator' ) ); ?></label></th>
	<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
	</tr>
</tbody>
</table>
</fieldset>
</div>
<div class="insert-box">
	<input type="text" name="select_custom" class="tag code" readonly="readonly" onfocus="this.select()" />
	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7-cost-calculator' ) ); ?>" />
	</div>
	<br class="clear" />
	<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7-cost-calculator' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
</div>
<?php
}
