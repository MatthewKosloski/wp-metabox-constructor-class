<?php

	if(!class_exists('Metabox_Constructor')) :

		class Metabox_Constructor {

			const BLOCK_NAMESPACE = 'mcc-box'; // (A.K.A "Metabox Constructor Class")
			const REPEATER_INDEX_PLACEHOLDER = 'CurrentCounter';

			/**
			* Stores the metabox config that
			* is supplied to the constructor.
			*
			* @var array
			*/
			private $_meta_box;

			private $_folder_name;

			private $_path;

			private $_nonce_name;

			/**
			* Stores the fields supplied to the
			* metabox.
			* @var array
			*/
			private $_fields;

			/**
			* Class constructor.
			* @param array Should be an associative array with the following keys:
			* 'title', 'post_type', 'context', 'priority'
			* @return void
			*/
			public function __construct($meta_box_config) {
				$this->_meta_box = $meta_box_config;
				$this->_nonce_name = $meta_box_config['id'] . '_nonce';
				$this->_folder_name = 'wp-metabox-constructor-class';
				$this->_path = plugins_url($this->_folder_name, plugin_basename(dirname( __FILE__ )));

				add_action('add_meta_boxes', array($this, 'add'));
				add_action('save_post', array($this, 'save'));
				add_action('admin_enqueue_scripts', array($this, 'scripts'));
			}

			public function scripts() {
				global $typenow;

				wp_enqueue_media();

			    if($typenow == $this->_meta_box['post_type']) {
			        wp_enqueue_style('mcc-styles', $this->_path . '/metabox.css', array(), null);
			        wp_enqueue_script('mcc-scripts', $this->_path . '/metabox.js', array('jquery'), null);
			    }
			}

			public function add() {
				add_meta_box(
					$this->_meta_box['id'],
					$this->_meta_box['title'],
					array($this, 'show'), // callback
					$this->_meta_box['post_type'], // screen
					$this->_meta_box['context'],
					$this->_meta_box['priority']
				);
			}

			/**
			* An aggregate function that shows tye contents of the metabox
			* by calling the appropriate, individual function for each
			* field type.
			*
			* @return void
			*/
			public function show() {
				global $post;

				wp_nonce_field(basename(__FILE__), $this->_nonce_name);
				echo sprintf('<div class="%s">', self::BLOCK_NAMESPACE);
				foreach($this->_fields as $field) {
					$meta = get_post_meta($post->ID, $field['id'], true);
					call_user_func( array($this, 'show_field_' . $field['type']), $field, $meta);
				}
				echo '</div>';
			}

			/**
			* Saves the data supplied to the metabox.
			* @return void
			*/
			public function save() {
				global $post;

				if(
			        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || // prevent the data from being auto-saved
			        (!current_user_can('edit_post', $post->ID)) || // check user permissions
			        ((!isset($_POST[$this->_nonce_name]))) || // verify nonce (same with below)
			        (!wp_verify_nonce($_POST[$this->_nonce_name], basename(__FILE__)))
			    ) 
			    {
			        return;
			    }

			    foreach($this->_fields as $field) {
			    	if(isset($_POST[$field['id']])) {
			    		if($field['type'] == 'text' || $field['type'] == 'textarea') {
			    			update_post_meta($post->ID, $field['id'], sanitize_text_field($_POST[$field['id']]));
			    		} else {
			    			update_post_meta($post->ID, $field['id'], $_POST[$field['id']]);
			    		}
				    } else {
				    	delete_post_meta($post->ID, $field['id']);
				    }
			    }
			}

			/**
			* Returns a formatted string for a class name of a field element
			* or non-field element.
			*
			* @param string $element
			* @param boolean $isField
			* @return string
			*/
			public function get_block_element_class($element, $isField = true) {
				if(isset($element)) {
					return trim(sprintf(
						'%s %s%s',  
						($isField 
							? (sprintf('%s__%s', self::BLOCK_NAMESPACE, 'field')) 
							: ''
						),
						sprintf('%s__%s', self::BLOCK_NAMESPACE, ($isField ? 'field-' : '')),
						$element
					));
				}
			}

			/**
			* Echos some HTML that preceeds a field (container, label, description, etc.)
			*
			* @param array $field
			* @param string | null $meta
			*/
			public function before_field($field, $meta = null) {
				echo sprintf(
					'<div class="%s %s">',
					esc_attr( $this->get_block_element_class('field-container', false) ),
					esc_attr( $this->get_block_element_class($field['type'].'-container', false) )
				);

				if(isset($field['label'])) {
					echo sprintf(
						'<label class="%s" for="%s">%s</label>',
						esc_attr( $this->get_block_element_class('label', false) ),
						esc_attr( $field['id'] ),
						esc_html( $field['label'] )
					);
				}
				
				if(isset($field['desc']) && $field['type'] != 'checkbox') $this->get_field_description($field['desc']);
				if($field['type'] == 'image') $this->get_image_preview($field, $meta);
			}

			/**
			* Echos HTML that comes after a field (container, description, etc).
			*
			* @param array | null $field
			*/
			public function after_field($field = null) {
				if(isset($field['desc']) && $field['type'] == 'checkbox') $this->get_field_description($field['desc']);
				echo '</div>';
			}

			/**
			* Echos a paragraph element with some description text that
			* serves as an assistant to the operator of the metabox.
			* 
			* @param string $desc
			*/
			public function get_field_description($desc) {
				echo sprintf(
					'<p class="%s">%s</p>',
					esc_attr( $this->get_block_element_class('description', false) ),
					esc_html( $desc )
				);	
			}

			/**
			* Echos an image tag that serves as preview.
			* 
			* @param array $field
			* @param string $meta
			*/
			public function get_image_preview($field, $meta) {
				global $post;

				echo sprintf(
					'<img id="%s" class="%s" src="%s" alt="%s">',
					esc_attr( sprintf('js-%s-image-preview', $field['id']) ),
					esc_attr( sprintf('%s %s', $this->get_block_element_class('image-preview', false), empty($meta) ? 'is-hidden' : '') ),
					esc_attr( $meta ),
					esc_attr( '' )	
				);
			}

			public function addText($args, $repeater = false) {
				$field = array_merge(array('type' => 'text'), $args);
				if(false == $repeater) {
					$this->_fields[] = $field;
				} else {
					return $field;
				}			
			}

			public function addTextArea($args, $repeater = false) {
				$field = array_merge(array('type' => 'textarea'), $args);
				if(!$repeater) {
					$this->_fields[] = $field;
				} else {
					return $field;
				}
			}

			public function addCheckbox($args, $repeater = false) {
				$field = array_merge(array('type' => 'checkbox'), $args);
				if(!$repeater) {
					$this->_fields[] = $field;
				} else {
					return $field;
				}
			}

			public function addImage($args, $repeater = false) {
				$field = array_merge(array('type' => 'image'), $args);
				if(!$repeater) {
					$this->_fields[] = $field;
				} else {
					return $field;
				}
			}

			public function addWysiwyg($args, $repeater = false) {
				$field = array_merge(array('type' => 'wysiwyg'), $args);
				if(!$repeater) {
					$this->_fields[] = $field;
				} else {
					return $field;
				}
			}

			public function addRepeaterBlock($args) {
				$field = array_merge(array('type' => 'repeater'), $args);
				$this->_fields[] = $field;
			}

			public function show_field_text($field, $meta) {				
				$this->before_field($field);
				echo sprintf(
					'<input type="text" class="%1$s" id="%2$s" name="%2$s" value="%3$s">',
					esc_attr( $this->get_block_element_class($field['type']) ),
					esc_attr( $field['id'] ),
					esc_attr( $meta )
				);
				$this->after_field();
			}

			public function show_field_textarea($field, $meta) {
				$this->before_field($field);
				echo sprintf(
					'<textarea class="%1$s" id="%2$s" name="%2$s">%3$s</textarea>',
					esc_attr( $this->get_block_element_class($field['type']) ),
					esc_attr( $field['id'] ),
					esc_html( $meta )
				);
				$this->after_field();
			}	

			public function show_field_checkbox($field, $meta) {
				$this->before_field($field);
				echo sprintf(
					'<input type="checkbox" class="%1$s" id="%2$s" name="%2$s" %3$s>',
					esc_attr( $this->get_block_element_class($field['type']) ),
					esc_attr( $field['id'] ),
					checked(!empty($meta), true, false)
				);
				$this->after_field($field); // pass in $field to render desc below input
			}

			public function show_field_image($field, $meta) {
				$this->before_field($field, $meta); // pass in $meta for preview image
				echo sprintf(
					'<input type="hidden" id="%s" name="%s" value="%s">',
					esc_attr( 'image-' . $field['id'] ),
					esc_attr( $field['id'] ),
					(isset($meta) ? $meta : '')
				);
				echo sprintf(
					'<a class="%s button" data-hidden-input="%s">%s</a>',
					esc_attr( sprintf('js-%s-image-upload-button', self::BLOCK_NAMESPACE) ),
					esc_attr( $field['id'] ),
					esc_html( sprintf('%s Image', empty($meta) ? 'Upload' : 'Change') )
				);
				$this->after_field();
			}

			public function show_field_wysiwyg($field, $meta) {
				$this->before_field($field);
				wp_editor($meta, $field['id']);
				$this->after_field();
			}

			public function show_field_repeater($field, $meta) {
				$this->before_field($field);

				echo sprintf(
					'<div id="%s" class="%s">',
					esc_attr( sprintf('js-%s-repeated-blocks', $field['id']) ),
					esc_attr( $this->get_block_element_class('repeated-blocks', false) )
				);

				$count = 0;
				if(count($meta) > 0 && is_array($meta)) {
					foreach($meta as $m) {
						$this->get_repeated_block($field, $m, $count);
						$count++;
					}
				} else {
					$this->get_repeated_block($field, '', $count);
				}

				echo '</div>';

				// "add" button
				echo sprintf(
					'<a id="%s" class="%s button">
						<span class="dashicons dashicons-plus"></span>
						Add Item
					</a>',
					esc_attr( sprintf('js-%s-add', $field['id']) ),
					esc_attr( $this->get_block_element_class('add', false)  )
				);

				$this->after_field();

				// create a repeater block to use for the "add" functionality
				ob_start();

				sprintf('<div>%s</div>', esc_html( $this->get_repeated_block($field, $meta, null, true) ));

			    $js_code = ob_get_clean();
			    $js_code = str_replace("\n", "", $js_code);
			    $js_code = str_replace("\r", "", $js_code);
			    $js_code = str_replace("'", "\"", $js_code);

			    /**
			    * JS to add, remove, and sort the repeatable blocks
			    */
				echo '<script> 
						jQuery(document).ready(function($) {
							var count = '.$count.';

							$("#js-'. $field['id'] .'-add").on("click", function() {
								var repeater = \''.$js_code.'\'.replace(/'. self::REPEATER_INDEX_PLACEHOLDER .'/g, count);
								$("#js-'. $field['id'] .'-repeated-blocks").append(repeater);
								count++;
								return false;
							});
						});
				</script>';
			}

			public function get_repeated_block($field, $meta, $index, $isTemplate = false) {
				echo sprintf(
					'<div class="%s">',
					esc_attr( $this->get_block_element_class('repeated', false) )	
				);

				foreach($field['fields'] as $child_field) {
					$old_id = $child_field['id'];

					$child_field['id'] = sprintf(
						'%s[%s][%s]', 
						$field['id'], 
						($isTemplate ? self::REPEATER_INDEX_PLACEHOLDER : $index), 
						$child_field['id']
					);

					$child_meta = isset($meta[$old_id]) && !$isTemplate ? $meta[$old_id] : '';

					call_user_func( array($this, 'show_field_' . $child_field['type']), $child_field, $child_meta );
				}

				// "remove" button
				echo sprintf(
					'<a class="%s %s button" title="Remove Item">
						<span class="dashicons dashicons-no"></span>
					</a>',
					esc_attr( $this->get_block_element_class('repeater-button', false)  ),
					esc_attr( $this->get_block_element_class('remove', false)  )
				);

				// "sort" button
				echo sprintf(
					'<a class="button %s %s" title="Click and drag to sort">
						<span class="dashicons dashicons-menu"></span></span>
					</a>',
					esc_attr( $this->get_block_element_class('repeater-button', false)  ),
					esc_attr( sprintf('js-%s-sort', self::BLOCK_NAMESPACE) )
				);
				
				echo '</div>';
			}

 		}

	endif;

?>