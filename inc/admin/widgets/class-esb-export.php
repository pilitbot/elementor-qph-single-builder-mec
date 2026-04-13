<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Export extends ESB_Base {
    public function get_name() { return 'qph-esb-export'; }
    public function get_title() { return __('MEC Export', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-download-button'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $ical_url = add_query_arg(array('ical' => 1, 'event' => $event_id), home_url('/'));
        $gcal_url = sprintf(
            'https://calendar.google.com/calendar/render?action=TEMPLATE&text=%s&details=%s',
            rawurlencode(get_the_title($event_id)),
            rawurlencode(get_permalink($event_id))
        );

        echo '<div class="qph-esb-export">';
        echo '<a class="qph-esb-export__btn qph-esb-export__btn--ical" href="' . esc_url($ical_url) . '">' . esc_html__('Exportar iCal', 'elementor-qph-single-builder-mec') . '</a> ';
        echo '<a class="qph-esb-export__btn qph-esb-export__btn--gcal" href="' . esc_url($gcal_url) . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Agregar a Google Calendar', 'elementor-qph-single-builder-mec') . '</a>';
        echo '</div>';
    }
}
