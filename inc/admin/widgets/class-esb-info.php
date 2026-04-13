<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_More_Info extends ESB_Base {
    public function get_name() { return 'qph-esb-more-info'; }
    public function get_title() { return __('MEC More Info', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-info-circle'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $more_info = get_post_meta($event_id, 'mec_more_info', true);
        if (empty($more_info)) {
            $more_info = get_post_meta($event_id, 'mec_additional_info', true);
        }

        if (empty($more_info)) {
            $this->render_empty(__('No hay información adicional.', 'elementor-qph-single-builder-mec'));
            return;
        }

        echo '<div class="qph-esb-more-info">' . wp_kses_post(wpautop($more_info)) . '</div>';
    }
}
