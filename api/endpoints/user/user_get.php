<?php
function api_user_get($request){
    $user = wp_get_current_user();
    
    if ($user->ID === 0){
        $response = new WP_Error('not found', 'Usuário não encontrado.', array('status' => 401));
        return rest_ensure_response($response);
    }
    return rest_ensure_response($user);
}

function register_api_user_get(){
    register_rest_route('api', '/user', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'api_user_get',
    ));
}
add_action('rest_api_init', 'register_api_user_get');
?>