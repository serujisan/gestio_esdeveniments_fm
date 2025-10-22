<?php

if (!defined('ABSPATH')) {
    exit;
}

class GE_Post_Types {
    
    public function __construct() {
        add_action('init', array($this, 'register_post_types'));
    }
    
    public static function register_post_types() {
        // Custom Post Type: Esdeveniments
        $labels = array(
            'name' => 'Esdeveniments',
            'singular_name' => 'Esdeveniment',
            'menu_name' => 'Esdeveniments',
            'add_new' => 'Afegir Nou',
            'add_new_item' => 'Afegir Nou Esdeveniment',
            'edit_item' => 'Editar Esdeveniment',
            'new_item' => 'Nou Esdeveniment',
            'view_item' => 'Veure Esdeveniment',
            'search_items' => 'Cercar Esdeveniments',
            'not_found' => 'No s\'han trobat esdeveniments',
            'not_found_in_trash' => 'No s\'han trobat esdeveniments a la paperera'
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'esdeveniment'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-calendar-alt',
            'supports' => array('title', 'editor', 'thumbnail'),
            'show_in_rest' => true
        );
        
        register_post_type('esdeveniment', $args);
        
        // Custom Post Type: Proveidors
        $labels_proveidors = array(
            'name' => 'Proveïdors',
            'singular_name' => 'Proveïdor',
            'menu_name' => 'Proveïdors',
            'add_new' => 'Afegir Nou',
            'add_new_item' => 'Afegir Nou Proveïdor',
            'edit_item' => 'Editar Proveïdor',
            'new_item' => 'Nou Proveïdor',
            'view_item' => 'Veure Proveïdor',
            'search_items' => 'Cercar Proveïdors',
            'not_found' => 'No s\'han trobat proveïdors',
            'not_found_in_trash' => 'No s\'han trobat proveïdors a la paperera'
        );
        
        $args_proveidors = array(
            'labels' => $labels_proveidors,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_position' => 6,
            'menu_icon' => 'dashicons-groups',
            'supports' => array('title'),
            'show_in_rest' => false
        );
        
        register_post_type('proveidor', $args_proveidors);
    }
}

new GE_Post_Types();
