<?php
// Se engancha al hook 'rest_api_init' para registrar las rutas solo cuando la API REST
// está activa, evitando errores si se llama antes de que WordPress la inicialice.
add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes()
{
  // Se registra la ruta POST para crear likes porque la API REST de WordPress
  // diferencia verbos HTTP, permitiendo reutilizar la misma URL para crear y eliminar.
  register_rest_route('university/v1', 'manageLike', array(
    'methods' => 'POST',
    'callback' => 'createLike',
  ));
  // Se registra la ruta DELETE en la misma URL para eliminar likes porque
  // mantiene la semántica REST correcta sin necesidad de crear un endpoint separado.
  register_rest_route('university/v1', 'manageLike', array(
    'methods' => 'DELETE',
    'callback' => 'deleteLike',
  ));
}

// Función que maneja la creación de un nuevo "like"
function createLike(WP_REST_Request $data)
{
  // Se verifica la autenticación aquí (en el backend) porque la validación
  // del frontend puede ser fácilmente omitida desde herramientas externas.
  if (is_user_logged_in()) {
    $professor = sanitize_text_field($data['professorId']); // Se sanitiza el ID para prevenir inyección de datos maliciosos en la consulta.
    
    // Se comprueba si ya existe un like previo para evitar duplicados
    // en la base de datos, garantizando que cada usuario solo dé un like por profesor.
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
    // Se verifica que sea un post de tipo 'professor' para evitar que alguien dé
    // like a un tipo de contenido incorrecto enviando un ID arbitrario.
    if ($existQuery->found_posts == 0 and get_post_type($professor) == 'professor') {
      // Se crea un post de tipo 'like' para persistir el like en la base de datos,
      // aprovechando la infraestructura de posts de WordPress sin crear tablas propias.
      $like_id = wp_insert_post(array(
        'post_type' => 'like',
        'post_status' => 'publish',
        'post_title' => 'Our PHP Create Post Test', // El título es requerido por WordPress pero no tiene relevancia funcional para los likes.
        'meta_input' => array(
          'liked_professor_id' => $professor, // Se guarda el ID del profesor en un campo personalizado para poder filtrar los likes por profesor después.
        ),
      ));
      // Se valida el resultado de wp_insert_post() porque puede fallar silenciosamente
      // devolviendo 0 o un WP_Error sin lanzar excepción.
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
  $likeId = sanitize_text_field($data['like']); // Se sanitiza el ID para prevenir inyección de datos maliciosos.
  // Se verifica que el usuario sea el autor del like Y que el post sea de tipo 'like'
  // para evitar que un usuario elimine los likes de otro usuario enviando un ID ajeno.
  if (get_current_user_id() == get_post_field('post_author', $likeId) and get_post_type($likeId) == 'like') {
    wp_delete_post($likeId, true); // Se pasa 'true' para eliminar permanentemente el like sin enviarlo a la papelera, ya que los likes no necesitan recuperarse.
    return array(
      'status' => 'success',
      'mensaje' => 'Like eliminado correctamente',
      'likeId' => $likeId,
    );
  } else {
    return new WP_Error('Invalid', 'Error al eliminar el like', array('status' => 403));
  }
}
