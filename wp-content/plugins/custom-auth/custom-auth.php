<?php
/*
Plugin Name: Sistema de Autenticación Personalizado
Description: Sistema de login y registro con verificación de email y recuperación de contraseña
Version: 1.1
Author: Antris
*/

// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

// Activación del plugin
register_activation_hook(__FILE__, 'cap_create_tables');

function cap_create_tables() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // Tabla de usuarios personalizada
    $table_users = $wpdb->prefix . 'custom_users';
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_users (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        email varchar(100) NOT NULL,
        password varchar(255) NOT NULL,
        first_name varchar(50) NOT NULL,
        last_name varchar(50) NOT NULL,
        role varchar(20) NOT NULL DEFAULT 'subscriber',
        is_verified tinyint(1) NOT NULL DEFAULT 0,
        verification_token varchar(255),
        reset_token varchar(255),
        reset_token_expiry datetime,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Agregar opciones por defecto
    add_option('cap_require_email_verification', '1');
    add_option('cap_debug_mode', '0');
}

// Agregar menú de administración
add_action('admin_menu', 'cap_admin_menu');

function cap_admin_menu() {
    add_menu_page(
        'Gestión de Usuarios',
        'Usuarios Personalizados',
        'manage_options',
        'cap-users',
        'cap_admin_page',
        'dashicons-admin-users'
    );
}

// Página de administración
function cap_admin_page() {
    global $wpdb;
    
    // Guardar cambios en la configuración
    if (isset($_POST['save_settings'])) {
        update_option('cap_require_email_verification', isset($_POST['require_email_verification']) ? '1' : '0');
        update_option('cap_debug_mode', isset($_POST['debug_mode']) ? '1' : '0');
    }

    // Verificar usuario manualmente
    if (isset($_GET['action']) && $_GET['action'] === 'verify_user' && isset($_GET['user_id'])) {
        $user_id = intval($_GET['user_id']);
        $wpdb->update(
            $wpdb->prefix . 'custom_users',
            array('is_verified' => 1),
            array('id' => $user_id)
        );
        echo '<div class="notice notice-success"><p>Usuario verificado exitosamente.</p></div>';
    }

    // Obtener usuarios
    $users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_users ORDER BY created_at DESC");
    
    ?>
    <div class="wrap">
        <h1>Gestión de Usuarios Personalizados</h1>
        
        <!-- Configuración -->
        <form method="post" action="">
            <h2>Configuración</h2>
            <table class="form-table">
                <tr>
                    <th>Requerir verificación por email</th>
                    <td>
                        <input type="checkbox" name="require_email_verification" 
                               <?php checked(get_option('cap_require_email_verification'), '1'); ?>>
                    </td>
                </tr>
                <tr>
                    <th>Modo debug (desarrollo local)</th>
                    <td>
                        <input type="checkbox" name="debug_mode" 
                               <?php checked(get_option('cap_debug_mode'), '1'); ?>>
                    </td>
                </tr>
            </table>
            <p><input type="submit" name="save_settings" class="button button-primary" value="Guardar Configuración"></p>
        </form>

        <!-- Lista de usuarios -->
        <h2>Usuarios Registrados</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Verificado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user->id; ?></td>
                    <td><?php echo esc_html($user->first_name . ' ' . $user->last_name); ?></td>
                    <td><?php echo esc_html($user->email); ?></td>
                    <td><?php echo esc_html($user->role); ?></td>
                    <td><?php echo $user->is_verified ? 'Sí' : 'No'; ?></td>
                    <td>
                        <?php if (!$user->is_verified): ?>
                        <a href="?page=cap-users&action=verify_user&user_id=<?php echo $user->id; ?>" 
                           class="button button-small">Verificar Usuario</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (get_option('cap_debug_mode') === '1'): ?>
        <!-- Información de Debug -->
        <h2>Información de Debug</h2>
        <div class="debug-info">
            <p>Enlaces de verificación para desarrollo local:</p>
            <?php
            foreach ($users as $user) {
                if (!$user->is_verified && $user->verification_token) {
                    $verify_url = add_query_arg(
                        array(
                            'action' => 'verify',
                            'email' => $user->email,
                            'token' => $user->verification_token
                        ),
                        home_url()
                    );
                    echo '<code>' . esc_url($verify_url) . '</code><br>';
                }
            }
            ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

// Modificar el proceso de registro para considerar la configuración
function cap_process_registration() {
    if (isset($_POST['custom_register_submit']) && wp_verify_nonce($_POST['_wpnonce'], 'custom_register_nonce')) {
        global $wpdb;
        
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        
        // Validaciones
        if (!is_email($email)) {
            wp_die('Email inválido');
        }
        
        if ($password !== $password_confirm) {
            wp_die('Las contraseñas no coinciden');
        }
        
        // Verificar si el email ya existe
        $existing_user = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}custom_users WHERE email = %s",
            $email
        ));
        
        if ($existing_user) {
            wp_die('Este email ya está registrado');
        }
        
        $require_verification = get_option('cap_require_email_verification') === '1';
        $verification_token = $require_verification ? wp_generate_password(32, false) : null;
        $is_verified = $require_verification ? 0 : 1;
        
        // Insertar usuario
        $wpdb->insert(
            $wpdb->prefix . 'custom_users',
            array(
                'email' => $email,
                'password' => wp_hash_password($password),
                'first_name' => $first_name,
                'last_name' => $last_name,
                'verification_token' => $verification_token,
                'is_verified' => $is_verified
            )
        );
        
        if ($require_verification) {
            // Enviar email de verificación
            cap_send_verification_email($email, $verification_token);
            
            // En modo debug, mostrar el enlace de verificación
            if (get_option('cap_debug_mode') === '1') {
                $verify_url = add_query_arg(
                    array(
                        'action' => 'verify',
                        'email' => $email,
                        'token' => $verification_token
                    ),
                    home_url()
                );
                wp_die('Usuario registrado. En modo debug, usa este enlace para verificar: <br><a href="' . esc_url($verify_url) . '">' . esc_url($verify_url) . '</a>');
            }
        }
        
        wp_redirect(home_url('/registro-exitoso'));
        exit;
    }
}

// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

// Shortcode para el formulario de registro
add_shortcode('custom_register_form', 'cap_register_form_shortcode');

function cap_register_form_shortcode() {
    ob_start();
    ?>
    <form id="custom-register-form" method="post">
        <div class="form-group">
            <label for="first_name">Nombre</label>
            <input type="text" name="first_name" required>
        </div>
        <div class="form-group">
            <label for="last_name">Apellido</label>
            <input type="text" name="last_name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirm">Confirmar Contraseña</label>
            <input type="password" name="password_confirm" required>
        </div>
        <?php wp_nonce_field('custom_register_nonce'); ?>
        <button type="submit" name="custom_register_submit">Registrarse</button>
    </form>
    <?php
    return ob_get_clean();
}

// Shortcode para el formulario de login
add_shortcode('custom_login_form', 'cap_login_form_shortcode');

function cap_login_form_shortcode() {
    ob_start();
    ?>
    <form id="custom-login-form" method="post">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" required>
        </div>
        <?php wp_nonce_field('custom_login_nonce'); ?>
        <button type="submit" name="custom_login_submit">Iniciar Sesión</button>
        <a href="<?php echo esc_url(wp_lostpassword_url()); ?>">¿Olvidaste tu contraseña?</a>
    </form>
    <?php
    return ob_get_clean();
}

// Procesar el registro
add_action('init', 'cap_process_registration');


// Procesar el login
add_action('init', 'cap_process_login');

