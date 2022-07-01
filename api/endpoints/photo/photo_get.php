<?php

function photo_data ($post){
    $post_meta = get_post_meta($post->ID);
    $src = wp_get_attachment_image_src($post_meta['img'][0], 'large')[0];
    $user = get_userdata( $post->post_author );
    $total_comments = get_comments_number( $post->ID );

    return array(
        'id' => $post->ID,
        'author' => $user->user_login,
        'title' => $post->post_title,
        'date' => $post->post_date,
        'src' => $src,
        'peso' => $post_meta['peso'][0],
        'idade' => $post_meta['idade'][0],
        'acessos' => $post_meta['acessos'][0],
        'total_comments' => $total_comments
    );
}

function api_photo_get ($request) {
    $post_id = $request['id'];

    $post = get_post( $post_id );

    if (!isset($post) || empty($post_id)){
        $response = new WP_Error ('error', "Post não encontrado.", array('status' => 404));
        return rest_ensure_response( $response );
    }

    $photo = photo_data($post);

    $photo['acessos'] = (int) $photo['acessos'] + 1;

    update_post_meta( $post->ID, 'acessos', $photo['acessos']);

    $comments = get_comments(array(
        'post_id' => $post->ID,
        'order' => 'ASC'
    ));

    return rest_ensure_response(array(
        'photo' => $photo,
        'comments' => $comments
    ));
}

function register_api_photo_get () {
    register_rest_route('api', '/photo/(?P<id>[0-9]+)', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'api_photo_get',
    ));
}
add_action('rest_api_init', 'register_api_photo_get');



function api_photos_get ($request) {
    $_total = sanitize_text_field($request['_total']) ?: 6;
    $_page = sanitize_text_field($request['_page']) ?: 1;
    $_user = sanitize_text_field($request['_user']) ?: 0;

    if (!is_numeric($_user)){
        $user = get_user_by( 'login' , $_user);

        if (!$user){
            $response = new WP_Error ('error', "Usuario não encontrado.", array('status' => 404));
            return rest_ensure_response( $response );
        }

        $_user = $user->ID;
    }

    $args = array(
        'post_type' => 'post',
        'author' => $_user,
        'posts_per_page' => $_total,
        'paged' => $_page,
    );

    $query = new WP_Query($args);

    $posts = $query->posts;

    $photo = [];
    if ($posts){
        foreach ($posts as $post){
            $photo[] = photo_data($post);
        }
    }

    return rest_ensure_response($photo);
}

function register_api_photos_get () {
    register_rest_route('api', '/photo', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'api_photos_get',
    ));
}
add_action('rest_api_init', 'register_api_photos_get');

?>