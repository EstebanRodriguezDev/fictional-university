<?php
// get_header(): Incluye el archivo header.php del tema, que contiene la cabecera HTML y navegación.
get_header();

// Inicia el bucle (Loop) de WordPress. have_posts() verifica si hay una entrada para mostrar.
while (have_posts()) {
  the_post(); // Prepara los datos de la entrada actual (título, contenido, autor, etc.).
  // Muestra el banner superior dinámico. Al estar sin argumentos, usará el título de la entrada actual.
  pageBanner();
?>


  <div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p>
        <!-- site_url('/blog'): Genera la URL dinámica hacia la página principal del blog. -->
        <a class="metabox__blog-home-link" href="<?php echo site_url('/blog'); ?>"><i class="fa fa-home" aria-hidden="true"></i> Blog Home</a>
        <span class="metabox__main">
          <!-- the_author_posts_link(): Enlace a los posts del autor. the_time(): Fecha formateada. get_the_category_list(): Lista de categorías. -->
          Posted by <?php the_author_posts_link(); ?> on <?php the_time('n.j.y'); ?> in <?php echo get_the_category_list(', '); ?>
        </span>
      </p>
    </div>
    <!-- the_content(): Imprime el contenido completo de la entrada redactado en el editor. -->
    <div class="generic-content"><?php the_content(); ?></div>
  </div>
<?php }
// get_footer(): Incluye el archivo footer.php del tema.
get_footer();
?>