<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();
// Se usan get_the_archive_title() y get_the_archive_description() de forma dinámica
// porque este archivo sirve para CUALQUIER categoría o autor, sin título fijo.
pageBanner(array(
  'title' => get_the_archive_title(),
  'subtitle' => get_the_archive_description(),
));
?>


<div class="container container--narrow page-section">
  <?php
  // Se usa el Loop estándar de WordPress para aprovechar la consulta principal
  // que WordPress ya preparó automáticamente para este archivo de entradas del blog.
  while (have_posts()) {
    the_post();
  ?>
    <div class="post-item">
      <!-- Se enlaza el título al post completo para que el usuario pueda acceder
           al artículo desde el listado sin necesidad de un botón extra. -->
      <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>
      <div class="metabox">
        <!-- Se muestra el autor, la fecha y las categorías para dar contexto editorial
             al lector y permitirle filtrar por autor o categoría si lo desea. -->
        <p>Posted by <?php the_author_posts_link(); ?> on <?php the_time('n.j.y'); ?> in <?php echo get_the_category_list(', '); ?></p>
      </div>
      <div class="generic-content">
        <!-- Se muestra solo el extracto (en vez del contenido completo) para que el listado
             sea liviano y el usuario decida si quiere leer el artículo completo. -->
        <?php the_excerpt(); ?>
        <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">Continue reading &raquo;</a></p>
      </div>
    </div>
  <?php }
  // Se genera la paginación para que el usuario pueda navegar entre páginas de resultados
  // sin necesidad de construir la lógica de navegación manualmente.
  echo paginate_links();
  ?>
</div>

<?php
// Se incluye el footer para cerrar correctamente el HTML y cargar los scripts que
// WordPress y los plugins necesitan inyectar al final del body.
get_footer();
?>