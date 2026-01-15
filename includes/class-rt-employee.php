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
    /**
     * Add Meta Boxes for Designation and Email
     */
    public function add_meta_boxes() {
        add_meta_box(
            'rt_employee_details',      // ID
            'Employee Details',         // Title
            array( $this, 'render_meta_box' ), // Callback
            'rt_employee',              // Screen (CPT Name)
            'normal',                   // Context
            'high'                      // Priority
        );
    }

    /**
     * Render the HTML for Meta Boxes
     * @param object $post The post object
     */
    public function render_meta_box( $post ) {
        // SECURITY: Create a Nonce field
        wp_nonce_field( 'rt_save_employee_data', 'rt_employee_nonce' );

        // Get existing values
        $designation = get_post_meta( $post->ID, '_rt_designation', true );
        $email = get_post_meta( $post->ID, '_rt_email', true );

        // HTML Output
        ?>
        <p>
            <label for="rt_designation">Designation:</label><br>
            <input type="text" id="rt_designation" name="rt_designation" value="<?php echo esc_attr( $designation ); ?>" class="widefat">
        </p>
        <p>
            <label for="rt_email">Email:</label><br>
            <input type="email" id="rt_email" name="rt_email" value="<?php echo esc_attr( $email ); ?>" class="widefat">
        </p>
        <?php
    }
    
}

// Initialize the class
new Rt_Employee_Directory();