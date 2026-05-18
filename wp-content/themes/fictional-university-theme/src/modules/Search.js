class Search {
 // 1- Descripcion del objeto asi como la creacion/iniciacion.
 constructor() {
  this.openButton = document.querySelector(".site-header__util .js-search-trigger");
  this.closeButton = document.querySelector(".search-overlay__close");
  this.searchOverlay = document.querySelector(".search-overlay");
  this.event();
  this.isOverlayOpen = false;
 }
 // 2- Eventos
 event() {
  this.openButton.addEventListener('click', this.openOverlay.bind(this));
  this.closeButton.addEventListener('click', this.closeOverlay.bind(this));
  document.addEventListener("keydown", this.keyPressDispatcher.bind(this))
 }
 // 3- Metodos
 openOverlay() {
  this.searchOverlay.classList.add('search-overlay--active');
  document.querySelector("body").classList.add('body-no-scroll');
  this.isOverlayOpen = true;
 }
 closeOverlay() {
  this.searchOverlay.classList.remove('search-overlay--active');
  document.querySelector("body").classList.remove('body-no-scroll');
  this.isOverlayOpen = false;
 }
 keyPressDispatcher(e) {
  if (e.keyCode == 83 && !this.isOverlayOpen) {
   this.openOverlay();
  } else if (e.keyCode == 27 && this.isOverlayOpen)
   this.closeOverlay();
 }
}


export default Search;