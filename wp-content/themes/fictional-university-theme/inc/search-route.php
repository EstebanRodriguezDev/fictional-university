<?php

// Engancha nuestra función personalizada a la inicialización de la API REST de WordPress.
add_action('rest_api_init', 'universityRegisterSearch');

// Registra la nueva ruta (endpoint) en la API REST.
function universityRegisterSearch()
{
 // Parámetros: namespace (university/v1), nombre de la ruta (search), y configuración.
 register_rest_route('university/v1', 'search', array(
  'methods' => WP_REST_Server::READABLE, // Acepta solo peticiones GET.
  'callback' => 'universitySearchResults', // La función que se ejecutará cuando se llame a esta ruta.
 ));
}

// Filtro personalizado para interceptar la consulta SQL y obligarla a buscar SOLO en el título.
function university_search_by_title_only($search, $wp_query)
{
 global $wpdb; // Objeto global de la base de datos de WordPress.
 
 // Si no hay búsqueda, devolvemos el SQL intacto.
 if (empty($search)) return $search;

 // Extraemos el término exacto que el usuario buscó.
 $search_term = $wp_query->query_vars['s'];
 
 // Reescribimos el SQL para que busque EXCLUSIVAMENTE en la columna post_title (evitando el post_content).
 // Usamos esc_like para proteger contra inyección SQL.
 $search = " AND ({$wpdb->posts}.post_title LIKE '%" . $wpdb->esc_like($search_term) . "%') ";

 return $search; // Devolvemos el SQL modificado.
}

// Función principal que procesa la búsqueda y devuelve el JSON.
function universitySearchResults(WP_REST_Request $data)
{
 // 1. Enganchamos el filtro JUSTO antes de ejecutar nuestra consulta para limitar la búsqueda al título.
 add_filter('posts_search', 'university_search_by_title_only', 10, 2);

 // Ejecuta la consulta principal buscando el término en múltiples Custom Post Types.
 $mainQuery = new WP_Query(array(
  'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
  's' => sanitize_text_field($data['term']) // Sanea la entrada del usuario por seguridad.
 ));

 // 2. Removemos el filtro inmediatamente después para no afectar el motor de búsqueda global del sitio web.
 remove_filter('posts_search', 'university_search_by_title_only', 10);

 // Inicializamos la estructura del array final que será convertido a JSON.
 $results = array(
  'generalInfo' => array(),
  'professors' => array(),
  'programs' => array(),
  'events' => array(),
  'campuses' => array(),
 );

 // Bucle para procesar los resultados de la consulta principal.
 while ($mainQuery->have_posts()) {
  $mainQuery->the_post(); // Prepara la información del post actual.

  // Si es un post o página, lo agregamos a generalInfo.
  if (get_post_type() == 'post' or get_post_type() == 'page') {
   array_push($results['generalInfo'], array(
    'title' => get_the_title(),
    'permalink' => get_the_permalink(),
    'author' => get_the_author(),
    'postType' => get_post_type(),
   ));
  }
  
  // Si es un profesor, extraemos su título, link e imagen destacada.
  if (get_post_type() == 'professor') {
   array_push($results['professors'], array(
    'title' => get_the_title(),
    'permalink' => get_the_permalink(),
    'image' => get_the_post_thumbnail_url(0, 'professorLandscape'),
   ));
  }
  
  // Si es un programa, lo guardamos e incluimos su ID (clave para la búsqueda relacional posterior).
  if (get_post_type() == 'program') {
   array_push($results['programs'], array(
    'title' => get_the_title(),
    'permalink' => get_the_permalink(),
    'id' => get_the_ID(), // ¡Fundamental para relacionar materias con profesores!
   ));
  }
  
  // Si es un campus.
  if (get_post_type() == 'campus') {
   array_push($results['campuses'], array(
    'title' => get_the_title(),
    'permalink' => get_the_permalink(),
   ));
  }
  
  // Si es un evento, procesamos la fecha y creamos un extracto personalizado de 18 palabras si es necesario.
  if (get_post_type() == 'event') {
   $eventDate = new DateTime(get_field('event_date')); // Obtiene fecha de ACF.
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
 } // Fin del primer bucle.

 // BÚSQUEDA RELACIONAL: Si encontramos al menos un programa, busquemos a los profesores relacionados.
 if ($results['programs']) {
  // Inicializamos la estructura meta_query con relación "OR" (encuentra A, B o C).
  $programsMetaQuery = array('relation' => 'OR');

  // Llenamos la estructura iterando sobre todos los programas encontrados.
  foreach ($results['programs'] as $item) {
   array_push($programsMetaQuery, array(
    'key' => 'related_programs', // El campo relacional de ACF.
    'compare' => 'LIKE', // Búsqueda parcial.
    'value' => '"' . $item['id'] . '"', // ID envuelto en comillas dobles para coincidencia estricta en el array serializado.
   ));
  }

  // Ejecutamos una segunda consulta para obtener exclusivamente a esos profesores.
  $programRelationshipQuery = new WP_Query(array(
   'post_type' => 'professor',
   'meta_query' => $programsMetaQuery,
  ));

  // Recorremos los profesores encontrados en la búsqueda relacional.
  while ($programRelationshipQuery->have_posts()) {
   $programRelationshipQuery->the_post();
   
   if (get_post_type() == 'professor') {
    array_push($results['professors'], array(
     'title' => get_the_title(),
     'permalink' => get_the_permalink(),
     'image' => get_the_post_thumbnail_url(0, 'professorLandscape'),
    ));
   }
  }

  // LIMPIEZA DE DUPLICADOS: Si un profesor fue encontrado por la consulta principal y también por la relacional,
  // aparecería dos veces. array_unique limpia los duplicados comparando objetos (SORT_REGULAR), y 
  // array_values reindexa el array para que el JSON resultante sea válido ([...] en lugar de {...}).
  $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
 }

 // WordPress se encarga de convertir este array asociativo en un string JSON y lo envía al Frontend.
 return $results;
}
