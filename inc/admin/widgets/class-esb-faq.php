<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Faq extends ESB_Base {
    public function get_name() { return 'qph-esb-faq'; }
    public function get_title() { return __('MEC FAQ', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-help-o'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $faq = get_post_meta($event_id, 'mec_faq', true);

        if (empty($faq)) {
            $output = $this->render_via_shortcode('mec-faq', array('event-id' => $event_id));
            if (!empty($output)) {
                echo '<div class="qph-esb-faq">' . $output . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                return;
            }

            $this->render_empty(__('No hay FAQ para este evento.', 'elementor-qph-single-builder-mec'));
            return;
        }

        echo '<div class="qph-esb-faq">' . wp_kses_post(wpautop($faq)) . '</div>';
    }
}
