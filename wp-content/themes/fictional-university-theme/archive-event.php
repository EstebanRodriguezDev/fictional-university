<?php
// get_header(): Función de WordPress que busca e incluye el archivo header.php de tu tema.
get_header();
// Llama a la función pageBanner definida en functions.php para mostrar el banner de cabecera.
// Le pasa un arreglo con el título y subtítulo fijos para el listado general de eventos.
pageBanner(array(
  'title' => 'All Events',
  'subtitle' => 'See what is going on ir our world',
));
?>

<div class="container container--narrow page-section">
  <?php
  // have_posts(): Función booleana que comprueba si la consulta actual tiene resultados (eventos) en la base de datos.
  // while(): Estructura de control que inicia el "Loop" de WordPress.
  while (have_posts()) {
    // the_post(): Prepara los datos del post actual (título, autor, campos, etc.) para que las funciones de plantilla puedan usarlos.
    the_post();
    get_template_part('template-parts/event');
  }
  // paginate_links(): Genera automáticamente la navegación por páginas (1, 2, 3, Siguiente...) si hay muchos resultados.
  echo paginate_links();
  ?>
  <hr class="section-break">
  <!-- site_url(): Devuelve la dirección URL de la raíz de tu sitio web. -->
  <p>Looking for a recap of past events? <a href="<?php echo site_url('/past-events') ?>">Check out our past events archive.</a></p>
</div>

<?php
// get_footer(): Función de WordPress que busca e incluye el archivo footer.php.
get_footer();
?>