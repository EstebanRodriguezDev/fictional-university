<?php
// get_header(): Incluye el archivo header.php del tema, que contiene la cabecera HTML, navegación, etc.
get_header();
// Inicia el bucle (Loop) de WordPress. have_posts() verifica si hay posts disponibles para la consulta actual.
// the_post() prepara los datos del post actual para ser utilizados por las funciones de plantilla.
while (have_posts()) {
  the_post(); // Prepara los datos del post (en este caso, un programa).
  // pageBanner() sin parámetros: tomará automáticamente el título del programa y su imagen destacada (vía ACF).
  pageBanner();
?>

  <div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p>
        <!-- get_post_type_archive_link('program'): Genera el enlace a la página de archivo (listado) de todos los programas. -->
        <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program'); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Programs</a>
        <!-- the_title(): Imprime el título del programa actual. -->
        <span class="metabox__main"><?php the_title(); ?></span>
      </p>
    </div>
    <!-- the_content(): Imprime el contenido principal del programa, tal como se introduce en el editor de WordPress. -->
    <div class="generic-content"><?php the_content(); ?></div>
    <?php
    // WP_Query para obtener profesores relacionados con este programa.
    $relatedProfessors = new WP_Query(array(
      'posts_per_page' => -1, // Muestra todos los posts que coincidan.
      'post_type' => 'professor', // Busca posts del tipo 'professor'.
      'orderby' => 'title', // Ordena los resultados por título.
      'order' => 'ASC', // Orden ascendente (A-Z).
      'meta_query' => array( // Permite filtrar posts basados en valores de campos personalizados (ACF).
        array(
          'key' => 'related_programs', // El campo personalizado de ACF que almacena los programas relacionados.
          'compare' => 'LIKE', // Compara si el valor del campo contiene la cadena.
          // get_the_ID(): Obtiene el ID del programa actual.
          // Se envuelve en comillas dobles para asegurar que se busca el ID exacto y no parte de otro ID (ej. "12" en lugar de "1", "2", "123").
          'value' => '"' . get_the_ID() . '"'
        )
      )
    ));

    // Verifica si la consulta de profesores relacionados encontró resultados.
    if ($relatedProfessors->have_posts()) {
      echo '<hr class="section-break">'; // Línea divisoria para separar secciones.
      echo '<h2 class="headline headline--medium">Upcoming ' . get_the_title() . ' Events</h2>'; // Título de la sección.
      // Bucle para mostrar cada profesor relacionado.
      echo '<ul class="professor-cards">';
      while ($relatedProfessors->have_posts()) {
        $relatedProfessors->the_post(); // Prepara los datos del profesor actual.
    ?>
        <li class="professor-card__list-item">
          <a class="professor-card" href="<?php the_permalink(); ?>">
            <img class="professor-card__image" src="<?php the_post_thumbnail_url('professorLandscape'); ?> ">
            <span class="professor-card__name"><?php the_title(); ?></span>
          </a>
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
        get_template_part('template-parts/event');
      }
    }
    wp_reset_postdata(); // Restaura los datos globales del post después de la consulta personalizada.
    // get_field('related_campus'): Obtiene el valor del campo personalizado de ACF para ver en qué campus se dicta el programa actual.
    $relatedCampuses = get_field('related_campus');
    if ($relatedCampuses) {
      echo '<hr class="section-break">';
      echo '<h2 class="headline headline--medium">' . get_the_title() . ' is Available At these Campuses:</h2>';

      echo '<ul class="min-list link-list">';
      // Itera sobre los campus relacionados.
      foreach ($relatedCampuses as $campus) {
      ?>
        <!-- get_the_permalink() y get_the_title(): Enlazan al detalle del campus y muestran su título. -->
        <li><a href="<?php echo get_the_permalink($campus); ?>"><?php echo get_the_title($campus); ?></a></li>
    <?php }
      echo '</ul>';
    }
    ?>
  </div>
<?php }
// get_footer(): Incluye el archivo footer.php del tema.
get_footer();
?>