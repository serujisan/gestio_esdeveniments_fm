<?php

if (!defined('ABSPATH')) {
    exit;
}

class GE_Admin_Categories {
    
    public function __construct() {
        add_action('categoria_esdeveniment_add_form_fields', array($this, 'add_category_fields'));
        add_action('categoria_esdeveniment_edit_form_fields', array($this, 'edit_category_fields'));
        add_action('created_categoria_esdeveniment', array($this, 'save_category_fields'));
        add_action('edited_categoria_esdeveniment', array($this, 'save_category_fields'));
        add_filter('manage_edit-categoria_esdeveniment_columns', array($this, 'add_custom_columns'));
        add_filter('manage_categoria_esdeveniment_custom_column', array($this, 'add_custom_column_content'), 10, 3);
    }
    
    public function add_category_fields($taxonomy) {
        ?>
        <div class="form-field">
            <label>Camps per Generar Text</label>
            <p>
                <label>
                    <input type="checkbox" name="ge_cat_nom_artista" value="1">
                    Nom Artista
                </label>
            </p>
            <p>
                <label>
                    <input type="checkbox" name="ge_cat_municipio" value="1">
                    Municipi
                </label>
            </p>
            <p>
                <label>
                    <input type="checkbox" name="ge_cat_any" value="1">
                    Any
                </label>
            </p>
            <p>
                <label>
                    <input type="checkbox" name="ge_cat_que_hacer" value="1">
                    Què Fer
                </label>
            </p>
            <p>
                <label>
                    <input type="checkbox" name="ge_cat_concierto" value="1">
                    Concert
                </label>
            </p>
            <p>
                <label>
                    <input type="checkbox" name="ge_cat_notas" value="1">
                    Notes
                </label>
            </p>
            <p class="description">Selecciona els camps que s'utilitzaran per generar el text de la categoria.</p>
        </div>
        <?php
    }
    
    public function edit_category_fields($term) {
        $term_id = $term->term_id;
        $nom_artista = get_term_meta($term_id, 'ge_cat_nom_artista', true);
        $municipio = get_term_meta($term_id, 'ge_cat_municipio', true);
        $any = get_term_meta($term_id, 'ge_cat_any', true);
        $que_hacer = get_term_meta($term_id, 'ge_cat_que_hacer', true);
        $concierto = get_term_meta($term_id, 'ge_cat_concierto', true);
        $notas = get_term_meta($term_id, 'ge_cat_notas', true);
        ?>
        <tr class="form-field">
            <th scope="row">
                <label>Camps per Generar Text</label>
            </th>
            <td>
                <fieldset>
                    <p>
                        <label>
                            <input type="checkbox" name="ge_cat_nom_artista" value="1" <?php checked($nom_artista, '1'); ?>>
                            Nom Artista
                        </label>
                    </p>
                    <p>
                        <label>
                            <input type="checkbox" name="ge_cat_municipio" value="1" <?php checked($municipio, '1'); ?>>
                            Municipi
                        </label>
                    </p>
                    <p>
                        <label>
                            <input type="checkbox" name="ge_cat_any" value="1" <?php checked($any, '1'); ?>>
                            Any
                        </label>
                    </p>
                    <p>
                        <label>
                            <input type="checkbox" name="ge_cat_que_hacer" value="1" <?php checked($que_hacer, '1'); ?>>
                            Què Fer
                        </label>
                    </p>
                    <p>
                        <label>
                            <input type="checkbox" name="ge_cat_concierto" value="1" <?php checked($concierto, '1'); ?>>
                            Concert
                        </label>
                    </p>
                    <p>
                        <label>
                            <input type="checkbox" name="ge_cat_notas" value="1" <?php checked($notas, '1'); ?>>
                            Notes
                        </label>
                    </p>
                    <p class="description">Selecciona els camps que s'utilitzaran per generar el text de la categoria.</p>
                </fieldset>
            </td>
        </tr>
        <?php
    }
    
    public function save_category_fields($term_id) {
        $fields = array(
            'ge_cat_nom_artista',
            'ge_cat_municipio',
            'ge_cat_any',
            'ge_cat_que_hacer',
            'ge_cat_concierto',
            'ge_cat_notas'
        );
        
        foreach ($fields as $field) {
            $value = isset($_POST[$field]) ? '1' : '0';
            update_term_meta($term_id, $field, $value);
        }
    }
    
    public function add_custom_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['name'] = $columns['name'];
        $new_columns['ge_fields'] = 'Camps Configurats';
        $new_columns['slug'] = $columns['slug'];
        $new_columns['posts'] = $columns['posts'];
        return $new_columns;
    }
    
    public function add_custom_column_content($content, $column_name, $term_id) {
        if ($column_name == 'ge_fields') {
            $fields = array(
                'ge_cat_nom_artista' => 'Nom Artista',
                'ge_cat_municipio' => 'Municipi',
                'ge_cat_any' => 'Any',
                'ge_cat_que_hacer' => 'Què Fer',
                'ge_cat_concierto' => 'Concert',
                'ge_cat_notas' => 'Notes'
            );
            
            $active_fields = array();
            foreach ($fields as $key => $label) {
                $value = get_term_meta($term_id, $key, true);
                if ($value == '1') {
                    $active_fields[] = $label;
                }
            }
            
            if (!empty($active_fields)) {
                $content = implode(', ', $active_fields);
            } else {
                $content = '<em>Cap camp configurat</em>';
            }
        }
        return $content;
    }
    
    /**
     * Funció auxiliar per generar text segons els camps configurats
     */
    public static function generate_category_text($term_id, $event_data) {
        $fields = array(
            'ge_cat_nom_artista' => isset($event_data['artista']) ? $event_data['artista'] : '',
            'ge_cat_municipio' => isset($event_data['poblacio']) ? $event_data['poblacio'] : '',
            'ge_cat_any' => date('Y'),
            'ge_cat_que_hacer' => isset($event_data['que_hacer']) ? $event_data['que_hacer'] : '',
            'ge_cat_concierto' => isset($event_data['concierto']) ? $event_data['concierto'] : '',
            'ge_cat_notas' => isset($event_data['notas']) ? $event_data['notas'] : ''
        );
        
        $text_parts = array();
        
        foreach ($fields as $key => $value) {
            $is_active = get_term_meta($term_id, $key, true);
            if ($is_active == '1' && !empty($value)) {
                $text_parts[] = $value;
            }
        }
        
        return implode(' - ', $text_parts);
    }
}

new GE_Admin_Categories();