function cap_process_login() {
    if (isset($_POST['custom_login_submit']) && wp_verify_nonce($_POST['_wpnonce'], 'custom_login_nonce')) {
        global $wpdb;
        
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        
        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}custom_users WHERE email = %s",
            $email
        ));
        
        if ($user && wp_check_password($password, $user->password)) {
            if (!$user->is_verified) {
                wp_die('Por favor verifica tu email antes de iniciar sesión');
            }
            
            // Iniciar sesión
            $_SESSION['custom_user_id'] = $user->id;
            
            wp_redirect(home_url('/dashboard'));
            exit;
        } else {
            wp_die('Credenciales inválidas');
        }
    }
}

// Función para enviar email de verificación
function cap_send_verification_email($email, $token) {
    $subject = 'Verifica tu cuenta';
    $message = sprintf(
        'Por favor verifica tu cuenta haciendo clic en el siguiente enlace: %s',
        add_query_arg(
            array(
                'action' => 'verify',
                'email' => $email,
                'token' => $token
            ),
            home_url()
        )
    );
    
    wp_mail($email, $subject, $message);
}

// Verificar email
add_action('init', 'cap_verify_email');

function cap_verify_email() {
    if (isset($_GET['action']) && $_GET['action'] === 'verify') {
        global $wpdb;
        
        $email = sanitize_email($_GET['email']);
        $token = $_GET['token'];
        
        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}custom_users WHERE email = %s AND verification_token = %s",
            $email,
            $token
        ));
        
        if ($user) {
            $wpdb->update(
                $wpdb->prefix . 'custom_users',
                array(
                    'is_verified' => 1,
                    'verification_token' => null
                ),
                array('id' => $user->id)
            );
            
            wp_redirect(home_url('/verificacion-exitosa'));
            exit;
        }
    }
}

// Recuperación de contraseña
add_action('init', 'cap_process_password_reset');

function cap_process_password_reset() {
    if (isset($_POST['custom_reset_submit'])) {
        global $wpdb;
        
        $email = sanitize_email($_POST['email']);
        
        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}custom_users WHERE email = %s",
            $email
        ));
        
        if ($user) {
            $reset_token = wp_generate_password(32, false);
            $reset_token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $wpdb->update(
                $wpdb->prefix . 'custom_users',
                array(
                    'reset_token' => $reset_token,
                    'reset_token_expiry' => $reset_token_expiry
                ),
                array('id' => $user->id)
            );
            
            // Enviar email con link de recuperación
            $subject = 'Recuperación de contraseña';
            $message = sprintf(
                'Para resetear tu contraseña, haz clic en el siguiente enlace (válido por 1 hora): %s',
                add_query_arg(
                    array(
                        'action' => 'reset',
                        'email' => $email,
                        'token' => $reset_token
                    ),
                    home_url()
                )
            );
            
            wp_mail($email, $subject, $message);
            
            wp_redirect(home_url('/reset-password-sent'));
            exit;
        }
    }
}

// Protección de contenido
function cap_is_user_logged_in() {
    return isset($_SESSION['custom_user_id']);
}

function cap_get_current_user_role() {
    if (!cap_is_user_logged_in()) {
        return false;
    }
    
    global $wpdb;
    
    $user = $wpdb->get_row($wpdb->prepare(
        "SELECT role FROM {$wpdb->prefix}custom_users WHERE id = %d",
        $_SESSION['custom_user_id']
    ));
    
    return $user ? $user->role : false;
}

// Shortcode para contenido protegido
add_shortcode('protected_content', 'cap_protected_content_shortcode');

function cap_protected_content_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'role' => 'subscriber'
    ), $atts);
    
    if (!cap_is_user_logged_in()) {
        return 'Debes iniciar sesión para ver este contenido.';
    }
    
    $user_role = cap_get_current_user_role();
    
    if ($user_role !== $atts['role'] && $user_role !== 'admin') {
        return 'No tienes permiso para ver este contenido.';
    }
    
    return do_shortcode($content);
}

// Estilos CSS
add_action('wp_enqueue_scripts', 'cap_enqueue_styles');

function cap_enqueue_styles() {
    wp_enqueue_style('custom-auth-styles', plugins_url('css/style.css', __FILE__));
}