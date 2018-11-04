<?php

	/**
	 * CMB2 cutom field for repeater group
	 */
	class RepeatableGroups
	{
		protected $post_id;
		protected $meta_box_id;
		protected $post_group_key;
		protected $values;
		protected $fieldsTypes;

		/**
		 * Class cunstructor
		 */
		function __construct()
		{
    		$this->meta_box_id = Config::$prefix . 'blocks_fields';
    		$this->post_group_key = Config::$prefix . 'repeatable_group';
			add_action( 'wp_loaded', array($this, 'enqueue_scripts') );
			add_filter( 'cmb2_render_repeatable_group', array($this, 'cmb2_render_repeatable_group_field_callback'), 10, 5 );
			// load ajax
			add_action( 'wp_ajax_basetheme_wp_editor', array($this, 'basetheme_wp_editor') );
    		add_action( 'wp_ajax_nopriv_basetheme_wp_editor', array($this, 'basetheme_wp_editor') );
    		// post save action
    		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
    		// Available fields type
    		$this->fieldsTypes = array(
				'title' => esc_html__('title', 'cmb2'),
				'text' => esc_html__('text', 'cmb2'),
				'text_small' => esc_html__('text small', 'cmb2'),
				'text_medium' => esc_html__('text medium', 'cmb2'),
				'text_email' => esc_html__('text email', 'cmb2'),
				'text_url' => esc_html__('text url', 'cmb2'),
				'text_money' => esc_html__('text money', 'cmb2'),
				'textarea' => esc_html__('textarea', 'cmb2'),
				'textarea_small' => esc_html__('textarea small', 'cmb2'),
				'textarea_code' => esc_html__('textarea code', 'cmb2'),
				'text_time' => esc_html__('text time', 'cmb2'),
				'select_timezone' => esc_html__('select timezone', 'cmb2'),
				'text_date' => esc_html__('text date', 'cmb2'),
				'text_date_timestamp' => esc_html__('text _date timestamp', 'cmb2'),
				'text_datetime_timestamp' => esc_html__('text datetime timestamp', 'cmb2'),
				'text_datetime_timestamp_timezone' => esc_html__('text datetime timestamp timezone', 'cmb2'),
				'hidden' => esc_html__('hidden', 'cmb2'),
				'radio' => esc_html__('radio', 'cmb2'),
				'radio_inline' => esc_html__('radio inline', 'cmb2'),
				'taxonomy_radio' => esc_html__('taxonomy radio', 'cmb2'),
				'taxonomy_radio_inline' => esc_html__('taxonomy radio inline', 'cmb2'),
				'taxonomy_radio_hierarchical' => esc_html__('taxonomy radio hierarchical', 'cmb2'),
				'select' => esc_html__('select', 'cmb2'),
				'taxonomy_select' => esc_html__('taxonomy select', 'cmb2'),
				'checkbox' => esc_html__('checkbox', 'cmb2'),
				'multicheck' => esc_html__('multicheck', 'cmb2'),
				'multicheck_inline' => esc_html__('multicheck inline', 'cmb2'),
				'taxonomy_multicheck' => esc_html__('taxonomy multicheck', 'cmb2'),
				'taxonomy_multicheck_inline' => esc_html__('taxonomy multicheck inline', 'cmb2'),
				'taxonomy_multicheck_hierarchical' => esc_html__('taxonomy multicheck hierarchical', 'cmb2'),
				'wysiwyg' => esc_html__('wysiwyg', 'cmb2'),
				'file' => esc_html__('file', 'cmb2'),
				'file_list' => esc_html__('file list', 'cmb2'),
				'oembed' => esc_html__('oembed', 'cmb2'),
				'group' => esc_html__('group', 'cmb2') ,
				'repeatable_group' => esc_html__('repeatable group', 'cmb2') ,
			);
		}

		/**
		 * Enqueue scripts & styles
		 */
		public function enqueue_scripts()
		{
			if (!is_admin()) return;
			
			wp_enqueue_editor(array( 'type' => 'text/html' ));
			wp_enqueue_style( 'repeatable-fields-css', Config::core_dir_uri() . 'libraries/CMB2-repeatable-fields/css/repeatable-fields.css', array(), microtime() );
			
			// tinymce
			wp_enqueue_style( 'editor-css', includes_url('css/') . 'editor.css', array(), microtime() );
			// init repeatable fields js
			wp_enqueue_script( 'repeatable-fields-js', Config::core_dir_uri() . 'libraries/CMB2-repeatable-fields/js/repeatable-fields.js' , array('jquery'), microtime(), true );
			wp_enqueue_script( 'init-js', Config::core_dir_uri() . 'libraries/CMB2-repeatable-fields/js/init.js' , array('jquery'), microtime(), true );
		}

		/**
		 * Init wp editor using ajax
		 */
		public function basetheme_wp_editor( $settings = array() ) {
			$data = [];
	        if(check_ajax_referer( 'uploader-ajax', 'security' ) ) {
	        	$editor_id = filter_var($_POST['param']['editor_id']);
	        	$editor_name = filter_var($_POST['param']['editor_name']);
	        	ob_start();
            	wp_editor( '', $editor_id, array(
			        'media_buttons' => true,
			        'textarea_rows' => 3,
			        'teeny' => true,
			        'wpautop' => true,
			        'textarea_name' => $editor_name,
			        'quicktags' => true,
			        'tinymce' => [
			            'paste_remove_styles' => true,
			            'paste_remove_spans' => true,
			            'plugins' => 'wplink',
			            'toolbar' => 'bold,italic,link,unlink',
			        ],
			    ) );
            	$data['editor'] = ob_get_clean();
	            if ($data) {
	                die(json_encode(array("error" => 0, 'message' => $data)));
	            }else{
	                die(json_encode(array("error" => 1, 'message' => "Array Empty")));
	            } 
	        }else{
	            die(json_encode(array("error" => 1, 'message' => ' No permission !!')));
	        }
	        die();
		}

		/**
		 * Save fields
		 */
		public function save_post($post_id)
		{
			if (empty($_POST)) return;
			$data = filter_input(INPUT_POST, Config::$prefix . 'repeatable_group', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
			update_post_meta( $post_id, $this->meta_box_id, $data );
		}

		/**
		 * Repeatable groups Field tempate
		 */
		public function repeater_template()
		{
			?>
				<tr class="template row" style="display: none;">
			        <td width="10%">
			            <!-- <span class="move btn btn-info">↕</span>
			            <span class="move-up btn btn-info">↑</span>
			            <input type="text" class="move-steps" size="1" value="1" disabled="">
			            <span class="move-down btn btn-info">↓</span> -->
			        </td>
			        <td width="80%">
			            <dl>
			               <dt>Field label</dt>
			               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][field_label]" class="form-control" disabled=""></dd>
			            </dl>
			            <dl>
			               <dt>Field type</dt>
			               <dd>
			               		<select type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][field_type]" class="form-control select-type" disabled="">
			               			<?php foreach ($this->fieldsTypes as $k => $type): ?>
			               				<option value="<?php echo $k; ?>"><?php echo $type; ?></option>
			               			<?php endforeach; ?>
			               		</select>
			           		</dd>
			            </dl>
			            <dl>
			               <dt>Field id</dt>
			               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][field_id]" class="form-control" data-no-space="true" disabled=""></dd>
			            </dl>
			            <dl>
			               <dt>Field required ?</dt>
			               <dd><input type="checkbox" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][field_required]" class="form-control" disabled=""></dd>
			            </dl>
			            <dl>
			               <dt>Field default value</dt>
			               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][field_default_value]" class="form-control" disabled=""></dd>
			            </dl>
			            <dl>
			               <dt>Field placeholder</dt>
			               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][field_placeholder]" class="form-control" disabled=""></dd>
			            </dl>
			            <dl>
                           <dt>Description</dt>
                           <dd><textarea name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][field_description]" class="form-control editor" disabled=""></textarea></dd>
                        </dl>

                        <!-- Options -->
			            <table class="table table-striped table-bordered nested-group">
			               	<thead>
				                <tr>
				                    <td width="10%" colspan="2"><h4>Add options</h4></td>
				                </tr>
			               	</thead>
			               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
								<tr class="row">
									<!-- <td width="10%">
									    <span class="move btn btn-info">↕</span>
									    <span class="move-up btn btn-info">↑</span>
									    <input type="text" class="move-steps" size="1" value="1" disabled="">
									    <span class="move-down btn btn-info">↓</span>
									</td> -->
									<td width="90%">
									    <table class="table table-striped table-bordered nested-group">
							               	<thead>
								                <tr>
								                    <td width="10%" colspan="4"><span class="add btn btn-success">Add</span></td>
								                </tr>
							               	</thead>
							               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
												<tr class="template row" style="display: none;">
													<td width="10%">
													    <!-- <span class="move btn btn-info">↕</span>
													    <span class="move-up btn btn-info">↑</span>
													    <input type="text" class="move-steps" size="1" value="1" disabled="">
													    <span class="move-down btn btn-info">↓</span> -->
													</td>
													<td width="45%">
													    <dl>
													       <dt class="option-count">Option</dt>
													       <dd>
													       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][options][{{row-count-placeholder}}][option_key]" class="form-control" disabled="">
													   		</dd>
													    </dl>
													</td>
													<td width="45%">
													    <dl>
													       <dt>Value</dt>
													       <dd>
													       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][options][{{row-count-placeholder}}][option_value]" class="form-control" disabled="">
													       </dd>
													    </dl>
													</td>
													<td width="10%"><span class="remove btn btn-danger">Remove</span></td>
												</tr>
							               	</tbody>
							            </table>
									</td>
									<!-- <td width="10%"><span class="remove btn btn-danger">Remove</span></td> -->
								</tr>
			               	</tbody>
			            </table>
			            <!-- Attributes -->
			            <table class="table table-striped table-bordered nested-group">
			               	<thead>
				                <tr>
				                    <td width="10%" colspan="2"><h4>Add attributes</h4></td>
				                </tr>
			               	</thead>
			               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
								<tr class="row">
									<!-- <td width="10%">
									    <span class="move btn btn-info">↕</span>
									    <span class="move-up btn btn-info">↑</span>
									    <input type="text" class="move-steps" size="1" value="1" disabled="">
									    <span class="move-down btn btn-info">↓</span>
									</td> -->
									<td width="90%">
									    <table class="table table-striped table-bordered nested-group">
							               	<thead>
								                <tr>
								                    <td width="10%" colspan="4"><span class="add btn btn-success">Add</span></td>
								                </tr>
							               	</thead>
							               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
												<tr class="template row" style="display: none;">
													<td width="10%">
													    <!-- <span class="move btn btn-info">↕</span>
													    <span class="move-up btn btn-info">↑</span>
													    <input type="text" class="move-steps" size="1" value="1" disabled="">
													    <span class="move-down btn btn-info">↓</span> -->
													</td>
													<td width="45%">
													    <dl>
													       <dt>Attribute</dt>
													       <dd>
													       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][attributes][{{row-count-placeholder}}][attribute_key]" class="form-control" disabled="">
													   		</dd>
													    </dl>
													</td>
													<td width="45%">
													    <dl>
													       <dt>Value</dt>
													       <dd>
													       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][attributes][{{row-count-placeholder}}][attribute_value]" class="form-control" disabled="">
													       	</dd>
													    </dl>
													</td>
													<td width="10%"><span class="remove btn btn-danger">Remove</span></td>
												</tr>
							               	</tbody>
							            </table>
									</td>
									<!-- <td width="10%"><span class="remove btn btn-danger">Remove</span></td> -->
								</tr>
			               	</tbody>
			            </table>
			            <!-- Groups fields template -->
			            <table class="table table-striped table-bordered nested-group">
			               	<thead>
				                <tr>
				                    <td width="10%" colspan="2"><h4>Add group fields</h4></td>
				                </tr>
			               	</thead>
			               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
								<tr class="row">
									<!-- <td width="10%">
									    <span class="move btn btn-info">↕</span>
									    <span class="move-up btn btn-info">↑</span>
									    <input type="text" class="move-steps" size="1" value="1" disabled="">
									    <span class="move-down btn btn-info">↓</span>
									</td> -->
									<td width="90%">
									    <table class="table table-striped table-bordered nested-group">
							               	<thead>
								                <tr>
								                    <td width="10%" colspan="4"><span class="add btn btn-success">Add</span></td>
								                </tr>
							               	</thead>
							               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
												<tr class="template row" style="display: none;">
											        <td width="10%">
											            <!-- <span class="move btn btn-info">↕</span>
											            <span class="move-up btn btn-info">↑</span>
											            <input type="text" class="move-steps" size="1" value="1" disabled="">
											            <span class="move-down btn btn-info">↓</span> -->
											        </td>
											        <td width="80%">
											            <dl>
											               <dt>Field label</dt>
											               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][field_label]" class="form-control" disabled=""></dd>
											            </dl>
											            <dl>
											               <dt>Field type</dt>
											               <dd>
											               		<select type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][field_type]" class="form-control select-type" disabled="">
											               			<?php foreach ($this->fieldsTypes as $k => $type): ?>
											               				<option value="<?php echo $k; ?>"><?php echo $type; ?></option>
											               			<?php endforeach; ?>
											               		</select>
											           		</dd>
											            </dl>
											            <dl>
											               <dt>Field id</dt>
											               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][field_id]" class="form-control" data-no-space="true" disabled=""></dd>
											            </dl>
											            <dl>
											               <dt>Field required ?</dt>
											               <dd><input type="checkbox" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][field_required]" class="form-control" disabled=""></dd>
											            </dl>
											            <dl>
											               <dt>Field default value</dt>
											               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][field_default_value]" class="form-control" disabled=""></dd>
											            </dl>
											            <dl>
											               <dt>Field placeholder</dt>
											               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][field_placeholder]" class="form-control" disabled=""></dd>
											            </dl>
											            <dl>
								                           <dt>Description</dt>
								                           <dd><textarea name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][field_description]" class="form-control editor" disabled=""></textarea></dd>
								                        </dl>
											            <table class="table table-striped table-bordered nested-group">
											               	<thead>
												                <tr>
												                    <td width="10%" colspan="2"><h4>Add options</h4></td>
												                </tr>
											               	</thead>
											               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
																<tr class="row">
																	<!-- <td width="10%">
																	    <span class="move btn btn-info">↕</span>
																	    <span class="move-up btn btn-info">↑</span>
																	    <input type="text" class="move-steps" size="1" value="1" disabled="">
																	    <span class="move-down btn btn-info">↓</span>
																	</td> -->
																	<td width="90%">
																	    <table class="table table-striped table-bordered nested-group">
															               	<thead>
																                <tr>
																                    <td width="10%" colspan="4"><span class="add btn btn-success">Add</span></td>
																                </tr>
															               	</thead>
															               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
																				<tr class="template row" style="display: none;">
																					<td width="10%">
																					    <!-- <span class="move btn btn-info">↕</span>
																					    <span class="move-up btn btn-info">↑</span>
																					    <input type="text" class="move-steps" size="1" value="1" disabled="">
																					    <span class="move-down btn btn-info">↓</span> -->
																					</td>
																					<td width="45%">
																					    <dl>
																					       <dt class="option-count">Option</dt>
																					       <dd>
																					       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][options][{{row-count-placeholder}}][option_key]" class="form-control" disabled="">
																					   		</dd>
																					    </dl>
																					</td>
																					<td width="45%">
																					    <dl>
																					       <dt>Value</dt>
																					       <dd>
																					       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][options][{{row-count-placeholder}}][option_value]" class="form-control" disabled="">
																					       </dd>
																					    </dl>
																					</td>
																					<td width="10%"><span class="remove btn btn-danger">Remove</span></td>
																				</tr>
															               	</tbody>
															            </table>
																	</td>
																	<!-- <td width="10%"><span class="remove btn btn-danger">Remove</span></td> -->
																</tr>
											               	</tbody>
											            </table>
											            <table class="table table-striped table-bordered nested-group">
											               	<thead>
												                <tr>
												                    <td width="10%" colspan="2"><h4>Add attributes</h4></td>
												                </tr>
											               	</thead>
											               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
																<tr class="row">
																	<!-- <td width="10%">
																	    <span class="move btn btn-info">↕</span>
																	    <span class="move-up btn btn-info">↑</span>
																	    <input type="text" class="move-steps" size="1" value="1" disabled="">
																	    <span class="move-down btn btn-info">↓</span>
																	</td> -->
																	<td width="90%">
																	    <table class="table table-striped table-bordered nested-group">
															               	<thead>
																                <tr>
																                    <td width="10%" colspan="4"><span class="add btn btn-success">Add</span></td>
																                </tr>
															               	</thead>
															               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
																				<tr class="template row" style="display: none;">
																					<td width="10%">
																					    <!-- <span class="move btn btn-info">↕</span>
																					    <span class="move-up btn btn-info">↑</span>
																					    <input type="text" class="move-steps" size="1" value="1" disabled="">
																					    <span class="move-down btn btn-info">↓</span> -->
																					</td>
																					<td width="45%">
																					    <dl>
																					       <dt>Attribute</dt>
																					       <dd>
																					       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][attributes][{{row-count-placeholder}}][attribute_key]" class="form-control" disabled="">
																					   		</dd>
																					    </dl>
																					</td>
																					<td width="45%">
																					    <dl>
																					       <dt>Value</dt>
																					       <dd>
																					       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][attributes][{{row-count-placeholder}}][attribute_value]" class="form-control" disabled="">
																					       	</dd>
																					    </dl>
																					</td>
																					<td width="10%"><span class="remove btn btn-danger">Remove</span></td>
																				</tr>
															               	</tbody>
															            </table>
																	</td>
																	<!-- <td width="10%"><span class="remove btn btn-danger">Remove</span></td> -->
																</tr>
											               	</tbody>
											            </table>
											        </td>
											        <td width="10%"><span class="remove btn btn-danger">Remove</span></td>
										      	</tr>
							               	</tbody>
							            </table>
									</td>
									<!-- <td width="10%"><span class="remove btn btn-danger">Remove</span></td> -->
								</tr>
			               	</tbody>
			            </table>
			        </td>
			        <td width="10%"><span class="remove btn btn-danger">Remove</span></td>
		      	</tr>
			<?php
		}

		/**
		 * Render repeatable groups Field
		 */
		public function cmb2_render_repeatable_group_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
			// make sure we specify each part of the value we need.
			// $this->meta_box_id = $field->args['group_args']['group_id'];
			$this->post_id = $object_id;
			// print_r(get_post_meta( $this->post_id ));
			$this->values = get_post_meta( $this->post_id, $this->meta_box_id, true );
			// print_r($this->values);
			// die();
			?>
			<div class="repeat">
				<h4>Fields</h4>
				<?php echo $field_type->_desc( true ); ?>
				<table class="table table-striped table-bordered">
				   <thead>
				      	<tr>
				        	<td width="10%" colspan="3"><span class="add btn btn-success">Add fields</span></td>
				      	</tr>
				   </thead>
				   <tbody class="container ui-sortable" data-rf-row-count="2">
				    	<!-- Template -->
				    	<?php $this->repeater_template(); ?>
				      	<?php if (!empty($this->values)): ?>
				      		<?php foreach ($this->values as $key => $val): ?>
					      		<tr class="row">
							        <td width="10%">
							            <!-- <span class="move btn btn-info">↕</span>
							            <span class="move-up btn btn-info">↑</span>
							            <input type="text" class="move-steps" size="1" value="1" disabled="">
							            <span class="move-down btn btn-info">↓</span> -->
							        </td>
							        <td width="80%">
							            <dl>
							               <dt>Field label</dt>
							               <dd><input type="text" name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][field_label]" value="<?php echo isset($val['field_label']) ? $val['field_label'] : ''; ?>" class="form-control"></dd>
							            </dl>
							            <dl>
							               <dt>Field type</dt>
							               <dd>
							               		<select type="text" name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][field_type]" class="form-control select-type">
							               			<?php foreach ($this->fieldsTypes as $k => $type): echo $type; ?>
							               				<option value="<?php echo $k; ?>" <?php echo (isset($val['field_type']) && $val['field_type'] == $k) ? 'selected="selected"' : ''; ?>><?php echo $type; ?></option>
							               			<?php endforeach; ?>
							               		</select>
							           		</dd>
							            </dl>
							            <dl>
							               <dt>Field id</dt>
							               <dd><input type="text" name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][field_id]" value="<?php echo isset($val['field_id']) ? $val['field_id'] : ''; ?>" class="form-control" data-no-space="true"></dd>
							            </dl>
							            <dl>
							               <dt>Field required ?</dt>
							               <dd><input type="checkbox" name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][field_required]" <?php (isset($val['field_required'])) ? checked( $val['field_required'], 'on', true ) : ''; ?> class="form-control"></dd>
							            </dl>
							            <dl>
							               <dt>Field default value</dt>
							               <dd><input type="text" name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][field_default_value]" value="<?php echo isset($val['field_default_value']) ? $val['field_default_value'] : ''; ?>" class="form-control"></dd>
							            </dl>
							            <dl>
							               <dt>Field placeholder</dt>
							               <dd><input type="text" name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][field_placeholder]" value="<?php echo isset($val['field_placeholder']) ? $val['field_placeholder'] : ''; ?>" class="form-control"></dd>
							            </dl>
							            <dl>
				                           <dt>Description</dt>
				                           <dd><textarea name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][field_description]" class="form-control editor"><?php echo isset($val['field_description']) ? $val['field_description'] : ''; ?></textarea></dd>
				                        </dl>
							            <table class="table table-striped table-bordered nested-group">
							               	<thead>
								                <tr>
								                    <td width="10%" colspan="2"><h4>Add options</h4></td>
								                </tr>
							               	</thead>
							               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
												<tr class="row">
													<!-- <td width="10%">
													    <span class="move btn btn-info">↕</span>
													    <span class="move-up btn btn-info">↑</span>
													    <input type="text" class="move-steps" size="1" value="1" disabled="">
													    <span class="move-down btn btn-info">↓</span>
													</td> -->
													<td width="90%">
														<?php $uniqid = uniqid(); ?>
													    <table class="table table-striped table-bordered nested-group">
											               	<thead>
												                <tr>
												                    <td width="10%" colspan="4"><span class="add btn btn-success">Add</span></td>
												                </tr>
											               	</thead>
											               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
											               		<tr class="template row" style="display: none;">
																	<td width="10%">
																	    <!-- <span class="move btn btn-info">↕</span>
																	    <span class="move-up btn btn-info">↑</span>
																	    <input type="text" class="move-steps" size="1" value="1" disabled="">
																	    <span class="move-down btn btn-info">↓</span> -->
																	</td>
																	<td width="45%">
																	    <dl>
																	       <dt class="option-count">Option</dt>
																	       <dd>
																	       		<input type="text" name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][options][<?php echo $uniqid; ?>][option_key]" class="form-control" disabled="">
																	   		</dd>
																	    </dl>
																	</td>
																	<td width="45%">
																	    <dl>
																	       <dt>Value</dt>
																	       <dd>
																	       		<input type="text" name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][options][<?php echo $uniqid; ?>][option_value]" class="form-control" disabled="">
																	       </dd>
																	    </dl>
																	</td>
																	<td width="10%"><span class="remove btn btn-danger">Remove</span></td>
																</tr>
											               		<?php if (!empty($val['options'])): foreach ($val['options'] as $o => $option): ?>
																	<tr class="row">
																		<td width="10%">
																		    <!-- <span class="move btn btn-info">↕</span>
																		    <span class="move-up btn btn-info">↑</span>
																		    <input type="text" class="move-steps" size="1" value="1" disabled="">
																		    <span class="move-down btn btn-info">↓</span> -->
																		</td>
																		<td width="45%">
																		    <dl>
																		       <dt class="option-count">Option</dt>
																		       <dd>
																		       		<input type="text" name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][options][<?php echo $o; ?>][option_key]" value="<?php echo isset($option['option_key']) ? $option['option_key'] : ''; ?>" class="form-control">
																		   		</dd>
																		    </dl>
																		</td>
																		<td width="45%">
																		    <dl>
																		       <dt>Value</dt>
																		       <dd>
																		       		<input type="text" name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][options][<?php echo $o; ?>][option_value]" value="<?php echo isset($option['option_value']) ? $option['option_value'] : ''; ?>" class="form-control">
																		       </dd>
																		    </dl>
																		</td>
																		<td width="10%"><span class="remove btn btn-danger">Remove</span></td>
																	</tr>
											               		<?php endforeach; endif; ?>
											               	</tbody>
											            </table>
													</td>
													<!-- <td width="10%"><span class="remove btn btn-danger">Remove</span></td> -->
												</tr>
							               	</tbody>
							            </table>
							            <table class="table table-striped table-bordered nested-group">
							               	<thead>
								                <tr>
								                    <td width="10%" colspan="2"><h4>Add attributes</h4></td>
								                </tr>
							               	</thead>
							               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
												<tr class="row">
													<!-- <td width="10%">
													    <span class="move btn btn-info">↕</span>
													    <span class="move-up btn btn-info">↑</span>
													    <input type="text" class="move-steps" size="1" value="1" disabled="">
													    <span class="move-down btn btn-info">↓</span>
													</td> -->
													<td width="90%">
														<?php $uniqid = uniqid(); ?>
													    <table class="table table-striped table-bordered nested-group">
											               	<thead>
												                <tr>
												                    <td width="10%" colspan="4"><span class="add btn btn-success">Add</span></td>
												                </tr>
											               	</thead>
											               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
																<tr class="template row" style="display: none;">
																	<td width="10%">
																	    <!-- <span class="move btn btn-info">↕</span>
																	    <span class="move-up btn btn-info">↑</span>
																	    <input type="text" class="move-steps" size="1" value="1" disabled="">
																	    <span class="move-down btn btn-info">↓</span> -->
																	</td>
																	<td width="45%">
																	    <dl>
																	       <dt>Attribute</dt>
																	       <dd>
																	       		<input type="text" name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][attributes][<?php echo $uniqid; ?>][attribute_key]" class="form-control" disabled="">
																	   		</dd>
																	    </dl>
																	</td>
																	<td width="45%">
																	    <dl>
																	       <dt>Value</dt>
																	       <dd>
																	       		<input type="text" name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][attributes][<?php echo $uniqid; ?>][attribute_value]" class="form-control" disabled="">
																	       	</dd>
																	    </dl>
																	</td>
																	<td width="10%"><span class="remove btn btn-danger">Remove</span></td>
																</tr>
																<?php if (!empty($val['attributes'])) : foreach ($val['attributes'] as $a => $attr) : ?>
																	<tr class="row">
																		<td width="10%">
																		    <!-- <span class="move btn btn-info">↕</span>
																		    <span class="move-up btn btn-info">↑</span>
																		    <input type="text" class="move-steps" size="1" value="1" disabled="">
																		    <span class="move-down btn btn-info">↓</span> -->
																		</td>
																		<td width="45%">
																		    <dl>
																		       <dt>Attribute</dt>
																		       <dd>
																		       		<input type="text" name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][attributes][<?php echo $a; ?>][attribute_key]" value="<?php echo isset($attr['attribute_key']) ? $attr['attribute_key'] : ''; ?>" class="form-control">
																		   		</dd>
																		    </dl>
																		</td>
																		<td width="45%">
																		    <dl>
																		       <dt>Value</dt>
																		       <dd>
																		       		<input type="text" name="<?php echo $field_type->_id(); ?>[<?php echo $key; ?>][attributes][<?php echo $a; ?>][attribute_value]" value="<?php echo isset($attr['attribute_value']) ? $attr['attribute_value'] : ''; ?>" class="form-control">
																		       	</dd>
																		    </dl>
																		</td>
																		<td width="10%"><span class="remove btn btn-danger">Remove</span></td>
																	</tr>
																<?php endforeach; endif; ?>
											               	</tbody>
											            </table>
													</td>
													<!-- <td width="10%"><span class="remove btn btn-danger">Remove</span></td> -->
												</tr>
							               	</tbody>
							            </table>
							            <!-- Groups fields template -->
							            <table class="table table-striped table-bordered nested-group">
							               	<thead>
								                <tr>
								                    <td width="10%" colspan="2"><h4>Add group fields</h4></td>
								                </tr>
							               	</thead>
							               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
												<tr class="row">
													<!-- <td width="10%">
													    <span class="move btn btn-info">↕</span>
													    <span class="move-up btn btn-info">↑</span>
													    <input type="text" class="move-steps" size="1" value="1" disabled="">
													    <span class="move-down btn btn-info">↓</span>
													</td> -->
													<td width="90%">
													    <table class="table table-striped table-bordered nested-group">
											               	<thead>
												                <tr>
												                    <td width="10%" colspan="4"><span class="add btn btn-success">Add</span></td>
												                </tr>
											               	</thead>
											               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
																<tr class="template row" style="display: none;">
															        <td width="10%">
															            <!-- <span class="move btn btn-info">↕</span>
															            <span class="move-up btn btn-info">↑</span>
															            <input type="text" class="move-steps" size="1" value="1" disabled="">
															            <span class="move-down btn btn-info">↓</span> -->
															        </td>
															        <td width="80%">
															            <dl>
															               <dt>Field label</dt>
															               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][field_label]" class="form-control" disabled=""></dd>
															            </dl>
															            <dl>
															               <dt>Field type</dt>
															               <dd>
															               		<select type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][field_type]" class="form-control select-type" disabled="">
															               			<?php foreach ($this->fieldsTypes as $k => $type): ?>
															               				<option value="<?php echo $k; ?>"><?php echo $type; ?></option>
															               			<?php endforeach; ?>
															               		</select>
															           		</dd>
															            </dl>
															            <dl>
															               <dt>Field id</dt>
															               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][field_id]" class="form-control" data-no-space="true" disabled=""></dd>
															            </dl>
															            <dl>
															               <dt>Field required ?</dt>
															               <dd><input type="checkbox" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][field_required]" class="form-control" disabled=""></dd>
															            </dl>
															            <dl>
															               <dt>Field default value</dt>
															               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][field_default_value]" class="form-control" disabled=""></dd>
															            </dl>
															            <dl>
															               <dt>Field placeholder</dt>
															               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][field_placeholder]" class="form-control" disabled=""></dd>
															            </dl>
															            <dl>
												                           <dt>Description</dt>
												                           <dd><textarea name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][field_description]" class="form-control editor" disabled=""></textarea></dd>
												                        </dl>
															            <table class="table table-striped table-bordered nested-group">
															               	<thead>
																                <tr>
																                    <td width="10%" colspan="2"><h4>Add options</h4></td>
																                </tr>
															               	</thead>
															               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
																				<tr class="row">
																					<!-- <td width="10%">
																					    <span class="move btn btn-info">↕</span>
																					    <span class="move-up btn btn-info">↑</span>
																					    <input type="text" class="move-steps" size="1" value="1" disabled="">
																					    <span class="move-down btn btn-info">↓</span>
																					</td> -->
																					<td width="90%">
																					    <table class="table table-striped table-bordered nested-group">
																			               	<thead>
																				                <tr>
																				                    <td width="10%" colspan="4"><span class="add btn btn-success">Add</span></td>
																				                </tr>
																			               	</thead>
																			               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
																								<tr class="template row" style="display: none;">
																									<td width="10%">
																									    <!-- <span class="move btn btn-info">↕</span>
																									    <span class="move-up btn btn-info">↑</span>
																									    <input type="text" class="move-steps" size="1" value="1" disabled="">
																									    <span class="move-down btn btn-info">↓</span> -->
																									</td>
																									<td width="45%">
																									    <dl>
																									       <dt class="option-count">Option</dt>
																									       <dd>
																									       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][options][{{row-count-placeholder}}][option_key]" class="form-control" disabled="">
																									   		</dd>
																									    </dl>
																									</td>
																									<td width="45%">
																									    <dl>
																									       <dt>Value</dt>
																									       <dd>
																									       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][options][{{row-count-placeholder}}][option_value]" class="form-control" disabled="">
																									       </dd>
																									    </dl>
																									</td>
																									<td width="10%"><span class="remove btn btn-danger">Remove</span></td>
																								</tr>
																			               	</tbody>
																			            </table>
																					</td>
																					<!-- <td width="10%"><span class="remove btn btn-danger">Remove</span></td> -->
																				</tr>
															               	</tbody>
															            </table>
															            <table class="table table-striped table-bordered nested-group">
															               	<thead>
																                <tr>
																                    <td width="10%" colspan="2"><h4>Add attributes</h4></td>
																                </tr>
															               	</thead>
															               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
																				<tr class="row">
																					<!-- <td width="10%">
																					    <span class="move btn btn-info">↕</span>
																					    <span class="move-up btn btn-info">↑</span>
																					    <input type="text" class="move-steps" size="1" value="1" disabled="">
																					    <span class="move-down btn btn-info">↓</span>
																					</td> -->
																					<td width="90%">
																					    <table class="table table-striped table-bordered nested-group">
																			               	<thead>
																				                <tr>
																				                    <td width="10%" colspan="4"><span class="add btn btn-success">Add</span></td>
																				                </tr>
																			               	</thead>
																			               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
																								<tr class="template row" style="display: none;">
																									<td width="10%">
																									    <!-- <span class="move btn btn-info">↕</span>
																									    <span class="move-up btn btn-info">↑</span>
																									    <input type="text" class="move-steps" size="1" value="1" disabled="">
																									    <span class="move-down btn btn-info">↓</span> -->
																									</td>
																									<td width="45%">
																									    <dl>
																									       <dt>Attribute</dt>
																									       <dd>
																									       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][attributes][{{row-count-placeholder}}][attribute_key]" class="form-control" disabled="">
																									   		</dd>
																									    </dl>
																									</td>
																									<td width="45%">
																									    <dl>
																									       <dt>Value</dt>
																									       <dd>
																									       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][attributes][{{row-count-placeholder}}][attribute_value]" class="form-control" disabled="">
																									       	</dd>
																									    </dl>
																									</td>
																									<td width="10%"><span class="remove btn btn-danger">Remove</span></td>
																								</tr>
																			               	</tbody>
																			            </table>
																					</td>
																					<!-- <td width="10%"><span class="remove btn btn-danger">Remove</span></td> -->
																				</tr>
															               	</tbody>
															            </table>
															        </td>
															        <td width="10%"><span class="remove btn btn-danger">Remove</span></td>
														      	</tr>
														      	<?php if (!empty($val['groups'])): foreach ($val['groups'] as $g => $group) : ?>
															      	<tr class="row">
																        <td width="10%">
																            <!-- <span class="move btn btn-info">↕</span>
																            <span class="move-up btn btn-info">↑</span>
																            <input type="text" class="move-steps" size="1" value="1">
																            <span class="move-down btn btn-info">↓</span> -->
																        </td>
																        <td width="80%">
																            <dl>
																               <dt>Field label</dt>
																               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[<?php echo $key; ?>][groups][<?php echo $g; ?>][field_label]" value="<?php echo isset($group['field_label']) ? $group['field_label'] : ''; ?>" class="form-control"></dd>
																            </dl>
																            <dl>
																               <dt>Field type</dt>
																               <dd>
																               		<select type="text" name="<?php echo $this->post_group_key; ?>[<?php echo $key; ?>][groups][<?php echo $g; ?>][field_type]" class="form-control select-type">
																               			<?php foreach ($this->fieldsTypes as $k => $type): ?>
																               				<option value="<?php echo $k; ?>" <?php echo (isset($group['field_type']) && $k == $group['field_type']) ? 'selected="selected"' : ''; ?>><?php echo $type; ?></option>
																               			<?php endforeach; ?>
																               		</select>
																           		</dd>
																            </dl>
																            <dl>
																               <dt>Field id</dt>
																               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[<?php echo $key; ?>][groups][<?php echo $g; ?>][field_id]" class="form-control" value="<?php echo isset($group['field_id']) ? $group['field_id'] : ''; ?>" data-no-space="true"></dd>
																            </dl>
																            <dl>
																               <dt>Field required ?</dt>
																               <dd><input type="checkbox" name="<?php echo $this->post_group_key; ?>[<?php echo $key; ?>][groups][<?php echo $g; ?>][field_required]" value="<?php echo isset($group['field_required']) ? $group['field_required'] : ''; ?>" class="form-control"></dd>
																            </dl>
																            <dl>
																               <dt>Field default value</dt>
																               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[<?php echo $key; ?>][groups][<?php echo $g; ?>][field_default_value]" value="<?php echo isset($group['field_default_value']) ? $group['field_default_value'] : ''; ?>" class="form-control"></dd>
																            </dl>
																            <dl>
																               <dt>Field placeholder</dt>
																               <dd><input type="text" name="<?php echo $this->post_group_key; ?>[<?php echo $key; ?>][groups][<?php echo $g; ?>][field_placeholder]" value="<?php echo isset($group['field_placeholder']) ? $group['field_placeholder'] : ''; ?>" class="form-control"></dd>
																            </dl>
																            <dl>
													                           <dt>Description</dt>
													                           <dd><textarea name="<?php echo $this->post_group_key; ?>[<?php echo $key; ?>][groups][<?php echo $g; ?>][field_description]" class="form-control editor"><?php echo isset($group['field_description']) ? $group['field_description'] : ''; ?></textarea></dd>
													                        </dl>
																            <table class="table table-striped table-bordered nested-group">
																               	<thead>
																	                <tr>
																	                    <td width="10%" colspan="2"><h4>Add options</h4></td>
																	                </tr>
																               	</thead>
																               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
																					<tr class="row">
																						<td width="90%">
																						    <table class="table table-striped table-bordered nested-group">
																				               	<thead>
																					                <tr>
																					                    <td width="10%" colspan="4"><span class="add btn btn-success">Add</span></td>
																					                </tr>
																				               	</thead>
																				               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
																									<tr class="template row" style="display: none;">
																										<td width="10%">
																										    <!-- <span class="move btn btn-info">↕</span>
																										    <span class="move-up btn btn-info">↑</span>
																										    <input type="text" class="move-steps" size="1" value="1">
																										    <span class="move-down btn btn-info">↓</span> -->
																										</td>
																										<td width="45%">
																										    <dl>
																										       <dt class="option-count">Option</dt>
																										       <dd>
																										       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][options][{{row-count-placeholder}}][option_key]" class="form-control">
																										   		</dd>
																										    </dl>
																										</td>
																										<td width="45%">
																										    <dl>
																										       <dt>Value</dt>
																										       <dd>
																										       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][options][{{row-count-placeholder}}][option_value]" class="form-control">
																										       </dd>
																										    </dl>
																										</td>
																										<td width="10%"><span class="remove btn btn-danger">Remove</span></td>
																									</tr>
																									<?php if (!empty($group['options'])) : foreach ($group['options'] as $go => $gv) : ?>
																										<tr class="row">
																											<td width="10%">
																											    <!-- <span class="move btn btn-info">↕</span>
																											    <span class="move-up btn btn-info">↑</span>
																											    <input type="text" class="move-steps" size="1" value="1">
																											    <span class="move-down btn btn-info">↓</span> -->
																											</td>
																											<td width="45%">
																											    <dl>
																											       <dt class="option-count">Option</dt>
																											       <dd>
																											       		<input type="text" name="<?php echo $this->post_group_key; ?>[<?php echo $key; ?>][groups][<?php echo $g; ?>][options][<?php echo $go ?>][option_key]" value="<?php echo isset($gv['option_key']) ? $gv['option_key'] : ''; ?>" class="form-control">
																											   		</dd>
																											    </dl>
																											</td>
																											<td width="45%">
																											    <dl>
																											       <dt>Value</dt>
																											       <dd>
																											       		<input type="text" name="<?php echo $this->post_group_key; ?>[<?php echo $key; ?>][groups][<?php echo $g; ?>][options][<?php echo $go ?>][option_value]" value="<?php echo isset($gv['option_value']) ? $gv['option_value'] : ''; ?>" class="form-control">
																											       </dd>
																											    </dl>
																											</td>
																											<td width="10%"><span class="remove btn btn-danger">Remove</span></td>
																										</tr>
																									<?php endforeach; endif; ?>
																				               	</tbody>
																				            </table>
																						</td>
																					</tr>
																               	</tbody>
																            </table>
																            <table class="table table-striped table-bordered nested-group">
																               	<thead>
																	                <tr>
																	                    <td width="10%" colspan="2"><h4>Add attributes</h4></td>
																	                </tr>
																               	</thead>
																               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
																					<tr class="row">
																						<!-- <td width="10%">
																						    <span class="move btn btn-info">↕</span>
																						    <span class="move-up btn btn-info">↑</span>
																						    <input type="text" class="move-steps" size="1" value="1">
																						    <span class="move-down btn btn-info">↓</span>
																						</td> -->
																						<td width="90%">
																						    <table class="table table-striped table-bordered nested-group">
																				               	<thead>
																					                <tr>
																					                    <td width="10%" colspan="4"><span class="add btn btn-success">Add</span></td>
																					                </tr>
																				               	</thead>
																				               	<tbody class="container ui-sortable" style="" data-rf-row-count="0">
																									<tr class="template row" style="display: none;">
																										<td width="10%">
																										    <!-- <span class="move btn btn-info">↕</span>
																										    <span class="move-up btn btn-info">↑</span>
																										    <input type="text" class="move-steps" size="1" value="1">
																										    <span class="move-down btn btn-info">↓</span> -->
																										</td>
																										<td width="45%">
																										    <dl>
																										       <dt>Attribute</dt>
																										       <dd>
																										       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][attributes][{{row-count-placeholder}}][attribute_key]" class="form-control">
																										   		</dd>
																										    </dl>
																										</td>
																										<td width="45%">
																										    <dl>
																										       <dt>Value</dt>
																										       <dd>
																										       		<input type="text" name="<?php echo $this->post_group_key; ?>[{{row-count-placeholder}}][groups][{{row-count-placeholder}}][attributes][{{row-count-placeholder}}][attribute_value]" class="form-control">
																										       	</dd>
																										    </dl>
																										</td>
																										<td width="10%"><span class="remove btn btn-danger">Remove</span></td>
																									</tr>
																									<?php if (!empty($group['attributes'])) : foreach ($group['attributes'] as $ga => $gv) : ?>
																										<tr class="row">
																											<td width="10%">
																											    <!-- <span class="move btn btn-info">↕</span>
																											    <span class="move-up btn btn-info">↑</span>
																											    <input type="text" class="move-steps" size="1" value="1">
																											    <span class="move-down btn btn-info">↓</span> -->
																											</td>
																											<td width="45%">
																											    <dl>
																											       <dt>Option</dt>
																											       <dd>
																											       		<input type="text" name="<?php echo $this->post_group_key; ?>[<?php echo $key; ?>][groups][<?php echo $g; ?>][attributes][<?php echo $ga; ?>][attribute_key]" value="<?php echo isset($gv['attribute_key']) ? $gv['attribute_key'] : ''; ?>" class="form-control">
																											   		</dd>
																											    </dl>
																											</td>
																											<td width="45%">
																											    <dl>
																											       <dt>Value</dt>
																											       <dd>
																											       		<input type="text" name="<?php echo $this->post_group_key; ?>[<?php echo $key; ?>][groups][<?php echo $g; ?>][attributes][<?php echo $ga; ?>][attribute_value]" value="<?php echo isset($gv['attribute_value']) ? $gv['attribute_value'] : ''; ?>" class="form-control">
																											       </dd>
																											    </dl>
																											</td>
																											<td width="10%"><span class="remove btn btn-danger">Remove</span></td>
																										</tr>
																									<?php endforeach; endif; ?>
																				               	</tbody>
																				            </table>
																						</td>
																						<!-- <td width="10%"><span class="remove btn btn-danger">Remove</span></td> -->
																					</tr>
																               	</tbody>
																            </table>
																        </td>
																        <td width="10%"><span class="remove btn btn-danger">Remove</span></td>
															      	</tr>
														      	<?php endforeach; endif; ?>
											               	</tbody>
											            </table>
													</td>
													<!-- <td width="10%"><span class="remove btn btn-danger">Remove</span></td> -->
												</tr>
							               	</tbody>
							            </table>
							        </td>
							        <td width="10%"><span class="remove btn btn-danger">Remove</span></td>
					    		</tr>
				      		<?php endforeach; ?>
				      	<?php endif; ?>
				   </tbody>
				</table>
			</div>
			<?php
		}
	}	
	new RepeatableGroups();
?>