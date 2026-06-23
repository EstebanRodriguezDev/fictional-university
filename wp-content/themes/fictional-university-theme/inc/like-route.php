<?php
// Registra las rutas personalizadas de la API REST al inicializar
add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes()
{
  // Ruta para crear un "like" (POST)
  register_rest_route('university/v1', 'manageLike', array(
    'methods' => 'POST',
    'callback' => 'createLike',
  ));
  // Ruta para eliminar un "like" (DELETE)
  register_rest_route('university/v1', 'manageLike', array(
    'methods' => 'DELETE',
    'callback' => 'deleteLike',
  ));
}

// Función que maneja la creación de un nuevo "like"
function createLike(WP_REST_Request $data)
{
  // Verifica que el usuario esté logueado antes de permitirle dar "like"
  if (is_user_logged_in()) {
    $professor = sanitize_text_field($data['professorId']); // Sanitiza el ID del profesor
    
    // Consulta para comprobar si el usuario ya le dio "like" a este profesor
    $existQuery = new WP_Query(array(
      'author' => get_current_user_id(),
      'post_type' => 'like',
      'meta_query' => array(
        array(
          'key' => 'liked_professor_id',
          'compare' => '=',
          'value' => $professor,
        )
      )
    ));
    // Si el usuario no tiene un "like" previo y el ID corresponde a un "professor"
    if ($existQuery->found_posts == 0 and get_post_type($professor) == 'professor') {
      // Crea un nuevo post de tipo "like" en la base de datos
      $like_id = wp_insert_post(array(
        'post_type' => 'like',
        'post_status' => 'publish',
        'post_title' => 'Our PHP Create Post Test', // El título no es relevante, pero es requerido
        'meta_input' => array(
          'liked_professor_id' => $professor, // Asigna el ID del profesor al campo personalizado
        ),
      ));
      // Validamos si falló la creación del post
      if (is_wp_error($like_id) || $like_id === 0) {
        return new WP_REST_Response('Error al guardar el like en la base de datos', 500);
      }
      return array(
        'status' => 'success',
        'mensaje' => 'Like creado correctamente',
        'likeId' => $like_id,
      );
    } else {
      return new WP_Error('Invalid', 'Invalid user operation.', array('status' => 403));
    }
  } else {
    return new WP_Error('unauthorized', 'Only Logged in user can create a like', array('status' => 401));
  }
}

// Función que maneja la eliminación de un "like"
function deleteLike(WP_REST_Request $data)
{
  $likeId = sanitize_text_field($data['like']); // Sanitiza el ID del like a eliminar
  // Verifica si el usuario actual es el autor del "like" y si el ID corresponde a un post tipo "like"
  if (get_current_user_id() == get_post_field('post_author', $likeId) and get_post_type($likeId) == 'like') {
    wp_delete_post($likeId, true); // Elimina permanentemente el post (true = saltar papelera)
    return array(
      'status' => 'success',
      'mensaje' => 'Like eliminado correctamente',
      'likeId' => $likeId,
    );
  } else {
    return new WP_Error('Invalid', 'Error al eliminar el like', array('status' => 403));
  }
}
