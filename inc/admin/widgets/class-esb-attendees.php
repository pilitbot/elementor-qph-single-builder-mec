<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Attendees extends ESB_Base {
    public function get_name() { return 'qph-esb-attendees'; }
    public function get_title() { return __('MEC Attendees', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-users'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $count = get_post_meta($event_id, 'mec_attendees_count', true);
        if ($count === '' || $count === null) {
            $count = get_post_meta($event_id, 'mec_bookings_count', true);
        }

        if ($count === '' || $count === null) {
            $this->render_empty(__('No hay asistentes registrados.', 'elementor-qph-single-builder-mec'));
            return;
        }

        echo '<div class="qph-esb-attendees">' . sprintf(esc_html__('%s asistentes', 'elementor-qph-single-builder-mec'), esc_html($count)) . '</div>';
    }
}
