<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?></title>
    <link rel="stylesheet" href="../TemaHito3/style.css">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header>
        <nav>
            <a href="<?php echo home_url('/'); ?>" class="logo">E-Commerce</a>
            <div class="nav-links">
                <a href="<?php echo home_url('/'); ?>">Inicio</a>
                <a href="<?php echo home_url('/Productos/'); ?>">Productos</a>
                <a href="<?php echo home_url('/Contacto/'); ?>">Contacto</a>
                <a href="<?php echo wc_get_cart_url(); ?>">Carrito (<?php echo WC()->cart->get_cart_contents_count(); ?>)</a>
            </div>
        </nav>

        <?php 
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['username']) && !empty($_POST['password'])) {
            $username = sanitize_user($_POST['username']);
            $password = $_POST['password'];

            $user = wp_authenticate($username, $password);
            if (is_wp_error($user)) {
                echo '<p class="error">Error: ' . esc_html($user->get_error_message()) . '</p>';
            } else {
                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID);
                wp_redirect(home_url());
                exit;
            }
        }
        ?>

        <?php 
        if ( !is_user_logged_in() ) {
        ?>
            <div class="login-form">
                <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                    <input type="text" name="username" placeholder="Nombre de usuario" required>
                    <input type="password" name="password" placeholder="Contraseña" required>
                    <button type="submit">Iniciar Sesión</button>
                </form>
            </div>
        <?php 
        } else { 
            echo '<p>Bienvenido, ' . esc_html(wp_get_current_user()->display_name) . '!</p>';
            echo '<a href="' . wp_logout_url() . '">Cerrar sesión</a>';
        }
        ?>
    </header>
    <h1>Bienvenidos a la tienda, para comprar dirijase a productos</h1>

    <div class="products">
        <h3>Productos mas vendidos</h3>
        <?php echo do_shortcode('[productos_mas_vendidos]'); ?>
    </div>
</body>
</html>
