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
  ?>
    <div class="event-summary">
      <a class="event-summary__date t-center" href="#">
        <span class="event-summary__month">
          <?php
          // get_field('event_date'): Función del plugin Advanced Custom Fields (ACF). 
          // Extrae el valor guardado en el campo personalizado 'event_date'.
          // new DateTime(): Clase nativa de PHP para crear un objeto de fecha y hora.
          $eventDate = new DateTime(get_field('event_date'));
          // format('M'): Método de la clase DateTime que devuelve el mes en formato de 3 letras (Jan, Feb, etc.).
          echo $eventDate->format('M');
          ?>
        </span>
        <!-- format('d'): Devuelve el día del mes con ceros iniciales (01 al 31). -->
        <span class="event-summary__day"><?php echo $eventDate->format('d'); ?></span>
      </a>
      <div class="event-summary__content">
        <!-- the_permalink(): Imprime la URL única de la entrada actual en el bucle. -->
        <!-- the_title(): Imprime el título del evento actual. -->
        <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
        <!-- get_the_content(): Recupera el contenido del editor de WP sin imprimirlo (para poder procesarlo). -->
        <!-- wp_trim_words(): Recorta un texto a un número exacto de palabras (en este caso 18). -->
        <p><?php echo wp_trim_words(get_the_content(), 18); ?> <a href="<?php the_permalink(); ?>" class="nu gray">Learn more</a></p>
      </div>
    </div>
  <?php }
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