<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();
// Se usa pageBanner() con textos fijos porque esta página siempre es de eventos pasados,
// por lo que su título y subtítulo son constantes y no dependen de un post en la base de datos.
pageBanner(array(
  'title' => 'Past Events',
  'subtitle' => 'See what is going on in our wolrd',
));
?>


<div class="container container--narrow page-section">
  <?php
  $today = date('Ymd'); // Se usa el formato 'AñoMesDía' porque ACF almacena las fechas en ese formato numérico para poder comparar y ordenar correctamente.

  // Se usa WP_Query personalizado (en vez del Loop principal) para controlar exactamente
  // los eventos a mostrar: solo del tipo 'event', filtrados por fecha pasada y con paginación.
  $pastEvents = new WP_Query(array(
    'paged' => get_query_var('paged', 1), // Se pasa la variable de paginación para que WordPress sepa qué página mostrar en una página estática.
    'post_type' => 'event',
    'meta_key' => 'event_date', // Se indica el campo de fecha para que WordPress lo use como criterio de ordenamiento.
    'orderby' => 'meta_value_num', // Se ordena numéricamente porque la fecha está guardada como número (ej. 20241230) en ACF.
    'order' => 'ASC',
    'meta_query' => array( // Se usa meta_query para filtrar y mostrar SOLO los eventos cuya fecha ya pasó.
      array(
        'key' => 'event_date',
        'compare' => '<', // Se compara con '<' para traer solo eventos anteriores a hoy.
        'value' => $today,
        'type' => 'numeric'
      )
    )
  ));

  // Se itera sobre la consulta personalizada de eventos pasados
  // para renderizar cada evento con su plantilla reutilizable.
  while ($pastEvents->have_posts()) {
    $pastEvents->the_post();
    get_template_part('template-parts/event');
  }
  // Se pasa el total de páginas del query personalizado porque paginate_links()
  // por defecto usa el total de la consulta principal (la página), no de $pastEvents.
  echo paginate_links(array(
    'total' => $pastEvents->max_num_pages
  ));
  ?>
</div>

<?php
// Se incluye el footer para cerrar correctamente el HTML y cargar los scripts que
// WordPress y los plugins necesitan inyectar al final del body.
get_footer();
?>