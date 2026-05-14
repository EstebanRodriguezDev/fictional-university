<?php
// get_header(): Carga el archivo de cabecera (header.php) global de tu sitio.
get_header();
// pageBanner(): Carga el diseño del banner superior.
// the_archive_title() y the_archive_description() obtienen dinámicamente el título y descripción de la categoría o autor actual.
pageBanner(array(
  'title' => get_the_archive_title(),
  'subtitle' => get_the_archive_description(),
));
?>


<div class="container container--narrow page-section">
  <?php
  // have_posts() y while(): Estructura estándar del Loop. Itera sobre las entradas del blog que coincidan con la vista actual.
  while (have_posts()) {
    // the_post(): Carga los datos del post actual en memoria (título, autor, extracto).
    the_post();
  ?>
    <div class="post-item">
      <!-- the_permalink(): Genera el link al post. the_title(): Imprime el título del post. -->
      <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>
      <div class="metabox">
        <!-- the_author_posts_link(): Crea un enlace que lleva a ver todas las publicaciones de ese autor. -->
        <!-- the_time(): Imprime la fecha. El formato 'n.j.y' significa: mes (número), día y año (dos dígitos). -->
        <!-- get_the_category_list(): Devuelve una lista de las categorías a las que pertenece el post, separadas por comas. -->
        <p>Posted by <?php the_author_posts_link(); ?> on <?php the_time('n.j.y'); ?> in <?php echo get_the_category_list(', '); ?></p>
      </div>
      <div class="generic-content">
        <!-- the_excerpt(): Imprime un resumen corto del contenido para no mostrar todo el post en la lista. -->
        <?php the_excerpt(); ?>
        <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">Continue reading &raquo;</a></p>
      </div>
    </div>
  <?php }
  // paginate_links(): Genera los botones de navegación entre páginas (1, 2, Siguiente) automáticamente.
  echo paginate_links();
  ?>
</div>

<?php
// get_footer(): Carga el archivo de pie de página (footer.php).
get_footer();
?>