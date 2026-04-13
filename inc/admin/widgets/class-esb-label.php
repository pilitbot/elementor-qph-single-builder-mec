<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Label extends ESB_Base {
    public function get_name() { return 'qph-esb-label'; }
    public function get_title() { return __('MEC Label', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-post-list'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $terms = get_the_terms($event_id, 'mec_label');
        if (is_array($terms) && !empty($terms)) {
            $items = array();
            foreach ($terms as $term) {
                $items[] = '<span class="qph-esb-label__item">' . esc_html($term->name) . '</span>';
            }
            echo '<div class="qph-esb-label">' . implode(' ', $items) . '</div>';
            return;
        }

        $meta_label = get_post_meta($event_id, 'mec_label', true);
        if (!empty($meta_label)) {
            echo '<div class="qph-esb-label"><span class="qph-esb-label__item">' . esc_html($meta_label) . '</span></div>';
            return;
        }

        $this->render_empty(__('No hay labels asignados.', 'elementor-qph-single-builder-mec'));
    }
}
