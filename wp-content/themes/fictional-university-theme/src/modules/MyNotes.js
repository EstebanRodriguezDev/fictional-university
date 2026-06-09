import $ from 'jquery';
class MyNotes {
  constructor() {
    this.eliminarNota = document.querySelectorAll(".delete-note");
    this.editarNota = document.querySelectorAll(".edit-note");
    this.events();
  }

  events() {
    this.eliminarNota.forEach(nota => {
      nota.addEventListener("click", this.deleteNote);
    });
    this.editarNota.forEach((nota) => {
      nota.addEventListener("click", this.editNote)
    })
  }

  // Metodos aqui
  deleteNote(e) {
    const liElement = e.target.parentElement;
    const thisNote = liElement.getAttribute('data-id');
    const url = `${universityData.root_url}/wp-json/wp/v2/note/${thisNote}`;

    fetch(url, {
      headers: {
        'X-WP-Nonce': `${universityData.nonce}`
      },
      method: 'DELETE',
    })
      .then(response => {
        if (response.ok) {
          $(liElement).slideUp();
        }
      })
      .catch(error => console.log(error.message))
  }
  editNote(e) {
    const liElement = e.target.parentElement;

    const titleField = liElement.querySelector(".note-title-field");
    const bodyField = liElement.querySelector(".note-body-field");
    const noteUpdate = liElement.querySelector(".update-note");
    noteUpdate.classList.add("update-note--visible");
    console.log(noteUpdate)

    titleField.removeAttribute("readonly");
    titleField.classList.add("note-active-field");

    bodyField.removeAttribute("readonly");
    bodyField.classList.add('note-active-field');


  }
}

export default MyNotes;