/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	config.autoParagraph = false;
	config.width = '620';
	config.height = '250';

	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	//config.toolbar = 'Full';
 	config.toolbar = 'TestsToolbar';
	
	config.toolbar_Full =
	[
		/*{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
		{ name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 
	 
			 'HiddenField' ] },
		'/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-
	 
		','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
		{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
		'/',
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		{ name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
		{ name: 'document', items : [ 'Save','NewPage' ] },
*/
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline'] },	
		{ name: 'justify', items : [ 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },																	
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },									
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'bullet', items : [ 'NumberedList','BulletedList','-','Outdent','Indent' ] },
		{ name: 'link', items : [ 'Link', 'Unlink','Anchor' ] },	
		{ name: 'insert', items : [ 'Image',/*'Flash',*/'Table' /*,'HorizontalRule'*/,'Source' ] },								
		{ name: 'colors', items : [ 'TextColor','BGColor' ] }
	];
	 
	config.toolbar_Basic =
	[
		['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-','About']
	];
	
	
	config.toolbar_TestsToolbar =
	[
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline'] },	
		{ name: 'justify', items : [ 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },																	
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },									
		{ name: 'document', items : [ 'Save','NewPage' ] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'bullet', items : [ 'NumberedList','BulletedList','-','Outdent','Indent' ] },
		{ name: 'link', items : [ 'Link', 'Unlink','Anchor' ] },	
		{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Source' ] },								
		{ name: 'colors', items : [ 'TextColor','BGColor' ] }
	];
	
	
};
