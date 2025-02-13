<?php
/**
 * Plugin Name: Read More Ajax
 * Plugin URI: https://github.com/stefangilgen/read-more
 * Description: Lädt "Weiterlesen" Inhalte per AJAX nach
 * Version: 1.0.0
 * Author: Stefan Gilgen
 * Author URI: https://blitzdonner.ch
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
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
        add_action('wp_ajax_load_full_post', [$this, 'loadFullPost']);
        add_action('wp_ajax_nopriv_load_full_post', [$this, 'loadFullPost']);
        add_filter('the_content_more_link', [$this, 'modifyReadMoreLink']);
        add_filter('excerpt_more', [$this, 'modifyExcerptMore']);
    }
    
    public function enqueueScripts() {
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
    
    public function loadFullPost() {
        check_ajax_referer('read_more_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        if (!$post_id) {
            wp_send_json_error('Invalid post ID');
        }
        
        $post = get_post($post_id);
        if (!$post) {
            wp_send_json_error('Post not found');
        }
        
        // Hole den vollständigen Inhalt
        $content = apply_filters('the_content', $post->post_content);
        
        // Wenn es einen "Weiterlesen"-Trenner gibt, hole nur den Teil danach
        if (strpos($post->post_content, '<!--more-->') !== false) {
            $parts = explode('<!--more-->', $content);
            $content = isset($parts[1]) ? trim($parts[1]) : $content;
        }
        
        wp_send_json_success([
            'content' => $content,
            'replace_all' => strpos($post->post_content, '<!--more-->') === false
        ]);
    }
    
    public function modifyReadMoreLink($link) {
        global $post;
        return str_replace('<a', '<a data-post-id="' . $post->ID . '"', $link);
    }
    
    public function modifyExcerptMore($more) {
        global $post;
        return ' ... <a class="more-link" data-post-id="' . $post->ID . '" href="' . get_permalink($post->ID) . '">Weiterlesen</a>';
    }
}

// Plugin initialisieren
add_action('plugins_loaded', function() {
    ReadMoreAjax::getInstance();
}); 