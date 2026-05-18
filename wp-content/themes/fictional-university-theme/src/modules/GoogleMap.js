// Clase GMap: Maneja la inicialización y el comportamiento de los mapas de Google Maps
// en los elementos con clase '.acf-map', que son generados por el campo de mapa de ACF.
class GMap {
  // El constructor busca todos los contenedores de mapa en la página y crea un mapa por cada uno.
  constructor() {
    document.querySelectorAll(".acf-map").forEach(el => {
      this.new_map(el)
    })
  }

  // new_map: Inicializa una instancia de Google Maps dentro del elemento contenedor recibido ($el).
  // Busca todos los marcadores dentro del contenedor, los agrega al mapa y luego centra la vista.
  new_map($el) {
    var $markers = $el.querySelectorAll(".marker") // Selecciona todos los elementos '.marker' dentro del contenedor del mapa.

    // args: Configuración inicial del mapa (zoom, centro provisional y tipo de vista).
    var args = {
      zoom: 16,
      center: new google.maps.LatLng(0, 0), // Centro provisional; se recalcula con center_map().
      mapTypeId: google.maps.MapTypeId.ROADMAP // Muestra el mapa estándar de calles.
    }

    var map = new google.maps.Map($el, args) // Crea la instancia del mapa de Google dentro del elemento HTML.
    map.markers = [] // Array para almacenar todos los marcadores que se añadan al mapa.
    var that = this // Guarda la referencia a 'this' para usarla dentro de callbacks donde 'this' cambia de contexto.

    // add markers
    // Itera sobre cada elemento '.marker' del DOM y lo agrega al mapa usando el método add_marker.
    $markers.forEach(function (x) {
      that.add_marker(x, map)
    })

    // center map
    // Una vez añadidos todos los marcadores, ajusta la vista del mapa para que quepan todos.
    this.center_map(map)
  } // end new_map

  // add_marker: Crea un marcador de Google Maps en la posición indicada por los atributos 'data-lat' y 'data-lng' del elemento.
  // Si el elemento tiene contenido HTML interno, lo usa para crear un InfoWindow (ventana emergente al hacer clic).
  add_marker($marker, map) {
    var latlng = new google.maps.LatLng($marker.getAttribute("data-lat"), $marker.getAttribute("data-lng")) // Obtiene las coordenadas del marcador desde los atributos HTML.

    var marker = new google.maps.Marker({
      position: latlng,
      map: map
    })

    map.markers.push(marker) // Agrega el nuevo marcador al array del mapa para poder usarlo en center_map.

    // if marker contains HTML, add it to an infoWindow
    // Si el elemento '.marker' tiene HTML dentro (nombre, dirección, etc.), se crea una ventana informativa.
    if ($marker.innerHTML) {
      // create info window
      var infowindow = new google.maps.InfoWindow({
        content: $marker.innerHTML // El contenido del popup es el HTML que estaba dentro del div '.marker'.
      })

      // show info window when marker is clicked
      // Agrega un listener para abrir la ventana informativa al hacer clic sobre el marcador.
      google.maps.event.addListener(marker, "click", function () {
        infowindow.open(map, marker)
      })
    }
  } // end add_marker

  // center_map: Ajusta el centro y el zoom del mapa para que todos los marcadores queden visibles.
  center_map(map) {
    var bounds = new google.maps.LatLngBounds() // LatLngBounds: Define un área geográfica rectangular que se expande para contener todos los puntos.

    // loop through all markers and create bounds
    // Expande el área límite para incluir la posición de cada marcador registrado.
    map.markers.forEach(function (marker) {
      var latlng = new google.maps.LatLng(marker.position.lat(), marker.position.lng())

      bounds.extend(latlng)
    })

    // only 1 marker?
    // Si solo hay un marcador, se centra el mapa en él con un zoom fijo (16).
    // Con varios marcadores, se ajusta automáticamente el zoom para que todos quepan en la vista.
    if (map.markers.length == 1) {
      // set center of map
      map.setCenter(bounds.getCenter())
      map.setZoom(16)
    } else {
      // fit to bounds
      map.fitBounds(bounds)
    }
  } // end center_map
}

export default GMap
