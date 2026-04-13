<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Booking extends ESB_Base {
    public function get_name() { return 'qph-esb-booking'; }
    public function get_title() { return __('MEC Booking', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-cart'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $output = $this->render_via_shortcode('MEC_booking_form', array('id' => $event_id));

        if (empty($output)) {
            $output = $this->render_via_shortcode('mec-booking', array('event-id' => $event_id));
        }

        if (empty($output)) {
            $this->render_empty(__('Booking no disponible para este evento.', 'elementor-qph-single-builder-mec'));
            return;
        }

        echo '<div class="qph-esb-booking">' . $output . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
