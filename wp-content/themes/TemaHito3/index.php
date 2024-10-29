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
            <!-- Usar home_url() para el logo -->
            <a href="<?php echo home_url('/'); ?>" class="logo">E-Commerce</a>
            <div class="nav-links">
                <!-- Usar home_url() para enlaces internos en WordPress -->
                <a href="<?php echo home_url('/'); ?>">Inicio</a>
                <a href="<?php echo home_url('/Productos/'); ?>">Productos</a>
                <a href="<?php echo home_url('/Contacto/'); ?>">Contacto</a>
            </div>
        </nav>
    </header>
    <h1>Descubre la Moda que Te Define</h1>
    <p>¡Bienvenido a nuestra tienda! Aquí encontrarás las últimas tendencias en ropa.</p>

    <h2>Productos Destacados</h2>
    [Shortcode de WooCommerce para productos destacados]

    <h2>¿Por qué elegirnos?</h2>
    <ul>
        <li>Envío gratis en pedidos superiores a 50€</li>
        <li>Devoluciones fáciles</li>
        <li>Atención al cliente 24/7</li>
    </ul>

    <h2>Testimonios</h2>
    <blockquote>¡Me encanta mi nueva ropa! - Cliente Satisfecho</blockquote>

    <h2>Suscríbete a nuestro boletín</h2>
    <form>
    <input type="email" placeholder="Tu correo electrónico">
    <button type="submit">Suscribirse</button>
    </form>

    <button><a href="#productos">Comprar Ahora</a></button>

</body>
