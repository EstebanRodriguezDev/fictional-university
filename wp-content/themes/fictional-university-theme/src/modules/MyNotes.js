import $ from 'jquery'; // Importa jQuery para animaciones como .slideUp()

class MyNotes {
  // Selecciona los botones del DOM e inicializa los eventos
  constructor() {
    this.listaNotas = document.querySelector('#my-notes');
    this.enviarNota = document.querySelector(".submit-note");
    this.events();
  }

  // Asigna eventos click a cada grupo de botones.
  // .bind(this) mantiene el contexto de la clase para poder usar this.makeNoteEditable(), etc.
  events() {
    this.enviarNota.addEventListener("click", this.createNote.bind(this));
    this.listaNotas.addEventListener("click", (e) => {
      console.log(e.target);
      if (e.target.closest('.delete-note')) {
        this.deleteNote(e);
      }
      if (e.target.closest('.edit-note')) {
        this.editNote(e);
      }
      if (e.target.closest('.update-note')) {
        this.updateNote(e);
      }
    });

  }
  createNote(e) {
    const noteTitle = document.querySelector('.new-note-title');
    const noteBody = document.querySelector('.new-note-body');
    const url = `${universityData.root_url}/wp-json/wp/v2/note/`;

    // Captura los valores actuales de los campos de título y contenido
    const ourNewPost = {
      'title': noteTitle.value,
      'content': noteBody.value,
      'status': 'publish',
    }
    fetch(url, {
      headers: {
        'Content-Type': 'application/json', // Indica al servidor que el body viene en formato JSON
        'X-WP-Nonce': `${universityData.nonce}`
      },
      method: 'POST',
      body: JSON.stringify(ourNewPost), // Convierte el objeto JS a string JSON (fetch requiere body)
    })
      .then(respuesta => respuesta.json())
      .then((resultado) => {
        console.log(resultado);
        if (resultado) {
          noteTitle.value = '';
          noteBody.value = '';
          const ulNotes = document.querySelector('#my-notes');

          const htmlString = `
          <li data-id="${resultado.id}">
          <input readonly class="note-title-field" value="${resultado.title.raw}">
          <span class="edit-note">
            <i class="fa fa-pencil" aria-hidden="true"></i>Edit</span>
          <span class="delete-note">
            <i class="fa fa-trash-o" aria-hidden="true"></i>Delete
          </span>
          <textarea readonly class="note-body-field">${resultado.content.raw}</textarea>
          <span class="update-note btn btn--blue btn--small">
            <i class="fa fa-arrow-right" aria-hidden="true"></i>Save</span>
          </li>
          
          `;
          ulNotes.insertAdjacentHTML('afterbegin', htmlString);

          // Inmediatamente le aplicas una animación nativa para simular el slideDown
          ulNotes.firstElementChild.animate([
            { height: '0px', opacity: 0, overflow: 'hidden' }, // Estado inicial (oculto)
            { height: ulNotes.firstElementChild.scrollHeight + 'px', opacity: 1 } // Estado final (tamaño real)
          ], {
            duration: 400, // Duración en milisegundos (equivalente a slideDown normal)
            easing: 'ease-out'
          });
        }
      })
      .catch(error => console.log(error.message))
  }
  // Elimina una nota del servidor y la oculta del DOM con animación
  deleteNote(e) {
    // closest("li") sube por el DOM hasta encontrar el <li> contenedor, sin importar si se clickeó el icono o el texto
    const liElement = e.target.closest("li");
    const thisNote = liElement.getAttribute('data-id'); // ID de la nota en WordPress
    const url = `${universityData.root_url}/wp-json/wp/v2/note/${thisNote}`;

    fetch(url, {
      headers: {
        'X-WP-Nonce': `${universityData.nonce}` // Token de seguridad para autenticar la petición
      },
      method: 'DELETE',
    })
      .then(response => {
        if (response.ok) {
          $(liElement).slideUp(); // Animación jQuery para ocultar la nota eliminada
        }
      })
      .catch(error => console.log(error.message))
  }

  // Envía los datos editados al servidor para actualizar la nota en la base de datos
  updateNote(e) {
    const liElement = e.target.closest("li");
    const thisNote = liElement.getAttribute('data-id');
    const url = `${universityData.root_url}/wp-json/wp/v2/note/${thisNote}`;

    // Captura los valores actuales de los campos de título y contenido
    const ourUpdatePost = {
      title: liElement.querySelector(".note-title-field").value,
      content: liElement.querySelector(".note-body-field").value,
    }
    fetch(url, {
      headers: {
        'Content-Type': 'application/json', // Indica al servidor que el body viene en formato JSON
        'X-WP-Nonce': `${universityData.nonce}`
      },
      method: 'POST',
      body: JSON.stringify(ourUpdatePost), // Convierte el objeto JS a string JSON (fetch requiere body, no data)
    })
      .then(response => {
        if (response.ok) {
          this.makeNoteReadOnly(liElement); // Bloquea los campos tras guardar exitosamente
        }
      })
      .catch(error => console.log(error.message))
  }

  // Toggle: alterna entre modo edición y modo lectura según el estado actual del campo
  editNote(e) {
    const liElement = e.target.closest("li");
    const titleField = liElement.querySelector(".note-title-field");

    if (titleField.hasAttribute("readonly")) {
      this.makeNoteEditable(liElement); // Readonly presente → desbloquear para editar
    } else {
      this.makeNoteReadOnly(liElement); // Sin readonly → el usuario canceló, bloquear de nuevo
      console.log()
    }
  }

  // Activa el modo edición: desbloquea inputs, muestra botón Save, cambia Edit por Cancelar
  makeNoteEditable(liElement) {
    const titleField = liElement.querySelector(".note-title-field");
    const bodyField = liElement.querySelector(".note-body-field");
    const noteUpdate = liElement.querySelector(".update-note");
    const editBtn = liElement.querySelector(".edit-note");

    titleField.removeAttribute("readonly");
    titleField.classList.add("note-active-field"); // Clase CSS que resalta visualmente el campo activo
    bodyField.removeAttribute("readonly");
    bodyField.classList.add('note-active-field');
    noteUpdate.classList.add("update-note--visible"); // Muestra el botón Save
    editBtn.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i> Cancelar';
  }

  // Activa el modo lectura: bloquea inputs, oculta botón Save, restablece el botón a Edit
  makeNoteReadOnly(liElement) {
    const titleField = liElement.querySelector(".note-title-field");
    const bodyField = liElement.querySelector(".note-body-field");
    const noteUpdate = liElement.querySelector(".update-note");
    const editBtn = liElement.querySelector(".edit-note");

    titleField.setAttribute("readonly", "true");
    titleField.classList.remove("note-active-field");
    bodyField.setAttribute("readonly", "true");
    bodyField.classList.remove('note-active-field');
    noteUpdate.classList.remove("update-note--visible"); // Oculta el botón Save
    editBtn.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i> Edit';
  }

}

export default MyNotes;