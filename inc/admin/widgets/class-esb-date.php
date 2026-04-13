<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Date extends ESB_Base {
    public function get_name() { return 'qph-esb-date'; }
    public function get_title() { return __('MEC Event Date', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-calendar'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function register_controls() {
        $this->start_controls_section('content', array('label' => __('Contenido', 'elementor-qph-single-builder-mec')));
        $this->add_control('format', array(
            'label' => __('Formato', 'elementor-qph-single-builder-mec'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'F j, Y g:i a',
        ));
        $this->end_controls_section();
    }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $settings = $this->get_settings_for_display();
        $format = !empty($settings['format']) ? $settings['format'] : 'F j, Y g:i a';

        $timestamp = strtotime(get_post_field('post_date', $event_id));
        if (!$timestamp) {
            $this->render_empty(__('No hay fecha disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        echo '<div class="qph-esb-date">' . esc_html(wp_date($format, $timestamp)) . '</div>';
    }
}
