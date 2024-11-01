<?php

function my_theme_enqueue_styles() {
    wp_enqueue_style('main-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

function iniciar_sesion_wp() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'iniciar_sesion_wp');

// Guardar datos en la sesión
function guardar_datos_sesion() {
    if (is_user_logged_in()) {
        $_SESSION['nombre_usuario'] = wp_get_current_user()->display_name;
    }
}
add_action('init', 'guardar_datos_sesion');

// Crear o actualizar una cookie con la cantidad de productos en el carrito
function actualizar_cookie_carrito() {
    // Verificamos que WooCommerce y el carrito estén activos
    if (function_exists('WC') && WC()->cart) {
        $cantidad_productos = WC()->cart->get_cart_contents_count();

        // Configuramos la cookie con la cantidad de productos, expira en 1 día
        setcookie('cantidad_productos_carrito', $cantidad_productos, time() + 86400, "/");

        // Hacer que la cookie esté disponible de inmediato para la sesión actual
        $_COOKIE['cantidad_productos_carrito'] = $cantidad_productos;
    }
}
add_action('template_redirect', 'actualizar_cookie_carrito');

// Mostrar la cantidad de productos en el carrito usando la cookie
function mostrar_productos_carrito_cookie() {
    if (isset($_COOKIE['cantidad_productos_carrito'])) {
        $cantidad = intval($_COOKIE['cantidad_productos_carrito']);
        echo '<p class="productos-carrito">Productos en el carrito: ' . esc_html($cantidad) . '</p>';
    }
}
add_action('wp_head', 'mostrar_productos_carrito_cookie');

