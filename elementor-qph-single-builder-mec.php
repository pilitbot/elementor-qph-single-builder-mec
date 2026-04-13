<?php
/**
 * Plugin Name: Elementor QPH Single Builder for MEC
 * Description: Widgets de Elementor para construir el single de eventos de Modern Events Calendar.
 * Version: 1.0.1
 * Author: QPH
 * Text Domain: elementor-qph-single-builder-mec
 */

if (!defined('ABSPATH')) exit;

define('QPH_ESB_VERSION', '1.0.1');
define('QPH_ESB_DIR', plugin_dir_path(__FILE__));
define('QPH_ESB_URL', plugin_dir_url(__FILE__));

final class QPH_ESB_Plugin {

    public function __construct() {
        add_action('plugins_loaded', array($this, 'bootstrap'));
        add_action('init', array($this, 'load_textdomain'));
    }

    public function load_textdomain() {
        load_plugin_textdomain('elementor-qph-single-builder-mec', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function bootstrap() {
        if (!$this->has_elementor()) {
            add_action('admin_notices', array($this, 'notice_missing_elementor'));
            return;
        }

        if (!$this->has_mec()) {
            add_action('admin_notices', array($this, 'notice_missing_mec'));
            return;
        }

        add_action('wp_enqueue_scripts', array($this, 'register_assets'));

        require_once QPH_ESB_DIR . 'inc/admin/class-elementor-esb.php';
        \QPH_MEC_Single_Builder\Admin\Elementor_ESB::instance();
    }

    public function register_assets() {
        wp_register_script(
            'qph-esb-countdown',
            QPH_ESB_URL . 'assets/js/esb-countdown.js',
            array(),
            QPH_ESB_VERSION,
            true
        );
    }

    private function has_elementor() {
        return did_action('elementor/loaded') || class_exists('Elementor\\Plugin');
    }

    private function has_mec() {
        return class_exists('MEC') || defined('MEC_VERSION') || defined('MEC_ABSPATH');
    }

    public function notice_missing_elementor() {
        if (!current_user_can('activate_plugins')) return;

        echo '<div class="notice notice-error"><p>' .
            esc_html__('Elementor QPH Single Builder for MEC requiere Elementor activo.', 'elementor-qph-single-builder-mec') .
            '</p></div>';
    }

    public function notice_missing_mec() {
        if (!current_user_can('activate_plugins')) return;

        echo '<div class="notice notice-error"><p>' .
            esc_html__('Elementor QPH Single Builder for MEC requiere Modern Events Calendar (MEC) activo.', 'elementor-qph-single-builder-mec') .
            '</p></div>';
    }
}

new QPH_ESB_Plugin();
