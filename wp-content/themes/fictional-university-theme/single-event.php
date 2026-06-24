<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();

// Se usa el Loop de WordPress para poder acceder a los datos del evento actual
// (título, imagen de banner, contenido, campos ACF) en el contexto correcto.
while (have_posts()) {
  the_post();
  // Se llama pageBanner() sin argumentos para que detecte automáticamente el título del evento
  // y su imagen de fondo configurada en ACF, sin necesidad de pasarlos explícitamente.
  pageBanner();
?>
  <div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p>
        <!-- Se usa get_post_type_archive_link() para generar el enlace de regreso al listado de eventos
             de forma dinámica, sin hardcodear una URL que podría cambiar. -->
        <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('event'); ?>"><i class="fa fa-home" aria-hidden="true"></i> Events Home</a>
        <span class="metabox__main"><?php the_title(); ?></span>
      </p>
    </div>
    <!-- Se usa the_content() para renderizar el contenido del editor de WordPress,
         permitiendo al editor añadir descripción del evento sin tocar el código de la plantilla. -->
    <div class="generic-content"><?php the_content(); ?></div>
    <?php
    // Se obtienen los programas relacionados desde ACF para mostrar en qué carreras
    // es relevante este evento, ayudando al estudiante a contextualizar su utilidad.
    $relatedPrograms = get_field('related_programs');

    // Se muestra la sección de programas solo si hay resultados para no dejar
    // una sección vacía que confundiría al visitante.
    if ($relatedPrograms) {
      echo '<hr class="section-break">';
      echo '<h2 class="headline headline--medium"> Related Program(s)</h2>';
      echo '<ul class="link-list min-list">';
      // Se itera sobre cada programa relacionado para generar su enlace individual,
      // permitiendo al visitante navegar directamente al programa de su interés.
      foreach ($relatedPrograms as $program) { ?>
        <!-- Se pasan los objetos $program directamente a get_the_permalink() y get_the_title()
             para obtener el enlace y título sin necesidad de estar dentro del Loop del programa. -->
        <li><a href="<?php echo get_the_permalink($program); ?>"><?php echo get_the_title($program); ?></a></li>
    <?php }
      echo '</ul>';
    }
    ?>
  </div>
<?php }
// Se incluye el footer para cerrar correctamente el HTML y cargar los scripts que
// WordPress y los plugins necesitan inyectar al final del body.
get_footer();
