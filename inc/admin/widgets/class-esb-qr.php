<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_QR extends ESB_Base {
    public function get_name() { return 'qph-esb-qr'; }
    public function get_title() { return __('MEC QR', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-barcode'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function register_controls() {
        $this->start_controls_section('content', array('label' => __('QR', 'elementor-qph-single-builder-mec')));
        $this->add_control('size', array(
            'label' => __('Tamaño (px)', 'elementor-qph-single-builder-mec'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 180,
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
        $size = !empty($settings['size']) ? max(80, (int) $settings['size']) : 180;

        $event_url = get_permalink($event_id);
        $qr_url = add_query_arg(
            array(
                'size' => $size . 'x' . $size,
                'data' => rawurlencode($event_url),
            ),
            'https://api.qrserver.com/v1/create-qr-code/'
        );

        echo '<div class="qph-esb-qr">';
        echo '<img src="' . esc_url($qr_url) . '" alt="' . esc_attr__('QR del evento', 'elementor-qph-single-builder-mec') . '" width="' . esc_attr($size) . '" height="' . esc_attr($size) . '" />';
        echo '</div>';
    }
}
