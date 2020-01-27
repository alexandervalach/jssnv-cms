var textEditor = CKEDITOR.instances['text-editor'];
if (textEditor) { textEditor.destroy(true); }
CKEDITOR.replace('text-editor');