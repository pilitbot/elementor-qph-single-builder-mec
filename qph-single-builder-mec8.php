<?php
/**
 * Plugin Name: QPH Single Builder for MEC
 * Plugin URI: https://quepasahoy.com.co
 * Description: Usa Elementor para diseñar eventos de MEC.
 * Version: 4.2.0
 * Author: QuePasaHoy
 * Author URI: https://quepasahoy.com.co
 * Text Domain: qph-single-builder
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

define('QPH_ESB_VERSION', '4.2.0');
define('QPH_ESB_PATH', plugin_dir_path(__FILE__));
define('QPH_ESB_URL', plugin_dir_url(__FILE__));

final class QPH_Single_Builder_MEC {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('plugins_loaded', array($this, 'init'), 20);
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    private function log($msg) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[QPH-ESB] ' . $msg);
        }
    }
    
    
    
    // ============================================
    // INIT
    // ============================================

    public function init() {
        $this->log('=== QPH ESB v4.2.0 Init ===');

        if (!class_exists('MEC') && !defined('MEC_VERSION')) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p><strong>QPH Single Builder:</strong> Requiere Modern Events Calendar.</p></div>';
            });
            return;
        }

        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p><strong>QPH Single Builder:</strong> Requiere Elementor.</p></div>';
            });
            return;
        }

        $this->log('Dependencias OK');
        $this->setup_hooks();
        $this->setup_elementor();
    }

    // ============================================
    // ELEMENTOR
    // ============================================

    private function setup_elementor() {
        add_action('elementor/init', array($this, 'register_elementor_cpt'));
        add_filter('template_include', array($this, 'elementor_canvas_template'), 99998);
        add_action('init', function() { add_post_type_support('mec_esb', 'elementor'); }, 999);
        add_action('elementor/preview/enqueue_styles', array($this, 'preview_enqueue'));
        add_action('elementor/editor/after_enqueue_styles', array($this, 'editor_styles'));
        add_action('elementor/elements/categories_registered', array($this, 'register_widget_category'));
        add_action('elementor/widgets/register', array($this, 'register_widgets'));
        add_action('save_post_mec_esb', array($this, 'clear_elementor_cache'));
        add_filter('mec_event_supports', array($this, 'apply_elementor_support'));
    }

    public function apply_elementor_support($supports) {
        $supports[] = 'elementor';
        return $supports;
    }

    public function preview_enqueue() {
        if (!class_exists('\Elementor\Plugin')) return;
        if (!\Elementor\Plugin::$instance->preview->is_preview_mode()) return;
        if (get_post_type(get_the_ID()) !== 'mec_esb') return;

        $mec_opts = get_option('mec_options', array());
        $api_key  = isset($mec_opts['settings']['google_maps_api_key'])
            ? trim($mec_opts['settings']['google_maps_api_key']) : '';

        if (!empty($api_key)) {
            wp_enqueue_script('googlemap',
                '//maps.googleapis.com/maps/api/js?libraries=places&key=' . $api_key);
        }

        add_filter('body_class', function($c) {
            $c[] = 'mec-single-event';
            $c[] = 'mec-wrap';
            return $c;
        });
    }

    public function editor_styles() {
        if (get_post_type(get_the_ID()) === 'mec_esb') {
            wp_enqueue_style('mec-font-icons');
        }
    }

    public function register_elementor_cpt() {
        $cpt = get_option('elementor_cpt_support', array('page', 'post'));
        if (!is_array($cpt)) $cpt = array('page', 'post');
        if (!in_array('mec_esb', $cpt)) {
            $cpt[] = 'mec_esb';
            update_option('elementor_cpt_support', $cpt);
        }
    }

    public function elementor_canvas_template($template) {
        if (!is_singular('mec_esb')) return $template;
        if (!class_exists('\Elementor\Plugin')) return $template;

        $doc = \Elementor\Plugin::$instance->documents->get(get_the_ID());
        if ($doc && $doc->is_built_with_elementor()) {
            $canvas = ELEMENTOR_PATH . 'modules/page-templates/templates/canvas.php';
            if (file_exists($canvas)) return $canvas;
        }

        $fb = QPH_ESB_PATH . 'templates/single-mec_esb.php';
        return file_exists($fb) ? $fb : $template;
    }

    public function clear_elementor_cache($pid) {
        if (class_exists('\Elementor\Plugin')) {
            \Elementor\Plugin::instance()->files_manager->clear_cache();
        }
    }

    public function register_widget_category($em) {
        $em->add_category('single_builder', array(
            'title' => 'MEC Single Builder',
            'icon'  => 'fa fa-plug',
        ));
    }

    public function register_widgets($wm) {
        // Cargar helper primero
        $helper = QPH_ESB_PATH . 'widgets/class-widget-helper.php';
        if (file_exists($helper)) {
            require_once $helper;
        }

        $widgets = array(
            'class-widget-title'          => 'QPH_ESB_Widget_Title',
            'class-widget-date'           => 'QPH_ESB_Widget_Date',
            'class-widget-time'           => 'QPH_ESB_Widget_Time',
            'class-widget-location'       => 'QPH_ESB_Widget_Location',
            'class-widget-cost'           => 'QPH_ESB_Widget_Cost',
            'class-widget-organizer'      => 'QPH_ESB_Widget_Organizer',
            'class-widget-content'        => 'QPH_ESB_Widget_Content',
            'class-widget-image'          => 'QPH_ESB_Widget_Image',
            'class-widget-category'       => 'QPH_ESB_Widget_Category',
            'class-widget-map'            => 'QPH_ESB_Widget_Map',
            'class-widget-tags'           => 'QPH_ESB_Widget_Tags',
            'class-widget-countdown'      => 'QPH_ESB_Widget_Countdown',
            'class-widget-featured-image' => 'QPH_ESB_Widget_FeaturedImage',
            'class-widget-export'         => 'QPH_ESB_Widget_Export',
            'class-widget-label'          => 'QPH_ESB_Widget_Label',
            'class-widget-breadcrumbs'    => 'QPH_ESB_Widget_Breadcrumbs',
            'class-widget-local-time'     => 'QPH_ESB_Widget_LocalTime',
            'class-widget-more-info'      => 'QPH_ESB_Widget_MoreInfo',
            'class-widget-next-previous'  => 'QPH_ESB_Widget_NextPrevious',
            'class-widget-social'         => 'QPH_ESB_Widget_Social',
            'class-widget-qr'             => 'QPH_ESB_Widget_QR',
            'class-widget-register-button'=> 'QPH_ESB_Widget_RegisterButton',
            'class-widget-hourly-schedule'=> 'QPH_ESB_Widget_HourlySchedule',
            'class-widget-attendees'      => 'QPH_ESB_Widget_Attendees',
            'class-widget-cancellation'   => 'QPH_ESB_Widget_Cancellation',
            'class-widget-custom-data'    => 'QPH_ESB_Widget_CustomData',
            'class-widget-faq'            => 'QPH_ESB_Widget_FAQ',
            'class-widget-trailer-url'    => 'QPH_ESB_Widget_TrailerUrl',
            'class-widget-event-gallery'  => 'QPH_ESB_Widget_EventGallery',
            'class-widget-googlemap'      => 'QPH_ESB_Widget_GoogleMap',
            'class-widget-booking'        => 'QPH_ESB_Widget_Booking',
            'class-widget-weather'        => 'QPH_ESB_Widget_Weather',
            'class-widget-virtual-event'  => 'QPH_ESB_Widget_VirtualEvent',
            'class-widget-zoom-event'     => 'QPH_ESB_Widget_ZoomEvent',
            'class-widget-public-download'=> 'QPH_ESB_Widget_PublicDownload',
            'class-widget-nxt-prv'        => 'QPH_ESB_Widget_NxtPrv',
            'class-widget-info'           => 'QPH_ESB_Widget_Info',
            'class-widget-base'           => 'QPH_ESB_Widget_Base',
            'class-widget-button'         => 'QPH_ESB_Widget_Button',
        );

        foreach ($widgets as $file => $class) {
            $path = QPH_ESB_PATH . 'widgets/' . $file . '.php';
            if (file_exists($path)) {
                require_once $path;
                if (class_exists($class)) {
                    $wm->register(new $class());
                }
            }
        }
    }

    public function register_elementor_post_type($pt) {
        $pt['mec_esb']    = 'QPH Event Builder';
        $pt['mec-events'] = 'Eventos MEC';
        return $pt;
    }

    // ============================================
    // HOOKS
    // ============================================

    private function setup_hooks() {
    add_action('init', array($this, 'register_post_type'), 5);
    add_action('admin_menu', array($this, 'register_menu'), 9999);

    // MEC INTEGRACIÓN
    add_action('mec_esb_content', array($this, 'render_builder_content'), 10, 1);
    add_action('mec-ajax-load-single-page-before', array($this, 'render_builder_modal'), 10, 1);
    add_filter('mec_filter_single_style', array($this, 'filter_single_style'), 1);
    
    add_filter('mec_settings', function($settings) {

    if(isset($settings['single']['single_single_style'])) {

        // Agregar opción builder
        $settings['single']['single_single_style']['options']['builder'] = 'QPH Single Builder (Elementor)';

        // Agregar tooltip
        $settings['single']['single_single_style']['tooltip'] = array(
            'title' => 'Single Event Style',
            'content' => 'Choose the single event page style.'
        );
    }

    return $settings;
    });

    // SETTINGS - Solo inyección JS
    // NO necesitamos interceptar wp_ajax_mec_save_options
    // porque MEC guarda TODO lo que venga en $_POST['mec']
    add_action('admin_footer', array($this, 'inject_settings_ui'), 99);
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

    // POPUP
    add_action('admin_enqueue_scripts', array($this, 'show_setup_popup'));
    add_action('admin_post_qph_esb_apply_style_direct', array($this, 'handle_direct_apply'));
    add_action('admin_post_qph_esb_skip_setup_direct', array($this, 'handle_direct_skip'));

    // META
    add_action('add_meta_boxes', array($this, 'add_event_metabox'));
    add_action('save_post_mec-events', array($this, 'save_event_meta'), 10, 1);

    // CATEGORÍAS
    add_action('mec_category_add_form_fields', array($this, 'category_add_fields'));
    add_action('mec_category_edit_form_fields', array($this, 'category_edit_fields'), 10, 2);
    add_action('created_mec_category', array($this, 'save_category_meta'));
    add_action('edited_mec_category', array($this, 'save_category_meta'));

    // ELEMENTOR
    add_filter('elementor/utils/get_public_post_types', array($this, 'register_elementor_post_type'));
    add_filter('post_row_actions', array($this, 'remove_view_action'), 10, 2);

    $this->log('Hooks OK');
    }

    // ============================================
    // POST TYPE
    // ============================================

    public function register_post_type() {
        register_post_type('mec_esb', array(
            'labels' => array(
                'name'          => 'QPH Event Builder',
                'singular_name' => 'Event Template',
                'add_new'       => 'Crear Nuevo',
                'add_new_item'  => 'Crear Nuevo Template',
                'edit_item'     => 'Editar Template',
                'all_items'     => 'QPH Event Builder',
                'not_found'     => 'No hay templates',
            ),
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'qph_esb'),
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'exclude_from_search' => true,
            'show_in_rest'        => true,
        ));
        add_post_type_support('mec_esb', 'elementor');
        $this->log('CPT registrado');
    }

    // ============================================
    // MENÚ
    // ============================================

    public function register_menu() {
        global $submenu;

        $parent_slug = null;
        $possible    = array('MEC-intro', 'mec-intro');

        foreach ($possible as $slug) {
            if (isset($submenu[$slug])) {
                $parent_slug = $slug;
                break;
            }
        }

        if (!$parent_slug) {
            foreach (array_keys($submenu) as $slug) {
                if (stripos($slug, 'mec') !== false || stripos($slug, 'calendar') !== false) {
                    $parent_slug = $slug;
                    break;
                }
            }
        }

        if (!$parent_slug) {
            $this->log('Menú MEC no encontrado');
            return;
        }

        $menu_slug = 'qph-event-builder';

        // Verificar si ya existe
        if (isset($submenu[$parent_slug])) {
            foreach ($submenu[$parent_slug] as $item) {
                if (isset($item[2]) && $item[2] === $menu_slug) {
                    return;
                }
            }
        }

        add_submenu_page(
            $parent_slug,
            'QPH Event Builder',
            'QPH Event Builder',
            'edit_posts',
            $menu_slug,
            array($this, 'render_admin_page')
        );

        $this->log('Menú registrado bajo: ' . $parent_slug);
    }

    public function render_admin_page() {
        $templates = $this->get_all_templates();
        $opts      = get_option('mec_options', array());
        $active_id = isset($opts['settings']['single_single_default_builder'])
            ? (int) $opts['settings']['single_single_default_builder'] : 0;
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">QPH Event Builder</h1>
            <a href="<?php echo esc_url(admin_url('post-new.php?post_type=mec_esb')); ?>"
               class="page-title-action">Añadir Nuevo</a>
            <hr class="wp-header-end">

            <?php if (empty($templates)) : ?>
                <div class="notice notice-warning">
                    <p>No hay templates.
                        <a href="<?php echo esc_url(admin_url('post-new.php?post_type=mec_esb')); ?>">
                            Crea tu primer template
                        </a>
                    </p>
                </div>
            <?php else : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($templates as $t) : ?>
                            <tr <?php echo ($t->ID === $active_id) ? 'style="background:#e8f5e9;"' : ''; ?>>
                                <td>
                                    <strong>
                                        <a href="<?php echo esc_url(get_edit_post_link($t->ID)); ?>">
                                            <?php echo esc_html($t->post_title); ?>
                                        </a>
                                    </strong>
                                    <?php if ($t->ID === $active_id) : ?>
                                        <span style="color:green;font-size:12px;"> ✅ Activo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html(ucfirst($t->post_status)); ?></td>
                                <td><?php echo esc_html(get_the_date('d/m/Y', $t->ID)); ?></td>
                                <td>
                                    <a href="<?php echo esc_url(get_edit_post_link($t->ID)); ?>"
                                       class="button button-small">Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p style="margin-top:15px;">
                    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=mec_esb')); ?>"
                       class="button button-primary">+ Nuevo Template</a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=MEC-settings')); ?>"
                       class="button" style="margin-left:10px;">⚙️ MEC Settings</a>
                </p>
            <?php endif; ?>
        </div>
        <?php
    }

    public function remove_view_action($actions, $post) {
        if ($post->post_type === 'mec_esb') unset($actions['view']);
        return $actions;
    }

    // ============================================
    // RENDERIZADO SINGLE
    // ============================================

    public function render_builder_content($event) {
        $this->log('========= RENDER BUILDER =========');
        $this->log('Event ID: ' . $event->ID);

        $style = self::get_event_template_style($event->ID);
        $this->log('Estilo: ' . $style);

        if ($style !== 'builder') {
            $this->log('No es builder, saliendo');
            return;
        }

        if (!class_exists('\Elementor\Plugin')) {
            $this->log('Elementor no disponible');
            return;
        }

        if (\Elementor\Plugin::$instance->editor->is_edit_mode()
            || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
            the_content();
            return;
        }

        global $eventt;
        $eventt = $event;

        $tid = $this->resolve_template_id($event->ID, 'single');
        $this->log('Template ID: ' . $tid);

        if (!$tid || !get_post($tid)) {
            echo '<div class="qph-esb-notice">Selecciona un template en MEC Settings.</div>';
            return;
        }

        $data = get_post_meta($tid, '_elementor_data', true);
        $this->log('Data length: ' . strlen($data));

        if (empty($data)) {
            echo '<div class="qph-esb-notice">Template vacío. <a href="'
                . esc_url(get_edit_post_link($tid)) . '">Edítalo</a></div>';
            return;
        }

        update_post_meta($tid, '_elementor_edit_mode', 'builder');

        // Cambiar post global al template
        global $post, $wp_query;
        $original_post  = $post;
        $original_query = $wp_query;

        $template_post = get_post($tid);
        $post          = $template_post;
        setup_postdata($post);

        $wp_query->post              = $template_post;
        $wp_query->posts             = array($template_post);
        $wp_query->queried_object    = $template_post;
        $wp_query->queried_object_id = $tid;

        \Elementor\Plugin::$instance->frontend->enqueue_styles();
        \Elementor\Plugin::$instance->frontend->enqueue_scripts();

        $content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($tid, true);
        $this->log('Método 1 - Content length: ' . strlen($content));

        if (empty($content)) {
            $content = \Elementor\Plugin::$instance->frontend->get_builder_content($tid, true);
            $this->log('Método 2 - Content length: ' . strlen($content));
        }

        if (empty($content)) {
            $document = \Elementor\Plugin::$instance->documents->get($tid);
            if ($document && method_exists($document, 'get_content')) {
                $content = $document->get_content();
                $this->log('Método 3 - Content length: ' . strlen($content));
            }
        }

        // Restaurar
        $post     = $original_post;
        $wp_query = $original_query;
        if ($original_post) setup_postdata($original_post);
        else wp_reset_postdata();

        if (empty($content)) {
            $this->log('ERROR: Contenido vacío');
            echo '<div class="qph-esb-notice">Error renderizando template. '
                . '<a href="' . esc_url(get_edit_post_link($tid)) . '">Verificar</a></div>';
            return;
        }

        echo '<div class="mec-wrap mec-single-builder-wrap qph-esb-wrapper">';
        echo '<div class="row mec-single-event"><div class="wn-single">';
        echo $content;
        echo '</div></div></div>';

        $this->load_template_css($tid);
        $this->log('✅ Renderizado OK');
    }

    // ============================================
    // RENDERIZADO MODAL
    // ============================================

    public function render_builder_modal($event_id) {
        if (!class_exists('\Elementor\Plugin')) return;
        if (self::get_event_template_style($event_id) !== 'builder') return;

        $tid = $this->resolve_template_id($event_id, 'modal');
        if (!$tid) return;

        global $post;
        $original = $post;

        $post = get_post($tid);
        setup_postdata($post);

        \Elementor\Plugin::$instance->frontend->enqueue_styles();

        $content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($tid, true);

        $post = $original;
        if ($original) setup_postdata($original);
        else wp_reset_postdata();

        if (empty($content)) return;

        echo '<div class="mec-wrap mec-single-builder-wrap clearfix">';
        echo '<div class="row mec-single-event"><div class="wn-single">';
        echo $content;
        echo '</div></div></div>';

        $this->load_template_css($tid);
        die();
    }


            /**
     * Guardar nuestros campos cuando MEC guarda sus settings
     */
    public function save_qph_settings() {
        // Solo procesar si es un POST de MEC Settings
        if (!isset($_POST['mec']) || !is_array($_POST['mec'])) {
            return;
        }

        // Verificar que estamos en la página correcta
        if (!isset($_GET['page']) || strpos(sanitize_text_field($_GET['page']), 'MEC') === false) {
            return;
        }

        $mec_data = $_POST['mec'];

        if (!isset($mec_data['settings'])) {
            return;
        }

        $settings = $mec_data['settings'];
        $opts     = get_option('mec_options', array());

        if (!is_array($opts)) {
            $opts = array();
        }

        if (!isset($opts['settings'])) {
            $opts['settings'] = array();
        }

        // Guardar single_single_style
        if (isset($settings['single_single_style'])) {
            $opts['settings']['single_single_style'] = sanitize_text_field($settings['single_single_style']);
            $this->log('Guardado single_single_style: ' . $settings['single_single_style']);
        }

        // Guardar single_single_default_builder
        if (isset($settings['single_single_default_builder'])) {
            $opts['settings']['single_single_default_builder'] = intval($settings['single_single_default_builder']);
            $this->log('Guardado single_single_default_builder: ' . $settings['single_single_default_builder']);
        }

        // Guardar single_modal_default_builder
        if (isset($settings['single_modal_default_builder'])) {
            $opts['settings']['single_modal_default_builder'] = intval($settings['single_modal_default_builder']);
            $this->log('Guardado single_modal_default_builder: ' . $settings['single_modal_default_builder']);
        }

        // Guardar custom_event_for_set_settings
        if (isset($settings['custom_event_for_set_settings'])) {
            $opts['settings']['custom_event_for_set_settings'] = intval($settings['custom_event_for_set_settings']);
            $this->log('Guardado custom_event_for_set_settings: ' . $settings['custom_event_for_set_settings']);
        }

        update_option('mec_options', $opts);
        $this->log('Settings QPH guardados correctamente');
    }
    
        /**
     * Guardar nuestros campos cuando MEC guarda via AJAX
     */
    public function save_qph_settings_ajax() {
        if (!isset($_POST['mec']) || !is_array($_POST['mec'])) {
            return;
        }

        $mec_data = $_POST['mec'];

        if (!isset($mec_data['settings'])) {
            return;
        }

        $settings = $mec_data['settings'];
        $opts     = get_option('mec_options', array());

        if (!is_array($opts)) {
            $opts = array();
        }

        if (!isset($opts['settings'])) {
            $opts['settings'] = array();
        }

        $changed = false;

        if (isset($settings['single_single_style'])) {
            $opts['settings']['single_single_style'] = sanitize_text_field($settings['single_single_style']);
            $changed = true;
        }

        if (isset($settings['single_single_default_builder'])) {
            $opts['settings']['single_single_default_builder'] = intval($settings['single_single_default_builder']);
            $changed = true;
        }

        if (isset($settings['single_modal_default_builder'])) {
            $opts['settings']['single_modal_default_builder'] = intval($settings['single_modal_default_builder']);
            $changed = true;
        }

        if (isset($settings['custom_event_for_set_settings'])) {
            $opts['settings']['custom_event_for_set_settings'] = intval($settings['custom_event_for_set_settings']);
            $changed = true;
        }

        if ($changed) {
            update_option('mec_options', $opts);
            $this->log('Settings QPH guardados via AJAX');
        }
    }
    
    // ============================================
    // HELPERS
    // ============================================

    public static function get_event_template_style($event_id) {
        $opts = get_option('mec_options', array());
        $per  = isset($opts['settings']['style_per_event']) ? $opts['settings']['style_per_event'] : '';

        if ($per) {
            $s = get_post_meta($event_id, 'mec_style_per_event', true);
            if (!empty($s) && $s !== 'global') return $s;
        }

        return isset($opts['settings']['single_single_style'])
            ? $opts['settings']['single_single_style']
            : 'default';
    }

    private function resolve_template_id($event_id, $type = 'single') {
        $opts = get_option('mec_options', array());
        $s    = isset($opts['settings']) ? $opts['settings'] : array();

        $mk = ($type === 'modal') ? 'single_modal_design_page' : 'single_design_page';
        $dk = ($type === 'modal') ? 'single_modal_default_builder' : 'single_single_default_builder';

        $id = (int) get_post_meta($event_id, $mk, true);
        if ($id > 0 && get_post($id)) return $id;

        $cats = wp_get_post_terms($event_id, 'mec_category', array('fields' => 'ids'));
        if (!is_wp_error($cats)) {
            foreach ($cats as $c) {
                $ct = (int) get_term_meta($c, $mk, true);
                if ($ct > 0 && get_post($ct)) return $ct;
            }
        }

        $g = isset($s[$dk]) ? (int) $s[$dk] : 0;
        if ($g > 0 && get_post($g)) return $g;

        if ($type === 'modal') {
            $si = isset($s['single_single_default_builder']) ? (int) $s['single_single_default_builder'] : 0;
            if ($si > 0 && get_post($si)) return $si;
        }

        $any = get_posts(array(
            'post_type'      => 'mec_esb',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
        ));
        return !empty($any) ? (int) $any[0] : 0;
    }

    private function load_template_css($tid) {
        if (class_exists('\Elementor\Core\Files\CSS\Post')) {
            $css = new \Elementor\Core\Files\CSS\Post($tid);
            $css->enqueue();
        }
        echo '<style>
            .mec-wrap .elementor-text-editor p{margin:inherit;color:inherit;font-size:inherit;line-height:inherit}
            .mec-container{width:auto!important}
            .qph-esb-wrapper .elementor-section-wrap{width:100%}
            .qph-esb-notice{padding:15px;background:#fff3cd;border-left:4px solid #ffc107;margin:15px 0}
            .qph-esb-notice a{color:#0073aa;font-weight:bold}
        </style>';
    }

    private function get_all_templates() {
        return get_posts(array(
            'post_type'      => 'mec_esb',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ));
    }

    public function filter_single_style($style) {
        if (is_singular('mec-events')) return self::get_event_template_style(get_the_ID());
        return $style;
    }

    // ============================================
    // ADMIN ASSETS
    // ============================================

    public function enqueue_admin_assets($hook) {
        global $post_type;

        $is_mec = $post_type === 'mec_esb'
               || strpos($hook, 'mec') !== false
               || (isset($_GET['page']) && (
                   strpos(sanitize_text_field($_GET['page']), 'MEC') !== false
                   || $_GET['page'] === 'qph-event-builder'
               ));

        if (!$is_mec) return;

        $css = QPH_ESB_PATH . 'assets/css/admin.css';
        if (file_exists($css)) {
            wp_enqueue_style('qph-esb-admin', QPH_ESB_URL . 'assets/css/admin.css', array(), QPH_ESB_VERSION);
        }

        $js = QPH_ESB_PATH . 'assets/js/admin.js';
        if (file_exists($js)) {
            wp_enqueue_script('qph-esb-admin', QPH_ESB_URL . 'assets/js/admin.js', array('jquery'), QPH_ESB_VERSION, true);
        }
    }

    // ============================================
    // SETTINGS UI
    // ============================================

    public function inject_settings_ui() {
    $screen = get_current_screen();
    if (!$screen) return;

    $is_mec = strpos($screen->id, 'mec') !== false
           || strpos($screen->id, 'MEC') !== false
           || (isset($_GET['page']) && strpos(
               sanitize_text_field($_GET['page']), 'MEC') !== false);

    if (!$is_mec) return;

    $opts     = get_option('mec_options', array());
    $settings = isset($opts['settings']) ? $opts['settings'] : array();
    $current  = isset($settings['single_single_style']) ? $settings['single_single_style'] : '';
    $isB      = ($current === 'builder');
    $selB     = isset($settings['single_single_default_builder']) ? (int)$settings['single_single_default_builder'] : 0;
    $selM     = isset($settings['single_modal_default_builder']) ? (int)$settings['single_modal_default_builder'] : 0;
    $selE     = isset($settings['custom_event_for_set_settings']) ? (int)$settings['custom_event_for_set_settings'] : 0;

    $builders = $this->get_all_templates();
    $events   = get_posts(array(
        'post_type'      => 'mec-events',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));

    $bj = array();
    foreach ($builders as $b) {
        $bj[] = array('id' => $b->ID, 'title' => esc_html($b->post_title));
    }

    $ej = array();
    foreach ($events as $ev) {
        $ej[] = array('id' => $ev->ID, 'title' => esc_html($ev->post_title));
    }

    $cu = esc_url(admin_url('post-new.php?post_type=mec_esb'));
    ?>
    <script>
    (function($) {

        var B  = <?php echo wp_json_encode($bj); ?>;
        var E  = <?php echo wp_json_encode($ej); ?>;
        var iB = <?php echo $isB ? 'true' : 'false'; ?>;
        var sB = <?php echo (int)$selB; ?>;
        var sM = <?php echo (int)$selM; ?>;
        var sE = <?php echo (int)$selE; ?>;
        var cu = '<?php echo esc_js($cu); ?>';

        function buildOpts(arr, sel) {
            var h = '';
            $.each(arr, function(i, o) {
                h += '<option value="' + o.id + '"'
                   + (o.id === sel ? ' selected="selected"' : '')
                   + '>' + o.title + '</option>';
            });
            return h;
        }

        function inject() {
            var $select = $('#mec_settings_single_event_single_style');
            if ($select.length === 0) {
                $select = $('select[name="mec[settings][single_single_style]"]');
            }
            if ($select.length === 0) return false;

            // Añadir opción builder
            if ($select.find('option[value="builder"]').length === 0) {
                $select.append(
                    '<option value="builder"' + (iB ? ' selected="selected"' : '') + '>'
                    + 'QPH Single Builder (Elementor)'
                    + '</option>'
                );
                if (iB) $select.val('builder');
            }

            // No duplicar
            if ($('#mec_settings_single_event_single_default_builder_wrap').length > 0) {
                return true;
            }

            var show = iB ? '' : 'display:none;';

            // ============================================================
            // HTML IDÉNTICO al plugin original
            // Mismos IDs, misma estructura, mismo name format
            // ============================================================

            var noBuilders = B.length === 0
                ? 'Please Create New Design for Single Event Page '
                  + '<a href="' + cu + '" class="taxonomy-add-new">Create new</a>'
                : '';

            // Default Builder for Single Event
            var html1 = '<div class="mec-form-row"'
                + ' id="mec_settings_single_event_single_default_builder_wrap"'
                + ' style="' + show + '">';

            if (B.length === 0) {
                html1 += noBuilders;
            }

            html1 += '<label class="mec-col-3"'
                + ' for="mec_settings_single_event_single_default_builder">'
                + 'Default Builder for Single Event'
                + '</label>'
                + '<div class="mec-col-9">'
                + '<select'
                + ' id="mec_settings_single_event_single_default_builder"'
                + ' name="mec[settings][single_single_default_builder]">'
                + buildOpts(B, sB)
                + '</select>'
                + '</div>'
                + '</div>';

            // Default Builder for Modal View
            var html2 = '<div class="mec-form-row"'
                + ' id="mec_settings_single_event_single_modal_default_builder_wrap"'
                + ' style="' + show + '">';

            if (B.length === 0) {
                html2 += noBuilders;
            }

            html2 += '<label class="mec-col-3"'
                + ' for="mec_settings_single_event_single_modal_default_builder">'
                + 'Default Builder for Modal View'
                + '</label>'
                + '<div class="mec-col-9">'
                + '<select'
                + ' id="mec_settings_single_event_single_modal_default_builder"'
                + ' name="mec[settings][single_modal_default_builder]">'
                + buildOpts(B, sM)
                + '</select>'
                + '</div>'
                + '</div>';

            // Custom Event For Set Settings
            var html3 = '<div class="mec-form-row"'
                + ' id="mec_settings_custom_event_for_set_settings_wrap"'
                + ' style="' + show + '">'
                + '<label class="mec-col-3"'
                + ' for="mec_settings_custom_event_for_set_settings">'
                + 'Custom Event For Set Settings'
                + '</label>'
                + '<div class="mec-col-9">'
                + '<select'
                + ' id="mec_settings_custom_event_for_set_settings"'
                + ' name="mec[settings][custom_event_for_set_settings]">'
                + buildOpts(E, sE)
                + '</select>'
                + '<span class="mec-tooltip">'
                + '<div class="box left">'
                + '<h5 class="title">Default Single Event Template on Elementor</h5>'
                + '<div class="content">'
                + '<p>Choose your event for single builder addon.</p>'
                + '</div></div>'
                + '<i title="" class="dashicons-before dashicons-editor-help"></i>'
                + '</span>'
                + '</div></div>';

            // ============================================================
            // INSERTAR: Después del row del SELECT de MEC
            // Exactamente como lo hace el plugin original
            // ============================================================
            var $selectRow = $select.closest('.mec-form-row');
            $selectRow.after(html3);
            $selectRow.after(html2);
            $selectRow.after(html1);

            console.log('[QPH-ESB] ✅ HTML inyectado con IDs idénticos al original');

            // Toggle
            $select.on('change.qph', function() {
                var v = $(this).val();
                var $wraps = $(
                    '#mec_settings_single_event_single_default_builder_wrap,'
                    + '#mec_settings_single_event_single_modal_default_builder_wrap,'
                    + '#mec_settings_custom_event_for_set_settings_wrap'
                );
                v === 'builder' ? $wraps.slideDown(300) : $wraps.slideUp(300);
            });

            return true;
        }

        var att = 0;
        function tryInject() {
            att++;
            if (inject()) {
                console.log('[QPH-ESB] ✅ Completado en intento #' + att);
                return;
            }
            if (att < 30) setTimeout(tryInject, 400);
        }

        $(document).ready(function() {
            console.log('[QPH-ESB] v4.5');
            tryInject();

            new MutationObserver(function() {
                if ($('#mec_settings_single_event_single_style').length > 0
                    && $('#mec_settings_single_event_single_default_builder_wrap').length === 0) {
                    att = 0;
                    tryInject();
                }
            }).observe(document.body, {childList: true, subtree: true});

            $(document).on('click', '.mec-settings-menu a, [data-id]', function() {
                setTimeout(function() { att = 0; tryInject(); }, 500);
            });
        });

    })(jQuery);
    </script>
    <?php
    }

    // ============================================
    // POPUP
    // ============================================

    public function show_setup_popup() {
        if (get_option('qph_esb_setup_done')) return;
        $screen = get_current_screen();
        if (!$screen) return;
        $is_mec = strpos($screen->id, 'mec') !== false
               || $screen->post_type === 'mec_esb'
               || (isset($_GET['page']) && strpos(sanitize_text_field($_GET['page']), 'MEC') !== false);
        if (!$is_mec) return;

        $builders = $this->get_all_templates();
        ?>
        <div id="qph-esb-setup-popup">
            <div class="qph-esb-overlay"></div>
            <div class="qph-esb-modal">
                <div style="background:#0073aa;padding:18px 24px;">
                    <h3 style="margin:0;color:#fff;">Select Single Event Style</h3>
                </div>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <div style="padding:24px;">
                        <input type="hidden" name="action" value="qph_esb_apply_style_direct" />
                        <?php wp_nonce_field('qph_esb_setup', '_wpnonce'); ?>
                        <div style="background:#f0f7ff;border:2px solid #0073aa;border-radius:6px;padding:12px 15px;margin-bottom:12px;">
                            <label><input type="radio" name="qph_style" value="builder" checked /> <strong>Builder</strong></label>
                        </div>
                        <?php if (!empty($builders)) : ?>
                            <select name="template_id" style="width:100%;padding:8px;">
                                <?php foreach ($builders as $b) : ?>
                                    <option value="<?php echo esc_attr($b->ID); ?>"><?php echo esc_html($b->post_title); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else : ?>
                            <input type="hidden" name="template_id" value="0" />
                            <p style="color:#666;">No hay templates aún.</p>
                        <?php endif; ?>
                        <p style="color:#999;font-size:12px;margin-top:15px;">If you are using QPH Single Builder for the first time, simply ignore this pop-up.</p>
                    </div>
                    <div style="padding:14px 24px;background:#f7f7f7;border-top:1px solid #e0e0e0;text-align:right;display:flex;gap:10px;justify-content:flex-end;">
                        <button type="submit" class="button button-primary">Apply</button>
                        <button type="submit" name="action" value="qph_esb_skip_setup_direct" class="button">Skip</button>
                    </div>
                </form>
            </div>
        </div>
        <style>
            #qph-esb-setup-popup{position:fixed;inset:0;z-index:999999;display:flex;align-items:center;justify-content:center}
            .qph-esb-overlay{position:absolute;inset:0;background:rgba(0,0,0,.55)}
            .qph-esb-modal{position:relative;background:#fff;border-radius:10px;width:90%;max-width:460px;box-shadow:0 15px 50px rgba(0,0,0,.25);overflow:hidden}
        </style>
        <?php
    }

    public function handle_direct_apply() {
        check_admin_referer('qph_esb_setup', '_wpnonce');
        $tid  = isset($_POST['template_id']) ? intval($_POST['template_id']) : 0;
        $opts = get_option('mec_options', array());
        if (!is_array($opts)) $opts = array();
        if (!isset($opts['settings'])) $opts['settings'] = array();
        $opts['settings']['single_single_style'] = 'builder';
        if ($tid > 0) {
            $opts['settings']['single_single_default_builder'] = $tid;
            $opts['settings']['single_modal_default_builder']  = $tid;
        }
        update_option('mec_options', $opts);
        update_option('qph_esb_setup_done', '1');
        wp_redirect(admin_url('admin.php?page=qph-event-builder'));
        exit;
    }

    public function handle_direct_skip() {
        check_admin_referer('qph_esb_setup', '_wpnonce');
        update_option('qph_esb_setup_done', '1');
        wp_redirect(admin_url('admin.php?page=MEC-settings'));
        exit;
    }

    // ============================================
    // METABOX
    // ============================================

    public function add_event_metabox() {
        add_meta_box('qph_esb_tpl', 'QPH Template', array($this, 'render_event_metabox'), 'mec-events', 'side');
    }

    public function render_event_metabox($post) {
        $bs = $this->get_all_templates();
        $s  = get_post_meta($post->ID, 'single_design_page', true);
        $m  = get_post_meta($post->ID, 'single_modal_design_page', true);
        wp_nonce_field('qph_esb_meta', 'qph_esb_nonce');
        echo '<p><label><strong>Template Single:</strong></label>';
        echo '<select name="mec[single_design_page]" style="width:100%;margin-top:4px">';
        echo '<option value="">-- Por defecto --</option>';
        foreach ($bs as $b) echo '<option value="' . esc_attr($b->ID) . '"' . selected($s, $b->ID, false) . '>' . esc_html($b->post_title) . '</option>';
        echo '</select></p>';
        echo '<p><label><strong>Template Modal:</strong></label>';
        echo '<select name="mec[single_modal_design_page]" style="width:100%;margin-top:4px">';
        echo '<option value="">-- Por defecto --</option>';
        foreach ($bs as $b) echo '<option value="' . esc_attr($b->ID) . '"' . selected($m, $b->ID, false) . '>' . esc_html($b->post_title) . '</option>';
        echo '</select></p>';
    }

    public function save_event_meta($pid) {
        if (!isset($_POST['qph_esb_nonce']) || !wp_verify_nonce($_POST['qph_esb_nonce'], 'qph_esb_meta')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        $mec = isset($_POST['mec']) ? $_POST['mec'] : array();
        if (isset($mec['single_design_page'])) update_post_meta($pid, 'single_design_page', sanitize_text_field($mec['single_design_page']));
        if (isset($mec['single_modal_design_page'])) update_post_meta($pid, 'single_modal_design_page', sanitize_text_field($mec['single_modal_design_page']));
    }

    // ============================================
    // CATEGORÍAS
    // ============================================

    public function category_add_fields() {
        $bs = $this->get_all_templates();
        if (empty($bs)) return;
        foreach (array('single_design_page' => 'Template Single (QPH)', 'single_modal_design_page' => 'Template Modal (QPH)') as $k => $l) {
            echo '<div class="form-field"><label>' . esc_html($l) . '</label>';
            echo '<select name="' . esc_attr($k) . '"><option value="">-- Por defecto --</option>';
            foreach ($bs as $b) echo '<option value="' . esc_attr($b->ID) . '">' . esc_html($b->post_title) . '</option>';
            echo '</select></div>';
        }
    }

    public function category_edit_fields($term) {
        $bs = $this->get_all_templates();
        if (empty($bs)) return;
        foreach (array('single_design_page' => 'Template Single (QPH)', 'single_modal_design_page' => 'Template Modal (QPH)') as $k => $l) {
            $v = get_term_meta($term->term_id, $k, true);
            echo '<tr class="form-field"><th><label>' . esc_html($l) . '</label></th><td>';
            echo '<select name="' . esc_attr($k) . '"><option value="">-- Por defecto --</option>';
            foreach ($bs as $b) echo '<option value="' . esc_attr($b->ID) . '"' . selected($v, $b->ID, false) . '>' . esc_html($b->post_title) . '</option>';
            echo '</select></td></tr>';
        }
    }

    public function save_category_meta($tid) {
        foreach (array('single_design_page', 'single_modal_design_page') as $k) {
            if (isset($_POST[$k])) update_term_meta($tid, $k, sanitize_text_field($_POST[$k]));
        }
    }

    // ============================================
    // ACTIVACIÓN
    // ============================================

    public function activate() {
        $this->register_post_type();
        $cpt = get_option('elementor_cpt_support', array('page', 'post'));
        if (!is_array($cpt)) $cpt = array('page', 'post');
        if (!in_array('mec_esb', $cpt)) {
            $cpt[] = 'mec_esb';
            update_option('elementor_cpt_support', $cpt);
        }
        delete_option('qph_esb_setup_done');
        flush_rewrite_rules();
        $this->log('Activado v4.2.0');
    }

    public function deactivate() {
        flush_rewrite_rules();
        $this->log('Desactivado');
    }

} // ← Cierre de la clase

QPH_Single_Builder_MEC::get_instance();