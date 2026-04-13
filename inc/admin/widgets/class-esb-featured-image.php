<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Featured_Image extends ESB_Base {
    public function get_name() { return 'qph-esb-featured-image'; }
    public function get_title() { return __('MEC Featured Image', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-image'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        if (!has_post_thumbnail($event_id)) {
            $this->render_empty(__('El evento no tiene imagen destacada.', 'elementor-qph-single-builder-mec'));
            return;
        }

        echo '<div class="qph-esb-featured-image">';
        echo get_the_post_thumbnail($event_id, 'full', array('class' => 'qph-esb-featured-image__img')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>';
    }
}
