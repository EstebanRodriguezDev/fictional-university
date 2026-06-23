  <div class="post-item">
    <!-- the_permalink(): Imprime la URL permanente de la entrada actual para crear el enlace al artículo completo. -->
    <!-- the_title(): Función que imprime el título de la entrada actual. -->
    <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>
    <div class="metabox">
      <!-- the_author_posts_link(): Muestra el nombre del autor como un enlace que lleva a todos sus artículos. -->
      <!-- the_time(): Imprime la fecha de publicación. El formato 'n.j.y' significa: mes (número), día y año (dos dígitos). -->
      <!-- get_the_category_list(): Recupera las categorías de la entrada como una cadena de texto, separadas aquí por una coma. -->
      <p>Posted by <?php the_author_posts_link(); ?> on <?php the_time('n.j.y'); ?> in <?php echo get_the_category_list(', '); ?></p>
    </div>
    <div class="generic-content">
      <!-- the_excerpt(): Imprime un resumen automático (o manual si existe) del contenido del post. -->
      <?php the_excerpt(); ?>
      <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">Continue reading &raquo;</a></p>
    </div>
  </div>