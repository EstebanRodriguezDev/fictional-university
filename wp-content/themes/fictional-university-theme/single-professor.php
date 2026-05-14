<?php
// get_header(): Incluye el archivo header.php del tema.
get_header();

// Inicia el bucle (Loop) de WordPress para procesar el contenido del profesor actual.
while (have_posts()) {
  the_post(); // Prepara los datos del post (título, contenido, etc.).
  // Despliega el banner superior. Al ser un profesor, detectará su título y su imagen de fondo (ACF).
  pageBanner();
?>
  <div class="container container--narrow page-section">
    <!-- the_content(): Imprime la biografía o descripción principal del profesor. -->
    <div class="generic-content">
      <div class="row group">
        <div class="one-third">
          <?php the_post_thumbnail('professorPortrait');  ?>
        </div>
        <div class="two-thirds">
          <?php the_content(); ?>
        </div>
      </div>
    </div>
    <?php
    // get_field(): Obtiene el campo personalizado de relación de ACF 'related_programs'.
    $relatedPrograms = get_field('related_programs');

    if ($relatedPrograms) { // Solo muestra la sección si hay programas asignados.
      echo '<hr class="section-break">';
      echo '<h2 class="headline headline--medium"> Subject(s) Taught</h2>';
      echo '<ul class="link-list min-list">';
      // Itera sobre el array de objetos de post retornados por ACF.
      foreach ($relatedPrograms as $program) { ?>
        <li><a href="<?php echo get_the_permalink($program); ?>"><?php echo get_the_title($program); ?></a></li>
    <?php }
      echo '</ul>';
    }
    ?>
  </div>
<?php }
// get_footer(): Incluye el archivo footer.php del tema.
get_footer();
