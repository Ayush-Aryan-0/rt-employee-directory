<?php

class Rt_Employee_Directory {

    public function __construct() {
        // Hook into WordPress init action
        add_action( 'init', array( $this, 'register_employee_cpt' ) );
        // New hook for Meta Boxes
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        // New hook to save data
        add_action( 'save_post', array( $this, 'save_employee_data' ) );
        add_shortcode( 'rt_employee_list', array( $this, 'render_frontend_shortcode' ) );
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
    /**
     * Save Meta Box Data securely
     */
    public function save_employee_data( $post_id ) {
        // 1. Check if nonce is set
        if ( ! isset( $_POST['rt_employee_nonce'] ) ) {
            return;
        }

        // 2. Verify Nonce (Security Check against CSRF)
        if ( ! wp_verify_nonce( $_POST['rt_employee_nonce'], 'rt_save_employee_data' ) ) {
            return;
        }

        // 3. Check for Autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // 4. Check User Permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // 5. Sanitize and Save 'Designation'
        if ( isset( $_POST['rt_designation'] ) ) {
            $designation = sanitize_text_field( $_POST['rt_designation'] );
            update_post_meta( $post_id, '_rt_designation', $designation );
        }

        // 6. Sanitize and Save 'Email'
        if ( isset( $_POST['rt_email'] ) ) {
            $email = sanitize_email( $_POST['rt_email'] );
            update_post_meta( $post_id, '_rt_email', $email );
        }
    }
    /**
     * Shortcode to display employees: [rt_employee_list]
     */
    public function render_frontend_shortcode() {
        $args = array(
            'post_type'      => 'rt_employee',
            'posts_per_page' => 10,
            'post_status'    => 'publish',
        );

        $query = new WP_Query( $args );
        $output = '<div class="rt-employee-grid">';

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                
                // Retrieve Metadata
                $designation = get_post_meta( get_the_ID(), '_rt_designation', true );
                $email = get_post_meta( get_the_ID(), '_rt_email', true );
                $photo = get_the_post_thumbnail_url( get_the_ID(), 'medium' );

                // Output HTML (Escaped for Security)
                $output .= '<div class="rt-employee-card" style="border:1px solid #ddd; padding:10px; margin:10px;">';
                if ( $photo ) {
                    $output .= '<img src="' . esc_url( $photo ) . '" style="max-width:100px; border-radius:50%;">';
                }
                $output .= '<h3>' . esc_html( get_the_title() ) . '</h3>';
                $output .= '<p><strong>' . esc_html( $designation ) . '</strong></p>';
                $output .= '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
                $output .= '</div>';
            }
            wp_reset_postdata();
        } else {
            $output .= '<p>No employees found.</p>';
        }

        $output .= '</div>';
        return $output;
    }
    
}

// Initialize the class
new Rt_Employee_Directory();