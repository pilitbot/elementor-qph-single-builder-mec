<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Nxt_Prv extends ESB_Base {
    public function get_name() { return 'qph-esb-nxt-prv'; }
    public function get_title() { return __('MEC Next/Previous (Legacy)', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-post-navigation'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $prev = get_previous_post();
        $next = get_next_post();

        if (!$prev && !$next) {
            $this->render_empty(__('No hay navegación disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        echo '<nav class="qph-esb-next-prev">';
        if ($prev) {
            echo '<a class="qph-esb-next-prev__prev" href="' . esc_url(get_permalink($prev->ID)) . '">← ' . esc_html(get_the_title($prev->ID)) . '</a>';
        }
        if ($next) {
            echo '<a class="qph-esb-next-prev__next" href="' . esc_url(get_permalink($next->ID)) . '">' . esc_html(get_the_title($next->ID)) . ' →</a>';
        }
        echo '</nav>';
    }
}
