<?php

/**
 * Plugin Name: Tailor - Sample extension
 * Plugin URI: http://www.gettailor.com
 * Description: A sample Tailor extension.
 * Version: 1.1.0
 * Author: Andrew Worsfold
 * Author URI:  http://www.andrewworsfold.com
 * Text Domain: tailor-extension
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Tailor_Extension' ) ) {

    /**
     * Tailor Extension class.
     */
    class Tailor_Extension {

        /**
         * Tailor Extension instance.
         *
         * @access private
         * @var Tailor_Extension
         */
        private static $instance;

        /**
         * The plugin version number.
         *
         * @access private
         * @var string
         */
        private static $version;

	    /**
	     * The plugin basename.
	     *
	     * @access private
	     * @var string
	     */
	    private static $plugin_basename;

        /**
         * The plugin name.
         *
         * @access private
         * @var string
         */
        private static $plugin_name;

        /**
         * The plugin directory.
         *
         * @access private
         * @var string
         */
        private static $plugin_dir;

        /**
         * The plugin URL.
         *
         * @access private
         * @var string
         */
        private static $plugin_url;

        /**
         * Returns the Tailor Extension instance.
         *
         * @return Tailor_Extension
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor.
         */
	    public function __construct() {

            $plugin_data = get_file_data( __FILE__, array( 'Plugin Name', 'Version' ) );

            self::$plugin_basename = plugin_basename( __FILE__ );
            self::$plugin_name = array_shift( $plugin_data );
            self::$version = array_shift( $plugin_data );
	        self::$plugin_dir = trailingslashit( plugin_dir_path( __FILE__ ) );
	        self::$plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );

		    add_action( 'plugins_loaded', array( $this, 'init' ) );
        }

	    /**
	     * Initializes the plugin.
	     */
	    public function init() {
		    if (
			    ! class_exists( 'Tailor' ) ||                               // Tailor is not active, or
			    ! version_compare( tailor()->version(), '1.7.0', '>=' )     // An unsupported version is being used
		    ) {
			    add_action( 'admin_notices', array( $this, 'display_version_notice' ) );
			    return;
		    }

		    $this->add_actions();
		    $this->includes();
	    }

	    /**
	     * Displays an admin notice if an unsupported version of Tailor is being used.
	     *
	     * @since 1.1.0
	     */
	    public function display_version_notice() {
		    printf(
			    '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
			    __( 'Please ensure that Tailor 1.7.0 (or newer) is active to use the sample extension.' )
		    );
	    }

	    /**
	     * Includes required plugin files.
	     *
	     * @access protected
	     */
	    protected function includes() {
		    require_once $this->plugin_dir() . 'includes/functions.php';
	    }

        /**
         * Adds required action hooks.
         *
         * @access protected
         */
        protected function add_actions() {

	        // Load element definitions
	        add_action( 'tailor_load_elements', array( $this, 'load_elements' ), 20 );

	        // Register custom elements
	        add_action( 'tailor_register_elements', array( $this, 'register_elements' ), 99 );

	        // Register custom template partials director
	        add_filter( 'tailor_plugin_partial_paths', array( $this, 'register_partial_path' ) );

	        // Enqueue scripts and styles
	        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	        add_action( 'tailor_canvas_enqueue_scripts', array( $this, 'enqueue_scripts' ), 99 );
        }

	    /**
	     * Loads and registers the new Tailor elements and shortcodes.
	     */
	    public function load_elements() {
		    require_once $this->plugin_dir() . 'includes/elements/class-custom-child.php';
		    require_once $this->plugin_dir() . 'includes/elements/class-custom-container.php';
		    require_once $this->plugin_dir() . 'includes/elements/class-custom-content.php';
            require_once $this->plugin_dir() . 'includes/elements/class-custom-wrapper.php';
            require_once $this->plugin_dir() . 'includes/elements/class-flipcard.php';
		    require_once $this->plugin_dir() . 'includes/elements/class-test.php';

		    require_once $this->plugin_dir() . 'includes/shortcodes/shortcode-custom-child.php';
		    require_once $this->plugin_dir() . 'includes/shortcodes/shortcode-custom-container.php';
		    require_once $this->plugin_dir() . 'includes/shortcodes/shortcode-custom-content.php';
            require_once $this->plugin_dir() . 'includes/shortcodes/shortcode-custom-wrapper.php';
            require_once $this->plugin_dir() . 'includes/shortcodes/shortcode-flipcard.php';
		    require_once $this->plugin_dir() . 'includes/shortcodes/shortcode-test.php';
	    }

	    /**
	     * Loads and registers the new Tailor elements and shortcodes.
	     *
	     * @param $element_manager Tailor_Elements
	     */
	    public function register_elements( $element_manager ) {

		    $element_manager->add_element( 'tailor_custom_content', array(
			    'label'             =>  __( 'Custom content' ),
			    'description'       =>  __( 'A custom content element' ),
			    'badge'             =>  __( 'Custom' ),
			    // 'class_name'       =>  'Tailor_Custom_Element',
			    // 'type'             =>  'container', 'wrapper', 'child', 'content',
		    ) );

            $element_manager->add_element( 'tailor_custom_wrapper', array(
                'label'             =>  __( 'Custom wrapper' ),
                'description'       =>  __( 'A custom wrapper element' ),
                'badge'             =>  __( 'Custom' ),
                'type'              =>  'wrapper',
                'child_container'   =>  '.tailor-custom-wrapper__content',
            ) );

            $element_manager->add_element( 'tailor_flipcard', array(
                'label'             =>  __( 'Flipcard' ),
                'description'       =>  __( 'Add a flipcard' ),
                'badge'             =>  __( 'Intensity' ),
                'type'              =>  'wrapper',
                'child_container'   =>  '.tailor-flipcard__content',
            ) );

		    $element_manager->add_element( 'tailor_custom_container', array(
			    'label'             =>  __( 'Custom container' ),
			    'description'       =>  __( 'A custom container element' ),
			    'badge'             =>  __( 'Custom' ),
			    'type'              =>  'container',
			    'child'             =>  'tailor_custom_child',
		    ) );

		    $element_manager->add_element( 'tailor_custom_child', array(
			    'label'             =>  __( 'Custom child' ),
			    'type'              =>  'child',
		    ) );

		    $element_manager->add_element( 'tailor_test', array(
			    'label'             =>  __( 'All controls' ),
			    'description'       =>  __( 'Contains all control types' ),
			    'badge'             =>  __( 'Custom' ),
		    ) );

	    }

	    /**
	     * Enqueues frontend styles.
	     */
	    public function enqueue_styles() {
		    wp_enqueue_style(
			    'tailor-custom-styles',
			    $this->plugin_url() . 'assets/css/frontend' . ( SCRIPT_DEBUG ? '.css' : '.min.css' ),
			    array(),
			    $this->version()
		    );
	    }

	    /**
	     * Enqueues canvas scripts.
	     */
	    public function enqueue_scripts() {
		    wp_enqueue_script(
			    'tailor-custom-canvas',
			    $this->plugin_url() . 'assets/js/' . ( SCRIPT_DEBUG ? 'src/canvas/canvas.js' : 'dist/canvas.min.js' ),
			    array( 'tailor-canvas' ),
			    $this->version(),
			    true
		    );
	    }

	    /**
	     * Registers the partial directory for this extension plugin.
	     *
	     * @param $paths
	     *
	     * @return array
	     */
	    public function register_partial_path( $paths ) {
		    $paths[] = $this->plugin_dir() . 'partials/';
		    return $paths;
	    }

        /**
         * Returns the version number of the plugin.
         *
         * @return string
         */
        public function version() {
            return self::$version;
        }

	    /**
	     * Returns the plugin basename.
	     *
	     * @return string
	     */
	    public function plugin_basename() {
		    return self::$plugin_basename;
	    }

        /**
         * Returns the plugin name.
         *
         * @return string
         */
        public function plugin_name() {
            return self::$plugin_name;
        }

        /**
         * Returns the plugin directory.
         *
         * @return string
         */
        public function plugin_dir() {
            return self::$plugin_dir;
        }

        /**
         * Returns the plugin URL.
         *
         * @return string
         */
        public function plugin_url() {
            return self::$plugin_url;
        }
    }
}

if ( ! function_exists( 'tailor_extension' ) ) {

	/**
	 * Returns the Tailor Extension instance.
	 *
	 * @return Tailor_Extension
	 */
	function tailor_extension() {
		return Tailor_Extension::instance();
	}
}

/**
 * Initializes the Tailor Extension.
 */
tailor_extension();
