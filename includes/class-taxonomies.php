<?php

if (!defined('ABSPATH')) {
    exit;
}

class GE_Taxonomies {
    
    public function __construct() {
        add_action('init', array($this, 'register_taxonomies'));
    }
    
    public static function register_taxonomies() {
        // Taxonomia: Categories d'Esdeveniments
        $labels = array(
            'name' => 'Categories',
            'singular_name' => 'Categoria',
            'menu_name' => 'Categories',
            'all_items' => 'Totes les Categories',
            'edit_item' => 'Editar Categoria',
            'view_item' => 'Veure Categoria',
            'update_item' => 'Actualitzar Categoria',
            'add_new_item' => 'Afegir Nova Categoria',
            'new_item_name' => 'Nom de la Nova Categoria',
            'search_items' => 'Cercar Categories',
            'popular_items' => 'Categories Populars',
            'not_found' => 'No s\'han trobat categories'
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_admin_column' => true,
            'hierarchical' => true,
            'rewrite' => array('slug' => 'categoria-esdeveniment'),
            'show_in_rest' => true
        );
        
        register_taxonomy('categoria_esdeveniment', array('esdeveniment'), $args);
    }
}

new GE_Taxonomies();
