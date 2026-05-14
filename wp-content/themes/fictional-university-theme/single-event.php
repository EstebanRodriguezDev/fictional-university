<?php
// get_header(): Función de WordPress que busca e incluye el archivo header.php del tema.
get_header();

// Inicia el bucle (Loop) de WordPress para mostrar el contenido del evento actual.
while (have_posts()) {
  the_post(); // Prepara los datos del post para que las funciones de plantilla funcionen correctamente.
  // Llama a la función pageBanner() sin argumentos. 
  // Al estar dentro del Loop, detectará automáticamente el título del evento y cualquier imagen de fondo configurada en ACF.
  pageBanner();
?>
  <div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p>
        <!-- get_post_type_archive_link('event'): Obtiene la URL de la página de archivo de eventos. -->
        <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('event'); ?>"><i class="fa fa-home" aria-hidden="true"></i> Events Home</a>
        <span class="metabox__main"><?php the_title(); ?></span>
      </p>
    </div>
    <!-- the_content(): Imprime el contenido principal del editor de WordPress para este evento. -->
    <div class="generic-content"><?php the_content(); ?></div>
    <?php
    // get_field('related_programs'): Obtiene el valor del campo personalizado de ACF 'related_programs'.
    $relatedPrograms = get_field('related_programs');

    if ($relatedPrograms) { // Verifica si existen programas relacionados.
      echo '<hr class="section-break">';
      echo '<h2 class="headline headline--medium"> Related Program(s)</h2>';
      echo '<ul class="link-list min-list">';
      // Itera sobre cada programa relacionado.
      foreach ($relatedPrograms as $program) { ?>
        <!-- get_the_permalink() y get_the_title(): Obtienen el enlace y el título de un post específico por su ID o objeto. -->
        <li><a href="<?php echo get_the_permalink($program); ?>"><?php echo get_the_title($program); ?></a></li>
    <?php }
      echo '</ul>';
    }
    ?>
  </div>
<?php }
// get_footer(): Función de WordPress que busca e incluye el archivo footer.php del tema.
get_footer();
