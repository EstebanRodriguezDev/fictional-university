class MyNotes {
 constructor() {
  this.notaEliminada = document.querySelectorAll(".delete-note");
  this.events();
 }

 events() {
  this.notaEliminada.forEach(nota => {
   nota.addEventListener("click", this.deleteNote);
  });
 }

 // Metodos aqui
 deleteNote() {
  const url = `${universityData.root_url}/wp-json/wp/v2/note/111`;

  fetch(url, {
   headers: {
    'X-WP-Nonce': `${universityData.nonce}`
   },
   method: 'DELETE',
  })
   .then(response => {
    if (response.ok) {
     console.log('success');
     console.log(response)
    } else {
     console.log('Fail');
     console.log(response);
    }
   })
   .catch(error => console.log(error))
 }
}

export default MyNotes;