<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Zoom_Event extends ESB_Base {
    public function get_name() { return 'qph-esb-zoom-event'; }
    public function get_title() { return __('MEC Zoom Event', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-video-camera'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $join_url = get_post_meta($event_id, 'mec_zoom_join_url', true);
        $meeting_id = get_post_meta($event_id, 'mec_zoom_meeting_id', true);

        if (empty($join_url)) {
            $this->render_empty(__('Este evento no tiene Zoom configurado.', 'elementor-qph-single-builder-mec'));
            return;
        }

        echo '<div class="qph-esb-zoom-event">';
        echo '<a class="qph-esb-zoom-event__btn" href="' . esc_url($join_url) . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Unirse por Zoom', 'elementor-qph-single-builder-mec') . '</a>';
        if (!empty($meeting_id)) {
            echo '<div class="qph-esb-zoom-event__id">' . esc_html__('ID de reunión:', 'elementor-qph-single-builder-mec') . ' ' . esc_html($meeting_id) . '</div>';
        }
        echo '</div>';
    }
}
