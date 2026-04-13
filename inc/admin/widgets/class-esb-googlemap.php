<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_GoogleMap extends ESB_Base {
    public function get_name() { return 'qph-esb-googlemap'; }
    public function get_title() { return __('MEC Google Map', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-google-maps'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function register_controls() {
        $this->start_controls_section('content', array('label' => __('Mapa', 'elementor-qph-single-builder-mec')));
        $this->add_control('height', array(
            'label' => __('Alto (px)', 'elementor-qph-single-builder-mec'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 320,
        ));
        $this->end_controls_section();
    }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $lat = get_post_meta($event_id, 'mec_latitude', true);
        $lng = get_post_meta($event_id, 'mec_longitude', true);

        if (!$lat || !$lng) {
            $this->render_empty(__('No hay coordenadas para el mapa.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $settings = $this->get_settings_for_display();
        $height = !empty($settings['height']) ? (int) $settings['height'] : 320;

        $map_url = sprintf('https://www.google.com/maps?q=%s,%s&z=15&output=embed', rawurlencode($lat), rawurlencode($lng));

        echo '<div class="qph-esb-googlemap" style="height:' . esc_attr($height) . 'px;">';
        echo '<iframe src="' . esc_url($map_url) . '" width="100%" height="100%" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
        echo '</div>';
    }
}
