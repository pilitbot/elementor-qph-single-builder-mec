<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Cancellation extends ESB_Base {
    public function get_name() { return 'qph-esb-cancellation'; }
    public function get_title() { return __('MEC Cancellation', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-ban'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $is_canceled = get_post_meta($event_id, 'mec_cancellation', true);
        $reason = get_post_meta($event_id, 'mec_cancellation_reason', true);

        if (empty($is_canceled) && empty($reason)) {
            return;
        }

        echo '<div class="qph-esb-cancellation">';
        echo '<strong>' . esc_html__('Evento cancelado', 'elementor-qph-single-builder-mec') . '</strong>';
        if (!empty($reason)) {
            echo '<div class="qph-esb-cancellation__reason">' . esc_html($reason) . '</div>';
        }
        echo '</div>';
    }
}
