// Clase MobileMenu: Controla la apertura y cierre del menú de navegación en dispositivos móviles.
// Alterna clases CSS en el botón y en el contenedor del menú para mostrarlo u ocultarlo.
class MobileMenu {
  // El constructor selecciona los elementos del DOM necesarios y registra los eventos.
  constructor() {
    this.menu = document.querySelector(".site-header__menu") // Contenedor principal del menú de navegación.
    this.openButton = document.querySelector(".site-header__menu-trigger") // Botón de hamburguesa (≡) que activa el menú.
    this.events()
  }

  // events: Registra el listener de clic sobre el botón del menú.
  events() {
    this.openButton.addEventListener("click", () => this.openMenu())
  }

  // openMenu: Alterna el estado abierto/cerrado del menú.
  // Cambia el ícono del botón entre 'fa-bars' (≡) y 'fa-window-close' (✕)
  // y muestra u oculta el menú agregando/quitando la clase activa.
  openMenu() {
    this.openButton.classList.toggle("fa-bars")
    this.openButton.classList.toggle("fa-window-close")
    this.menu.classList.toggle("site-header__menu--active")
  }
}

export default MobileMenu
