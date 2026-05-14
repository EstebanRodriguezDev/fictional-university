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
  ?>
    <div class="event-summary">
      <a class="event-summary__date t-center" href="#">
        <span class="event-summary__month">
          <?php
          // get_field('event_date'): Obtiene el valor del campo de fecha de ACF.
          $eventDate = new DateTime(get_field('event_date'));
          echo $eventDate->format('M'); // Imprime el mes abreviado (ej: Jan, Feb).
          ?>
        </span>
        <!-- format('d'): Imprime el día del mes. -->
        <span class="event-summary__day"><?php echo $eventDate->format('d'); ?></span>
      </a>
      <div class="event-summary__content">
        <!-- the_permalink() y the_title(): Funciones para mostrar el enlace y el título del evento. -->
        <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
        <!-- wp_trim_words(): Recorta el contenido del post a un número específico de palabras (18 en este caso). -->
        <p><?php echo wp_trim_words(get_the_content(), 18); ?> <a href="<?php the_permalink(); ?>" class="nu gray">Learn more</a></p>
      </div>
    </div>
  <?php }
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