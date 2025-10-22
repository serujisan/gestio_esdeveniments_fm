<?php

if (!defined('ABSPATH')) {
    exit;
}

class GE_Admin_Import {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_ge_import_proveidors', array($this, 'handle_proveidors_import'));
        add_action('admin_post_ge_import_categories', array($this, 'handle_categories_import'));
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
                        <code>nom,email_usuari,actiu</code>
                    </div>
                    <ul>
                        <li><strong>nom</strong> (obligatori): Nom del proveïdor</li>
                        <li><strong>email_usuari</strong> (opcional): Email de l'usuari WordPress a associar</li>
                        <li><strong>actiu</strong> (opcional): 1 per actiu, 0 per inactiu (per defecte: 1)</li>
                    </ul>
                    
                    <h4>Exemple:</h4>
                    <pre>nom,email_usuari,actiu
Proveïdor 1,usuari1@example.com,1
Proveïdor 2,usuari2@example.com,1
Proveïdor 3,,0</pre>
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
        </style>
        
        <script>
        function downloadProveidorsTemplate() {
            var csv = 'nom,email_usuari,actiu\n';
            csv += 'Proveïdor Exemple 1,usuari1@example.com,1\n';
            csv += 'Proveïdor Exemple 2,usuari2@example.com,1\n';
            csv += 'Proveïdor Exemple 3,,0\n';
            
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
            
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (empty($data[0])) continue; // Skip empty rows
                
                $nom = sanitize_text_field($data[0]);
                $email_usuari = isset($data[1]) ? sanitize_email($data[1]) : '';
                $actiu = isset($data[2]) && $data[2] == '1' ? '1' : '1'; // Default actiu
                
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
                        'ge_cat_nom_artista' => isset($data[3]) && $data[3] == '1' ? '1' : '0',
                        'ge_cat_municipio' => isset($data[4]) && $data[4] == '1' ? '1' : '0',
                        'ge_cat_any' => isset($data[5]) && $data[5] == '1' ? '1' : '0',
                        'ge_cat_que_hacer' => isset($data[6]) && $data[6] == '1' ? '1' : '0',
                        'ge_cat_concierto' => isset($data[7]) && $data[7] == '1' ? '1' : '0',
                        'ge_cat_notas' => isset($data[8]) && $data[8] == '1' ? '1' : '0'
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
}

new GE_Admin_Import();
