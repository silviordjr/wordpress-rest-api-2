<?php
function api_user_post($request){
    $email = sanitize_email($request['email']);
    $username =  sanitize_text_field($request['username']);
    $password =  $request['password'];

    $response = wp_insert_user(array(
        'user_login' => $username,
        'user_email' => $email,
        'user_pass' => $password,
        'role' => 'subscriber'
    ));

    if (empty($email) || empty($username) || empty($password)){
        $response = new WP_Error('Error', 'Dados incompletos', array('status' => 406));
        return rest_ensure_response($response);
    }

    if (username_exists($username) || email_exists($email)){
        $response = new WP_Error('Error', 'Email já cadastrado.', array('status' => 403));
        return rest_ensure_response($response);
    }

    return rest_ensure_response($response);
}

function register_api_user_post(){
    register_rest_route('api', '/user', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'api_user_post',
    ));
}
add_action('rest_api_init', 'register_api_user_post');
?>