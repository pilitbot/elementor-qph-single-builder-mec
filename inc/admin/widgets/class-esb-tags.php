<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Tags extends ESB_Base {
    public function get_name() { return 'qph-esb-tags'; }
    public function get_title() { return __('MEC Event Tags', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-tags'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $tags = get_the_terms($event_id, 'post_tag');
        if (!is_array($tags) || empty($tags)) {
            $tags = get_the_terms($event_id, 'mec_tag');
        }

        if (!is_array($tags) || empty($tags)) {
            $this->render_empty(__('No hay etiquetas.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $items = array();
        foreach ($tags as $tag) {
            $items[] = '<span class="qph-esb-tag">' . esc_html($tag->name) . '</span>';
        }

        echo '<div class="qph-esb-tags">' . implode(' ', $items) . '</div>';
    }
}
