class Like {
  // Inicializa la clase y selecciona el elemento interactivo del DOM
  constructor() {
    this.likeBox = document.querySelector(".like-box");
    // Si el elemento existe en la página actual, asigna los eventos
    if (this.likeBox) {
      this.events();
    }
  }

  // Agrupa los event listeners
  events() {
    this.likeBox.addEventListener("click", this.ourClickDispatcher.bind(this));
  }

  // Método que decide si se debe crear o eliminar un "like" basado en el estado actual
  ourClickDispatcher(e) {
    const currentLikeBox = e.target.closest(".like-box")
    if (currentLikeBox.getAttribute('data-exists') == 'yes') {
      this.deleteLike(currentLikeBox);
    } else {
      this.createLike(currentLikeBox);
    }
  }

  // Envía una petición POST para registrar un nuevo "like" en la base de datos
  createLike(currentLikeBox) {
    const professorId = {
      'professorId': currentLikeBox.dataset.professor,
    }
    const url = `${universityData.root_url}/wp-json/university/v1/manageLike/`;
    
    fetch(url, {
      headers: {
        'Content-Type': 'application/json', // Indica al servidor que el body viene en formato JSON
        'X-WP-Nonce': `${universityData.nonce}`
      },
      method: 'POST',
      body: JSON.stringify(professorId),
    })
      .then((respuesta) => {
        if (!respuesta.ok) {
          return respuesta.json().then((errorDelServidor) => {
            return Promise.reject(errorDelServidor);
          })
        }
        console.log(respuesta);
        return respuesta.json();
      })
      .then((resultado) => {
        // Actualiza la UI: marca el botón como clickeado y suma 1 al contador
        currentLikeBox.setAttribute('data-exists', 'yes');
        let likeCount = parseInt(currentLikeBox.querySelector('.like-count').textContent, 10);
        likeCount++;
        currentLikeBox.querySelector('.like-count').textContent = likeCount;
        // Guarda el ID devuelto por la base de datos para poder eliminarlo después
        currentLikeBox.setAttribute('data-like', resultado.likeId);
        console.log(resultado);
      })
      .catch((error) => {
        console.error("Error al crear like:", error);
      })
  }

  // Envía una petición DELETE para eliminar un "like" existente
  deleteLike(currentLikeBox) {
    const url = `${universityData.root_url}/wp-json/university/v1/manageLike/`;
    fetch(url, {
      headers: {
        'Content-Type': 'application/json', // Indica al servidor que el body viene en formato JSON
        'X-WP-Nonce': `${universityData.nonce}`
      },
      method: 'DELETE',
      body: JSON.stringify({
        'like': currentLikeBox.getAttribute('data-like')
      }),
    })
      .then((respuesta) => {
        if (!respuesta.ok) {
          return respuesta.json().then((error) => Promise.reject(error));
        }
        console.log(respuesta);
        return respuesta.json();
      })
      .then((resultado) => {
        // Actualiza la UI: desmarca el botón y resta 1 al contador
        currentLikeBox.setAttribute('data-exists', 'no');
        let likeCount = parseInt(currentLikeBox.querySelector('.like-count').textContent, 10);
        likeCount--;
        currentLikeBox.querySelector('.like-count').textContent = likeCount;
        // Limpia el ID guardado
        currentLikeBox.setAttribute('data-like', '');
        console.log(resultado);
      })
      .catch((error) => {
        console.error("Error al eliminar like:", error);
      })
  }
}

export default Like;