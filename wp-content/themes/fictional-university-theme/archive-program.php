<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();
// Se usa pageBanner() con textos fijos porque esta es la página de listado de todos los programas,
// no una página individual, por lo que no tiene un título dinámico propio en la base de datos.
pageBanner(array(
  'title' => 'All Programs',
  'subtitle' => 'There is something for everyone. Have a look around.',
));
?>

<div class="container container--narrow page-section">
  <ul class="link-list min-list">
    <?php
    // Se usa el Loop estándar de WordPress para aprovechar la consulta principal
    // que WordPress ya preparó automáticamente para este archivo de tipo 'program'.
    while (have_posts()) {
      the_post();
    ?>
      <!-- Se enlaza al detalle de cada programa para que el usuario pueda navegar
           directamente a la información completa de cada carrera desde esta lista. -->
      <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
    <?php }
    // Se genera la paginación para que el usuario pueda navegar entre páginas de resultados
    // sin necesidad de construir la lógica de navegación manualmente.
    echo paginate_links();
    ?>
  </ul>
</div>

<?php
// Se incluye el footer para cerrar correctamente el HTML y cargar los scripts que
// WordPress y los plugins necesitan inyectar al final del body.
get_footer();
?>