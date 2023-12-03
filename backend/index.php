<?php
class Superaddons_Contactform7_Cost_Calculator_Backend{
	function __construct(  ){
		add_action( 'wpcf7_admin_init' , array($this,'add_tag_generator_total') , 100 );
		add_action( 'wpcf7_init', array($this,'add_shortcode_total') , 20 );
		add_action("admin_enqueue_scripts",array($this,"add_lib"),0,0);
		add_filter( 'wpcf7_validate_calculated', array($this,'wpcf7_calculated_validation_filter'), 10, 2 );
		add_filter( 'wpcf7_validate_calculated*', array($this,'wpcf7_calculated_validation_filter'), 10, 2 );
		add_filter("wpcf7_form_tag",array($this,"custom_options"),10,2);
	}
	function wpcf7_calculated_validation_filter( $result, $tag ) {
		$name = $tag->name;
		$value = isset( $_POST[$name] )
			? trim( strtr( (string) $_POST[$name], "\n", " " ) )
			: '';
		$min = $tag->get_option( 'min', 'signed_int', true );
		$max = $tag->get_option( 'max', 'signed_int', true );
		if ( $tag->is_required() && '' == $value ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}  elseif ( '' != $value && '' != $min && (float) $value < (float) $min ) {
			$result->invalidate( $tag, wpcf7_get_message( 'number_too_small' ) );
		} elseif ( '' != $value && '' != $max && (float) $max < (float) $value ) {
			$result->invalidate( $tag, wpcf7_get_message( 'number_too_large' ) );
		}
		return $result;
	}
	function add_lib(){
        wp_enqueue_script("tribute",CT_7_COST_PLUGIN_URL."backend/libs/tribute/tribute.js",array("jquery"));
        wp_enqueue_style("tribute",CT_7_COST_PLUGIN_URL."backend/libs/tribute/tribute.css");
        wp_enqueue_script("cf7_calculator",CT_7_COST_PLUGIN_URL."backend/js/cf7_calculator.js",array("jquery","tribute"),time(),false);
        $demo = '';
        wp_localize_script( "cf7_calculator", "cf7_calculator", array("data"=>$demo) );
    }
	function add_tag_generator_total(){
		if ( ! class_exists( 'WPCF7_TagGenerator' ) ) return;
		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add( 'calculated', __( 'Calculator', 'contact-form-7-cost-calculator' ),
			array($this,'tag_generator_total') );
	}
	function tag_generator_total($contact_form , $args = ''){
		$args = wp_parse_args( $args, array() );
		$check = get_option( '_redmuber_item_20085516');
		$type = $args['id'];
		$datas = array();
		$datas_done = array();
		$text_pro = "";
		$disable_pro = "";
		if($check != "ok"){
			$text_pro = "-Pro version";
			$disable_pro = " disabled";
		}
		$datas[] = array("key"=>"if( condition, true, false)", "value"=>"if( condition, true, false)");
		$datas[] = array("key"=>"if( condition, true, if(condition, true, false))", "value"=>"if( condition, true, if( condition, true, false))");
		$datas[] = array("key"=>"days( date_end, date_start)", "value"=>"days( end, start)");
		$datas[] = array("key"=>"months( date_end, date_start)", "value"=>"months( end, start)");
		$datas[] = array("key"=>"years( date_end, date_start)", "value"=>"years( end, start)");
		$datas[] = array("key"=>"round( number )", "value"=>"round( number )");
		$datas[] = array("key"=>"round2( number, decimal)", "value"=>"round2( number, 2)");
		$datas[] = array("key"=>"floor( number )", "value"=>"floor( number )");
		$datas[] = array("key"=>"floor2( number, decimal)", "value"=>"floor2( number, 2)");
		$datas[] = array("key"=>"ceil( number )", "value"=>"ceil( number )");
		$datas[] = array("key"=>"mod( number % number)", "value"=>"mod( number, number)");
		$datas[] = array("key"=>"age( Birth date )", "value"=>"age()");
		$datas[] = array("key"=>"age2( Birth date, Age at the Date of)", "value"=>"age2( birth_date, date)");
		$datas[] = array("key"=>"now (Current date)", "value"=>"now");
		
		$datas[] = array("key"=>"==", "value"=>"==");
		$datas[] = array("key"=>"pi = 3.14", "value"=>"pi");
		$datas[] = array("key"=>"e = 2.71", "value"=>"e");
		$datas[] = array("key"=>"abs( -3 ) = 3", "value"=>"abs( number )");
		$datas[] = array("key"=>"sqrt( 16 ) = 4", "value"=>"sqrt( number )");
		$datas[] = array("key"=>"sin( 0 ) = 0", "value"=>"sin( number )");
		$datas[] = array("key"=>"cos( 0 ) = 1", "value"=>"cos( number )");
		$datas[] = array("key"=>"pow( 2,3 ) = 8", "value"=>"pow( number , number )");
		$datas[] = array("key"=>"random( number start , number end ) ", "value"=>"random( number, number )");
		$datas[] = array("key"=>"mod( 2,3) = 1", "value"=>"mod( number, number )");
		$datas[] = array("key"=>"avg( 10,20,60,...) = 30", "value"=>"avg( number, number )");
		$datas[] = array("key"=>"min( number 1, number 2, ...)", "value"=>"min( number1, number2)");
		$datas[] = array("key"=>"max( number 1, number 2, ...)", "value"=>"max( number1, number2)");
		$tags = $contact_form->scan_form_tags();
        foreach( $datas as $data ){
        	$datas_done[] = array("key"=>$data["key"].$text_pro,"value"=>$data["value"]);
        }
        $datas_done[] = array("key"=>"a + b", "value"=>"+");
		$datas_done[] = array("key"=>"a - b", "value"=>"-");
		$datas_done[] = array("key"=>"a / b", "value"=>"/");
		$datas_done[] = array("key"=>"a * b", "value"=>"*");
		foreach ($tags as $tag_inner):
    		$datas_done[] = array("key"=>$tag_inner["name"] ." (Name)", "value"=>$tag_inner["name"]);
        endforeach;
		?>
		<div class="control-box">
			<script>
     var contact_form_7_calculator_name = <?php echo json_encode($datas_done); ?>
    </script>
			<fieldset>
				<table class="form-table">
					<tbody>
						<?php if($check != "ok") {
							?>
							<tr>
						<th scope="row"><?php echo esc_html( __( 'Pro version', 'contact-form-7-cost-calculator' ) ); ?></th>
						<td>
							<a href="https://add-ons.org/plugin/contact-form-7-cost-calculator/" target="_blank">https://add-ons.org/plugin/contact-form-7-cost-calculator/</a>
						</td>
						</tr>
							<?php
						} ?>
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
							<th scope="row"><?php esc_html_e("Type input","contact-form-7-cost-calculator") ?></th>
							<td><label><input type="checkbox" name="cf7_label" class="option" value="on"> <?php esc_html_e("Hide input and show lable","contact-form-7-cost-calculator") ?></label></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e("Lable display Property","contact-form-7-cost-calculator") ?></th>
							<td><label><input type="checkbox" name="cf7_block" class="option" value="on"> <?php esc_html_e("Displays an element as a block element (like <p>). It starts on a new line, and takes up the whole width","contact-form-7-cost-calculator") ?></label></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e("Hide Field","contact-form-7-cost-calculator") ?></th>
							<td><label><input type="checkbox" name="cf7_hide" class="option" value="on"> <?php esc_html_e("Hide Field","contact-form-7-cost-calculator") ?></label></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e("Float Right","contact-form-7-cost-calculator") ?></th>
							<td><label><input type="checkbox" name="float_right" class="option" value="on"> <?php esc_html_e("Float Right","contact-form-7-cost-calculator") ?></label></td>
						</tr>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7-cost-calculator' ) ); ?></label></th>
							<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
						</tr>
						
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Set Formula', 'contact-form-7-cost-calculator' ) ); ?></label></th>
							<td>
								<div id="autocomplete-textarea-container">
									<textarea rows="10" id="autocomplete-textarea" class="large-text code" name="values" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"></textarea>
								</div>
								 <br>
								<?php _e( 'Eg: (number-253 + number-254)/ 2 + radio_custom-708 + checkbox_custom-708', 'contact-form-7-cost-calculator' ); ?> <br>
									<strong>number-253, number-254, radio_custom-708, checkbox_custom-708</strong> is name field
								</td>
							</tr>
							<tr>
							<th scope="row">
								<?php esc_html_e("Number Format","contact-form-7-cost-calculator") ?></th>
								<td>
								<label><input type="checkbox" name="format" class="option calculatedformat_enable" value="on"> <?php esc_html_e("Enable Number Format","contact-form-7-cost-calculator") ?></label>
								</td>
						    </tr>
						    <tr class="calculatedformat hidden">
						    <th scope="row">
								<?php esc_html_e("Symbols","contact-form-7-cost-calculator") ?></th>
								<td>
								<label>
									<input <?php echo esc_attr($disable_pro) ?> type="text" name="symbols" class="option" id="<?php echo esc_attr( $args['content'] . '-symbols' ); ?>" /></label>
									<?php  echo esc_html($text_pro);  ?>
									<p><?php esc_html_e("Special characters:","contact-form-7-cost-calculator") ?>
										<ul style="margin:0px">
											<li><strong>space</strong></li>
											<li><strong>€</strong> = EUR</li>
											<li><strong>,</strong> = comma</li>
										</ul>
									</p>
								</td>
						    </tr>
						     <tr class="calculatedformat hidden">
						    <th scope="row">
								<?php esc_html_e("Symbols position Right","contact-form-7-cost-calculator");?>
								</th>
								<td>
									<label>
									<input <?php echo esc_attr($disable_pro) ?> type="checkbox" name="symbols_position_right" class="option" value="on" id="<?php echo esc_attr( $args['content'] . '-symbols_position_right' ); ?>" /> <?php  echo esc_html($text_pro);  ?>
								</label>
								</td>
						    </tr>
						     <tr class="calculatedformat hidden">
						    <th scope="row">
								<?php esc_html_e("Thousand separator","contact-form-7-cost-calculator") ?></th>
								<td>
									<input <?php echo esc_attr($disable_pro) ?> type="text" name="thousand_sep" class="option" id="<?php echo esc_attr( $args['content'] . '-thousand_sep' ); ?>" value="" placeholder="," /><?php  echo esc_html($text_pro);  ?>
								</td>
						    </tr>
						     <tr class="calculatedformat hidden">
						    <th scope="row">
								<?php esc_html_e("Decimal separator","contact-form-7-cost-calculator") ?></th>
								<td>
									<input <?php echo esc_attr($disable_pro) ?> type="text" name="decimal_sep" class="option" id="<?php echo esc_attr( $args['content'] . '-decimal_sep' ); ?>" value="" placeholder="." /><?php  echo esc_html($text_pro);  ?>
								</td>
						    </tr>
						     <tr class="calculatedformat hidden">
						    <th scope="row">
								<?php esc_html_e("Number of decimals","contact-form-7-cost-calculator") ?></th>
								<td>
									<input <?php echo esc_attr($disable_pro) ?> type="text" name="num_decimals" class="option" id="<?php echo esc_attr( $args['content'] . '-num_decimals' ); ?>" value="" placeholder="2" /><?php  echo esc_html($text_pro);  ?>
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
				<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />
				<div class="submitbox">
					<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7-cost-calculator' ) ); ?>" />
				</div>
				<br class="clear" />
			</div>
			<?php
		}
		function parse_atts($text){
			$atts = array( 'options' => array(), 'values' => array() );
			$text = preg_replace( "/[\x{00a0}\x{200b}]+/u", " ", $text );
			$text = trim( $text );
			$pattern = '%^([-+*=0-9a-zA-Z:.€,!?#$&@_/|\%\r\n\t ]*?)((?:[\r\n\t ]*"[^"]*"|[\r\n\t ]*\'[^\']*\')*)$%';
			if ( preg_match( $pattern, $text, $match ) ) {
				if ( ! empty( $match[1] ) ) {
					$atts['options'] = preg_split( '/[\r\n\t ]+/', trim( $match[1] ) );
				}
				if ( ! empty( $match[2] ) ) {
					preg_match_all( '/"[^"]*"|\'[^\']*\'/', $match[2], $matched_values );
					$atts['values'] = wpcf7_strip_quote_deep( $matched_values[0] );
				}
			} else {
				$atts = $text;
			}
			return $atts;
		}
		function custom_options($scanned_tag, $replace){
			$attr = "";
			if( $scanned_tag["type"] == "calculated" && $scanned_tag["attr"] !="" ) {
				$text = $scanned_tag["attr"];
				$attr = $this->parse_atts( $text );
				if ( is_array( $attr ) ) {
						if ( is_array( $attr['options'] ) ) {
							if ( ! empty( $attr['options'] ) ) {
								$scanned_tag['raw_name'] = array_shift( $attr['options'] );
								if ( ! wpcf7_is_name( $scanned_tag['raw_name'] ) ) {
									return $m[0]; // Invalid name is used. Ignore this tag.
								}
								$scanned_tag['name'] = strtr( $scanned_tag['raw_name'], '.', '_' );
							}
							$scanned_tag['options'] = (array) $attr['options'];
						}
						$scanned_tag['raw_values'] = (array) $attr['values'];
						if ( WPCF7_USE_PIPE ) {
							$pipes = new WPCF7_Pipes( $scanned_tag['raw_values'] );
							$scanned_tag['values'] = $pipes->collect_befores();
							$scanned_tag['pipes'] = $pipes;
						} else {
							$scanned_tag['values'] = $scanned_tag['raw_values'];
						}
						$scanned_tag['labels'] = $scanned_tag['values'];
					} else {
						$scanned_tag['attr'] = $attr;
					}
					$scanned_tag['values'] = array_map( 'trim', $scanned_tag['values'] );
					$scanned_tag['labels'] = array_map( 'trim', $scanned_tag['labels'] );
					$content = trim( $m[5] );
					$content = preg_replace( "/<br[\r\n\t ]*\/?>$/m", '', $content );
					$scanned_tag['content'] = $content;
			}
			return $scanned_tag;
		}
		function add_shortcode_total(){
			wpcf7_add_form_tag(
				array( 'calculated' , 'calculated*'  ),
				array($this,'total_shortcode_handler'), true );
		}
		function total_shortcode_handler( $tag ) {
			if ( empty( $tag->name ) )
				return '';
			$validation_error = wpcf7_get_validation_error( $tag->name );
			$class = wpcf7_form_controls_class( $tag->type, 'wpcf7-total' );
			if ( $validation_error )
				$class .= ' wpcf7-not-valid';
			$atts = array();
			$atts['size'] = $tag->get_size_option( '40' );
			$atts['maxlength'] = $tag->get_maxlength_option();
			$atts['minlength'] = $tag->get_minlength_option();
			$atts['min'] = $tag->get_option( 'min', 'signed_int', true );
			$atts['max'] = $tag->get_option( 'max', 'signed_int', true );
			if ( $atts['maxlength'] && $atts['minlength'] && $atts['maxlength'] < $atts['minlength'] ) {
				unset( $atts['maxlength'], $atts['minlength'] );
			}
			$atts['class'] = $tag->get_class_option( $class );
			$cf_lable = false;
			$cf_block = "";
			$float_right ="";
			if( $tag->has_option("cf7_hide") ) { 
				$atts['class'] .=" cf7-hide";
			}
			if( $tag->has_option("cf7_label") ) { 
				$cf_lable = true;
			}
			if( $tag->has_option("cf7_block") ) { 
				$cf_block = "cf7-block";
			}
			if( $tag->has_option("float_right") ) { 
				$float_right = "cf7-total-right";
			}
			$format = $tag->get_option( 'format' );
			if( $tag->has_option("format") ) {
				$atts['class'] .= " number-format";
				$symbols = $tag->get_option( 'symbols',"",true);
				$symbols = str_replace("EUR","€",$symbols);
				if( $symbols != "" ){
					$atts['data-a-sign']= str_replace("space","&nbsp;",$symbols);
				}
				$thousand_sep = $tag->get_option( 'thousand_sep',"",true);
				$thousand_sep = str_replace("comma",",",$thousand_sep);
				if( $thousand_sep != "" ){
					$atts['data-a-sep']=  str_replace("space","&nbsp;",$thousand_sep);
				}else{
					$atts['data-a-sep']=  ",";
				}
				$decimal_sep = $tag->get_option( 'decimal_sep',"",true);
				$decimal_sep = str_replace("comma",",",$decimal_sep);
				if( $decimal_sep != "" ){
					$atts['data-a-dec']=  str_replace("space","&nbsp;",$decimal_sep);
				}else{
					$atts['data-a-dec']= ".";
				}
				$num_decimals= $tag->get_option( 'num_decimals',"",true);
				if( $num_decimals != "" ){
					$atts['data-m-dec']= $num_decimals;
				}else{
					$atts['data-m-dec']= 2;
				}
				if( $tag->has_option("symbols_position_right") ) { 
					$atts['data-p-sign']= "s";
				}
			}
			$atts['class'] .= " ctf7-total";
			$atts['id'] = $tag->get_id_option();
			$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );
			$atts['readonly'] = 'readonly';
			if ( $tag->is_required() )
				$atts['aria-required'] = 'true';
			$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
			$value = (string) reset( $tag->values );
			if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
				$atts['placeholder'] = $value;
				$value = '';
			}
			$value = $tag->get_default_option( $value );
			$scval = do_shortcode('['.$value.']');
			if( $scval != '['.$value.']' ){
				$value = @$scval;
			}
			$atts['value'] = 0;
			$atts['type'] = 'text';
			$atts['name'] = $tag->name;
			$atts = wpcf7_format_atts( $atts );
			if( $cf_lable ) {
				$custom = '<strong class="cf7-calculated-name '.$cf_block.'" '.$atts.'>0</strong>';
				$html = sprintf(
				'<span class="wpcf7-form-control-wrap %1$s">%5$s <input data-number="0" style="display:none;" %2$s %4$s />%3$s</span>',
				sanitize_html_class( $tag->name ) ." " .$float_right , $atts, $validation_error, 'data-formulas="'.$value.'"',$custom );
			}else{
				$html = sprintf(
				'<span class="%5$s wpcf7-form-control-wrap %1$s"><input data-number="0" %2$s %4$s />%3$s</span>',
				sanitize_html_class( $tag->name ), $atts, $validation_error, 'data-formulas="'.$value.'"',$float_right );
			}
			return $html;
		}
	}
new Superaddons_Contactform7_Cost_Calculator_Backend;