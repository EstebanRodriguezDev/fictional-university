  <div class="post-item">
    <!-- the_permalink(): Imprime la URL permanente de la entrada actual para crear el enlace al artículo completo. -->
    <!-- the_title(): Función que imprime el título de la entrada actual. -->
    <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>

    <div class="generic-content">
      <!-- the_excerpt(): Imprime un resumen automático (o manual si existe) del contenido del post. -->
      <?php the_excerpt(); ?>
      <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">View program &raquo;</a></p>
    </div>
  </div>