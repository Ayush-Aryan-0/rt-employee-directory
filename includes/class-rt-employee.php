<?php

class Rt_Employee_Directory {

    public function __construct() {
        // Hook into WordPress init action
        add_action( 'init', array( $this, 'register_employee_cpt' ) );
        // New hook for Meta Boxes
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
    }

    /**
     * Register Custom Post Type 'Employee'
     */
    public function register_employee_cpt() {
        $labels = array(
            'name'               => 'Employees',
            'singular_name'      => 'Employee',
            'menu_name'          => 'rt Employees',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Employee',
            'edit_item'          => 'Edit Employee',
            'all_items'          => 'All Employees',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => false,
            'menu_icon'          => 'dashicons-businessperson',
            'supports'           => array( 'title', 'thumbnail' ), // Title = Name, Thumbnail = Photo
            'show_in_rest'       => true, // Modern WP standard
        );

        register_post_type( 'rt_employee', $args );
    }
    
}

// Initialize the class
new Rt_Employee_Directory();