<?php

// Se engancha al hook 'rest_api_init' para registrar la ruta solo cuando la API REST
// está activa, evitando errores si se llama antes de que WordPress la inicialice.
add_action('rest_api_init', 'universityRegisterSearch');

// Se centraliza el registro de la ruta en una función separada para mantener
// el código organizado y que add_action reciba un callback limpio.
function universityRegisterSearch()
{
  // Se usa el namespace 'university/v1' para versionar la API y poder introducir
  // cambios incompatibles en el futuro sin romper clientes que usen 'v1'.
  register_rest_route('university/v1', 'search', array(
    'methods' => WP_REST_Server::READABLE, // Se acepta solo GET porque la búsqueda es una operación de lectura y debe ser cacheable.
    'callback' => 'universitySearchResults', // Se separa la lógica en su propia función para facilitar el mantenimiento y las pruebas.
  ));
}

/**
 * Se usa un filtro sobre 'posts_search' para reescribir el SQL de búsqueda
 * y limitarlo al título, ya que por defecto WordPress también busca en el contenido
 * del post, lo que produce resultados irrelevantes para una búsqueda de títulos.
 *
 * @param string $search   La cláusula de búsqueda original generada por WordPress.
 * @param WP_Query $wp_query El objeto de consulta de WordPress con los parámetros.
 * @return string La cláusula de búsqueda modificada para buscar solo en el título.
 */
function university_search_by_title_only($search, WP_Query $wp_query)
{
  global $wpdb; // Se usa el objeto global $wpdb para construir consultas SQL compatibles con cualquier prefijo de tabla.

  // Se devuelve el SQL intacto si no hay búsqueda para no interferir
  // con consultas que no usan el parámetro 's'.
  if (empty($search)) return $search;

  // Se extrae el término de búsqueda del objeto WP_Query en vez de $_GET
  // para que el filtro sea agnóstico del origen de la petición.
  $search_term = $wp_query->query_vars['s'];

  // Se reescribe la cláusula WHERE de búsqueda para que apunte exclusivamente
  // a post_title. Se usa esc_like() para proteger contra inyección de SQL por
  // caracteres especiales como '%' o '_' en el término de búsqueda.
  $search = " AND ({$wpdb->posts}.post_title LIKE '%" . $wpdb->esc_like($search_term) . "%') ";

  return $search; // Se devuelve la cláusula modificada para que WordPress la use en la consulta final.
}

