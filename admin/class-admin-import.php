<?php

if (!defined('ABSPATH')) {
    exit;
}

class GE_Admin_Import {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_ge_import_proveidors', array($this, 'handle_proveidors_import'));
        add_action('admin_post_ge_import_categories', array($this, 'handle_categories_import'));
        add_action('admin_post_ge_import_events', array($this, 'handle_events_import'));
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=esdeveniment',
            'Importar Dades',
            'Importar CSV',
            'manage_options',
            'ge-import',
            array($this, 'render_admin_page')
        );
    }
    
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1>Importar Dades des de CSV</h1>
            <p>Utilitza aquesta pàgina per importar proveïdors i categories des d'arxius CSV.</p>
            
            <?php if (isset($_GET['import_success'])): ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <?php 
                        $type = sanitize_text_field($_GET['type']);
                        $count = intval($_GET['count']);
                        if ($type == 'proveidors') {
                            echo sprintf('S\'han importat <strong>%d proveïdors</strong> correctament.', $count);
                        } elseif ($type == 'categories') {
                            echo sprintf('S\'han importat <strong>%d categories</strong> correctament.', $count);
                        } elseif ($type == 'esdeveniments') {
                            echo sprintf('S\'han importat <strong>%d esdeveniments</strong> correctament.', $count);
                            if (isset($_GET['errors'])) {
                                $errors = intval($_GET['errors']);
                                echo sprintf(' <em>(%d amb errors)</em>', $errors);
                            }
                        }
                        ?>
                    </p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['import_error'])): ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php echo sanitize_text_field($_GET['message']); ?></p>
                </div>
            <?php endif; ?>
            
            <!-- Importar Proveïdors -->
            <div class="ge-admin-section">
                <h2>📋 Importar Proveïdors</h2>
                <p>Importa una llista de proveïdors des d'un arxiu CSV.</p>
                
                <div class="ge-import-format">
                    <h3>Format del CSV:</h3>
                    <p>L'arxiu CSV ha de tenir les següents columnes (amb capçalera):</p>
                    <div class="ge-csv-example">
                        <code>nom,link_wordpress,email_usuari,actiu</code>
                    </div>
                    <ul>
                        <li><strong>nom</strong> (obligatori): Nom del proveïdor</li>
                        <li><strong>link_wordpress</strong> (opcional): Enllaç a la pàgina del proveïdor</li>
                        <li><strong>email_usuari</strong> (opcional): Email de l'usuari WordPress a associar</li>
                        <li><strong>actiu</strong> (opcional): 1 per actiu, 0 per inactiu (per defecte: 1)</li>
                    </ul>
                    
                    <h4>Exemple:</h4>
                    <pre>nom,link_wordpress,email_usuari,actiu
Proveïdor 1,https://example.com/proveidor1,usuari1@example.com,1
Proveïdor 2,https://example.com/proveidor2,usuari2@example.com,1
Proveïdor 3,,,0</pre>
                </div>
                
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
                    <?php wp_nonce_field('ge_import_proveidors_action', 'ge_import_proveidors_nonce'); ?>
                    <input type="hidden" name="action" value="ge_import_proveidors">
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="proveidors_csv">Arxiu CSV de Proveïdors</label>
                            </th>
                            <td>
                                <input type="file" name="proveidors_csv" id="proveidors_csv" accept=".csv" required>
                                <p class="description">Selecciona un arxiu CSV amb els proveïdors a importar.</p>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" name="submit" class="button button-primary" value="Importar Proveïdors">
                    </p>
                </form>
                
                <div class="ge-download-template">
                    <h4>Descarregar plantilla:</h4>
                    <button class="button" onclick="downloadProveidorsTemplate()">📥 Descarregar Plantilla CSV</button>
                </div>
            </div>
            
            <!-- Importar Categories -->
            <div class="ge-admin-section">
                <h2>🏷️ Importar Categories</h2>
                <p>Importa categories d'esdeveniments des d'un arxiu CSV.</p>
                
                <div class="ge-import-format">
                    <h3>Format del CSV:</h3>
                    <p>L'arxiu CSV ha de tenir les següents columnes (amb capçalera):</p>
                    <div class="ge-csv-example">
                        <code>nom,slug,descripcio,nom_artista,municipio,any,que_hacer,concierto,notas</code>
                    </div>
                    <ul>
                        <li><strong>nom</strong> (obligatori): Nom de la categoria</li>
                        <li><strong>slug</strong> (opcional): Slug per la URL</li>
                        <li><strong>descripcio</strong> (opcional): Descripció de la categoria</li>
                        <li><strong>nom_artista, municipio, any, que_hacer, concierto, notas</strong> (opcional): 1 per activar, 0 per desactivar</li>
                    </ul>
                    
                    <h4>Exemple:</h4>
                    <pre>nom,slug,descripcio,nom_artista,municipio,any,que_hacer,concierto,notas
