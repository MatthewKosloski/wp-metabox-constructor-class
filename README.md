# wp-metabox-constructor-class

This is a PHP class that allows WordPress plugin developers to easily create custom Metaboxes.  

Adapted from [My-Meta-Box](https://github.com/bainternet/My-Meta-Box).

- [Installation](https://github.com/MatthewKosloski/wp-metabox-constructor-class#installation)
- [Usage](https://github.com/MatthewKosloski/wp-metabox-constructor-class#usage)
- [Available Fields](https://github.com/MatthewKosloski/wp-metabox-constructor-class#available-fields)

![Demo photo](https://i.imgur.com/rm5dpsT.jpg)

## Installation

- Download this repo as a zip file and extract its contents.
- Move the folder inside your plugin folder.
- Require the demo file `require_once("wp-metabox-constructor-class/demo.php");`

## Usage

To create a metabox, first instantiate an instance of `Metabox_Constructor`.  The class takes one argument, which is an associative array.  The keys to the array are similar to the arguments provided to the [add_meta_box](https://developer.wordpress.org/reference/functions/add_meta_box/) WordPress function; however, you don't provide `callback` or `callback_args`.

```php
$metabox = new Metabox_Constructor(array(
	'id' => 'metabox_id', // required
	'title' => 'My awesome metabox', // required
	'screen' => 'post', // required
	'context' => 'advanced',
	'priority' => 'default'
));
```

## Available Fields

After instantiating the above metabox, add a few fields to it.  Below is a list of the available fields.  Click on one to see how to implement it.

- [Text](https://github.com/MatthewKosloski/wp-metabox-constructor-class#text)
- [Textarea](https://github.com/MatthewKosloski/wp-metabox-constructor-class#textarea)
- [Checkbox](https://github.com/MatthewKosloski/wp-metabox-constructor-class#checkbox)
- [Radio](https://github.com/MatthewKosloski/wp-metabox-constructor-class#radio)
- [Image Upload](https://github.com/MatthewKosloski/wp-metabox-constructor-class#image-upload)
- [WYSIWYG Editor](https://github.com/MatthewKosloski/wp-metabox-constructor-class#wysiwyg-editor)
- [Repeater](https://github.com/MatthewKosloski/wp-metabox-constructor-class#repeater)

### Text

A simple text input.  Nothing special.

```php
$metabox->addText(array(
	'id' => 'metabox_text_field', // required
	'label' => 'Text', // required
	'desc' => 'An example description paragraph that appears below the label.'
));
```

### Textarea

Textareas are used to store a body of text.  For a richer experience with HTML, see the [WYSIWYG](https://github.com/MatthewKosloski/wp-metabox-constructor-class#wysiwyg-editor) editor.

```php
$metabox->addTextArea(array(
	'id' => 'metabox_textarea_field', // required
	'label' => 'Textarea', // required
	'desc' => 'An example description paragraph that appears below the label.'
));
```

### Checkbox

Checkboxes are a great way to facilitate conditional logic.

```php
$metabox->addCheckbox(array(
	'id' => 'metabox_checkbox_field', // required
	'label' => 'Checkbox', // required
	'desc' => 'An example description paragraph that appears below the label.'
));
```

### Radio

Radio fields are a great way to choose from a selection of options.

```php
$metabox->addRadio(
	array(
		'id' => 'metabox_radio_field', // required
		'label' => 'Radio', // required
		'desc' => 'An example description paragraph that appears below the label.',
	),
	array( // required
		'key1' => 'Value One',
		'key2' => 'Value Two'
	)
);
```

### Image Upload

Use this to permit users to upload an image within the metabox.  Pro tip: use this with the [repeater](https://github.com/MatthewKosloski/wp-metabox-constructor-class#repeater) to dynamically manage the photos within a gallery or slideshow.

```php
$metabox->addImage(array(
	'id' => 'metabox_image_field', // required
	'label' => 'Image Upload', // required
	'desc' => 'An example description paragraph that appears below the label.'
));
```

### WYSIWYG Editor

You can use a WYSIWYG editor to facilitate the management of HTML content.

```php
$metabox->addWysiwyg(array(
	'id' => 'metabox_wysiwyg_field', // required
	'label' => 'WYSIWYG', // required
	'desc' => 'An example description paragraph that appears below the label.'
));
```

### Repeater

All of the above fields can be added to a repeater to store an array of content with a dynamic length.  Here is an example of a repeater block with three fields: text, textarea, and image upload.

Notice:  `true` is a second argument to the repeater fields.  This is required.  Also, the variable, `$metabox_repeater_block_fields[]`, which stores the repeater block's fields, has a pair of brackets `[]` at the end of the variable name.  This is required. 

```php
$metabox_repeater_block_fields[] = $metabox->addText(array(
	'id' => 'metabox_repeater_text_field',
	'label' => 'Photo Title'
), true);

$metabox_repeater_block_fields[] = $metabox->addTextArea(array(
	'id' => 'metabox_repeater_textarea_field',
	'label' => 'Photo Description'
), true);

$metabox_repeater_block_fields[] = $metabox->addImage(array(
	'id' => 'metabox_repeater_image_field',
	'label' => 'Upload Photo'
));

$metabox->addRepeaterBlock(array(
	'id' => 'metabox_repeater_block', // required
	'label' => 'Photo Gallery', // required
	'fields' => $metabox_repeater_block_fields, // required
	'desc' => 'Photos in a photo gallery.',
	'single_label' => 'Photo'
));
```