<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();
// Se usa el Loop de WordPress para poder acceder a los datos del programa actual
// (título, imagen de banner, contenido, campos ACF) en el contexto correcto.
while (have_posts()) {
  the_post();
  // Se llama pageBanner() sin argumentos para que detecte automáticamente el título del programa
  // y su imagen de fondo configurada en ACF, sin necesidad de pasarlos explícitamente.
  pageBanner();
?>

  <div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p>
        <!-- Se usa get_post_type_archive_link() para generar el enlace de regreso al listado de programas
             de forma dinámica, sin hardcodear una URL que podría cambiar. -->
        <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program'); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Programs</a>
        <!-- Se muestra el título del programa para que el usuario sepa en qué carrera está sin mirar el banner. -->
        <span class="metabox__main"><?php the_title(); ?></span>
      </p>
    </div>
    <!-- Se usa the_content() para renderizar el contenido del editor de WordPress,
         permitiendo al editor enriquecer la descripción del programa sin tocar el código. -->
    <div class="generic-content"><?php the_content(); ?></div>
    <?php
    // Se usa WP_Query personalizado para buscar profesores cuyo campo ACF 'related_programs'
    // contenga el ID de este programa, ya que el Loop principal solo tiene el programa actual.
    $relatedProfessors = new WP_Query(array(
      'posts_per_page' => -1,
      'post_type' => 'professor',
      'orderby' => 'title',
      'order' => 'ASC',
      'meta_query' => array( // Se usa meta_query para filtrar profesores basándose en el campo relacional de ACF.
        array(
          'key' => 'related_programs',
          'compare' => 'LIKE',
          // Se envuelve el ID en comillas dobles para buscar la coincidencia exacta del ID
          // en el array serializado por ACF, evitando falsos positivos con IDs similares (ej. "1" en "12").
          'value' => '"' . get_the_ID() . '"'
        )
      )
    ));

    // Se muestra la sección de profesores solo si hay resultados para no dejar
    // una sección vacía que confundiría al visitante.
    if ($relatedProfessors->have_posts()) {
      echo '<hr class="section-break">';
      echo '<h2 class="headline headline--medium">Upcoming ' . get_the_title() . ' Events</h2>';
      echo '<ul class="professor-cards">';
      while ($relatedProfessors->have_posts()) {
        $relatedProfessors->the_post();
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
    // Se llama wp_reset_postdata() para restaurar el contexto global del post
    // y evitar que funciones posteriores usen datos del profesor en vez del programa.
    wp_reset_postdata();

    // Se obtiene la fecha en formato 'AñoMesDía' porque ACF almacena las fechas en ese
    // formato numérico para poder comparar y ordenar correctamente.
    $today = date('Ymd');
    // Se usa WP_Query personalizado para buscar eventos futuros relacionados con este programa,
    // filtrando por fecha y por el campo relacional de ACF, sin afectar la consulta principal.
    $homePageEvents = new WP_Query(array(
      'posts_per_page' => 2, // Se limita a 2 eventos para no sobrecargar visualmente la sección del programa.
      'post_type' => 'event',
      'meta_key' => 'event_date', // Se indica el campo de fecha para que WordPress lo use como criterio de ordenamiento.
      'orderby' => 'meta_value_num', // Se ordena numéricamente porque la fecha está guardada como número (ej. 20241230) en ACF.
      'order' => 'ASC', // Se muestra el evento más próximo primero para que sea lo más útil posible para el usuario.
      'meta_query' => array( // Se usa un array de condiciones para combinar el filtro de fecha futura con el de programa relacionado.
        array(
          'key' => 'event_date',
          'compare' => '>=', // Se compara con '>=' para traer solo eventos de hoy en adelante.
          'value' => $today,
          'type' => 'numeric' // Se indica tipo numérico para que la comparación de fechas funcione correctamente.
        ),
        array(
          'key' => 'related_programs',
          'compare' => 'LIKE',
          'value' => '"' . get_the_ID() . '"' // ID envuelto para coincidencia exacta en el array serializado de ACF.
        )
      )
    ));

    // Se muestra la sección de eventos solo si hay resultados para no dejar
    // una sección vacía que confundiría al visitante.
    if ($homePageEvents->have_posts()) {
      echo '<hr class="section-break">';
      echo '<h2 class="headline headline--medium">Upcoming ' . get_the_title() . ' Event</h2>';
      while ($homePageEvents->have_posts()) {
        $homePageEvents->the_post();
        get_template_part('template-parts/event');
      }
    }
    // Se llama wp_reset_postdata() para restaurar el contexto global del post
    // y evitar que funciones posteriores usen datos del evento en vez del programa.
    wp_reset_postdata();
    // Se obtienen los campus relacionados desde ACF para mostrar dónde se puede estudiar
    // este programa, ayudando al estudiante a elegir según su ubicación.
    $relatedCampuses = get_field('related_campus');
    if ($relatedCampuses) {
      echo '<hr class="section-break">';
      echo '<h2 class="headline headline--medium">' . get_the_title() . ' is Available At these Campuses:</h2>';

      echo '<ul class="min-list link-list">';
      // Se itera sobre los campus relacionados para generar el enlace de cada uno,
      // permitiendo al visitante navegar directamente al campus de su interés.
      foreach ($relatedCampuses as $campus) {
      ?>
        <!-- Se pasan los objetos $campus directamente a get_the_permalink() y get_the_title()
             para obtener el enlace y título sin necesidad de estar dentro del Loop del campus. -->
        <li><a href="<?php echo get_the_permalink($campus); ?>"><?php echo get_the_title($campus); ?></a></li>
    <?php }
      echo '</ul>';
    }
    ?>
  </div>
<?php }
// Se incluye el footer para cerrar correctamente el HTML y cargar los scripts que
// WordPress y los plugins necesitan inyectar al final del body.
get_footer();
?>