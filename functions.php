<?php
/**
 * Define function for Dynamic pages widget
 *
 *
 */

/**
 * Enqueue script for admin
 *
 * @param $hook
 */
function dynamic_pages_widget_enqueue_script() {
    wp_enqueue_style('admin-dynamic-pages',	WIDGET_PATH.'assets/style.css');
    wp_enqueue_script('admin-dynamic-pages',	WIDGET_PATH.'assets/admin.js', array('jquery'));
}

/**
 * Register widget
 */
function dynamic_pages_load_widget() {
    register_widget( 'dynamic_pages_widget' );
}


/**
 * Get pages id and title by search query
 */
function ajax_get_pages()
{
    $s = wp_unslash($_GET['term']);

    $comma = _x(',', 'tag delimiter');
    if (',' !== $comma) {
        $s = str_replace($comma, ',', $s);
    }

    if (false !== strpos($s, ',')) {
        $s = explode(',', $s);
        $s = $s[count($s) - 1];
    }
    $s = trim($s);

    $page = array();

    $pages = new WP_Query(array('post_type' => 'page', 's' => $s));
    while ($pages->have_posts()) {
        $pages->the_post();
        $p['value'] = get_the_ID();
        $p['label'] = get_the_title();
        $page[] = $p;
    }

    echo json_encode($page);
    exit;
}