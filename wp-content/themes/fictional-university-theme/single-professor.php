<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();

// Se usa el Loop de WordPress para poder acceder a los datos del profesor actual
// (título, imagen de banner, contenido, campos ACF) en el contexto correcto.
while (have_posts()) {
  the_post();
  // Se llama pageBanner() sin argumentos para que detecte automáticamente el título del profesor
  // y su imagen de fondo configurada en ACF, sin necesidad de pasarlos explícitamente.
  pageBanner();
?>
  <div class="container container--narrow page-section">
    <!-- Se usa the_content() para renderizar el contenido del editor de WordPress,
         permitiendo al editor añadir la biografía del profesor sin tocar el código de la plantilla. -->
    <div class="generic-content">
      <div class="row group">
        <div class="one-third">
          <?php the_post_thumbnail('professorPortrait');  ?>
        </div>
        <div class="two-thirds">
          <?php
          // Se hace una WP_Query de posts tipo 'like' filtrados por el ID de este profesor
          // para contar cuántos usuarios le han dado like, sin depender del JS para el conteo inicial.
          $likeCount = new WP_Query(array(
            'post_type' => 'like',
            'meta_query' => array(
              array(
                'key' => 'liked_professor_id', // Se filtra por este campo para contar solo los likes de ESTE profesor específico.
                'compare' => '=',
                'value' => get_the_ID(),
              )
            )
          ));
          $existStatus = 'no';
          $like_id = '';
          
          // Se verifica si el usuario está logueado antes de buscar su like para
          // evitar hacer una consulta innecesaria a la base de datos para visitantes anónimos.
          if (is_user_logged_in()) {
            $existQuery = new WP_Query(array(
              'author' => get_current_user_id(), // Se filtra por el usuario actual para saber si ÉL específicamente ya le dio like.
              'post_type' => 'like',
              'meta_query' => array(
                array(
                  'key' => 'liked_professor_id',
                  'compare' => '=',
                  'value' => get_the_ID(),
                )
              )
            ));
            // Se guarda el ID del like existente para que el JS pueda enviarlo
            // en la petición DELETE si el usuario decide quitar su like.
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
    // Se obtienen los programas relacionados desde ACF para mostrar qué asignaturas
    // imparte el profesor, ayudando al estudiante a encontrar a su docente por área.
    $relatedPrograms = get_field('related_programs');

    // Se muestra la sección solo si el profesor tiene programas asignados para no
    // dejar un bloque vacío en la página.
    if ($relatedPrograms) {
      echo '<hr class="section-break">';
      echo '<h2 class="headline headline--medium"> Subject(s) Taught</h2>';
      echo '<ul class="link-list min-list">';
      // Se itera sobre el array de objetos retornado por ACF para generar el enlace
      // a cada programa, sin necesidad de hacer una WP_Query adicional.
      foreach ($relatedPrograms as $program) { ?>
        <li><a href="<?php echo get_the_permalink($program); ?>"><?php echo get_the_title($program); ?></a></li>
    <?php }
      echo '</ul>';
    }
    ?>
  </div>
<?php }
// Se incluye el footer para cerrar correctamente el HTML y cargar los scripts que
// WordPress y los plugins necesitan inyectar al final del body.
get_footer();
