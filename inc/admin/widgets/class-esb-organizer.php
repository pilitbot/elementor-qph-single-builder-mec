<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Organizer extends ESB_Base {
    public function get_name() { return 'qph-esb-organizer'; }
    public function get_title() { return __('MEC Event Organizer', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-person'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $terms = get_the_terms($event_id, 'mec_organizer');
        if (is_array($terms) && !empty($terms)) {
            $names = wp_list_pluck($terms, 'name');
            echo '<div class="qph-esb-organizer">' . esc_html(implode(', ', $names)) . '</div>';
            return;
        }

        $meta_organizer = get_post_meta($event_id, 'mec_organizer_name', true);
        if ($meta_organizer) {
            echo '<div class="qph-esb-organizer">' . esc_html($meta_organizer) . '</div>';
            return;
        }

        $this->render_empty(__('No hay organizador definido.', 'elementor-qph-single-builder-mec'));
    }
}
