<?php
// get_header(): Incluye el archivo header.php del tema.
get_header();

// Inicia el bucle (Loop) de WordPress para procesar el contenido del campus actual.
while (have_posts()) {
  the_post(); // Prepara los datos del post (título, contenido, etc.).
  // Despliega el banner superior. Al ser un campus, detectará su título y su imagen de fondo (ACF).
  pageBanner();
?>

  <div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p>
        <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('campus'); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Campuses</a>
        <span class="metabox__main"><?php the_title(); ?></span>
      </p>
    </div>

    <!-- the_content(): Imprime el contenido principal del campus, tal como se introduce en el editor de WordPress. -->
    <div class="generic-content"><?php the_content(); ?></div>
    <div class="acf-map">
      <?php 
      // get_field('map_location'): Obtiene el valor del campo personalizado de mapa de Google (ACF).
      $mapLocation = get_field('map_location'); 
      ?>
      <!-- Se usa data-lat y data-lng para pasar la latitud y longitud a JavaScript para pintar el marcador en el mapa. -->
      <div class="marker"
        data-lat="<?php echo $mapLocation['lat']; ?>" data-lng="<?php echo $mapLocation['lng']; ?>">
        <h3>
          <!-- the_title(): Imprime el nombre del campus en el marcador del mapa. -->
          <?php the_title(); ?>
        </h3>
        <!-- Muestra la dirección formateada obtenida de los datos del mapa de ACF. -->
        <p><?php echo $mapLocation['address']; ?></p>
      </div>
    </div>
    <?php
    $relatedPrograms = new WP_Query(array(
      'posts_per_page' => -1, // Muestra todos los posts que coincidan.
      'post_type' => 'program', // Busca posts del tipo 'program' para relacionarlos con el campus actual.
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
    ?>
  </div>
<?php }
// get_footer(): Incluye el archivo footer.php del tema.
get_footer();
?>