<?php

function api_comment_post ($request) {
    $user = wp_get_current_user();

    if ($user->ID === 0){
        $response = new WP_Error('autorizacao', 'Usuário não autorizado.', array('status' => 401));
        return rest_ensure_response($response);
    }

    $post_id = $request['id'];
    $comment = sanitize_text_field( $request['comment'] );
    
    if (empty($comment)){
        $response = new WP_Error('dados', 'Dados incompletos.', array('status' => 422));
        return rest_ensure_response($response);
    }

    $response = array(
        'comment_author' => $user->user_login,
        'comment_content' => $comment,
        'comment_post_ID' => $post_id,
        'user_id' => $user->ID,
    );

    $comment_id = wp_insert_comment( $response );

    $comment = get_comment( $comment_id );

    return rest_ensure_response($comment);
}

function register_api_comment_post () {
    register_rest_route('api', '/comment/(?P<id>[0-9]+)', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'api_comment_post',
    ));
}
add_action('rest_api_init', 'register_api_comment_post');
?>