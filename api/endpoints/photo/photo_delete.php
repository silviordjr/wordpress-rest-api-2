<?php

function api_photo_delete ($request) {
    $user = wp_get_current_user();
    $post = get_post($request['id']);
    $user_id = (int) $user->ID;
    $author_id = (int) $post->post_author;

    if ($author_id !== $user_id || !isset($post)){
        $response = new WP_Error ('autorizacao', "Usuário não autorizado.", array('status' => 401));
        return rest_ensure_response( $response );
    }

    $attachment_id = get_post_meta($request['id'], 'img', true);
    
    wp_delete_attachment($attachment_id, true);
    wp_delete_post($request['id'], true);

    return rest_ensure_response( 'post deletado' );

}

function register_api_photo_delete () {
    register_rest_route('api', '/photo/(?P<id>[0-9]+)', array(
        'methods' => WP_REST_Server::DELETABLE,
        'callback' => 'api_photo_delete',
    ));
}
add_action('rest_api_init', 'register_api_photo_delete');
?>