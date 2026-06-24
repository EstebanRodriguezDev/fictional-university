  <div class="post-item">
    <!-- Se enlaza el título al programa completo para que el usuario pueda acceder
         a todos sus detalles (profesores, eventos, campus) desde el listado. -->
    <!-- Se muestra el título del programa como encabezado para que el usuario
         identifique visualmente cada carrera de un vistazo en el listado. -->
    <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>

    <div class="generic-content">
      <!-- Se usa el extracto en vez del contenido completo para que el listado sea liviano
           y el usuario decida si quiere ver todos los detalles del programa. -->
      <?php the_excerpt(); ?>
      <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">View program &raquo;</a></p>
    </div>
  </div>