  <div class="post-item">
    <!-- Se enlaza el título al artículo completo para que el usuario pueda acceder
         a la entrada desde el listado del blog. -->
    <!-- Se muestra el título como encabezado para que el lector identifique
         el artículo de un vistazo en el listado. -->
    <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>
    <div class="metabox">
      <!-- Se muestra el autor como enlace para que el lector pueda explorar
           otros artículos del mismo autor con un solo clic. -->
      <!-- Se usa 'n.j.y' como formato de fecha para una presentación compacta
           y consistente con el estilo visual del resto del sitio. -->
      <!-- Se listan las categorías para que el lector pueda filtrar contenido
           por tema sin necesidad de buscador. -->
      <p>Posted by <?php the_author_posts_link(); ?> on <?php the_time('n.j.y'); ?> in <?php echo get_the_category_list(', '); ?></p>
    </div>
    <div class="generic-content">
      <!-- Se usa el extracto en vez del contenido completo para que el listado sea liviano
           y el usuario decida si quiere leer el artículo completo. -->
      <?php the_excerpt(); ?>
      <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">Continue reading &raquo;</a></p>
    </div>
  </div>