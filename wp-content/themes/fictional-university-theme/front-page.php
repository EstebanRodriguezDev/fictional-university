<?php
// get_header(): Función de WordPress que incluye el archivo 'header.php' del tema.
// Es esencial para cargar la cabecera, metadatos, estilos y scripts.
get_header();
?>

<div class="page-banner">
  <!-- get_theme_file_uri('images/library-hero.jpg'): Obtiene la URL completa de un archivo dentro del directorio del tema.
       En este caso, se usa para cargar una imagen de fondo. -->
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('images/library-hero.jpg') ?>)"></div>
  <div class="page-banner__content container t-center c-white">
    <h1 class="headline headline--large">Welcome!</h1>
    <h2 class="headline headline--medium">We think you&rsquo;ll like it here.</h2>
    <h3 class="headline headline--small">Why don&rsquo;t you check out the <strong>major</strong> you&rsquo;re interested in?</h3>
    <!-- get_post_type_archive_link('program'): Devuelve la URL del archivo (página de listado) para el tipo de post personalizado 'program'. -->
    <a href="<?php echo get_post_type_archive_link('program'); ?>" class="btn btn--large btn--blue">Find Your Major</a>
  </div>
</div>

<div class="full-width-split group">
  <div class="full-width-split__one">
    <div class="full-width-split__inner">
      <h2 class="headline headline--small-plus t-center">Upcoming Events</h2>
      <?php
      $today = date('Ymd'); // date('Ymd'): Función de PHP para obtener la fecha actual en formato 'AñoMesDía' (ej. 20240130).
      // WP_Query: Una "pregunta" personalizada a la base de datos.
      // Aquí pedimos: 2 posts, de tipo 'event', ordenados por el campo 'event_date'.
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
      // while ($homePageEvents->have_posts()): Inicia el "Loop" de WordPress para la consulta personalizada.
      // have_posts(): Método que devuelve true si hay posts disponibles para la consulta.
      while ($homePageEvents->have_posts()) { // Inicia el bucle para mostrar los eventos.
        $homePageEvents->the_post(); // the_post(): Prepara los datos del post actual para ser usados por las funciones de plantilla.
        get_template_part('template-parts/event');
      }
      wp_reset_postdata(); // wp_reset_postdata(): Restaura los datos globales del post a la consulta principal de WordPress. Es crucial después de usar WP_Query.
      ?>
      <p class="t-center no-margin"><a href="<?php echo get_post_type_archive_link('event'); ?>" class="btn btn--blue">View All Events</a></p> <!-- get_post_type_archive_link('event'): Devuelve la URL del archivo para el tipo de post 'event'. -->
    </div>
  </div>
  <div class="full-width-split__two">
    <div class="full-width-split__inner">
      <h2 class="headline headline--small-plus t-center">From Our Blogs</h2>
      <?php
      // new WP_Query(): Crea una nueva instancia de la clase WP_Query para una consulta personalizada.
      $homepagePost = new WP_Query(array(
        'posts_per_page' => 2, // 'posts_per_page': Muestra solo 2 posts. Por defecto, el tipo de post es 'post' (entradas de blog).
      ));
      while ($homepagePost->have_posts()) { // Inicia el Loop para la consulta de posts de blog.
        $homepagePost->the_post(); // Prepara los datos del post actual.
      ?>
        <div class="event-summary">
          <a class="event-summary__date event-summary__date--beige t-center" href="<?php the_permalink(); ?>"> <!-- the_permalink(): Imprime la URL permanente del post actual. -->
            <span class="event-summary__month"><?php the_time('M'); ?></span> <!-- the_time('M'): Imprime el mes abreviado del post actual. -->
            <span class="event-summary__day"><?php the_time('d'); ?></span> <!-- the_time('d'): Imprime el día del mes del post actual. -->
          </a>
          <div class="event-summary__content">
            <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5> <!-- the_title(): Imprime el título del post actual. -->
            <p><?php if (has_excerpt()) { // has_excerpt(): Condición para verificar si el post tiene un extracto manual.
                  echo get_the_excerpt(); // get_the_excerpt(): Devuelve el extracto del post.
                } else echo wp_trim_words(get_the_content(), 18); // wp_trim_words(): Recorta el contenido a 18 palabras. get_the_content(): Devuelve el contenido completo del post.
                ?><a href="<?php the_permalink(); ?>" class="nu gray">Read more</a></p>
          </div>
        </div>
      <?php }
      wp_reset_postdata(); // Restaura los datos globales del post después de la consulta personalizada.
      ?>
      <p class="t-center no-margin"><a href="<?php echo site_url('/blog'); ?>" class="btn btn--yellow">View All Blog Posts</a></p> <!-- site_url('/blog'): Devuelve la URL del sitio con el slug '/blog' adjunto. -->
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
// get_footer(): Función de WordPress que incluye el archivo 'footer.php' del tema.
// Es esencial para cargar el pie de página y scripts finales.
get_footer();
?>