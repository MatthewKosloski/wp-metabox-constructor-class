<?php

require_once("metabox_constructor_class.php");

$metabox = new Metabox_Constructor(array(
	'id' => 'metabox_id',
	'title' => __('Metabox Title', 'experiment_functionality'),
	'post_type' => 'post',
	'context' => 'advanced',
	'priority' => 'default'
));

$metabox->addText(array(
	'id' => 'metabox_text_field',
	'label' => 'Text Field'
));

$metabox->addTextArea(array(
	'id' => 'metabox_textarea_field',
	'desc' => 'Some description text for a textarea.', 
	'label' => 'Textarea Field'
));

$metabox->addCheckbox(array(
	'id' => 'metabox_checkbox_field',
	'desc' => 'Foobarbax',
	'label' => 'Checkbox Field'
));

$metabox->addImage(array(
	'id' => 'metabox_image_field2',
	'desc' => 'Upload an image by clicking the button',
	'label' => 'Image Upload'
));

$metabox_repeater_block15_fields[] = $metabox->addText(array(
	'id' => 'metabox_repeater_text_field',
	'label' => 'Text Field here'
), true);

$metabox_repeater_block15_fields[] = $metabox->addTextArea(array(
	'id' => 'metabox_repeater_textarea_field',
	'label' => 'Text Area here'
), true);

$metabox->addRepeaterBlock(array(
	'id' => 'metabox_repeater_block15',
	'desc' => 'This is a description for the repeater block.',
	'fields' => $metabox_repeater_block15_fields,
	'label' => 'Repeater Block'
));

$metabox_repeater_block20_fields[] = $metabox->addImage(array(
	'id' => 'metabox_repeater_block20_image',
	'desc' => 'Upload an image by clicking the button',
	'label' => 'Image Upload'
), true);

$metabox_repeater_block20_fields[] = $metabox->addCheckbox(array(
	'id' => 'metabox_repeater_block20_checkboz',
	'desc' => 'Foobarbax',
	'label' => 'Checkbox Field'
), true);

$metabox->addRepeaterBlock(array(
	'id' => 'metabox_repeater_block20',
	'desc' => 'Repeater block with photos.',
	'fields' => $metabox_repeater_block20_fields,
	'label' => 'Photo Repeater'
));

$metabox_repeater_block15_fields[] = $metabox->addWysiwyg(array(
	'id' => 'metabox_wysiwyg_field',
	'label' => 'Example Wysiwyg Editor'
));

?>