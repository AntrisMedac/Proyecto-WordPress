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

/*
    Funciones del carrito
                            */
//Calcula cuando tendremos envio gratis.
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


//Calcula cunado tendremos que pagar por el envio
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


//Calcula cuando tendremos decuento en la compra
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


/*
    Sesiones y cookies - Integrado con WooCommerce
                                                    */
// Iniciar la sesión
function iniciarSesion() {
    if (!session_id()) {
        session_start(['cookie_lifetime' => 86400]); // 24 horas
    }
}
add_action('init', 'iniciarSesion');


// Se ejecuta al inicio de la página y guarda las preferencias del usuario
function preferenciasUsuario() {
    // Guardar preferencias de idioma
    if (isset($_POST['idioma_preferido'])) {
        setcookie('user_language', $_POST['idioma_preferido'], time() + (86400 * 30), '/', '', true, true);
    }

    // Guardar preferencias de moneda
    if (isset($_POST['moneda_preferida'])) {
        setcookie('user_currency', $_POST['moneda_preferida'], time() + (86400 * 30), '/', '', true, true);
        // Actualiza la moneda en la sesión de WooCommerce
        WC()->session->set('currency', $_POST['moneda_preferida']);
    }

    // Guardar tema de color
    if (isset($_POST['tema_color'])) {
        setcookie('user_theme', $_POST['tema_color'], time() + (86400 * 30), '/', '', true, true);
    }
}
add_action('init', 'preferenciasUsuario');

// Se ejecuta en las páginas de productos de WooCommerce y guarda los últimos 5 productos vistos en una cookie
function productosVistos() {
    if (is_product()) {
        $producto_actual = get_the_ID();
        $productos_vistos = isset($_COOKIE['productos_vistos']) ? json_decode($_COOKIE['productos_vistos'], true) : array();

        if (!in_array($producto_actual, $productos_vistos)) {
            array_unshift($productos_vistos, $producto_actual);
            $productos_vistos = array_slice($productos_vistos, 0, 5); // Mantener solo los últimos 5
        }

        setcookie('productos_vistos', json_encode($productos_vistos), time() + (86400 * 30), '/', '');
    }
}
add_action('wp', 'productosVistos');

/*
    Formulario Personalizado
                                */
// Personalizar formulario de registro para incluir el campo de teléfono
add_action('woocommerce_register_form_start', 'campoNtelefono');
function campoNtelefono() {
    ?>
    <p class="form-row form-row-wide">
        <label for="reg_phone">Teléfono <span class="required">*</span></label>
        <input type="text" class="input-text" name="phone" id="reg_phone" value="<?php if (!empty($_POST['phone'])) echo esc_attr($_POST['phone']); ?>" />
    </p>
    <?php
}

// Validar el campo de teléfono durante el registro
add_action('woocommerce_register_post', 'validarCampoNtelefono', 10, 3);
function validarCampoNtelefono($username, $email, $validation_errors) {
    // Verifica que el teléfono no esté vacío y que tenga exactamente 9 dígitos
    if (empty($_POST['phone']) || !preg_match('/^\d{9}$/', $_POST['phone'])) {
        $validation_errors->add('phone_error', __('Por favor ingresa un teléfono válido (9 dígitos).', 'woocommerce'));
    }    
    return $validation_errors;
}

// Guardar el número de teléfono en la base de datos
add_action('woocommerce_created_customer', 'guardarCampoNtelefono');
function guardarCampoNtelefono($customer_id) {
    if (isset($_POST['phone'])) {
        // Sanear y guardar el teléfono como un número sin caracteres no numéricos
        $phone = sanitize_text_field($_POST['phone']);
        $phone = preg_replace('/\D/', '', $phone); // Elimina cualquier carácter no numérico
        update_user_meta($customer_id, 'phone', $phone);
    }
}


// Redirigir al usuario después de iniciar sesión
add_filter('woocommerce_login_redirect', 'redireccionarDespuesLogin', 10, 2);
function redireccionarDespuesLogin($redirect, $user) {
    return home_url('/mi-cuenta');  // Redirige al usuario a su página de cuenta
}


// Añadir un filtro para asegurar que el correo esté validado
add_filter('woocommerce_registration_errors', 'validarCorreoRegistro', 10, 3);
function validarCorreoRegistro($errors, $username, $email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors->add('invalid_email', __('El correo electrónico ingresado no es válido.', 'woocommerce'));
    }
    return $errors;
}

// Validación de contraseña segura (al menos 8 caracteres, 1 número, 1 letra)
add_filter('woocommerce_registration_errors', 'validarContraseñaRegistro', 10, 3);
function validarContraseñaRegistro($errors, $username, $email) {
    if (isset($_POST['password_1'])) {
        $password = $_POST['password_1'];
        if (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[A-Za-z]/', $password)) {
            $errors->add('password_error', __('La contraseña debe tener al menos 8 caracteres, 1 número y 1 letra.', 'woocommerce'));
        }
    }
    return $errors;
}






/*
    Iniciar sesión y registrarte con Google
                                            */

function configurar_nextend_social_login() {
    if (class_exists('NextendSocialLogin')) {
        // Personalizar la apariencia de los botones
        add_filter('nsl_auth_buttons_style', function($style) {
            return 'icon'; // Opciones: 'icon', 'icon_label', 'full'
        });
        
        // Personalizar la ubicación de los botones
        add_filter('nsl_auth_buttons_align', function($align) {
            return 'center'; // Opciones: 'left', 'center', 'right'
        });
    }
}
add_action('init', 'configurar_nextend_social_login');

function mostrarBotonesSociales() {
    if (function_exists('do_shortcode')) {
        echo do_shortcode('[nextend_social_login]');
    }
}
add_action('woocommerce_login_form_end', 'mostrarBotonesSociales');