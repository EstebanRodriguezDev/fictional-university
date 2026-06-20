class Like {
  constructor() {
    this.likeBox = document.querySelector(".like-box");
    if (this.likeBox) {
      this.events();
    }
  }
  events() {
    this.likeBox.addEventListener("click", this.ourClickDispatcher.bind(this));
  }

  // Metodos aqui
  ourClickDispatcher(e) {
    const currentLikeBox = e.target.closest(".like-box")
    if (currentLikeBox.getAttribute('data-exists') == 'yes') {
      this.deleteLike(currentLikeBox);
    } else {
      this.createLike(currentLikeBox);
    }
  }
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
        currentLikeBox.setAttribute('data-exists', 'yes');
        let likeCount = parseInt(currentLikeBox.querySelector('.like-count').textContent, 10);
        likeCount++;
        currentLikeBox.querySelector('.like-count').textContent = likeCount;
        currentLikeBox.setAttribute('data-like', resultado.likeId);
        console.log(resultado);
      })
      .catch((error) => {
        console.log(error);
      })
  }
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
        currentLikeBox.setAttribute('data-exists', 'no');
        let likeCount = parseInt(currentLikeBox.querySelector('.like-count').textContent, 10);
        likeCount--;
        currentLikeBox.querySelector('.like-count').textContent = likeCount;
        currentLikeBox.setAttribute('data-like', '');
        console.log(resultado);
      })
      .catch((error) => {
        console.log(error);
      })
  }
}

export default Like;