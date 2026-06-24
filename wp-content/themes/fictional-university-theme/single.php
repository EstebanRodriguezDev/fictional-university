<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();

// Se usa el Loop de WordPress para poder acceder a los datos del post actual
// (título, contenido, etc.) y pasarlos a pageBanner() y the_content().
while (have_posts()) {
  the_post();
  // Se llama pageBanner() sin argumentos para que use el título de la entrada actual,
  // manteniendo el diseño consistente con el resto del sitio.
  pageBanner();
?>


  <div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p>
        <!-- Se usa site_url('/blog') para generar la URL del blog de forma dinámica,
             evitando hardcodear una URL que podría cambiar si se mueve el sitio. -->
        <a class="metabox__blog-home-link" href="<?php echo site_url('/blog'); ?>"><i class="fa fa-home" aria-hidden="true"></i> Blog Home</a>
        <span class="metabox__main">
          <!-- Se muestran el autor, la fecha y las categorías para dar contexto editorial
               al lector y facilitar la navegación hacia contenido relacionado. -->
          Posted by <?php the_author_posts_link(); ?> on <?php the_time('n.j.y'); ?> in <?php echo get_the_category_list(', '); ?>
        </span>
      </p>
    </div>
    <!-- Se usa the_content() para renderizar el contenido del editor de WordPress,
         permitiendo al editor enriquecer la entrada sin tocar el código de la plantilla. -->
    <div class="generic-content"><?php the_content(); ?></div>
  </div>
<?php }
// Se incluye el footer para cerrar correctamente el HTML y cargar los scripts que
// WordPress y los plugins necesitan inyectar al final del body.
get_footer();
?>