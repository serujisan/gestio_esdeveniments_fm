<?php

if (!defined('ABSPATH')) {
    exit;
}

class GE_Admin_Shortcodes {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=esdeveniment',
            'Shortcodes i Previsualització',
            'Shortcodes',
            'manage_options',
            'ge-shortcodes',
            array($this, 'render_admin_page')
        );
    }
    
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1>Shortcodes de Gestió d'Espectacles</h1>
            <p>Utilitza aquests shortcodes per mostrar el formulari i la llista d'esdeveniments a les teves pàgines.</p>
            
            <!-- Shortcode Formulari -->
            <div class="ge-admin-section">
                <h2>📝 Formulari per Enviar Esdeveniments</h2>
                <p>Aquest formulari permet als proveïdors actius enviar nous esdeveniments.</p>
                
                <div class="ge-shortcode-box">
                    <h3>Shortcode:</h3>
                    <div class="ge-shortcode-display">
                        <code>[ge_formulari_esdeveniment]</code>
                        <button class="button button-small ge-copy-shortcode" data-shortcode="[ge_formulari_esdeveniment]">
                            Copiar
                        </button>
                    </div>
                </div>
                
                <div class="ge-preview-section">
                    <h3>Previsualització:</h3>
                    <div class="ge-preview-box">
                        <?php echo do_shortcode('[ge_formulari_esdeveniment]'); ?>
                    </div>
                </div>
                
                <div class="ge-instructions">
                    <h4>Com utilitzar-lo:</h4>
                    <ol>
                        <li>Copia el shortcode de dalt</li>
                        <li>Edita o crea una pàgina on vulguis mostrar el formulari</li>
                        <li>Enganxa el shortcode al contingut de la pàgina</li>
                        <li>Publica o actualitza la pàgina</li>
                    </ol>
                    <p><strong>Nota:</strong> Només els usuaris autenticats i associats a un proveïdor actiu podran veure i utilitzar aquest formulari.</p>
                </div>
            </div>
            
            <!-- Shortcode Llista d'Esdeveniments -->
            <div class="ge-admin-section">
                <h2>📅 Llista d'Esdeveniments</h2>
                <p>Mostra tots els esdeveniments de la setmana actual amb filtres interactius.</p>
                
                <div class="ge-shortcode-box">
                    <h3>Shortcode:</h3>
                    <div class="ge-shortcode-display">
                        <code>[ge_esdeveniments_list]</code>
                        <button class="button button-small ge-copy-shortcode" data-shortcode="[ge_esdeveniments_list]">
                            Copiar
                        </button>
                    </div>
                </div>
                
                <div class="ge-preview-section">
                    <h3>Previsualització:</h3>
                    <div class="ge-preview-box">
                        <?php echo do_shortcode('[ge_esdeveniments_list]'); ?>
                    </div>
                </div>
                
                <div class="ge-instructions">
                    <h4>Com utilitzar-lo:</h4>
                    <ol>
                        <li>Copia el shortcode de dalt</li>
                        <li>Edita o crea una pàgina on vulguis mostrar els esdeveniments</li>
                        <li>Enganxa el shortcode al contingut de la pàgina</li>
                        <li>Publica o actualitza la pàgina</li>
                    </ol>
                    <p><strong>Característiques:</strong></p>
                    <ul>
                        <li>Mostra només els esdeveniments de la setmana actual</li>
                        <li>Filtres per província, població i categoria</li>
                        <li>Els esdeveniments patrocinats apareixen primers</li>
                        <li>Disseny responsive</li>
                    </ul>
                </div>
            </div>
            
            <!-- Exemples d'ús -->
            <div class="ge-admin-section">
                <h2>💡 Exemples d'Ús</h2>
                
                <h3>Pàgina d'Esdeveniments (Recomanat)</h3>
                <div class="ge-example-box">
                    <p>Crea una pàgina anomenada "Esdeveniments" i afegeix:</p>
                    <pre><code>[ge_esdeveniments_list]</code></pre>
                    <p>Aquesta serà la pàgina principal on els visitants podran veure tots els esdeveniments.</p>
                </div>
                
                <h3>Pàgina per Proveïdors</h3>
                <div class="ge-example-box">
                    <p>Crea una pàgina anomenada "Enviar Esdeveniment" i afegeix:</p>
                    <pre><code>[ge_formulari_esdeveniment]</code></pre>
                    <p>Aquesta pàgina permetrà als proveïdors enviar nous esdeveniments.</p>
                </div>
                
                <h3>Pàgina Completa</h3>
                <div class="ge-example-box">
                    <p>També pots combinar-los en una sola pàgina:</p>
                    <pre><code>&lt;h2&gt;Esdeveniments d'aquesta setmana&lt;/h2&gt;
