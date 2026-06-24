<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();
// Se usa pageBanner() con textos fijos porque esta es la página de listado de todos los eventos,
// no una página individual, por lo que no tiene un título dinámico propio en la base de datos.
pageBanner(array(
  'title' => 'All Events',
  'subtitle' => 'See what is going on ir our world',
));
?>

<div class="container container--narrow page-section">
  <?php
  // Se usa el Loop estándar de WordPress (have_posts + the_post) para aprovechar
  // la consulta principal que WordPress ya preparó automáticamente para este archivo.
  while (have_posts()) {
    the_post();
    get_template_part('template-parts/event');
  }
  // Se genera la paginación para que el usuario pueda navegar entre páginas de resultados
  // sin necesidad de construir la lógica de navegación manualmente.
  echo paginate_links();
  ?>
  <hr class="section-break">
  <!-- Se enlaza a la página de eventos pasados usando site_url para que la URL sea
       siempre correcta independientemente del dominio o subdirectorio donde esté instalado WordPress. -->
  <p>Looking for a recap of past events? <a href="<?php echo site_url('/past-events') ?>">Check out our past events archive.</a></p>
</div>

<?php
// Se incluye el footer para cerrar correctamente el HTML y cargar los scripts que
// WordPress y los plugins necesitan inyectar al final del body.
get_footer();
?>