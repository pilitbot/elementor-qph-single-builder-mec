<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Category extends ESB_Base {
    public function get_name() { return 'qph-esb-category'; }
    public function get_title() { return __('MEC Category', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-folder'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $terms = get_the_terms($event_id, 'mec_category');
        if (!is_array($terms) || empty($terms)) {
            $terms = get_the_terms($event_id, 'category');
        }

        if (!is_array($terms) || empty($terms)) {
            $this->render_empty(__('No hay categorías asignadas.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $names = wp_list_pluck($terms, 'name');
        echo '<div class="qph-esb-category">' . esc_html(implode(', ', $names)) . '</div>';
    }
}
