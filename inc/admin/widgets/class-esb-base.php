<?php
namespace QPH_MEC_Single_Builder\Widgets;

if (!defined('ABSPATH')) exit;

class ESB_Title extends \Elementor\Widget_Base {

    public function get_name() {
        return 'qph-esb-title';
    }

    public function get_title() {
        return __('MEC Event Title', 'elementor-qph-single-builder-mec');
    }

    public function get_icon() {
        return 'eicon-heading';
    }

    public function get_categories() {
        return array('qph_mec_single_builder');
    }

    public function get_keywords() {
        return array('mec', 'event', 'title', 'qph');
    }

    protected function register_controls() {
        $this->start_controls_section('section_content', array(
            'label' => __('Contenido', 'elementor-qph-single-builder-mec'),
        ));

        $this->add_control('html_tag', array(
            'label' => __('HTML Tag', 'elementor-qph-single-builder-mec'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'h2',
            'options' => array(
                'h1' => 'H1',
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
                'h6' => 'H6',
                'div' => 'div',
                'span' => 'span',
                'p' => 'p',
            ),
        ));

        $this->end_controls_section();

        $this->start_controls_section('section_style', array(
            'label' => __('Estilo', 'elementor-qph-single-builder-mec'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ));

        $this->add_control('text_color', array(
            'label' => __('Color', 'elementor-qph-single-builder-mec'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .qph-mec-event-title' => 'color: {{VALUE}};',
            ),
        ));

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .qph-mec-event-title',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $tag = !empty($settings['html_tag']) ? $settings['html_tag'] : 'h2';

        $event_title = get_the_title();

        if (empty($event_title) && function_exists('MEC')) {
            $event_title = get_the_title(get_the_ID());
        }

        if (empty($event_title)) {
            $event_title = __('(Sin título de evento)', 'elementor-qph-single-builder-mec');
        }

        printf(
            '<%1$s class="qph-mec-event-title">%2$s</%1$s>',
            esc_attr($tag),
            esc_html($event_title)
        );
    }
}