[ge_esdeveniments_list]

&lt;hr&gt;

&lt;h2&gt;Ets proveïdor? Envia el teu esdeveniment&lt;/h2&gt;
[ge_formulari_esdeveniment]</code></pre>
                </div>
            </div>
            
            <!-- Enllaços ràpids -->
            <div class="ge-admin-section">
                <h2>🔗 Enllaços Ràpids</h2>
                <p>Gestiona els diferents aspectes del plugin:</p>
                <ul class="ge-quick-links">
                    <li>
                        <a href="<?php echo admin_url('edit.php?post_type=esdeveniment'); ?>" class="button">
                            Gestionar Esdeveniments
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo admin_url('admin.php?page=ge-proveidors'); ?>" class="button">
                            Gestionar Proveïdors
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo admin_url('edit-tags.php?taxonomy=categoria_esdeveniment&post_type=esdeveniment'); ?>" class="button">
                            Gestionar Categories
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo admin_url('post-new.php?post_type=page'); ?>" class="button button-primary">
                            Crear Nova Pàgina
                        </a>
                    </li>
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
            
            .ge-shortcode-box {
                background: #f0f0f1;
                padding: 15px;
                border-left: 4px solid #0073aa;
                margin: 15px 0;
            }
            
            .ge-shortcode-display {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-top: 10px;
            }
            
            .ge-shortcode-display code {
                background: #fff;
                padding: 10px 15px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 16px;
                flex-grow: 1;
                font-family: monospace;
            }
            
            .ge-copy-shortcode {
                white-space: nowrap;
            }
            
            .ge-preview-section {
                margin: 20px 0;
            }
            
            .ge-preview-box {
                border: 2px dashed #ccc;
                padding: 20px;
                background: #fafafa;
                border-radius: 4px;
                max-height: 600px;
                overflow-y: auto;
            }
            
            .ge-instructions {
                background: #e7f5ff;
                padding: 15px;
                border-left: 4px solid #2196F3;
                margin-top: 15px;
            }
            
            .ge-instructions h4 {
                margin-top: 0;
                color: #1976D2;
            }
            
            .ge-instructions ol, .ge-instructions ul {
                margin: 10px 0;
                padding-left: 20px;
            }
            
            .ge-instructions li {
                margin: 5px 0;
            }
            
            .ge-example-box {
                background: #f9f9f9;
                padding: 15px;
                border: 1px solid #ddd;
                border-radius: 4px;
                margin: 10px 0;
            }
            
            .ge-example-box pre {
                background: #2d2d2d;
                color: #f8f8f2;
                padding: 15px;
                border-radius: 4px;
                overflow-x: auto;
            }
            
            .ge-example-box code {
                font-family: 'Courier New', monospace;
                font-size: 14px;
            }
            
            .ge-quick-links {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                list-style: none;
                padding: 0;
            }
            
            .ge-quick-links li {
                margin: 0;
            }
            
            .ge-copied-message {
                color: #46b450;
                font-weight: bold;
                margin-left: 10px;
            }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            $('.ge-copy-shortcode').on('click', function() {
                var shortcode = $(this).data('shortcode');
                var button = $(this);
                
                // Crear element temporal per copiar
                var temp = $('<textarea>');
                $('body').append(temp);
                temp.val(shortcode).select();
                document.execCommand('copy');
                temp.remove();
                
                // Mostrar missatge de confirmació
                var originalText = button.text();
                button.text('✓ Copiat!');
                button.addClass('button-primary');
                
                setTimeout(function() {
                    button.text(originalText);
                    button.removeClass('button-primary');
                }, 2000);
            });
        });
        </script>
        <?php
    }
}

new GE_Admin_Shortcodes();
