<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

// END ENQUEUE PARENT ACTION

add_action('woocommerce_cart_calculate_fees', 'envioGratis', 20);

function envioGratis() {
    $total_carrito = WC()->cart->subtotal;
    $umbral_envio_gratis = 50; // Cambia esto si necesitas otro umbral

    // Verifica si el total del carrito es mayor o igual al umbral
    if ($total_carrito >= $umbral_envio_gratis) {
        // Desactiva todas las opciones de envío y establece el envío gratuito
        foreach (WC()->shipping->get_shipping_methods() as $método) {
            if ($método->id === 'free_shipping') {
                $método->enabled = 'yes';
            } else {
                $método->enabled = 'no';
            }
        }
    } else {
        // Reactiva todos los métodos de envío si no se cumple el umbral
        foreach (WC()->shipping->get_shipping_methods() as $método) {
            $método->enabled = 'yes';
        }
    }
}



add_action('woocommerce_cart_calculate_fees', 'envioPagado', 20);

function envioPagado() {
    $total_carrito = WC()->cart->subtotal;
    $umbral_envio_gratis = 50; // Cambia esto si necesitas otro umbral
    $tarifa_envio = 4.99; // Establece el coste de envío cuando el pedido es menor de 50 €

    if ($total_carrito < $umbral_envio_gratis) {
        // Agrega una tarifa de envío cuando el total del carrito es menor a 50 €
        WC()->cart->add_fee(__('Gastos de Envío', 'MiTema'), $tarifa_envio);
    }
}


add_action('woocommerce_cart_calculate_fees', 'descuentoCarrito', 20);

function descuentoCarrito() {
    $total_carrito = WC()->cart->subtotal;
    $umbral_descuento = 100; // Umbral de descuento
    $porcentaje_descuento = 0.05; // 5% de descuento

    if ($total_carrito > $umbral_descuento) {
        // Calcula el monto del descuento
        $descuento = $total_carrito * $porcentaje_descuento;
        // Añade el descuento al carrito
        WC()->cart->add_fee(__('Descuento 5%', 'MiTema'), -$descuento);
    }
}

function iniciarSesion() {
    if (!session_id()) {
        session_start(['cookie_lifetime' => 86400]); // 24 horas
    }
}
add_action('init', 'iniciarSesion');

function preferenciasUsuario() {
    // Guardar preferencias de idioma
    if (isset($_POST['idioma_preferido'])) {
        setcookie('user_language', $_POST['idioma_preferido'], time() + (86400 * 30), '/');
    }
    
    // Guardar preferencias de moneda
    if (isset($_POST['moneda_preferida'])) {
        setcookie('user_currency', $_POST['moneda_preferida'], time() + (86400 * 30), '/');
    }
    
    // Guardar tema de color
    if (isset($_POST['tema_color'])) {
        setcookie('user_theme', $_POST['tema_color'], time() + (86400 * 30), '/');
    }
}
add_action('init', 'preferenciasUsuario');

function productosVistos() {
    if (is_product()) {
        $producto_actual = get_the_ID();
        $productos_vistos = isset($_COOKIE['productos_vistos']) ? json_decode($_COOKIE['productos_vistos'], true) : array();
        
        if (!in_array($producto_actual, $productos_vistos)) {
            array_unshift($productos_vistos, $producto_actual);
            $productos_vistos = array_slice($productos_vistos, 0, 5); // Mantener solo los últimos 5
        }
        
        setcookie('productos_vistos', json_encode($productos_vistos), time() + (86400 * 30), '/');
    }
}
add_action('wp', 'productosVistos');

function configurar_nextend_social_login() {
    if (class_exists('NextendSocialLogin')) {
        // Personalizar la apariencia de los botones
        add_filter('nsl_auth_buttons_style', function($style) {
            return 'icon'; // Opciones: 'icon', 'full'
        });
        
        // Personalizar la ubicación de los botones
        add_filter('nsl_auth_buttons_align', function($align) {
            return 'center'; // Opciones: 'left', 'center', 'right'
        });
    }
}
add_action('init', 'configurar_nextend_social_login');

function mostraBotonesSociales() {
    if (function_exists('do_shortcode')) {
        echo do_shortcode('[nextend_social_login]');
    }
}
add_action('woocommerce_login_form_end', 'mostraBotonesSociales');
