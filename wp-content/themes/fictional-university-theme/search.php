<?php
// get_header(): Función de WordPress que busca e incluye el archivo header.php del tema.
get_header();
pageBanner(array(
 'title' => 'Search Results',
 'subtitle' => 'You searched for &ldquo;' . esc_html(get_search_query()) . '&rdquo;',
));
?>
<div class="container container--narrow page-section">
 <?php
 if (have_posts()) {
  // have_posts(): Función booleana que verifica si hay entradas (posts) disponibles en la base de datos para la consulta actual.
  // while(): Inicia el "Loop" (bucle) fundamental de WordPress.
  while (have_posts()) {
   // the_post(): Prepara los datos de la entrada actual (ID, título, autor, etc.) permitiendo que las funciones de plantilla funcionen correctamente.
   the_post();
   get_template_part('template-parts/' . get_post_type());
  }
  // paginate_links(): Genera automáticamente la numeración de páginas (1, 2, Siguiente...) para navegar por el archivo del blog.
  echo paginate_links();
 } else {
  echo '<h2 class="headline headline--small-plus">No results match that search.</h2>';
 }

 get_search_form();
 ?>
</div>

<?php
// get_footer(): Función de WordPress que busca e incluye el archivo footer.php del tema.
get_footer();
?>