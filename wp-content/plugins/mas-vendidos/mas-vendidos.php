<?php
/**
 * Plugin Name: Mas vendidos
 * Description: Mas vendidos
 * Version: 1.1
 * Author: Antris
 */

 // Registrar el shortcode [productos_mas_vendidos]
function mostrar_productos_mas_vendidos() {
    ob_start();
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 10,
        'meta_key' => 'total_sales',
        'orderby' => 'meta_value_num',
    );
    $productos = new WP_Query($args);
    if ($productos->have_posts()) {
        echo '<ul class="productos-mas-vendidos">';
        while ($productos->have_posts()) {
            $productos->the_post();
            global $product;
            echo '<li>';
            echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
            echo '<p>Precio: ' . $product->get_price_html() . '</p>';
            echo '</li>';
        }
        echo '</ul>';
        wp_reset_postdata();
    } else {
        echo '<p>No hay productos disponibles.</p>';
    }
    return ob_get_clean();
}
add_shortcode('productos_mas_vendidos', 'mostrar_productos_mas_vendidos');

function crear_tabla_estadisticas() {
    global $wpdb;
    $tabla_estadisticas = $wpdb->prefix . 'estadisticas_productos';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tabla_estadisticas (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        product_id mediumint(9) NOT NULL,
        visitas bigint(20) DEFAULT 0 NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY product_id (product_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'crear_tabla_estadisticas');

function incrementar_visitas_producto() {
    if (is_singular('product')) {
        global $post, $wpdb;
        $tabla_estadisticas = $wpdb->prefix . 'estadisticas_productos';
        $product_id = $post->ID;
        $visitas = $wpdb->get_var($wpdb->prepare(
            "SELECT visitas FROM $tabla_estadisticas WHERE product_id = %d",
            $product_id
        ));

        if ($visitas !== null) {
            $wpdb->update(
                $tabla_estadisticas,
                array('visitas' => $visitas + 1),
                array('product_id' => $product_id),
                array('%d'),
                array('%d')
            );
        } else {
            $wpdb->insert(
                $tabla_estadisticas,
                array(
                    'product_id' => $product_id,
                    'visitas' => 1,
                ),
                array(
                    '%d',
                    '%d',
                )
            );
        }
    }
}
add_action('wp', 'incrementar_visitas_producto');

function mostrar_visitas_producto() {
    if (is_singular('product')) {
        global $post, $wpdb;
        $tabla_estadisticas = $wpdb->prefix . 'estadisticas_productos';
        $product_id = $post->ID;
        $visitas = $wpdb->get_var($wpdb->prepare(
            "SELECT visitas FROM $tabla_estadisticas WHERE product_id = %d",
            $product_id
        ));
        if ($visitas !== null) {
            echo '<p>Este producto ha sido visto ' . esc_html($visitas) . ' veces.</p>';
        }
    }
}
add_action('woocommerce_after_single_product_summary', 'mostrar_visitas_producto', 15);
