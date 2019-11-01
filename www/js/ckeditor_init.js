const selector = '#editor'
const editorExists = document.querySelector( selector )
let editor = null

console.log(editorExists)

if (editorExists) {
  ClassicEditor
  .create( document.querySelector( selector ) )
  .catch( error => {
    console.error( error );
  });
}

/*

 */