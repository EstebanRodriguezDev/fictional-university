  <div class="post-item">
    <!-- Se enlaza el título a la página completa para que el usuario pueda acceder
         a todo el contenido de la página desde el resultado de búsqueda. -->
    <!-- Se muestra el título como encabezado para identificar visualmente la página
         de un vistazo en el listado de resultados. -->
    <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>

    <div class="generic-content">
      <!-- Se usa el extracto en vez del contenido completo para que el listado sea liviano
           y el usuario decida si quiere leer la página completa. -->
      <?php the_excerpt(); ?>
      <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">Continue reading &raquo;</a></p>
    </div>
  </div>