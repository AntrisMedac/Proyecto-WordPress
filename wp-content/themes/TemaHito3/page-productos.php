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
<h1>Productos</h1>

<div class="products">
    <?php
    // Define the query arguments
    $args = array(
        'post_type' => 'product', // Only get products
        'posts_per_page' => -1,   // Get all products
    );

    // Custom query to fetch products
    $loop = new WP_Query($args);

    // Loop through products
    if ($loop->have_posts()) {
        while ($loop->have_posts()) {
            $loop->the_post();
            global $product; // Access product properties
            ?>
            <div class="product">
                <h2><?php the_title(); ?></h2>
                <?php if (has_post_thumbnail()) : ?>
                    <div class="product-image">
                        <?php the_post_thumbnail(); ?>
                    </div>
                <?php endif; ?>
                <p><?php echo $product->get_price_html(); ?></p>
                <?php
                // Display Add to Cart button
                woocommerce_template_loop_add_to_cart();
                ?>
            </div>
            <?php
        }
        wp_reset_postdata(); // Reset the post data
    } else {
        echo '<p>No hay productos disponibles.</p>';
    }
    ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
