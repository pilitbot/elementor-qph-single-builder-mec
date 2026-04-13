<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Breadcrumbs extends ESB_Base {
    public function get_name() { return 'qph-esb-breadcrumbs'; }
    public function get_title() { return __('MEC Breadcrumbs', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-editor-list-ul'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        $event_title = $event_id ? get_the_title($event_id) : '';

        echo '<nav class="qph-esb-breadcrumbs" aria-label="Breadcrumb">';
        echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Inicio', 'elementor-qph-single-builder-mec') . '</a>';
        echo ' <span class="sep">/</span> ';
        echo '<a href="' . esc_url(home_url('/events/')) . '">' . esc_html__('Eventos', 'elementor-qph-single-builder-mec') . '</a>';

        if (!empty($event_title)) {
            echo ' <span class="sep">/</span> <span class="current">' . esc_html($event_title) . '</span>';
        }

        echo '</nav>';
    }
}
