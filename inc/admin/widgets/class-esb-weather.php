<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Virtual_Event extends ESB_Base {
    public function get_name() { return 'qph-esb-virtual-event'; }
    public function get_title() { return __('MEC Virtual Event', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-video-camera'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $virtual_url = get_post_meta($event_id, 'mec_virtual_url', true);
        if (empty($virtual_url)) {
            $virtual_url = get_post_meta($event_id, 'mec_zoom_join_url', true);
        }

        if (empty($virtual_url)) {
            $this->render_empty(__('Este evento no está marcado como virtual.', 'elementor-qph-single-builder-mec'));
            return;
        }

        echo '<a class="qph-esb-virtual-event" href="' . esc_url($virtual_url) . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Entrar al evento virtual', 'elementor-qph-single-builder-mec') . '</a>';
    }
}
