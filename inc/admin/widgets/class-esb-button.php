<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Register_Button extends ESB_Base {
    public function get_name() { return 'qph-esb-register-button'; }
    public function get_title() { return __('MEC Register Button', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-button'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function register_controls() {
        $this->start_controls_section('content', array('label' => __('Botón', 'elementor-qph-single-builder-mec')));
        $this->add_control('label', array(
            'label' => __('Texto', 'elementor-qph-single-builder-mec'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => __('Registrarme', 'elementor-qph-single-builder-mec'),
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
        $label = !empty($settings['label']) ? $settings['label'] : __('Registrarme', 'elementor-qph-single-builder-mec');

        $booking_url = add_query_arg(array('event' => $event_id), get_permalink($event_id));

        echo '<a class="qph-esb-register-button" href="' . esc_url($booking_url) . '">' . esc_html($label) . '</a>';
    }
}
