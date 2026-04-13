<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Hourly_Schedule extends ESB_Base {
    public function get_name() { return 'qph-esb-hourly-schedule'; }
    public function get_title() { return __('MEC Hourly Schedule', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-time-line'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $schedule = get_post_meta($event_id, 'mec_hourly_schedules', true);
        if (empty($schedule) || !is_array($schedule)) {
            $this->render_empty(__('No hay programación horaria.', 'elementor-qph-single-builder-mec'));
            return;
        }

        echo '<ul class="qph-esb-hourly-schedule">';
        foreach ($schedule as $row) {
            $time = isset($row['time']) ? $row['time'] : '';
            $title = isset($row['title']) ? $row['title'] : '';
            echo '<li><span class="time">' . esc_html($time) . '</span> <span class="title">' . esc_html($title) . '</span></li>';
        }
        echo '</ul>';
    }
}