// Función principal que procesa la búsqueda y devuelve el JSON.
function universitySearchResults(WP_REST_Request $data)
{
  // Se engancha el filtro JUSTO ANTES de ejecutar la consulta principal para
  // que solo afecte a esta búsqueda y no a otras consultas que pueda hacer WordPress.
  // Los parámetros (10, 2) especifican prioridad y número de argumentos que recibe la función.
  add_filter('posts_search', 'university_search_by_title_only', 10, 2);

  // Se busca en múltiples custom post types en una sola consulta para
  // devolver resultados consolidados sin hacer N peticiones a la base de datos.
  $mainQuery = new WP_Query(array(
    'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
    's' => sanitize_text_field($data['term']) // Se sanitiza la entrada del usuario para prevenir inyección de caracteres maliciosos antes de pasarla a la consulta.
  ));

  // Se elimina el filtro inmediatamente después de ejecutar la consulta para
  // que no afecte ninguna otra búsqueda del sitio durante esta misma petición.
  remove_filter('posts_search', 'university_search_by_title_only', 10);

  // Se inicializa la estructura de resultados por categorías para que el frontend
  // pueda renderizar cada tipo de contenido con su propio componente visual.
  $results = array(
    'generalInfo' => array(),
    'professors' => array(),
    'programs' => array(),
    'events' => array(),
    'campuses' => array(),
  );

  // Se itera sobre los resultados de la consulta principal para clasificarlos
  // por tipo de contenido y construir el array de respuesta.
  while ($mainQuery->have_posts()) {
    $mainQuery->the_post(); // Se prepara el contexto del post actual para poder usar las funciones de plantilla de WordPress.

    // Se agrupan posts y páginas en 'generalInfo' porque comparten el mismo
    // componente visual en el frontend (resultado genérico con título, enlace y autor).
    if (get_post_type() == 'post' or get_post_type() == 'page') {
      array_push($results['generalInfo'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink(),
        'author' => get_the_author(),
        'postType' => get_post_type(),
      ));
    }

    // Se incluye la imagen del profesor porque el frontend la necesita para
    // renderizar la tarjeta visual del docente en los resultados.
    if (get_post_type() == 'professor') {
      array_push($results['professors'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink(),
        'image' => get_the_post_thumbnail_url(0, 'professorLandscape'),
      ));
    }

    // Se guarda el ID del programa porque es necesario en la búsqueda relacional
    // posterior para encontrar profesores y eventos vinculados a ese programa.
    if (get_post_type() == 'program') {
      $relatedCampuses = get_field('related_campus');

      // Se agregan los campus relacionados al programa directamente en este bloque
      // para aprovechar el contexto del Loop y evitar una WP_Query extra.
      if ($relatedCampuses) {
        foreach ($relatedCampuses as $campus) {
          array_push($results['campuses'], array(
            'title' => get_the_title($campus),
            'permalink' => get_the_permalink($campus),
          ));
        }
      }

      array_push($results['programs'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink(),
        'id' => get_the_ID(), // Se incluye el ID porque es la clave para la búsqueda relacional de profesores y eventos vinculados al programa.
      ));
    }

    // Se agrupan los campus por separado porque el frontend usa un componente
    // visual distinto para mostrarlos (con mapa o diferente estructura).
    if (get_post_type() == 'campus') {
      array_push($results['campuses'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink(),
      ));
    }

    // Se procesa la fecha del evento con DateTime para convertirla del formato
    // de ACF (YYYYMMDD) a un formato legible por el componente del frontend (mes/día).
    if (get_post_type() == 'event') {
      $eventDate = new DateTime(get_field('event_date')); // Se crea un objeto DateTime desde el campo ACF para poder formatearlo de forma flexible.
      $description = null;
      // Se da prioridad al extracto manual porque el editor lo redacta específicamente
      // para ser mostrado en listados, a diferencia del contenido completo recortado.
      if (has_excerpt()) {
        $description = get_the_excerpt();
      } else {
        $description = wp_trim_words(get_the_content(), 18); // Se recorta a 18 palabras para mantener la tarjeta del evento uniforme cuando no hay extracto.
      }

      array_push($results['events'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink(),
        'month' => $eventDate->format('M'),
        'day' => $eventDate->format('d'),
        'description' => $description,
      ));
    }
  } // Fin del primer bucle.

  // BÚSQUEDA RELACIONAL: Se busca una segunda vez si hay programas en los resultados
  // porque los profesores y eventos no mencionan el programa en su título, pero sí
  // están vinculados a él a través del campo relacional de ACF.
  if ($results['programs']) {
    // Se usa 'relation' => 'OR' para que la consulta devuelva profesores o eventos
    // que estén relacionados con CUALQUIERA de los programas encontrados, no con todos.
    $programsMetaQuery = array('relation' => 'OR');

    // Se construye la meta_query dinámicamente para incluir todos los programas
    // encontrados en una sola consulta, evitando N consultas a la base de datos.
    foreach ($results['programs'] as $item) {
      array_push($programsMetaQuery, array(
        'key' => 'related_programs', // Se especifica el campo relacional de ACF donde ACF almacena los IDs relacionados.
        'compare' => 'LIKE', // Se usa LIKE porque ACF serializa los IDs en una cadena y no como valores individuales.
        'value' => '"' . $item['id'] . '"', // Se envuelve en comillas dobles para buscar la coincidencia exacta del ID en el array serializado, evitando falsos positivos.
      ));
    }

    // Se lanza una segunda consulta para obtener los profesores y eventos
    // relacionados que la búsqueda de título no habría encontrado directamente.
    $programRelationshipQuery = new WP_Query(array(
      'post_type' => array('professor', 'event'),
      'meta_query' => $programsMetaQuery,
    ));

    // Se agregan los profesores y eventos relacionados al array de resultados
    // para que el frontend los muestre junto con los resultados directos de búsqueda.
    while ($programRelationshipQuery->have_posts()) {
      $programRelationshipQuery->the_post();

      if (get_post_type() == 'professor') {
        array_push($results['professors'], array(
          'title' => get_the_title(),
          'permalink' => get_the_permalink(),
          'image' => get_the_post_thumbnail_url(0, 'professorLandscape'),
        ));
      }
      if (get_post_type() == 'event') {
        $eventDate = new DateTime(get_field('event_date')); // Se crea un objeto DateTime desde el campo ACF para poder formatearlo de forma flexible.
        $description = null;
        if (has_excerpt()) {
          $description = get_the_excerpt();
        } else {
          $description = wp_trim_words(get_the_content(), 18);
        }

        array_push($results['events'], array(
          'title' => get_the_title(),
          'permalink' => get_the_permalink(),
          'month' => $eventDate->format('M'),
          'day' => $eventDate->format('d'),
          'description' => $description,
        ));
      }
    }

    // Se eliminan duplicados porque un profesor o evento puede aparecer tanto en la
    // búsqueda principal (por título) como en la relacional (por programa).
    // Se usa SORT_REGULAR para comparar arrays por valor y array_values para
    // re-indexar numéricamente el array, ya que JSON necesita un array ([]) y no un objeto ({}).
    $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
    $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
  }

  // Se devuelve el array directamente porque WordPress lo serializa a JSON
  // automáticamente y lo envía con los headers correctos al frontend.
  return $results;
}
