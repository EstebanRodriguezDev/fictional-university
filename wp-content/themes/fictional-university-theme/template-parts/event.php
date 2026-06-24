<div class="event-summary">
  <a class="event-summary__date t-center" href="#">
    <span class="event-summary__month">
      <?php
      // Se usa la clase DateTime de PHP para convertir la fecha guardada por ACF
      // (en formato YYYYMMDD) a un objeto manipulable y poder formatearla libremente.
      // Se usa get_field() de ACF porque la fecha del evento es un campo personalizado,
      // no un campo nativo de WordPress como la fecha de publicación.
      $eventDate = new DateTime(get_field('event_date'));
      echo $eventDate->format('M'); // Se usa el formato 'M' para mostrar el mes abreviado (ej. Jan) y que ocupe poco espacio en la tarjeta del evento.
      ?>
    </span>
    <span class="event-summary__day"><?php echo $eventDate->format('d'); ?></span> <!-- Se usa 'd' para mostrar el día con dos dígitos, manteniendo el diseño de la tarjeta consistente. -->
  </a>
  <div class="event-summary__content">
    <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5> <!-- Se enlaza el título al evento para que el usuario pueda acceder a todos sus detalles desde la tarjeta. -->
    <!-- Se da prioridad al extracto manual porque el editor lo redacta específicamente
         para ser mostrado en listados, a diferencia del contenido completo recortado. -->
    <p>
      <?php
      if (has_excerpt()) {
        echo get_the_excerpt(); // Se usa el extracto manual si existe porque el editor ya lo redactó para ser breve y descriptivo.
      } else {
        echo wp_trim_words(get_the_content(), 18); // Se recorta a 18 palabras para mantener el diseño de la tarjeta uniforme cuando no hay extracto manual.
      }
      ?>
      <a href="<?php the_permalink(); ?>" class="nu gray">Learn more</a>
    </p>
  </div>
</div>