Concerts,concerts,Concerts de música en viu,1,1,1,0,1,0
Teatre,teatre,Obres de teatre,1,1,1,1,0,1
Festes,festes,Festes populars,0,1,1,1,0,0</pre>
                </div>
                
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
                    <?php wp_nonce_field('ge_import_categories_action', 'ge_import_categories_nonce'); ?>
                    <input type="hidden" name="action" value="ge_import_categories">
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="categories_csv">Arxiu CSV de Categories</label>
                            </th>
                            <td>
                                <input type="file" name="categories_csv" id="categories_csv" accept=".csv" required>
                                <p class="description">Selecciona un arxiu CSV amb les categories a importar.</p>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" name="submit" class="button button-primary" value="Importar Categories">
                    </p>
                </form>
                
                <div class="ge-download-template">
                    <h4>Descarregar plantilla:</h4>
                    <button class="button" onclick="downloadCategoriesTemplate()">📥 Descarregar Plantilla CSV</button>
                </div>
            </div>
            
            <!-- Importar Esdeveniments -->
            <div class="ge-admin-section">
                <h2>📅 Importar Esdeveniments</h2>
                <p>Importa esdeveniments massivament des d'un arxiu CSV.</p>
                
                <div class="ge-import-format">
                    <h3>Format del CSV:</h3>
                    <p>L'arxiu CSV ha de tenir les següents columnes (amb capçalera):</p>
                    <div class="ge-csv-example">
                        <code>Nom,Artista/DJ,Lloc Esdeveniment,Població,Provincia,Categoria,Data Inici,Data Fi,Codi Setmanal,Proveïdor link,Link,Informació Adicional,Prioridad</code>
                    </div>
                    <ul>
                        <li><strong>Nom</strong> (obligatori): Nom de l'esdeveniment</li>
                        <li><strong>Artista/DJ</strong> (obligatori): Nom de l'artista o DJ</li>
                        <li><strong>Lloc Esdeveniment</strong> (obligatori): Lloc on es celebra</li>
                        <li><strong>Població</strong> (obligatori): Població de l'esdeveniment</li>
                        <li><strong>Provincia</strong> (obligatori): Província</li>
                        <li><strong>Categoria</strong> (obligatori): Nom de la categoria</li>
                        <li><strong>Data Inici</strong> (obligatori): Format: DD/MM/YYYY HH:MM</li>
                        <li><strong>Data Fi</strong> (obligatori): Format: DD/MM/YYYY HH:MM</li>
                        <li><strong>Codi Setmanal</strong> (obligatori): Codi de la setmana (ex: 20971)</li>
                        <li><strong>Proveïdor link</strong> (opcional): Nom del proveïdor</li>
                        <li><strong>Link</strong> (opcional): Enllaç web</li>
                        <li><strong>Informació Adicional</strong> (opcional): Informació extra</li>
                        <li><strong>Prioridad</strong> (opcional): 9 per patrocinat, buit per normal</li>
                    </ul>
                    
                    <h4>Exemple:</h4>
                    <pre>Nom,Artista/DJ,Lloc Esdeveniment,Població,Provincia,Categoria,Data Inici,Data Fi,Codi Setmanal,Proveïdor link,Link,Informació Adicional,Prioridad
