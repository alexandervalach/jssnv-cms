const selector = '#editor'
const editorExists = document.querySelector( selector )
let editor

if (editorExists) {
  ClassicEditor
  .create( document.querySelector( selector ) )
  .catch( error => {
    console.error( error );
  });
}