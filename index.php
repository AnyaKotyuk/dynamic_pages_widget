<?php

if (!defined('WIDGET_PATH')) define('WIDGET_PATH', get_stylesheet_directory_uri().'/widgets/dynamic_pages/');

include_once 'functions.php';
include_once 'widget.php';


add_action( 'admin_enqueue_scripts', 'dynamic_pages_widget_enqueue_script' );
add_action( 'widgets_init', 'dynamic_pages_load_widget' );


// Pages searc
add_action('wp_ajax_get_pages', 'ajax_get_pages');
add_action('wp_ajax_nopriv_get_pages', 'ajax_get_pages');