<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Location extends ESB_Base {
    public function get_name() { return 'qph-esb-location'; }
    public function get_title() { return __('MEC Event Location', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-google-maps'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $terms = get_the_terms($event_id, 'mec_location');
        if (is_array($terms) && !empty($terms)) {
            $names = wp_list_pluck($terms, 'name');
            echo '<div class="qph-esb-location">' . esc_html(implode(', ', $names)) . '</div>';
            return;
        }

        $meta_location = get_post_meta($event_id, 'mec_location_name', true);
        if ($meta_location) {
            echo '<div class="qph-esb-location">' . esc_html($meta_location) . '</div>';
            return;
        }

        $this->render_empty(__('No hay ubicación definida.', 'elementor-qph-single-builder-mec'));
    }
}
