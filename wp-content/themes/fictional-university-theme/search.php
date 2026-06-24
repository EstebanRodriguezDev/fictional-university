<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();
pageBanner(array(
  'title' => 'Search Results',
  'subtitle' => 'You searched for &ldquo;' . esc_html(get_search_query()) . '&rdquo;',
));
?>
<div class="container container--narrow page-section">
  <?php
  // Se verifica primero con if (have_posts()) para poder mostrar un mensaje alternativo
  // cuando no hay resultados, en vez de simplemente no mostrar nada.
  if (have_posts()) {
    // Se usa el Loop estándar para aprovechar la consulta de búsqueda que WordPress
    // preparó automáticamente a partir del término enviado por el formulario.
    while (have_posts()) {
      the_post();
      // Se usa get_post_type() en el nombre de la plantilla para reutilizar las plantillas
      // existentes de cada tipo de contenido sin necesidad de duplicar código.
      get_template_part('template-parts/' . get_post_type());
    }
    // Se genera la paginación para que el usuario pueda navegar entre páginas de resultados
    // sin necesidad de construir la lógica de navegación manualmente.
    echo paginate_links();
  } else {
    echo '<h2 class="headline headline--small-plus">No results match that search.</h2>';
  }

  // Se muestra el formulario de búsqueda al final para que el usuario pueda
  // refinar su consulta sin tener que navegar a otra página.
  get_search_form();
  ?>
</div>

<?php
// Se incluye el footer para cerrar correctamente el HTML y cargar los scripts que
// WordPress y los plugins necesitan inyectar al final del body.
get_footer();
?>