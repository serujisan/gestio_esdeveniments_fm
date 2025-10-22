<?php
/**
 * Plugin Name: Gestió d'Espectacles
 * Plugin URI: https://github.com/serujisan/gestio_esdeveniments_fm
 * Description: Plugin per gestionar esdeveniments i espectacles amb formulari públic, gestió de proveidors i categories
 * Version: 1.0.0
 * Author: Seruji
 * Text Domain: gestio-espectacles
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('GE_VERSION', '1.0.0');
define('GE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GE_PLUGIN_URL', plugin_dir_url(__FILE__));

class GestioEspectacles {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }
    
    private function includes() {
        require_once GE_PLUGIN_DIR . 'includes/class-post-types.php';
        require_once GE_PLUGIN_DIR . 'includes/class-taxonomies.php';
        require_once GE_PLUGIN_DIR . 'includes/class-meta-boxes.php';
        require_once GE_PLUGIN_DIR . 'includes/class-frontend-form.php';
        require_once GE_PLUGIN_DIR . 'includes/class-event-display.php';
        require_once GE_PLUGIN_DIR . 'admin/class-admin-proveidors.php';
        require_once GE_PLUGIN_DIR . 'admin/class-admin-categories.php';
        require_once GE_PLUGIN_DIR . 'admin/class-admin-shortcodes.php';
        require_once GE_PLUGIN_DIR . 'admin/class-admin-import.php';
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'load_textdomain'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('gestio-espectacles', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function enqueue_scripts() {
        wp_enqueue_style('ge-styles', GE_PLUGIN_URL . 'assets/css/styles.css', array(), GE_VERSION);
        wp_enqueue_script('ge-scripts', GE_PLUGIN_URL . 'assets/js/scripts.js', array('jquery'), GE_VERSION, true);
        
        wp_localize_script('ge-scripts', 'geAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ge_nonce')
        ));
    }
    
    public function enqueue_admin_scripts($hook) {
        wp_enqueue_style('ge-admin-styles', GE_PLUGIN_URL . 'assets/css/admin-styles.css', array(), GE_VERSION);
        wp_enqueue_script('ge-admin-scripts', GE_PLUGIN_URL . 'assets/js/admin-scripts.js', array('jquery'), GE_VERSION, true);
    }
    
    public function activate() {
        // Registrar post types i taxonomies
        GE_Post_Types::register_post_types();
        GE_Taxonomies::register_taxonomies();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Crear pàgina per mostrar esdeveniments si no existeix
        $page_check = get_page_by_path('esdeveniments');
        if (!$page_check) {
            $page_data = array(
                'post_title' => 'Esdeveniments',
                'post_content' => '[ge_esdeveniments_list]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'esdeveniments'
            );
            wp_insert_post($page_data);
        }
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
}

// Inicialitzar el plugin
function gestio_espectacles() {
    return GestioEspectacles::get_instance();
}

gestio_espectacles();
