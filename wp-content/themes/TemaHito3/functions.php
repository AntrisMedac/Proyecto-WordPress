<?php

function my_theme_enqueue_styles() {
    wp_enqueue_style('main-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

// function cookie_carrito() {
//     $nombre_cookie = 'carrito_usuario';

//     if (!is_user_logged_in()) {
//         $carrito = WC()->cart->get_cart();

//         $productos_carrito = array();
//         foreach ($carrito as $item) {
//             $productos_carrito[] = $item['product_id'];
//         }

//         $valor_cookie = json_encode($productos_carrito);

//         $duracion_cookie = time() + (86400 * 365);

//         if (!isset($_COOKIE[$nombre_cookie])) {
//             setcookie($nombre_cookie, $valor_cookie, $duracion_cookie, "/");
//         }
//     }
// }
// add_action('init', 'configurar_cookie_carrito');

