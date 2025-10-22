<?php

if (!defined('ABSPATH')) {
    exit;
}

class GE_Frontend_Form {
    
    public function __construct() {
        add_shortcode('ge_formulari_esdeveniment', array($this, 'render_form'));
        add_action('wp_ajax_ge_submit_event', array($this, 'handle_form_submission'));
        add_action('wp_ajax_nopriv_ge_submit_event', array($this, 'handle_form_submission'));
    }
    
    public function render_form() {
        if (!is_user_logged_in()) {
            return '<p>Has d\'iniciar sessió per enviar esdeveniments.</p>';
        }
        
        $current_user = wp_get_current_user();
        
        // Comprovar si l'usuari és un proveïdor actiu
        $proveidor = get_posts(array(
            'post_type' => 'proveidor',
            'meta_query' => array(
                array(
                    'key' => '_ge_proveidor_wp_user_id',
                    'value' => $current_user->ID,
                    'compare' => '='
                ),
                array(
                    'key' => '_ge_proveidor_actiu',
                    'value' => '1',
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        if (empty($proveidor)) {
            return '<p>No tens permisos per enviar esdeveniments. Contacta amb l\'administrador.</p>';
        }
        
        $proveidor_id = $proveidor[0]->ID;
        
        // Obtenir categories
        $categories = get_terms(array(
            'taxonomy' => 'categoria_esdeveniment',
            'hide_empty' => false
        ));
        
        // Províncies
        $provincies = array(
            'Barcelona', 'Girona', 'Lleida', 'Tarragona',
            'Madrid', 'Valencia', 'Alacant', 'Castelló',
            'Sevilla', 'Màlaga', 'Granada', 'Còrdova', 'Cadis', 'Almeria', 'Huelva', 'Jaén',
            'Múrcia',
            'La Corunya', 'Lugo', 'Ourense', 'Pontevedra',
            'Àvila', 'Burgos', 'León', 'Palència', 'Salamanca', 'Segòvia', 'Soria', 'Valladolid', 'Zamora',
            'Albacete', 'Ciudad Real', 'Conca', 'Guadalajara', 'Toledo',
            'Badajoz', 'Càceres',
            'Àlaba', 'Guipúscoa', 'Biscaia',
            'Astúries', 'Cantàbria', 'La Rioja', 'Navarra',
            'Osca', 'Saragossa', 'Terol',
            'Balears', 'Canàries (Las Palmas)', 'Canàries (Santa Cruz de Tenerife)',
            'Ceuta', 'Melilla'
        );
        
        sort($provincies);
        
        ob_start();
        ?>
        <div class="ge-frontend-form">
            <h2>Enviar Nou Esdeveniment</h2>
            
            <div id="ge-form-message"></div>
            
            <form id="ge-esdeveniment-form" enctype="multipart/form-data">
                <?php wp_nonce_field('ge_submit_event', 'ge_event_nonce'); ?>
                <input type="hidden" name="ge_proveidor_id" value="<?php echo esc_attr($proveidor_id); ?>">
                
                <div class="ge-form-group">
                    <label for="ge_artista">Artista/DJ *</label>
                    <input type="text" name="ge_artista" id="ge_artista" required>
                </div>
                
                <div class="ge-form-group">
                    <label for="ge_nom_espectacle">Nom de l'Espectacle *</label>
                    <input type="text" name="ge_nom_espectacle" id="ge_nom_espectacle" required>
                </div>
                
                <div class="ge-form-group">
                    <label for="ge_categoria">Categoria *</label>
                    <select name="ge_categoria" id="ge_categoria" required>
                        <option value="">Selecciona una categoria</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo esc_attr($cat->term_id); ?>">
                                <?php echo esc_html($cat->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="ge-form-group">
                    <label for="ge_enllac_web">Enllaç Web</label>
                    <input type="url" name="ge_enllac_web" id="ge_enllac_web">
                </div>
                
                <div class="ge-form-group">
                    <label for="ge_info_adicional">Informació Adicional</label>
                    <textarea name="ge_info_adicional" id="ge_info_adicional" rows="4"></textarea>
                </div>
                
                <div class="ge-form-group">
                    <label for="ge_imatges">Imatges de l'Esdeveniment *</label>
                    <input type="file" name="ge_imatges[]" id="ge_imatges" accept="image/*" multiple required>
                    <small>Selecciona almenys una imatge (obligatòria)</small>
                </div>
                
                <div class="ge-form-group">
                    <label for="ge_lloc_esdeveniment">Lloc de l'Esdeveniment *</label>
                    <input type="text" name="ge_lloc_esdeveniment" id="ge_lloc_esdeveniment" required>
                </div>
                
                <div class="ge-form-group">
                    <label for="ge_poblacio">Població *</label>
                    <input type="text" name="ge_poblacio" id="ge_poblacio" required>
                </div>
                
                <div class="ge-form-group">
                    <label for="ge_provincia">Província *</label>
                    <select name="ge_provincia" id="ge_provincia" required>
                        <option value="">Selecciona una província</option>
                        <?php foreach ($provincies as $prov): ?>
                            <option value="<?php echo esc_attr($prov); ?>">
                                <?php echo esc_html($prov); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="ge-form-group">
                    <label for="ge_data_inici">Data d'Inici *</label>
                    <input type="date" name="ge_data_inici" id="ge_data_inici" required>
                </div>
                
                <div class="ge-form-group">
                    <label for="ge_hora_inici">Hora d'Inici *</label>
                    <input type="time" name="ge_hora_inici" id="ge_hora_inici" required>
                </div>
                
                <div class="ge-form-group">
                    <label for="ge_data_final">Data Final *</label>
                    <input type="date" name="ge_data_final" id="ge_data_final" required>
                </div>
                
                <div class="ge-form-group">
                    <label for="ge_hora_final">Hora Final *</label>
                    <input type="time" name="ge_hora_final" id="ge_hora_final" required>
                </div>
                
                <div class="ge-form-group">
                    <label for="ge_codi_setmanal">Codi Setmanal *</label>
                    <input type="text" name="ge_codi_setmanal" id="ge_codi_setmanal" required>
                    <small>Codi per identificar la setmana de l'esdeveniment</small>
                </div>
                
                <div class="ge-form-group">
                    <button type="submit" class="ge-submit-btn">Enviar Esdeveniment</button>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function handle_form_submission() {
        check_ajax_referer('ge_submit_event', 'ge_event_nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('Has d\'iniciar sessió.');
        }
        
        $proveidor_id = intval($_POST['ge_proveidor_id']);
        
        // Validar que el proveïdor està actiu
        $actiu = get_post_meta($proveidor_id, '_ge_proveidor_actiu', true);
        if ($actiu != '1') {
            wp_send_json_error('El proveïdor no està actiu.');
        }
        
        // Crear esdeveniment
        $post_data = array(
            'post_title' => sanitize_text_field($_POST['ge_nom_espectacle']),
            'post_type' => 'esdeveniment',
            'post_status' => 'pending', // Pendent de revisió
        );
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            wp_send_json_error('Error al crear l\'esdeveniment.');
        }
        
        // Guardar meta dades
        $meta_fields = array(
            'ge_proveidor_id',
            'ge_artista',
            'ge_nom_espectacle',
            'ge_enllac_web',
            'ge_info_adicional',
            'ge_lloc_esdeveniment',
            'ge_poblacio',
            'ge_provincia',
            'ge_data_inici',
            'ge_hora_inici',
            'ge_data_final',
            'ge_hora_final',
            'ge_codi_setmanal'
        );
        
        foreach ($meta_fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
        
        // Assignar categoria
        if (isset($_POST['ge_categoria'])) {
            wp_set_object_terms($post_id, intval($_POST['ge_categoria']), 'categoria_esdeveniment');
        }
        
        // Processar imatges
        if (!empty($_FILES['ge_imatges']['name'][0])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            
            $files = $_FILES['ge_imatges'];
            $first_image = true;
            
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = array(
                        'name' => $files['name'][$key],
                        'type' => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error' => $files['error'][$key],
                        'size' => $files['size'][$key]
                    );
                    
                    $_FILES = array('upload' => $file);
                    
                    $attachment_id = media_handle_upload('upload', $post_id);
                    
                    if (!is_wp_error($attachment_id)) {
                        if ($first_image) {
                            set_post_thumbnail($post_id, $attachment_id);
                            $first_image = false;
                        }
                    }
                }
            }
        }
        
        wp_send_json_success('Esdeveniment enviat correctament. Pendent de revisió.');
    }
}

new GE_Frontend_Form();
