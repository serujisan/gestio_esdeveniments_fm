<?php

if (!defined('ABSPATH')) {
    exit;
}

class GE_Meta_Boxes {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_esdeveniment_meta'), 10, 2);
        add_action('save_post', array($this, 'save_proveidor_meta'), 10, 2);
    }
    
    public function add_meta_boxes() {
        // Meta box per Esdeveniments
        add_meta_box(
            'ge_esdeveniment_details',
            'Detalls de l\'Esdeveniment',
            array($this, 'render_esdeveniment_meta_box'),
            'esdeveniment',
            'normal',
            'high'
        );
        
        // Meta box per Proveïdors
        add_meta_box(
            'ge_proveidor_details',
            'Detalls del Proveïdor',
            array($this, 'render_proveidor_meta_box'),
            'proveidor',
            'normal',
            'high'
        );
    }
    
    public function render_esdeveniment_meta_box($post) {
        wp_nonce_field('ge_esdeveniment_meta_box', 'ge_esdeveniment_meta_box_nonce');
        
        $proveidor_id = get_post_meta($post->ID, '_ge_proveidor_id', true);
        $artista = get_post_meta($post->ID, '_ge_artista', true);
        $nom_espectacle = get_post_meta($post->ID, '_ge_nom_espectacle', true);
        $enllac_web = get_post_meta($post->ID, '_ge_enllac_web', true);
        $info_adicional = get_post_meta($post->ID, '_ge_info_adicional', true);
        $lloc_esdeveniment = get_post_meta($post->ID, '_ge_lloc_esdeveniment', true);
        $poblacio = get_post_meta($post->ID, '_ge_poblacio', true);
        $provincia = get_post_meta($post->ID, '_ge_provincia', true);
        $data_inici = get_post_meta($post->ID, '_ge_data_inici', true);
        $hora_inici = get_post_meta($post->ID, '_ge_hora_inici', true);
        $data_final = get_post_meta($post->ID, '_ge_data_final', true);
        $hora_final = get_post_meta($post->ID, '_ge_hora_final', true);
        $codi_setmanal = get_post_meta($post->ID, '_ge_codi_setmanal', true);
        $patrocinat = get_post_meta($post->ID, '_ge_patrocinat', true);
        
        // Obtenir proveidors
        $proveidors = get_posts(array(
            'post_type' => 'proveidor',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_ge_proveidor_actiu',
                    'value' => '1',
                    'compare' => '='
                )
            )
        ));
        
        // Províncies d'Espanya
        $provincies = array(
            'Barcelona', 'Girona', 'Lleida', 'Tarragona',
            'Madrid', 'Valencia', 'Alacant', 'Castelló',
            'Sevilla', 'Màlaga', 'Granada', 'Còrdova', 'Cadis', 'Almeria', 'Huelva', 'Jaén',
            'Múrcia', 'Alacant', 'Castelló', 'Valencia',
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
        
        ?>
        <div class="ge-meta-box">
            <p>
                <label for="ge_proveidor_id"><strong>Proveïdor *</strong></label><br>
                <select name="ge_proveidor_id" id="ge_proveidor_id" style="width: 100%;" required>
                    <option value="">Selecciona un proveïdor</option>
                    <?php foreach ($proveidors as $proveidor): ?>
                        <option value="<?php echo esc_attr($proveidor->ID); ?>" <?php selected($proveidor_id, $proveidor->ID); ?>>
                            <?php echo esc_html($proveidor->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            
            <p>
                <label for="ge_artista"><strong>Artista/DJ *</strong></label><br>
                <input type="text" name="ge_artista" id="ge_artista" value="<?php echo esc_attr($artista); ?>" style="width: 100%;" required>
            </p>
            
            <p>
                <label for="ge_nom_espectacle"><strong>Nom de l'Espectacle *</strong></label><br>
                <input type="text" name="ge_nom_espectacle" id="ge_nom_espectacle" value="<?php echo esc_attr($nom_espectacle); ?>" style="width: 100%;" required>
            </p>
            
            <p>
                <label for="ge_enllac_web"><strong>Enllaç Web</strong></label><br>
                <input type="url" name="ge_enllac_web" id="ge_enllac_web" value="<?php echo esc_attr($enllac_web); ?>" style="width: 100%;">
            </p>
            
            <p>
                <label for="ge_info_adicional"><strong>Informació Adicional</strong></label><br>
                <textarea name="ge_info_adicional" id="ge_info_adicional" rows="4" style="width: 100%;"><?php echo esc_textarea($info_adicional); ?></textarea>
            </p>
            
            <p>
                <label for="ge_lloc_esdeveniment"><strong>Lloc de l'Esdeveniment *</strong></label><br>
                <input type="text" name="ge_lloc_esdeveniment" id="ge_lloc_esdeveniment" value="<?php echo esc_attr($lloc_esdeveniment); ?>" style="width: 100%;" required>
            </p>
            
            <p>
                <label for="ge_poblacio"><strong>Població *</strong></label><br>
                <input type="text" name="ge_poblacio" id="ge_poblacio" value="<?php echo esc_attr($poblacio); ?>" style="width: 100%;" required>
            </p>
            
            <p>
                <label for="ge_provincia"><strong>Província *</strong></label><br>
                <select name="ge_provincia" id="ge_provincia" style="width: 100%;" required>
                    <option value="">Selecciona una província</option>
                    <?php foreach ($provincies as $prov): ?>
                        <option value="<?php echo esc_attr($prov); ?>" <?php selected($provincia, $prov); ?>>
                            <?php echo esc_html($prov); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            
            <p>
                <label for="ge_data_inici"><strong>Data d'Inici *</strong></label><br>
                <input type="date" name="ge_data_inici" id="ge_data_inici" value="<?php echo esc_attr($data_inici); ?>" required>
            </p>
            
            <p>
                <label for="ge_hora_inici"><strong>Hora d'Inici *</strong></label><br>
                <input type="time" name="ge_hora_inici" id="ge_hora_inici" value="<?php echo esc_attr($hora_inici); ?>" required>
            </p>
            
            <p>
                <label for="ge_data_final"><strong>Data Final *</strong></label><br>
                <input type="date" name="ge_data_final" id="ge_data_final" value="<?php echo esc_attr($data_final); ?>" required>
            </p>
            
            <p>
                <label for="ge_hora_final"><strong>Hora Final *</strong></label><br>
                <input type="time" name="ge_hora_final" id="ge_hora_final" value="<?php echo esc_attr($hora_final); ?>" required>
            </p>
            
            <p>
                <label for="ge_codi_setmanal"><strong>Codi Setmanal *</strong></label><br>
                <input type="text" name="ge_codi_setmanal" id="ge_codi_setmanal" value="<?php echo esc_attr($codi_setmanal); ?>" style="width: 100%;" required>
                <small>Codi per identificar la setmana de l'esdeveniment</small>
            </p>
            
            <p>
                <label>
                    <input type="checkbox" name="ge_patrocinat" id="ge_patrocinat" value="1" <?php checked($patrocinat, '1'); ?>>
                    <strong>Esdeveniment Patrocinat</strong>
                </label>
            </p>
        </div>
        <?php
    }
    
    public function render_proveidor_meta_box($post) {
        wp_nonce_field('ge_proveidor_meta_box', 'ge_proveidor_meta_box_nonce');
        
        $wp_user_id = get_post_meta($post->ID, '_ge_proveidor_wp_user_id', true);
        $actiu = get_post_meta($post->ID, '_ge_proveidor_actiu', true);
        $link_wordpress = get_post_meta($post->ID, '_ge_proveidor_link_wordpress', true);
        
        // Obtenir usuaris de WordPress
        $users = get_users(array('orderby' => 'display_name'));
        
        ?>
        <div class="ge-meta-box">
            <p>
                <label for="ge_proveidor_link_wordpress"><strong>Link WordPress</strong></label><br>
                <input type="url" name="ge_proveidor_link_wordpress" id="ge_proveidor_link_wordpress" value="<?php echo esc_attr($link_wordpress); ?>" style="width: 100%;">
                <small>Enllaç a la pàgina del proveïdor</small>
            </p>
            
            <p>
                <label for="ge_proveidor_wp_user_id"><strong>Usuari WordPress Associat</strong></label><br>
                <select name="ge_proveidor_wp_user_id" id="ge_proveidor_wp_user_id" style="width: 100%;">
                    <option value="">Cap usuari associat</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($wp_user_id, $user->ID); ?>>
                            <?php echo esc_html($user->display_name . ' (' . $user->user_email . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            
            <p>
                <label>
                    <input type="checkbox" name="ge_proveidor_actiu" id="ge_proveidor_actiu" value="1" <?php checked($actiu, '1'); ?>>
                    <strong>Proveïdor Actiu</strong>
                </label>
            </p>
        </div>
        <?php
    }
    
    public function save_esdeveniment_meta($post_id, $post) {
        if (!isset($_POST['ge_esdeveniment_meta_box_nonce']) || 
            !wp_verify_nonce($_POST['ge_esdeveniment_meta_box_nonce'], 'ge_esdeveniment_meta_box')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if ($post->post_type != 'esdeveniment') {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        $fields = array(
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
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
        
        $patrocinat = isset($_POST['ge_patrocinat']) ? '1' : '0';
        update_post_meta($post_id, '_ge_patrocinat', $patrocinat);
    }
    
    public function save_proveidor_meta($post_id, $post) {
        if (!isset($_POST['ge_proveidor_meta_box_nonce']) || 
            !wp_verify_nonce($_POST['ge_proveidor_meta_box_nonce'], 'ge_proveidor_meta_box')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if ($post->post_type != 'proveidor') {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (isset($_POST['ge_proveidor_link_wordpress'])) {
            update_post_meta($post_id, '_ge_proveidor_link_wordpress', esc_url_raw($_POST['ge_proveidor_link_wordpress']));
        }
        
        if (isset($_POST['ge_proveidor_wp_user_id'])) {
            update_post_meta($post_id, '_ge_proveidor_wp_user_id', intval($_POST['ge_proveidor_wp_user_id']));
        }
        
        $actiu = isset($_POST['ge_proveidor_actiu']) ? '1' : '0';
        update_post_meta($post_id, '_ge_proveidor_actiu', $actiu);
    }
}

new GE_Meta_Boxes();
