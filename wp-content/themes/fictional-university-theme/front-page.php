<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();
?>

<div class="page-banner">
  <!-- Se usa get_theme_file_uri() para referenciar la imagen dentro del tema y obtener
       una URL absoluta correcta, sin importar dónde esté instalado WordPress. -->
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('images/library-hero.jpg') ?>)"></div>
  <div class="page-banner__content container t-center c-white">
    <h1 class="headline headline--large">Welcome!</h1>
    <h2 class="headline headline--medium">We think you&rsquo;ll like it here.</h2>
    <h3 class="headline headline--small">Why don&rsquo;t you check out the <strong>major</strong> you&rsquo;re interested in?</h3>
    <!-- Se usa get_post_type_archive_link() para generar la URL del listado de programas de forma
         dinámica, evitando hardcodear una URL que podría cambiar. -->
    <a href="<?php echo get_post_type_archive_link('program'); ?>" class="btn btn--large btn--blue">Find Your Major</a>
  </div>
</div>

<div class="full-width-split group">
  <div class="full-width-split__one">
    <div class="full-width-split__inner">
      <h2 class="headline headline--small-plus t-center">Upcoming Events</h2>
      <?php
      $today = date('Ymd'); // Se obtiene la fecha en formato 'AñoMesDía' porque ACF almacena las fechas en ese formato numérico para poder comparar y ordenar correctamente.
      // Se usa WP_Query personalizado (en vez del Loop principal) para controlar exactamente
      // cuántos eventos mostrar (2) y filtrar solo los futuros, independientemente del contenido de la página.
      $homePageEvents = new WP_Query(array(
        'posts_per_page' => 2,
        'post_type' => 'event',
        'meta_key' => 'event_date',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'meta_query' => array(
          array(
            'key' => 'event_date',
            'compare' => '>=',
            'value' => $today,
            'type' => 'numeric'
          )
        )
      ));
      // Se itera sobre la consulta personalizada de eventos para mostrar solo
      // los próximos eventos en la página de inicio.
      while ($homePageEvents->have_posts()) {
        $homePageEvents->the_post();
        get_template_part('template-parts/event');
      }
      // Se llama wp_reset_postdata() para restaurar el contexto global del post
      // y evitar que las funciones de plantilla posteriores usen datos del evento en vez de la página.
      wp_reset_postdata();
      ?>
      <p class="t-center no-margin"><a href="<?php echo get_post_type_archive_link('event'); ?>" class="btn btn--blue">View All Events</a></p> <!-- Se usa get_post_type_archive_link() para que el botón siempre apunte al listado correcto sin hardcodear una URL. -->
    </div>
  </div>
  <div class="full-width-split__two">
    <div class="full-width-split__inner">
      <h2 class="headline headline--small-plus t-center">From Our Blogs</h2>
      <?php
      // Se usa WP_Query personalizado para controlar exactamente cuántos posts del blog
      // mostrar en el home (2), independientemente de la configuración global de WordPress.
      $homepagePost = new WP_Query(array(
        'posts_per_page' => 2, // Se limita a 2 posts para no sobrecargar visualmente la sección del home.
      ));
      while ($homepagePost->have_posts()) {
        $homepagePost->the_post();
      ?>
        <div class="event-summary">
          <a class="event-summary__date event-summary__date--beige t-center" href="<?php the_permalink(); ?>"> <!-- Se enlaza el bloque de fecha al post para que el usuario pueda acceder al artículo haciendo clic en la fecha. -->
            <span class="event-summary__month"><?php the_time('M'); ?></span> <!-- Se usa el formato 'M' para mostrar el mes abreviado (ej. Jan) y que ocupe poco espacio visual. -->
            <span class="event-summary__day"><?php the_time('d'); ?></span> <!-- Se usa el formato 'd' para mostrar el día con dos dígitos, consistente con el diseño de la tarjeta. -->
          </a>
          <div class="event-summary__content">
            <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5> <!-- Se enlaza el título al post para facilitar la navegación al artículo completo. -->
            <p><?php if (has_excerpt()) { // Se usa has_excerpt() para dar prioridad al resumen manual del editor por ser más preciso y descriptivo.
                  echo get_the_excerpt();
                } else echo wp_trim_words(get_the_content(), 18); // Se recorta a 18 palabras para mantener el diseño de la tarjeta uniforme cuando no hay extracto manual.
                ?><a href="<?php the_permalink(); ?>" class="nu gray">Read more</a></p>
          </div>
        </div>
      <?php }
      // Se llama wp_reset_postdata() para restaurar el contexto global del post
      // y evitar que las funciones de plantilla posteriores usen datos del blog en vez de la página.
      wp_reset_postdata();
      ?>
      <p class="t-center no-margin"><a href="<?php echo site_url('/blog'); ?>" class="btn btn--yellow">View All Blog Posts</a></p> <!-- Se usa site_url('/blog') para que el botón siempre apunte a la URL correcta sin importar el dominio. -->
    </div>
  </div>
</div>

<div class="hero-slider">
  <div data-glide-el="track" class="glide__track">
    <div class="glide__slides">
      <div class="hero-slider__slide" style="background-image: url(<?php echo get_theme_file_uri('images/bus.jpg'); ?>)">
        <div class="hero-slider__interior container">
          <div class="hero-slider__overlay">
            <h2 class="headline headline--medium t-center">Free Transportation</h2>
            <p class="t-center">All students have free unlimited bus fare.</p>
            <p class="t-center no-margin"><a href="#" class="btn btn--blue">Learn more</a></p>
          </div>
        </div>
      </div>
      <div class="hero-slider__slide" style="background-image: url(<?php echo get_theme_file_uri('images/apples.jpg'); ?>)">
        <div class="hero-slider__interior container">
          <div class="hero-slider__overlay">
            <h2 class="headline headline--medium t-center">An Apple a Day</h2>
            <p class="t-center">Our dentistry program recommends eating apples.</p>
            <p class="t-center no-margin"><a href="#" class="btn btn--blue">Learn more</a></p>
          </div>
        </div>
      </div>
      <div class="hero-slider__slide" style="background-image: url(<?php echo get_theme_file_uri('images/bread.jpg'); ?>)">
        <div class="hero-slider__interior container">
          <div class="hero-slider__overlay">
            <h2 class="headline headline--medium t-center">Free Food</h2>
            <p class="t-center">Fictional University offers lunch plans for those in need.</p>
            <p class="t-center no-margin"><a href="#" class="btn btn--blue">Learn more</a></p>
          </div>
        </div>
      </div>
    </div>
    <div class="slider__bullets glide__bullets" data-glide-el="controls[nav]"></div>
  </div>
</div>

<?php
// Se incluye el footer para cerrar correctamente el HTML y cargar los scripts que
// WordPress y los plugins necesitan inyectar al final del body.
get_footer();
?>