<?php
add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes()
{
  register_rest_route('university/v1', 'manageLike', array(
    'methods' => 'POST',
    'callback' => 'createLike',
  ));
  register_rest_route('university/v1', 'manageLike', array(
    'methods' => 'DELETE',
    'callback' => 'deleteLike',
  ));
}

function createLike(WP_REST_Request $data)
{
  if (is_user_logged_in()) {
    $professor = sanitize_text_field($data['professorId']);
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
    if ($existQuery->found_posts == 0 and get_post_type($professor) == 'professor') {
      $like_id = wp_insert_post(array(
        'post_type' => 'like',
        'post_status' => 'publish',
        'post_title' => 'Our PHP Create Post Test',
        'meta_input' => array(
          'liked_professor_id' => $professor,
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

function deleteLike(WP_REST_Request $data)
{
  $likeId = sanitize_text_field($data['like']);
  if (get_current_user_id() == get_post_field('post_author', $likeId) and get_post_type($likeId) == 'like') {
    wp_delete_post($likeId, true);
    return array(
      'status' => 'success',
      'mensaje' => 'Like eliminado correctamente',
      'likeId' => $likeId,
    );
  } else {
    return new WP_Error('Invalid', 'Error al eliminar el like', array('status' => 403));
  }
}
