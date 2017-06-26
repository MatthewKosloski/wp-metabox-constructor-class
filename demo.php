<?php

require_once("metabox_constructor_class.php");

$metabox = new Metabox_Constructor(array(
	'id' => 'metabox_id',
	'title' => __('Metabox Demo', 'experiment_functionality'),
	'post_type' => 'post'
));

$metabox_repeater_block15_fields[] = $metabox->addText(array(
	'id' => 'metabox_repeater_text_field',
	'label' => 'Product Title'
), true);

$metabox_repeater_block15_fields[] = $metabox->addTextArea(array(
	'id' => 'metabox_repeater_textarea_field',
	'label' => 'Product Description'
), true);

$metabox->addRepeaterBlock(array(
	'id' => 'metabox_repeater_block15',
	'desc' => 'Repeater blocks can be used to store an array of content with a dynamic length (e.g, Products).',
	'fields' => $metabox_repeater_block15_fields,
	'label' => 'Repeater Block Field',
	'single_label' => 'Product'
));

$metabox_repeater_block15_fields[] = $metabox->addWysiwyg(array(
	'id' => 'metabox_wysiwyg_field',
	'label' => 'WYSIWYG Field',
	'desc' => 'You can use a WYSIWYG editor to facilitate the management of HTML content.'
));

$metabox->addCheckbox(array(
	'id' => 'metabox_checkbox_field',
	'desc' => 'Checkboxes are a great way to facilitate conditional logic.',
	'label' => 'Checkbox Field'
));

$metabox->addImage(array(
	'id' => 'metabox_image_field2',
	'desc' => 'Upload an image, or change it, by clicking the button below the preview.',
	'label' => 'Image Upload Field'
));

$metabox->addText(array(
	'id' => 'metabox_text_field',
	'label' => 'Text Field'
));

$metabox->addTextArea(array(
	'id' => 'metabox_textarea_field',
	'label' => 'Textarea Field'
));

?>