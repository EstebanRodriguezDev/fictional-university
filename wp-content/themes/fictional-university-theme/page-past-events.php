<?php
// get_header(): Función de WordPress que busca e incluye el archivo header.php del tema.
get_header();
// Llama al banner superior y le pasa textos estáticos específicos para esta página de eventos pasados.
pageBanner(array(
  'title' => 'Past Events',
  'subtitle' => 'See what is going on in our wolrd',
));
?>


<div class="container container--narrow page-section">
  <?php
  $today = date('Ymd'); // date('Ymd'): Obtiene la fecha actual para comparar con el formato de ACF.

  // WP_Query: Realiza una consulta personalizada para mostrar eventos cuya fecha ya pasó.
  $pastEvents = new WP_Query(array(
    'paged' => get_query_var('paged', 1), // Permite que la paginación funcione en una página personalizada.
    'post_type' => 'event', // Filtra por el tipo de contenido 'event'.
    'meta_key' => 'event_date', // Indica que se usará el campo personalizado 'event_date' para ordenar.
    'orderby' => 'meta_value_num', // Ordena numéricamente basándose en el meta_key.
    'order' => 'ASC', // Orden ascendente.
    'meta_query' => array( // Filtro adicional para traer solo eventos pasados.
      array(
        'key' => 'event_date',
        'compare' => '<', // Compara si la fecha del evento es menor que la fecha de hoy.
        'value' => $today,
        'type' => 'numeric'
      )
    )
  ));

  // Inicia el bucle para recorrer los resultados de la consulta personalizada.
  while ($pastEvents->have_posts()) {
    $pastEvents->the_post(); // Prepara los datos del evento actual (título, contenido, etc.).
    get_template_part('template-parts/event');
  }
  // paginate_links(): Genera los botones de navegación (1, 2, 3...).
  echo paginate_links(array(
    'total' => $pastEvents->max_num_pages // Se debe pasar el total de páginas del query personalizado para que funcione.
  ));
  ?>
</div>

<?php
// get_footer(): Función de WordPress que busca e incluye el archivo footer.php del tema.
get_footer();
?>