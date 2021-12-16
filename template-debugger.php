<?php
/*
Plugin Name: Template Debugger
Description: Query all page templates and see if they are being used or not
Author: Greggory Hogan 
Version: 1.0.0
Author URI: https://mynameisgregg.com
*/

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

class TemplateDebugger {
   
	/**
	 * Setup the shortcode's properties
	 *
	 * @param array  $atts
	 * @param string $content
	 */
	public function __construct() {
        $this->init_shortcodes();
	}

    /**
     * Add actions for plugin
     * 
     * @return void
     */
    private function init_shortcodes() {
        add_shortcode('template_debugger',array($this,'get_template_data'));
    }

     /**
     * Pull template data for output
     */
    public function get_template_data() {
        $template_data = '';
        $templates = wp_get_theme()->get_page_templates();
        foreach ( $templates as $file => $name ) {
            $q = new WP_Query( array(
                'post_type' => 'page',
                'posts_per_page' => -1,
                'meta_query' => array( array(
                    'key' => '_wp_page_template',
                    'value' => $file
                ) )
            ) );
            $page_count = sizeof( $q->posts );
            if ( $page_count > 0 ) {
                $template_data .= '<p style="margin-top: 15px;"><strong>' . $file . ': <em>' . sizeof( $q->posts ) . '</em> pages are using this template:</strong><br>';        
                foreach ( $q->posts as $p ) {
                    $template_data .= '<a href="' . get_permalink( $p, false ) . '" target="_blank" style="display: block; margin-left: 15px;">' . $p->post_title . '</a>';
                }
                $template_data .= "</p>";
            } else {
                //$template_data .= $file.'<br>';
                $template_data .= '<p style="color:red">' . $file . ': <strong>0</strong> pages are using this template, you should be able to safely delete it from your theme.</p>';
            }
            /*foreach ( $q->posts as $p ) {
                $report[$file][$p->ID] = $p->post_title;
            }*/
        }   
        // Reset our postdata:
        wp_reset_postdata();
        return $template_data;
    }
}
new TemplateDebugger();