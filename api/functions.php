<?php
// remove_action('rest_api_init', 'create_initial_rest_routes', 99); remove todas as rotas

// add_filter('rest_endpoints', function($endpoints){
//     unset($endpoints['/wp/v2/users']);
//     unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
// });

$dirbase = get_template_directory();

require_once $dirbase . '/endpoints/user/user_post.php';
require_once $dirbase . '/endpoints/user/user_get.php';
require_once $dirbase . '/endpoints/user/password.php';
require_once $dirbase . '/endpoints/user/stats.php';

require_once $dirbase . '/endpoints/photo/photo_post.php';
require_once $dirbase . '/endpoints/photo/photo_delete.php';
require_once $dirbase . '/endpoints/photo/photo_get.php';

require_once $dirbase . '/endpoints/comment/comment_post.php';
require_once $dirbase . '/endpoints/comment/comment_get.php';

update_option('large_size_w', 1000);
update_option('large_size_h', 1000);
update_option('large_crop', 1);


function change_api(){
    return 'json';
}
add_filter('rest_url_prefix', 'change_api');

function expire_acess_token () {
    return time() + (60 * 60 * 24 * 7);
}
add_action( "jwt_auth_expire", "expire_acess_token");


?>