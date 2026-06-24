<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();
pageBanner(array(
  'title' => 'Welcome to our blog!',
  'subtitle' => 'keep ip with our latest news',
));
?>

<div class="container container--narrow page-section">
  <?php
  // Se usa el Loop estándar de WordPress para aprovechar la consulta principal
  // que WordPress ya preparó automáticamente para el archivo del blog.
  while (have_posts()) {
    the_post();
  ?>
    <div class="post-item">
      <!-- Se enlaza el título al post completo para que el usuario pueda acceder
           al artículo desde el listado sin necesidad de un botón extra. -->
      <!-- Se muestra el título como enlace para dar una forma clara e intuitiva de navegar al artículo. -->
      <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>
      <div class="metabox">
        <!-- Se muestra el autor como enlace para que el lector pueda explorar
             otros artículos del mismo autor con un solo clic. -->
        <!-- Se usa el formato 'n.j.y' para una fecha compacta (mes, día, año) consistente
             con el estilo visual del resto del sitio. -->
        <!-- Se listan las categorías para que el lector pueda filtrar contenido
             por tema sin necesidad de buscador. -->
        <p>Posted by <?php the_author_posts_link(); ?> on <?php the_time('n.j.y'); ?> in <?php echo get_the_category_list(', '); ?></p>
      </div>
      <div class="generic-content">
        <!-- Se usa el extracto en vez del contenido completo para que el listado sea liviano
             y el usuario decida si quiere leer el artículo completo. -->
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