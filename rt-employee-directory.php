<?php
/**
 * Plugin Name: rtCamp Employee Directory
 * Description: A secure, OOP-based plugin to manage and display employees. Built for the rtCamp application process.
 * Version: 1.0.0
 * Author: Ayush Aryan
 * Text Domain: rt-employee
 */

// Security: Block direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define plugin path for future includes
define( 'RT_EMP_PATH', plugin_dir_path( __FILE__ ) );

// We will include our main class
// require_once RT_EMP_PATH . 'includes/class-rt-employee-loader.php';