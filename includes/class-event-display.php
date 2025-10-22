<?php

if (!defined('ABSPATH')) {
    exit;
}

class GE_Event_Display {
    
    public function __construct() {
        add_shortcode('ge_esdeveniments_list', array($this, 'render_events_list'));
        add_action('wp_ajax_ge_filter_events', array($this, 'ajax_filter_events'));
        add_action('wp_ajax_nopriv_ge_filter_events', array($this, 'ajax_filter_events'));
    }
    
    public function render_events_list($atts) {
        $atts = shortcode_atts(array(
            'posts_per_page' => -1
        ), $atts);
        
        // Obtenir categories, províncies i poblacions per filtres
        $categories = get_terms(array(
            'taxonomy' => 'categoria_esdeveniment',
            'hide_empty' => true
        ));
        
        // Obtenir províncies úniques
        global $wpdb;
        $provincies = $wpdb->get_col(
            "SELECT DISTINCT meta_value 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = '_ge_provincia' 
            AND meta_value != '' 
            ORDER BY meta_value ASC"
        );
        
        ob_start();
        ?>
        <div class="ge-events-container">
            <div class="ge-filters">
                <h3>Filtres</h3>
                <div class="ge-filter-group">
                    <label for="filter_provincia">Província:</label>
                    <select id="filter_provincia" name="filter_provincia">
                        <option value="">Totes les províncies</option>
                        <?php foreach ($provincies as $prov): ?>
                            <option value="<?php echo esc_attr($prov); ?>">
                                <?php echo esc_html($prov); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="ge-filter-group">
                    <label for="filter_poblacio">Població:</label>
                    <input type="text" id="filter_poblacio" name="filter_poblacio" placeholder="Escriu una població">
                </div>
                
                <div class="ge-filter-group">
                    <label for="filter_categoria">Categoria:</label>
                    <select id="filter_categoria" name="filter_categoria">
                        <option value="">Totes les categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo esc_attr($cat->term_id); ?>">
                                <?php echo esc_html($cat->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button id="ge-apply-filters" class="ge-filter-btn">Aplicar Filtres</button>
                <button id="ge-reset-filters" class="ge-filter-btn">Netejar Filtres</button>
            </div>
            
            <div id="ge-events-list">
                <?php echo $this->get_events_html(); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function ajax_filter_events() {
        $provincia = isset($_POST['provincia']) ? sanitize_text_field($_POST['provincia']) : '';
        $poblacio = isset($_POST['poblacio']) ? sanitize_text_field($_POST['poblacio']) : '';
        $categoria = isset($_POST['categoria']) ? intval($_POST['categoria']) : 0;
        
        echo $this->get_events_html($provincia, $poblacio, $categoria);
        wp_die();
    }
    
    private function get_events_html($provincia = '', $poblacio = '', $categoria = 0) {
        // Obtenir la setmana actual
        $current_week = date('W');
        $current_year = date('Y');
        $codi_setmanal = $current_year . '-W' . str_pad($current_week, 2, '0', STR_PAD_LEFT);
        
        // Construir meta_query
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key' => '_ge_codi_setmanal',
                'value' => $codi_setmanal,
                'compare' => '='
            )
        );
        
        if (!empty($provincia)) {
            $meta_query[] = array(
                'key' => '_ge_provincia',
                'value' => $provincia,
                'compare' => '='
            );
        }
        
        if (!empty($poblacio)) {
            $meta_query[] = array(
                'key' => '_ge_poblacio',
                'value' => $poblacio,
                'compare' => 'LIKE'
            );
        }
        
        // Data query per ordenar per data i hora d'inici
        $meta_query['data_inici'] = array(
            'key' => '_ge_data_inici',
            'type' => 'DATE'
        );
        
        $meta_query['hora_inici'] = array(
            'key' => '_ge_hora_inici',
            'type' => 'TIME'
        );
        
        $meta_query['patrocinat'] = array(
            'key' => '_ge_patrocinat',
            'compare' => 'EXISTS'
        );
        
        // Args per la query
        $args = array(
            'post_type' => 'esdeveniment',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => $meta_query,
            'orderby' => array(
                'patrocinat' => 'DESC',
                'data_inici' => 'ASC',
                'hora_inici' => 'ASC'
            )
        );
        
        // Afegir tax_query si hi ha categoria
        if ($categoria > 0) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'categoria_esdeveniment',
                    'field' => 'term_id',
                    'terms' => $categoria
                )
            );
        }
        
        $query = new WP_Query($args);
        
        ob_start();
        
        if ($query->have_posts()) {
            echo '<div class="ge-events-grid">';
            
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                
                $artista = get_post_meta($post_id, '_ge_artista', true);
                $lloc = get_post_meta($post_id, '_ge_lloc_esdeveniment', true);
                $poblacio_post = get_post_meta($post_id, '_ge_poblacio', true);
                $provincia_post = get_post_meta($post_id, '_ge_provincia', true);
                $data_inici = get_post_meta($post_id, '_ge_data_inici', true);
                $hora_inici = get_post_meta($post_id, '_ge_hora_inici', true);
                $patrocinat = get_post_meta($post_id, '_ge_patrocinat', true);
                $enllac_web = get_post_meta($post_id, '_ge_enllac_web', true);
                
                $thumbnail = get_the_post_thumbnail($post_id, 'medium');
                if (empty($thumbnail)) {
                    $thumbnail = '<img src="' . GE_PLUGIN_URL . 'assets/images/placeholder.jpg" alt="Sense imatge">';
                }
                
                $categories = get_the_terms($post_id, 'categoria_esdeveniment');
                $categoria_nom = '';
                if ($categories && !is_wp_error($categories)) {
                    $categoria_nom = $categories[0]->name;
                }
                
                $patrocinat_class = ($patrocinat == '1') ? ' ge-event-sponsored' : '';
                
                ?>
                <div class="ge-event-item<?php echo $patrocinat_class; ?>">
                    <?php if ($patrocinat == '1'): ?>
                        <span class="ge-sponsored-badge">Patrocinat</span>
                    <?php endif; ?>
                    
                    <div class="ge-event-image">
                        <?php echo $thumbnail; ?>
                    </div>
                    
                    <div class="ge-event-content">
                        <h3><?php the_title(); ?></h3>
                        
                        <?php if (!empty($artista)): ?>
                            <p class="ge-event-artist"><strong>Artista:</strong> <?php echo esc_html($artista); ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($categoria_nom)): ?>
                            <p class="ge-event-category"><strong>Categoria:</strong> <?php echo esc_html($categoria_nom); ?></p>
                        <?php endif; ?>
                        
                        <p class="ge-event-location">
                            <strong>Lloc:</strong> <?php echo esc_html($lloc); ?><br>
                            <?php echo esc_html($poblacio_post); ?>, <?php echo esc_html($provincia_post); ?>
                        </p>
                        
                        <p class="ge-event-date">
                            <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($data_inici)); ?> a les <?php echo esc_html($hora_inici); ?>
                        </p>
                        
                        <?php if (!empty($enllac_web)): ?>
                            <p class="ge-event-link">
                                <a href="<?php echo esc_url($enllac_web); ?>" target="_blank" rel="noopener">Més informació</a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p class="ge-no-events">No s\'han trobat esdeveniments per aquesta setmana amb els filtres seleccionats.</p>';
        }
        
        return ob_get_clean();
    }
}

new GE_Event_Display();