Festa Major,DJ Example,Plaça Major,Barcelona,Barcelona,Concert,25/10/2025 20:00,25/10/2025 23:00,20971,Proveïdor 1,https://example.com,,9</pre>
                </div>
                
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
                    <?php wp_nonce_field('ge_import_events_action', 'ge_import_events_nonce'); ?>
                    <input type="hidden" name="action" value="ge_import_events">
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="events_csv">Arxiu CSV d'Esdeveniments</label>
                            </th>
                            <td>
                                <input type="file" name="events_csv" id="events_csv" accept=".csv" required>
                                <p class="description">Selecciona un arxiu CSV amb els esdeveniments a importar.</p>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" name="submit" class="button button-primary" value="Importar Esdeveniments">
                    </p>
                </form>
                
                <div class="ge-info-box">
                    <h4>⚠️ Notes importants:</h4>
                    <ul>
                        <li>Els esdeveniments s'importaran com a <strong>Publicats</strong></li>
                        <li>Si la categoria no existeix, es crearà automàticament</li>
                        <li>Si el proveïdor no existeix, el camp quedarà buit</li>
                        <li>Els esdeveniments amb el mateix nom i data NO es duplicaran</li>
                        <li>Les dates han d'estar en format DD/MM/YYYY HH:MM</li>
                    </ul>
                </div>
            </div>
            
            <!-- Instruccions -->
            <div class="ge-admin-section">
                <h2>ℹ️ Instruccions</h2>
                <ol>
                    <li>Descarrega la plantilla CSV corresponent</li>
                    <li>Omple l'arxiu amb les teves dades</li>
                    <li>Assegura't que l'arxiu està codificat en UTF-8</li>
                    <li>Puja l'arxiu utilitzant el formulari de dalt</li>
                    <li>Revisa els resultats de la importació</li>
                </ol>
                
                <h3>Notes importants:</h3>
                <ul>
                    <li>Els camps amb comes han d'estar entre cometes dobles</li>
                    <li>Si un proveïdor amb el mateix nom ja existeix, no es duplicarà</li>
                    <li>Per associar un usuari, l'email ha de correspondre a un usuari existent de WordPress</li>
                    <li>Les categories es crearan només si no existeixen</li>
                </ul>
            </div>
        </div>
        
        <style>
            .ge-admin-section {
                background: #fff;
                padding: 20px;
                margin-bottom: 20px;
                border: 1px solid #ccd0d4;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
            }
            
            .ge-admin-section h2 {
                margin-top: 0;
                border-bottom: 2px solid #0073aa;
                padding-bottom: 10px;
                color: #0073aa;
            }
            
            .ge-import-format {
                background: #f9f9f9;
                padding: 15px;
                border-left: 4px solid #0073aa;
                margin: 15px 0;
            }
            
            .ge-csv-example {
                background: #2d2d2d;
                color: #f8f8f2;
                padding: 10px;
                border-radius: 4px;
                margin: 10px 0;
                font-family: monospace;
            }
            
            .ge-csv-example code {
                color: #f8f8f2;
            }
            
            .ge-import-format pre {
                background: #2d2d2d;
                color: #f8f8f2;
                padding: 15px;
                border-radius: 4px;
                overflow-x: auto;
                font-family: monospace;
            }
            
            .ge-download-template {
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #ddd;
            }
            
            .ge-info-box {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                padding: 15px;
                margin-top: 15px;
            }
            
            .ge-info-box h4 {
                margin-top: 0;
                color: #856404;
            }
            
            .ge-info-box ul {
                margin: 10px 0;
                padding-left: 20px;
            }
        </style>
        
        <script>
        function downloadProveidorsTemplate() {
            var csv = 'nom,link_wordpress,email_usuari,actiu\\n';
            csv += 'Proveïdor Exemple 1,https://example.com/proveidor1,usuari1@example.com,1\\n';
            csv += 'Proveïdor Exemple 2,https://example.com/proveidor2,usuari2@example.com,1\\n';
            csv += 'Proveïdor Exemple 3,,,0\\n';
            
            downloadCSV(csv, 'plantilla_proveidors.csv');
        }
        
        function downloadCategoriesTemplate() {
            var csv = 'nom,slug,descripcio,nom_artista,municipio,any,que_hacer,concierto,notas\n';
            csv += 'Concerts,concerts,Concerts de música en viu,1,1,1,0,1,0\n';
            csv += 'Teatre,teatre,Obres de teatre,1,1,1,1,0,1\n';
            csv += 'Festes,festes,Festes populars,0,1,1,1,0,0\n';
            
            downloadCSV(csv, 'plantilla_categories.csv');
        }
        
        function downloadCSV(csv, filename) {
            var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            if (link.download !== undefined) {
                var url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', filename);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
        </script>
        <?php
    }
    
    public function handle_proveidors_import() {
        if (!current_user_can('manage_options')) {
            wp_die('No tens permisos per fer aquesta acció.');
        }
        
        check_admin_referer('ge_import_proveidors_action', 'ge_import_proveidors_nonce');
        
        if (!isset($_FILES['proveidors_csv']) || $_FILES['proveidors_csv']['error'] !== UPLOAD_ERR_OK) {
            wp_redirect(admin_url('admin.php?page=ge-import&import_error=1&message=' . urlencode('Error al pujar l\'arxiu')));
            exit;
        }
        
        $file = $_FILES['proveidors_csv']['tmp_name'];
        $imported = 0;
        
        if (($handle = fopen($file, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            
            // Mapear columnas
            $column_map = array();
            foreach ($header as $index => $column_name) {
                $clean_name = strtolower(trim(str_replace(array('\xEF\xBB\xBF', '\ufeff'), '', $column_name)));
                $column_map[$clean_name] = $index;
            }
            
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (empty($data[0])) continue; // Skip empty rows
                
                // Soporte para ambos formatos: nuevo (con link) y antiguo (sin link)
                $nom = isset($column_map['name']) ? sanitize_text_field($data[$column_map['name']]) : sanitize_text_field($data[0]);
                if (empty($nom)) continue;
                
                $link_wordpress = '';
                $email_usuari = '';
                $actiu = '1';
                
                // Formato nuevo con link
                if (isset($column_map['link wordpress'])) {
                    $link_wordpress = isset($data[$column_map['link wordpress']]) ? esc_url_raw($data[$column_map['link wordpress']]) : '';
                    $email_usuari = isset($column_map['email_usuari']) && isset($data[$column_map['email_usuari']]) ? sanitize_email($data[$column_map['email_usuari']]) : '';
                    $actiu = isset($column_map['actiu']) && isset($data[$column_map['actiu']]) && $data[$column_map['actiu']] != 'Si' ? '0' : '1';
                }
                // Formato antiguo sin link
                else {
                    $email_usuari = isset($data[1]) ? sanitize_email($data[1]) : '';
                    $actiu = isset($data[2]) && $data[2] == '0' ? '0' : '1';
                }
                
                // Check if proveidor already exists
                $existing = get_page_by_title($nom, OBJECT, 'proveidor');
                if ($existing) continue;
                
                // Create proveidor
                $post_id = wp_insert_post(array(
                    'post_title' => $nom,
                    'post_type' => 'proveidor',
                    'post_status' => 'publish'
                ));
                
                if (!is_wp_error($post_id)) {
                    update_post_meta($post_id, '_ge_proveidor_actiu', $actiu);
                    
                    // Guardar link de WordPress
                    if (!empty($link_wordpress)) {
                        update_post_meta($post_id, '_ge_proveidor_link_wordpress', $link_wordpress);
                    }
                    
                    // Associate with WordPress user if email provided
                    if (!empty($email_usuari)) {
                        $user = get_user_by('email', $email_usuari);
                        if ($user) {
                            update_post_meta($post_id, '_ge_proveidor_wp_user_id', $user->ID);
                        }
                    }
                    
                    $imported++;
                }
            }
            
            fclose($handle);
        }
        
        wp_redirect(admin_url('admin.php?page=ge-import&import_success=1&type=proveidors&count=' . $imported));
        exit;
    }
    
    public function handle_categories_import() {
        if (!current_user_can('manage_options')) {
            wp_die('No tens permisos per fer aquesta acció.');
        }
        
        check_admin_referer('ge_import_categories_action', 'ge_import_categories_nonce');
        
        if (!isset($_FILES['categories_csv']) || $_FILES['categories_csv']['error'] !== UPLOAD_ERR_OK) {
            wp_redirect(admin_url('admin.php?page=ge-import&import_error=1&message=' . urlencode('Error al pujar l\'arxiu')));
            exit;
        }
        
        $file = $_FILES['categories_csv']['tmp_name'];
        $imported = 0;
        
        if (($handle = fopen($file, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (empty($data[0])) continue; // Skip empty rows
                
                $nom = sanitize_text_field($data[0]);
                $slug = isset($data[1]) && !empty($data[1]) ? sanitize_title($data[1]) : sanitize_title($nom);
                $descripcio = isset($data[2]) ? sanitize_text_field($data[2]) : '';
                
                // Check if category exists
                $existing = term_exists($nom, 'categoria_esdeveniment');
                if ($existing) continue;
                
                // Create category
                $term = wp_insert_term($nom, 'categoria_esdeveniment', array(
                    'slug' => $slug,
                    'description' => $descripcio
                ));
                
                if (!is_wp_error($term)) {
                    $term_id = $term['term_id'];
                    
                    // Set meta fields for text generation
                    $meta_fields = array(
                        'nom_artista' => isset($data[3]) && $data[3] == '1' ? '1' : '0',
                        'municipio' => isset($data[4]) && $data[4] == '1' ? '1' : '0',
                        'any' => isset($data[5]) && $data[5] == '1' ? '1' : '0',
                        'que_hacer' => isset($data[6]) && $data[6] == '1' ? '1' : '0',
                        'concierto' => isset($data[7]) && $data[7] == '1' ? '1' : '0',
                        'notas' => isset($data[8]) && $data[8] == '1' ? '1' : '0'
                    );
                    
                    foreach ($meta_fields as $key => $value) {
                        update_term_meta($term_id, $key, $value);
                    }
                    
                    $imported++;
                }
            }
            
            fclose($handle);
        }
        
        wp_redirect(admin_url('admin.php?page=ge-import&import_success=1&type=categories&count=' . $imported));
        exit;
    }
    
    public function handle_events_import() {
        if (!current_user_can('manage_options')) {
            wp_die('No tens permisos per fer aquesta acció.');
        }
        
        check_admin_referer('ge_import_events_action', 'ge_import_events_nonce');
        
        if (!isset($_FILES['events_csv']) || $_FILES['events_csv']['error'] !== UPLOAD_ERR_OK) {
            wp_redirect(admin_url('admin.php?page=ge-import&import_error=1&message=' . urlencode('Error al pujar l\'arxiu')));
            exit;
        }
        
        $file = $_FILES['events_csv']['tmp_name'];
        $imported = 0;
        $errors = array();
        
        if (($handle = fopen($file, 'r')) !== false) {
            // Leer la cabecera
            $header = fgetcsv($handle, 10000, ',');
            
            // Mapear las columnas (case insensitive)
            $column_map = array();
            foreach ($header as $index => $column_name) {
                $clean_name = strtolower(trim($column_name));
                $column_map[$clean_name] = $index;
            }
            
            while (($data = fgetcsv($handle, 10000, ',')) !== false) {
                if (empty($data[0]) || $data[0] == '﻿') continue; // Skip empty rows or BOM
                
                // Extraer datos basados en el mapeo de columnas
                $nom = isset($column_map['nom']) ? sanitize_text_field($data[$column_map['nom']]) : '';
                if (empty($nom)) continue;
                
                $artista = isset($column_map['artista/dj']) ? sanitize_text_field($data[$column_map['artista/dj']]) : '';
                $lloc = isset($column_map['lloc esdeveniment']) ? sanitize_text_field($data[$column_map['lloc esdeveniment']]) : '';
                $poblacio = isset($column_map['població']) ? sanitize_text_field($data[$column_map['població']]) : '';
                $provincia = isset($column_map['provincia']) ? sanitize_text_field($data[$column_map['provincia']]) : '';
                $categoria_nom = isset($column_map['categoria']) ? sanitize_text_field($data[$column_map['categoria']]) : '';
                $data_inici_str = isset($column_map['data inici']) ? trim($data[$column_map['data inici']]) : '';
                $data_fi_str = isset($column_map['data fi']) ? trim($data[$column_map['data fi']]) : '';
                $codi_setmanal = isset($column_map['codi setmanal']) ? sanitize_text_field($data[$column_map['codi setmanal']]) : '';
                $proveidor_nom = isset($column_map['proveïdor link']) ? sanitize_text_field($data[$column_map['proveïdor link']]) : '';
                $link = isset($column_map['link']) ? esc_url_raw($data[$column_map['link']]) : '';
                $info_adicional = isset($column_map['informació adicional']) ? sanitize_textarea_field($data[$column_map['informació adicional']]) : '';
                $prioridad = isset($column_map['prioridad']) ? trim($data[$column_map['prioridad']]) : '';
                
                // Convertir fecha formato DD/MM/YYYY HH:MM a formato MySQL
                $data_inici = $this->parse_date($data_inici_str);
                $hora_inici = $this->parse_time($data_inici_str);
                $data_final = $this->parse_date($data_fi_str);
                $hora_final = $this->parse_time($data_fi_str);
                
                if (!$data_inici || !$hora_inici || !$data_final || !$hora_final) {
                    $errors[] = "Fecha inválida para: $nom";
                    continue;
                }
                
                // Comprobar si ya existe
                $existing = get_posts(array(
                    'post_type' => 'esdeveniment',
                    'title' => $nom,
                    'posts_per_page' => 1,
                    'post_status' => 'any',
                    'meta_query' => array(
                        array(
                            'key' => '_ge_data_inici',
                            'value' => $data_inici,
                            'compare' => '='
                        )
                    )
                ));
                
                if (!empty($existing)) continue; // Skip duplicates
                
                // Crear evento
                $post_id = wp_insert_post(array(
                    'post_title' => $nom,
                    'post_type' => 'esdeveniment',
                    'post_status' => 'publish'
                ));
                
                if (is_wp_error($post_id)) {
                    $errors[] = "Error al crear: $nom";
                    continue;
                }
                
                // Guardar meta datos
                update_post_meta($post_id, '_ge_artista', $artista);
                update_post_meta($post_id, '_ge_nom_espectacle', $nom);
                update_post_meta($post_id, '_ge_lloc_esdeveniment', $lloc);
                update_post_meta($post_id, '_ge_poblacio', $poblacio);
                update_post_meta($post_id, '_ge_provincia', $provincia);
                update_post_meta($post_id, '_ge_data_inici', $data_inici);
                update_post_meta($post_id, '_ge_hora_inici', $hora_inici);
                update_post_meta($post_id, '_ge_data_final', $data_final);
                update_post_meta($post_id, '_ge_hora_final', $hora_final);
                update_post_meta($post_id, '_ge_codi_setmanal', $codi_setmanal);
                update_post_meta($post_id, '_ge_enllac_web', $link);
                update_post_meta($post_id, '_ge_info_adicional', $info_adicional);
                
                // Marcar como patrocinado si prioridad es 9
                $patrocinat = ($prioridad == '9') ? '1' : '0';
                update_post_meta($post_id, '_ge_patrocinat', $patrocinat);
                
                // Asignar categoría (crear si no existe)
                if (!empty($categoria_nom)) {
                    $term = term_exists($categoria_nom, 'categoria_esdeveniment');
                    if (!$term) {
                        $term = wp_insert_term($categoria_nom, 'categoria_esdeveniment');
                    }
                    if (!is_wp_error($term)) {
                        wp_set_object_terms($post_id, intval($term['term_id']), 'categoria_esdeveniment');
                    }
                }
                
                // Buscar y asignar proveïdor
                if (!empty($proveidor_nom)) {
                    $proveidor = get_page_by_title($proveidor_nom, OBJECT, 'proveidor');
                    if ($proveidor) {
                        update_post_meta($post_id, '_ge_proveidor_id', $proveidor->ID);
                    }
                }
                
                $imported++;
            }
            
            fclose($handle);
        }
        
        $redirect_url = admin_url('admin.php?page=ge-import&import_success=1&type=esdeveniments&count=' . $imported);
        if (!empty($errors)) {
            $redirect_url .= '&errors=' . count($errors);
        }
        
        wp_redirect($redirect_url);
        exit;
    }
    
    private function parse_date($date_string) {
        // Format: DD/MM/YYYY HH:MM
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})/', $date_string, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];
            return "$year-$month-$day";
        }
        return false;
    }
    
    private function parse_time($datetime_string) {
        // Format: DD/MM/YYYY HH:MM
        if (preg_match('/(\d{1,2}):(\d{2})/', $datetime_string, $matches)) {
            $hour = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $minute = $matches[2];
            return "$hour:$minute";
        }
        return false;
    }
}

new GE_Admin_Import();
