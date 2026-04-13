<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Custom_Data extends ESB_Base {
    public function get_name() { return 'qph-esb-custom-data'; }
    public function get_title() { return __('MEC Custom Data', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-code'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function register_controls() {
        $this->start_controls_section('content', array('label' => __('Custom Data', 'elementor-qph-single-builder-mec')));
        $this->add_control('meta_key', array(
            'label' => __('Meta Key', 'elementor-qph-single-builder-mec'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'mec_custom_data',
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
        $meta_key = !empty($settings['meta_key']) ? sanitize_key($settings['meta_key']) : 'mec_custom_data';
        $value = get_post_meta($event_id, $meta_key, true);

        if (empty($value)) {
            $this->render_empty(__('No hay datos personalizados.', 'elementor-qph-single-builder-mec'));
            return;
        }

        if (is_array($value)) {
            $value = wp_json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        echo '<div class="qph-esb-custom-data">' . esc_html((string) $value) . '</div>';
    }
}
