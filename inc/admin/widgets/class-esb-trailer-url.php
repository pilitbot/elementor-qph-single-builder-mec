<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Trailer_URL extends ESB_Base {
    public function get_name() { return 'qph-esb-trailer-url'; }
    public function get_title() { return __('MEC Trailer URL', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-play'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $trailer_url = get_post_meta($event_id, 'mec_trailer_url', true);
        if (empty($trailer_url)) {
            $trailer_url = get_post_meta($event_id, 'mec_video_url', true);
        }

        if (empty($trailer_url)) {
            $this->render_empty(__('No hay trailer configurado.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $embed = wp_oembed_get($trailer_url);

        echo '<div class="qph-esb-trailer-url">';
        if (!empty($embed)) {
            echo $embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        } else {
            echo '<a href="' . esc_url($trailer_url) . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Ver trailer', 'elementor-qph-single-builder-mec') . '</a>';
        }
        echo '</div>';
    }
}
