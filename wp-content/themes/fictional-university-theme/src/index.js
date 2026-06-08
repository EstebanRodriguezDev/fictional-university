// Importa el archivo principal de estilos SCSS para que Webpack lo procese y lo incluya en el build final.
import "../css/style.scss"


// Importa las diferentes clases de Javascript que controlan funcionalidades específicas de la página.
import MobileMenu from "./modules/MobileMenu"
import HeroSlider from "./modules/HeroSlider"
import GoogleMap from "./modules/GoogleMap";
import Search from "./modules/Search";
import MyNotes from "./modules/MyNotes";

// document.addEventListener('DOMContentLoaded', ...): Se asegura de que el código JavaScript
// se ejecute sólo después de que todo el documento HTML (el DOM) haya cargado completamente.
document.addEventListener('DOMContentLoaded', () => {
 // Instantiate a new object using our modules/classes
 // Instancia (crea un objeto de) cada clase importada, lo que activa sus constructores y por ende su funcionalidad en la página.
 const mobileMenu = new MobileMenu()
 const heroSlider = new HeroSlider()
 const googleMap = new GoogleMap();
 const search = new Search(); // Inicializa el módulo de búsqueda superpuesta.
 const myNotes = new MyNotes();
})
