<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Event_Gallery extends ESB_Base {
    public function get_name() { return 'qph-esb-event-gallery'; }
    public function get_title() { return __('MEC Event Gallery', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-gallery-grid'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $gallery = get_post_meta($event_id, 'mec_event_gallery', true);
        if (empty($gallery) || !is_array($gallery)) {
            $this->render_empty(__('No hay galería para este evento.', 'elementor-qph-single-builder-mec'));
            return;
        }

        echo '<div class="qph-esb-event-gallery">';
        foreach ($gallery as $image_id) {
            echo '<div class="qph-esb-event-gallery__item">' . wp_get_attachment_image((int) $image_id, 'medium') . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        echo '</div>';
    }
}
