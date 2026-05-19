class Search {
  // 1- Descripcion del objeto asi como la creacion/iniciacion.
  // El constructor se ejecuta automáticamente cuando se crea una instancia de la clase Search.
  // Aquí se seleccionan los elementos del DOM que se usarán en la funcionalidad de búsqueda.
  constructor() {
    this.resultDiv = document.querySelector('#search-overlay__results'); // Contenedor donde se mostrarán los resultados de búsqueda.
    this.openButton = document.querySelector(".site-header__util .js-search-trigger"); // Botón(es) que abre la capa de búsqueda (icono de lupa).
    this.closeButton = document.querySelector(".search-overlay__close"); // Botón que cierra la capa de búsqueda (la "X").
    this.searchOverlay = document.querySelector(".search-overlay"); // La capa superpuesta de búsqueda a pantalla completa.
    this.searchField = document.querySelector('#search-term'); // El input de texto donde el usuario escribe su búsqueda.
    this.events(); // Llama a la función events() para registrar los event listeners.
    this.isOverlayOpen = false; // Variable de estado para saber si la capa está abierta o cerrada.
    this.typingTimer; // Variable para almacenar el temporizador del retraso al escribir (debounce).
    this.isSpinnerVisible = false;
    this.previusValue;
  }
  // 2- Eventos
  // Este método agrupa y registra todos los event listeners del módulo.
  events() {
    // Al hacer clic en el botón de abrir, ejecuta openOverlay. bind(this) asegura que 'this' siga refiriéndose a la clase Search.
    // this.openButton.addEventListener('click', this.openOverlay.bind(this));
    this.openButton.addEventListener('click', () => {
      this.openOverlay();
    });
    // Al hacer clic en el botón de cerrar, ejecuta closeOverlay.
    // this.closeButton.addEventListener('click', this.closeOverlay.bind(this));
    this.closeButton.addEventListener('click', () => {
      this.closeOverlay();
    })
    // Escucha cualquier tecla presionada en el documento para atajos de teclado.
    // document.addEventListener("keydown", this.keyPressDispatcher.bind(this));
    document.addEventListener('keydown', (e) => {
      this.keyPressDispatcher(e);
    })
    // Escucha cada vez que el usuario suelta una tecla al escribir en el campo de búsqueda.
    // this.searchField.addEventListener('keyup', this.typingLogic.bind(this));
    this.searchField.addEventListener('keyup', () => {
      this.typingLogic();
    })
  }
  // 3- Metodos
  // Abre la capa de búsqueda añadiendo una clase CSS y evita que el fondo haga scroll.
  openOverlay() {
    this.searchOverlay.classList.add('search-overlay--active');
    document.querySelector("body").classList.add('body-no-scroll');
    this.isOverlayOpen = true; // Actualiza el estado a abierto.
  }
  // Cierra la capa de búsqueda eliminando la clase CSS y permite que el fondo haga scroll de nuevo.
  closeOverlay() {
    this.searchOverlay.classList.remove('search-overlay--active');
    document.querySelector("body").classList.remove('body-no-scroll');
    this.isOverlayOpen = false; // Actualiza el estado a cerrado.
  }
  // Maneja los atajos de teclado (tecla 'S' para abrir, 'Esc' para cerrar).
  keyPressDispatcher(e) {
    // Si se presiona la tecla 'S' (código 83) y la capa no está abierta, se abre.
    if (e.keyCode == 83 && !this.isOverlayOpen && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
      this.openOverlay();
    } else if (e.keyCode == 27 && this.isOverlayOpen) // Si se presiona la tecla 'Esc' (código 27) y la capa está abierta, se cierra.
      this.closeOverlay();
  }
  // Lógica de tipeo con 'debounce' (retraso) para no hacer múltiples acciones por cada letra que se escriba,
  // sino esperar a que el usuario deje de escribir.
  typingLogic() {
    if (this.searchField.value != this.previusValue) {
      clearTimeout(this.typingTimer); // Reinicia el temporizador cada vez que se presiona una tecla.
      if (this.searchField.value) {
        if (!this.isSpinnerVisible) {
          this.resultDiv.innerHTML = '<div class="spinner-loader"></div>';
          this.isSpinnerVisible = true;
        } else {
          this.isSpinnerVisible = false;
        }
        // Inicia un nuevo temporizador que se ejecutará después de 2000 milisegundos (2 segundos) de inactividad.
        this.typingTimer = setTimeout(this.getResults.bind(this), 2000);
      } else {
        this.resultDiv.innerHTML = '';
        this.isSpinnerVisible = false;
      }
    }
    this.previusValue = this.searchField.value;
  }
  getResults() {
    const url = `http://fictional-university.local/wp-json/wp/v2/posts?search=${this.searchField.value}`;
    fetch(url)
      .then(respuesta => respuesta.json())
      .then(datos => this.resultDiv.innerHTML = `
          <h2 class="search-overlay__section-title">General Information</h2>
          <ul class="link-list min-list">
            ${datos.map(dato => `<li><a href="${dato.link}">${dato.title.rendered}</a></li>`)}
          </ul>
        `)
      .catch(error => console.log('No hay concidencias en su busqueda'))
  }
}

export default Search;