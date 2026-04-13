<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Countdown extends ESB_Base {
    public function get_name() { return 'qph-esb-countdown'; }
    public function get_title() { return __('MEC Countdown', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-countdown'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $timestamp = strtotime(get_post_field('post_date', $event_id));
        if (!$timestamp) {
            $this->render_empty(__('No hay fecha para countdown.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $target = gmdate('c', $timestamp);

        echo '<div class="qph-esb-countdown" data-target="' . esc_attr($target) . '">';
        echo esc_html__('Cuenta regresiva activa (conecta tu JS de frontend).', 'elementor-qph-single-builder-mec');
        echo '</div>';
    }
}
