<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?></title>
    <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>">
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
    </header>

    <div class="form-container">
        <?php echo do_shortcode('[woocommerce_cart]'); ?>
    </div>

    <?php wp_footer(); ?>
</body>
</html>