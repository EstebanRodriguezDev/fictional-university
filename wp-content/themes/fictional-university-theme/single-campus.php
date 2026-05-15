<?php
get_header();

while (have_posts()) {
 the_post();
 pageBanner();
?>

 <div class="container container--narrow page-section">
  <div class="metabox metabox--position-up metabox--with-home-link">
   <p>
    <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('campus'); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Campuses</a>
    <span class="metabox__main"><?php the_title(); ?></span>
   </p>
  </div>

  <div class="generic-content"><?php the_content(); ?></div>
  <div class="acf-map">
   <?php $mapLocation = get_field('map_location'); ?>
   <div class="marker"
    data-lat="<?php echo $mapLocation['lat']; ?>" data-lng="<?php echo $mapLocation['lng']; ?>">
    <h3>
     <?php the_title(); ?>
    </h3>
    <p><?php echo $mapLocation['address']; ?></p>
   </div>
  </div>
  <?php
  $relatedPrograms = new WP_Query(array(
   'posts_per_page' => -1, // Muestra todos los posts que coincidan.
   'post_type' => 'program', // Busca posts del tipo 'campus'.
   'orderby' => 'title', // Ordena los resultados por título.
   'order' => 'ASC', // Orden ascendente (A-Z).
   'meta_query' => array( // Permite filtrar posts basados en valores de campos personalizados (ACF).
    array(
     'key' => 'related_campus', // El campo personalizado de ACF que almacena los campus relacionados.
     'compare' => 'LIKE', // Compara si el valor del campo contiene la cadena.
     // get_the_ID(): Obtiene el ID del programa actual.
     // Se envuelve en comillas dobles para asegurar que se busca el ID exacto y no parte de otro ID (ej. "12" en lugar de "1", "2", "123").
     'value' => '"' . get_the_ID() . '"'
    )
   )
  ));

  // Verifica si la consulta de profesores relacionados encontró resultados.
  if ($relatedPrograms->have_posts()) {
   echo '<hr class="section-break">'; // Línea divisoria para separar secciones.
   echo '<h2 class="headline headline--medium">Programs Available At this Campus</h2>'; // Título de la sección.
   // Bucle para mostrar cada profesor relacionado.
   echo '<ul class="min-list link-list">';
   while ($relatedPrograms->have_posts()) {
    $relatedPrograms->the_post(); // Prepara los datos del profesor actual.
  ?>
    <li>
     <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </li>
   <?php }
   echo '</ul>';
  }
  wp_reset_postdata(); // Restaura los datos globales del post a la consulta principal de WordPress. ¡Importante después de WP_Query!

  // Obtiene la fecha actual en formato 'AñoMesDía' para comparar con los campos de fecha de ACF.
  $today = date('Ymd');
  // WP_Query para obtener eventos futuros relacionados con este programa.
  $homePageEvents = new WP_Query(array(
   'posts_per_page' => 2, // Muestra un máximo de 2 eventos.
   'post_type' => 'event', // Busca posts del tipo 'event'.
   'meta_key' => 'event_date', // Ordena por el campo personalizado 'event_date'.
   'orderby' => 'meta_value_num', // Ordena numéricamente por el valor del campo.
   'order' => 'ASC', // Orden ascendente (eventos más próximos primero).
   'meta_query' => array( // Filtra posts basados en múltiples condiciones de campos personalizados.
    array(
     'key' => 'event_date', // Campo de fecha del evento.
     'compare' => '>=', // Compara si la fecha del evento es mayor o igual a la fecha de hoy.
     'value' => $today, // La fecha de hoy.
     'type' => 'numeric' // Indica que el valor es numérico para una comparación correcta.
    ),
    array(
     'key' => 'related_programs', // Campo de ACF que relaciona eventos con programas.
     'compare' => 'LIKE', // Compara si el valor del campo contiene la cadena.
     'value' => '"' . get_the_ID() . '"' // ID del programa actual, envuelto para coincidencia exacta.
    )
   )
  ));

  // Verifica si la consulta de eventos relacionados encontró resultados.
  if ($homePageEvents->have_posts()) {
   echo '<hr class="section-break">'; // Línea divisoria.
   echo '<h2 class="headline headline--medium">Upcoming ' . get_the_title() . ' Event</h2>'; // Título de la sección.
   // Bucle para mostrar cada evento relacionado.
   while ($homePageEvents->have_posts()) {
    $homePageEvents->the_post(); // Prepara los datos del evento actual.
   ?>
    <div class="event-summary">
     <a class="event-summary__date t-center" href="#">
      <span class="event-summary__month">
       <?php
       // get_field('event_date'): Obtiene el valor del campo personalizado 'event_date' de ACF.
       // new DateTime(): Crea un objeto DateTime para formatear la fecha.
       $eventDate = new DateTime(get_field('event_date'));
       echo $eventDate->format('M'); // Imprime el mes abreviado (ej. Jan, Feb).
       ?>
      </span>
      <!-- format('d'): Imprime el día del mes. -->
      <span class="event-summary__day"><?php echo $eventDate->format('d'); ?></span>
     </a>
     <div class="event-summary__content">
      <!-- the_permalink(): Imprime la URL del evento. the_title(): Imprime el título del evento. -->
      <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
      <p>
       <?php
       // has_excerpt(): Verifica si el post tiene un extracto manual.
       if (has_excerpt()) {
        echo get_the_excerpt(); // Muestra el extracto manual.
       } else {
        // wp_trim_words(): Recorta el contenido a 18 palabras si no hay extracto manual.
        echo wp_trim_words(get_the_content(), 18);
       }
       ?>
       <a href="<?php the_permalink(); ?>" class="nu gray">Learn more</a>
      </p>
     </div>
    </div>
  <?php }
  }
  wp_reset_postdata(); // Restaura los datos globales del post después de la consulta personalizada.
  ?>
 </div>
<?php }
// get_footer(): Incluye el archivo footer.php del tema.
get_footer();
?>