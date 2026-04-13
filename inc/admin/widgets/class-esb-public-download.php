<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Public_Download extends ESB_Base {
    public function get_name() { return 'qph-esb-public-download'; }
    public function get_title() { return __('MEC Public Download', 'elementor-qph-single-builder-mec'); }
    public function get_icon() { return 'eicon-download-button'; }
    public function get_categories() { return array('qph_mec_single_builder'); }

    protected function render() {
        $event_id = $this->get_event_id();
        if (!$event_id) {
            $this->render_empty(__('Evento no disponible.', 'elementor-qph-single-builder-mec'));
            return;
        }

        $file_url = get_post_meta($event_id, 'mec_public_download', true);
        if (empty($file_url)) {
            $file_url = get_post_meta($event_id, 'mec_download_file', true);
        }

        if (empty($file_url)) {
            $this->render_empty(__('No hay archivo público para descargar.', 'elementor-qph-single-builder-mec'));
            return;
        }

        echo '<a class="qph-esb-public-download" href="' . esc_url($file_url) . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Descargar archivo', 'elementor-qph-single-builder-mec') . '</a>';
    }
}
