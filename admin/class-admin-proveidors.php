<?php

if (!defined('ABSPATH')) {
    exit;
}

class GE_Admin_Proveidors {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=esdeveniment',
            'Gestió de Proveïdors',
            'Gestió Proveïdors',
            'manage_options',
            'ge-proveidors',
            array($this, 'render_admin_page')
        );
    }
    
    public function render_admin_page() {
        // Gestionar accions
        if (isset($_POST['ge_save_proveidor']) && check_admin_referer('ge_save_proveidor_action')) {
            $this->save_proveidor();
        }
        
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['proveidor_id'])) {
            $this->delete_proveidor(intval($_GET['proveidor_id']));
        }
        
        $proveidors = get_posts(array(
            'post_type' => 'proveidor',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        ?>
        <div class="wrap">
            <h1>Gestió de Proveïdors</h1>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <?php 
                        if ($_GET['message'] == 'saved') {
                            echo 'Proveïdor desat correctament.';
                        } elseif ($_GET['message'] == 'deleted') {
                            echo 'Proveïdor eliminat correctament.';
                        }
                        ?>
                    </p>
                </div>
            <?php endif; ?>
            
            <div class="ge-admin-section">
                <h2>Afegir/Editar Proveïdor</h2>
                <form method="post" action="">
                    <?php wp_nonce_field('ge_save_proveidor_action'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="proveidor_nom">Nom del Proveïdor *</label>
                            </th>
                            <td>
                                <input type="text" name="proveidor_nom" id="proveidor_nom" class="regular-text" required>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="proveidor_link">Link WordPress</label>
                            </th>
                            <td>
                                <input type="url" name="proveidor_link" id="proveidor_link" class="regular-text" placeholder="https://example.com/proveidor">
                                <p class="description">Enllaç a la pàgina del proveïdor</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="proveidor_wp_user">Usuari WordPress Associat</label>
                            </th>
                            <td>
                                <select name="proveidor_wp_user" id="proveidor_wp_user" class="regular-text">
                                    <option value="">Cap usuari associat</option>
                                    <?php
                                    $users = get_users(array('orderby' => 'display_name'));
                                    foreach ($users as $user) {
                                        echo '<option value="' . esc_attr($user->ID) . '">' . esc_html($user->display_name . ' (' . $user->user_email . ')') . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="proveidor_actiu">Estat</label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="proveidor_actiu" id="proveidor_actiu" value="1" checked>
                                    Proveïdor Actiu
                                </label>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" name="ge_save_proveidor" class="button button-primary" value="Desar Proveïdor">
                    </p>
                </form>
            </div>
            
            <div class="ge-admin-section">
                <h2>Llistat de Proveïdors</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Usuari WordPress</th>
                            <th>Estat</th>
                            <th>Esdeveniments</th>
                            <th>Accions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($proveidors)): ?>
                            <?php foreach ($proveidors as $prov): ?>
                                <?php
                                $wp_user_id = get_post_meta($prov->ID, '_ge_proveidor_wp_user_id', true);
                                $actiu = get_post_meta($prov->ID, '_ge_proveidor_actiu', true);
                                $user_info = '';
                                
                                if ($wp_user_id) {
                                    $user = get_userdata($wp_user_id);
                                    if ($user) {
                                        $user_info = $user->display_name . ' (' . $user->user_email . ')';
                                    }
                                }
                                
                                // Comptar esdeveniments d'aquest proveïdor
                                $esdeveniments_count = count(get_posts(array(
                                    'post_type' => 'esdeveniment',
                                    'posts_per_page' => -1,
                                    'meta_query' => array(
                                        array(
                                            'key' => '_ge_proveidor_id',
                                            'value' => $prov->ID,
                                            'compare' => '='
                                        )
                                    )
                                )));
                                ?>
                                <tr>
                                    <td><strong><?php echo esc_html($prov->post_title); ?></strong></td>
                                    <td><?php echo esc_html($user_info ? $user_info : 'Cap usuari'); ?></td>
                                    <td>
                                        <?php if ($actiu == '1'): ?>
                                            <span style="color: green;">✓ Actiu</span>
                                        <?php else: ?>
                                            <span style="color: red;">✗ Inactiu</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $esdeveniments_count; ?> esdeveniments</td>
                                    <td>
                                        <a href="<?php echo admin_url('post.php?post=' . $prov->ID . '&action=edit'); ?>" class="button button-small">Editar</a>
                                        <a href="<?php echo admin_url('admin.php?page=ge-proveidors&action=delete&proveidor_id=' . $prov->ID); ?>" 
                                           class="button button-small" 
                                           onclick="return confirm('Estàs segur que vols eliminar aquest proveïdor?');">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No hi ha proveïdors registrats.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
    
    private function save_proveidor() {
        if (empty($_POST['proveidor_nom'])) {
            return;
        }
        
        $post_data = array(
            'post_title' => sanitize_text_field($_POST['proveidor_nom']),
            'post_type' => 'proveidor',
            'post_status' => 'publish'
        );
        
        $post_id = wp_insert_post($post_data);
        
        if (!is_wp_error($post_id)) {
            if (isset($_POST['proveidor_link']) && !empty($_POST['proveidor_link'])) {
                update_post_meta($post_id, '_ge_proveidor_link_wordpress', esc_url_raw($_POST['proveidor_link']));
            }
            
            if (isset($_POST['proveidor_wp_user']) && !empty($_POST['proveidor_wp_user'])) {
                update_post_meta($post_id, '_ge_proveidor_wp_user_id', intval($_POST['proveidor_wp_user']));
            }
            
            $actiu = isset($_POST['proveidor_actiu']) ? '1' : '0';
            update_post_meta($post_id, '_ge_proveidor_actiu', $actiu);
            
            wp_redirect(admin_url('admin.php?page=ge-proveidors&message=saved'));
            exit;
        }
    }
    
    private function delete_proveidor($proveidor_id) {
        if ($proveidor_id > 0) {
            wp_delete_post($proveidor_id, true);
            wp_redirect(admin_url('admin.php?page=ge-proveidors&message=deleted'));
            exit;
        }
    }
}

new GE_Admin_Proveidors();
