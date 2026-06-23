<?php
// get_header(): Incluye el archivo header.php del tema.
get_header();

// Inicia el bucle (Loop) de WordPress para procesar el contenido del profesor actual.
while (have_posts()) {
  the_post(); // Prepara los datos del post (título, contenido, etc.).
  // Despliega el banner superior. Al ser un profesor, detectará su título y su imagen de fondo (ACF).
  pageBanner();
?>
  <div class="container container--narrow page-section">
    <!-- the_content(): Imprime la biografía o descripción principal del profesor. -->
    <div class="generic-content">
      <div class="row group">
        <div class="one-third">
          <?php the_post_thumbnail('professorPortrait');  ?>
        </div>
        <div class="two-thirds">
          <?php
          // Consulta para obtener la cantidad total de "likes" de este profesor
          $likeCount = new WP_Query(array(
            'post_type' => 'like',
            'meta_query' => array(
              array(
                'key' => 'liked_professor_id', // Busca por el ID del profesor que fue "likeado"
                'compare' => '=',
                'value' => get_the_ID(),
              )
            )
          ));
          $existStatus = 'no';
          $like_id = '';
          
          // Si el usuario está logueado, verificamos si él específicamente le ha dado "like" a este profesor
          if (is_user_logged_in()) {
            $existQuery = new WP_Query(array(
              'author' => get_current_user_id(), // Solo likes del usuario actual
              'post_type' => 'like',
              'meta_query' => array(
                array(
                  'key' => 'liked_professor_id',
                  'compare' => '=',
                  'value' => get_the_ID(),
                )
              )
            ));
            // Si encuentra un registro, el estado cambia a 'yes' y guardamos el ID del "like" para poder eliminarlo después
            if ($existQuery->found_posts) {
              $existStatus = 'yes';
              $like_id = $existQuery->posts[0]->ID;
            }
          }
          ?>
          <span class="like-box" data-like="<?php echo $like_id; ?>" data-professor="<?php the_ID(); ?>" data-exists="<?php echo $existStatus; ?>">
            <i class="fa fa-heart-o" aria-hidden="true"></i>
            <i class="fa fa-heart" aria-hidden="true"></i>
            <span class="like-count"><?php echo $likeCount->found_posts; ?></span>
          </span>
          <?php the_content(); ?>
        </div>
      </div>
    </div>
    <?php
    // get_field(): Obtiene el campo personalizado de relación de ACF 'related_programs'.
    $relatedPrograms = get_field('related_programs');

    if ($relatedPrograms) { // Solo muestra la sección si hay programas asignados.
      echo '<hr class="section-break">';
      echo '<h2 class="headline headline--medium"> Subject(s) Taught</h2>';
      echo '<ul class="link-list min-list">';
      // Itera sobre el array de objetos de post retornados por ACF.
      foreach ($relatedPrograms as $program) { ?>
        <li><a href="<?php echo get_the_permalink($program); ?>"><?php echo get_the_title($program); ?></a></li>
    <?php }
      echo '</ul>';
    }
    ?>
  </div>
<?php }
// get_footer(): Incluye el archivo footer.php del tema.
get_footer();
