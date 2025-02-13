<?php
/**
 * Plugin Name: Read More Ajax
 * Description: Lädt "Weiterlesen" Inhalte per AJAX nach
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: read-more-ajax
 */

if (!defined('ABSPATH')) exit;

class ReadMoreAjax {
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('wp_ajax_load_more_content', [$this, 'loadMoreContent']);
        add_action('wp_ajax_nopriv_load_more_content', [$this, 'loadMoreContent']);
        
        // Füge Klasse zum more-link hinzu
        add_filter('the_content_more_link', [$this, 'addMoreLinkClass']);
    }
    
    public function enqueueScripts() {
        // CSS einbinden
        wp_enqueue_style(
            'read-more-ajax',
            plugins_url('css/read-more.css', __FILE__),
            [],
            '1.0.0'
        );

        // JavaScript einbinden
        wp_enqueue_script(
            'read-more-ajax',
            plugins_url('js/read-more.js', __FILE__),
            ['jquery'],
            '1.0.0',
            true
        );
        
        wp_localize_script('read-more-ajax', 'readMoreAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('read_more_nonce')
        ]);
    }
    
    public function loadMoreContent() {
        check_ajax_referer('read_more_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        if (!$post_id) {
            wp_send_json_error('Invalid post ID');
        }
        
        $post = get_post($post_id);
        if (!$post) {
            wp_send_json_error('Post not found');
        }
        
        $content = apply_filters('the_content', $post->post_content);
        $more_content = $this->getContentAfterMore($content);
        
        wp_send_json_success([
            'content' => $more_content
        ]);
    }
    
    private function getContentAfterMore($content) {
        $parts = explode('<!--more-->', $content);
        return isset($parts[1]) ? trim($parts[1]) : '';
    }

    public function addMoreLinkClass($link) {
        global $post;
        // Füge data-post-id Attribut hinzu
        $link = str_replace('<a', '<a data-post-id="' . $post->ID . '"', $link);
        return str_replace('more-link', 'more-link read-more-ajax', $link);
    }
}

// Plugin initialisieren
add_action('plugins_loaded', function() {
    ReadMoreAjax::getInstance();
}); 