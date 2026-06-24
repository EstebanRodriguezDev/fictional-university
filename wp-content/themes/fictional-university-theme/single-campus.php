<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();

// Se usa el Loop de WordPress para poder acceder a los datos del campus actual
// (título, imagen de banner, contenido, campos ACF) en el contexto correcto.
while (have_posts()) {
  the_post();
  // Se llama pageBanner() sin argumentos para que detecte automáticamente el título del campus
  // y su imagen de fondo configurada en ACF, sin necesidad de pasarlos explícitamente.
  pageBanner();
?>

  <div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p>
        <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('campus'); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Campuses</a>
        <span class="metabox__main"><?php the_title(); ?></span>
      </p>
    </div>

    <!-- Se usa the_content() para renderizar el contenido del editor de WordPress,
         permitiendo al editor añadir descripción del campus sin tocar el código de la plantilla. -->
    <div class="generic-content"><?php the_content(); ?></div>
    <div class="acf-map">
      <?php 
      // Se obtiene el campo de mapa de ACF para extraer las coordenadas (lat/lng)
      // y pasarlas al script de Google Maps como atributos data-* del marcador.
      $mapLocation = get_field('map_location'); 
      ?>
      <!-- Se usan data-lat y data-lng en el HTML para que el script de JavaScript
           pueda leer las coordenadas y pintar el marcador sin necesidad de una petición extra. -->
      <div class="marker"
        data-lat="<?php echo $mapLocation['lat']; ?>" data-lng="<?php echo $mapLocation['lng']; ?>">
        <h3>
          <!-- Se muestra el título del campus en el popup del marcador para que el usuario
               sepa de qué campus es el marcador al hacer clic en él. -->
          <?php the_title(); ?>
        </h3>
        <!-- Se muestra la dirección del campus desde ACF para que el usuario pueda
             identificar físicamente la ubicación del campus en el mapa. -->
        <p><?php echo $mapLocation['address']; ?></p>
      </div>
    </div>
    <?php
    // Se usa WP_Query personalizado para obtener los programas relacionados con este campus,
    // ya que el Loop principal solo contiene el campus, no sus programas asociados.
    $relatedPrograms = new WP_Query(array(
      'posts_per_page' => -1,
      'post_type' => 'program',
      'orderby' => 'title',
      'order' => 'ASC',
      'meta_query' => array( // Se usa meta_query para buscar programas cuyo campo ACF 'related_campus' contenga el ID de este campus.
        array(
          'key' => 'related_campus',
          'compare' => 'LIKE',
          // Se envuelve el ID en comillas dobles para buscar la coincidencia exacta del ID
          // en el array serializado por ACF, evitando falsos positivos con IDs similares (ej. "1" en "12").
          'value' => '"' . get_the_ID() . '"'
        )
      )
    ));

    // Se muestra la sección de programas solo si hay resultados para no dejar
    // una sección vacía que confundiría al visitante.
    if ($relatedPrograms->have_posts()) {
      echo '<hr class="section-break">'; // Se usa un separador visual para distinguir la sección de programas del contenido principal del campus.
      echo '<h2 class="headline headline--medium">Programs Available At this Campus</h2>';
      echo '<ul class="min-list link-list">';
      while ($relatedPrograms->have_posts()) {
        $relatedPrograms->the_post();
    ?>
        <li>
          <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </li>
    <?php }
      echo '</ul>';
    }
    // Se llama wp_reset_postdata() para restaurar el contexto global del post
    // y evitar que funciones posteriores usen datos del programa en vez del campus.
    wp_reset_postdata();
    ?>
  </div>
<?php }
// Se incluye el footer para cerrar correctamente el HTML y cargar los scripts que
// WordPress y los plugins necesitan inyectar al final del body.
get_footer();
?>