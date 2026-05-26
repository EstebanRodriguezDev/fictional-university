<div class="event-summary">
  <a class="event-summary__date t-center" href="#">
    <span class="event-summary__month">
      <?php
      // DateTime: Clase nativa de PHP para formatear fechas. 
      // get_field: Función de ACF para obtener el valor de un campo personalizado.
      // get_field('event_date'): Obtiene el valor del campo personalizado 'event_date' para el post actual.
      $eventDate = new DateTime(get_field('event_date'));
      echo $eventDate->format('M'); // format('M'): Formatea la fecha para mostrar la abreviatura del mes (ej. Jan, Feb).
      ?>
    </span>
    <span class="event-summary__day"><?php echo $eventDate->format('d'); ?></span> <!-- format('d'): Formatea la fecha para mostrar el día del mes (ej. 01, 30). -->
  </a>
  <div class="event-summary__content">
    <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5> <!-- the_permalink(): Imprime la URL permanente del post actual. the_title(): Imprime el título del post actual. -->
    <!-- has_excerpt: Verifica si el post tiene un resumen manual. 
       wp_trim_words: Corta el contenido a un número exacto de palabras. -->
    <p>
      <?php
      if (has_excerpt()) { // has_excerpt(): Condición que devuelve true si el post actual tiene un extracto definido manualmente.
        echo get_the_excerpt(); // get_the_excerpt(): Devuelve el extracto del post actual.
      } else {
        echo wp_trim_words(get_the_content(), 18); // wp_trim_words(): Recorta una cadena a un número específico de palabras. get_the_content(): Devuelve el contenido completo del post actual.
      }
      ?>
      <a href="<?php the_permalink(); ?>" class="nu gray">Learn more</a>
    </p>
  </div>
</div>