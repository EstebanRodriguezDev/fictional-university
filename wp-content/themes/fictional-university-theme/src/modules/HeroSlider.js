// Importa la librería Glide.js, que es el motor del slider/carousel.
import Glide from "@glidejs/glide"

// Clase HeroSlider: Inicializa el slider de imágenes de la página principal.
// Genera dinámicamente los botones de navegación (puntos) y activa el carrusel automático.
class HeroSlider {
  constructor() {
    // Verifica si existe el slider en la página antes de inicializarlo
    // para evitar errores en páginas que no lo tienen.
    if (document.querySelector(".hero-slider")) {
      // count how many slides there are
      // Cuenta el número total de slides para generar el mismo número de botones de navegación.
      const dotCount = document.querySelectorAll(".hero-slider__slide").length

      // Generate the HTML for the navigation dots
      // Crea dinámicamente un botón por cada slide, usando el atributo 'data-glide-dir' para que Glide sepa a qué slide navegar.
      let dotHTML = ""
      for (let i = 0; i < dotCount; i++) {
        dotHTML += `<button class="slider__bullet glide__bullet" data-glide-dir="=${i}"></button>`
      }

      // Add the dots HTML to the DOM
      // Inserta los botones generados dentro del contenedor de bullets del slider.
      document.querySelector(".glide__bullets").insertAdjacentHTML("beforeend", dotHTML)

      // Actually initialize the glide / slider script
      // Crea la instancia de Glide con tipo carrusel (loop infinito), 1 slide visible y autoplay cada 3 segundos.
      var glide = new Glide(".hero-slider", {
        type: "carousel",
        perView: 1,
        autoplay: 3000
      })

      // mount(): Activa y renderiza el slider en el DOM.
      glide.mount()
    }
  }
}

export default HeroSlider
