<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Cost extends ESB_Base {
    public function get_name() { return 'qph-esb-cost'; }
    public function get_title() { return __('MEC Event Cost', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-price-list'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $cost = get_post_meta($event_id, 'mec_cost', true);
        if ($cost === '' || $cost === null) {
            $cost = get_post_meta($event_id, 'mec_price', true);
        }

        if ($cost === '' || $cost === null) {
            $this->render_empty(__('Evento gratuito o sin costo definido.', 'elementor-qph-single-builder-mec'));
            return;
        }

        echo '<div class="qph-esb-cost">' . esc_html($cost) . '</div>';
    }
}
