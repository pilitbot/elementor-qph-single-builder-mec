<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Local_Time extends ESB_Base {
    public function get_name() { return 'qph-esb-local-time'; }
    public function get_title() { return __('MEC Local Time', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-clock'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $timezone = get_post_meta($event_id, 'mec_timezone', true);
        $start_raw = get_post_meta($event_id, 'mec_start_date', true);
        $timestamp = strtotime($start_raw ? $start_raw : get_post_field('post_date', $event_id));

        if (!$timestamp) {
            $this->render_empty(__('No hay fecha para hora local.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $tz = !empty($timezone) ? $timezone : wp_timezone_string();
        $dt = new \DateTime('@' . $timestamp);
        $dt->setTimezone(new \DateTimeZone($tz));

        echo '<div class="qph-esb-local-time">' . esc_html($dt->format('Y-m-d H:i')) . ' (' . esc_html($tz) . ')</div>';
    }
}
