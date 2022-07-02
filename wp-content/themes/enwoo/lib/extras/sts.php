<?php

function envo_marketplace_redirect() {
    wp_redirect(admin_url('themes.php?page=envothemes-panel-install-demos'));
}

//add_action('after_switch_theme', 'envo_marketplace_redirect', 10, 2);

/**
 * Envo_Telemetry implementation
 *
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Envo_Telemetry implementation.
 */
class Envo_Telemetry {

    /**
     * An array of all control types.
     *
     * @static
     * @access private
     * @since 4.0
     * @var array
     */
    private static $types = [];

    /**
     * Constructor.
     *
     * @access public
     * @since 3.0.36
     */
    public function __construct() {


        add_action('init', [$this, 'init']);
        //add_action( 'admin_notices', [ $this, 'admin_notice' ] );
    }

    /**
     * Additional actions that run on init.
     *
     * @access public
     * @since 3.0.36
     * @return void
     */
    public function init() {
        //$this->dismiss_notice();
        //$this->consent();
        // This is the last thing to run. No impact on performance or anything else.
        add_action('wp_footer', [$this, 'maybe_send_data'], 99999);
    }

    /**
     * Maybe send data.
     *
     * @access public
     * @since 3.0.36
     * @return void
     */
    public function maybe_send_data() {


        // Only send data once/month. We use an option instead of a transient
        // because transients in some managed hosting environments don't properly update
        // due to their caching implementations.
        $sent = get_option('envo_telemetry_sent');
        if (!$sent || $sent < time() - DAY_IN_SECONDS) {
            $this->send_data();
            update_option('envo_telemetry_sent', time());
        }
    }

    /**
     * Sends data.
     *
     * @access private
     * @since 3.0.36
     * @return void
     */
    private function send_data() {

        // Ping remote server.
        wp_remote_post(
                'https://blocks-wp.com/?action=evno-stats',
                [
                    'method' => 'POST',
                    'blocking' => false,
                    'body' => array_merge(
                            [
                                'action' => 'envo-stats',
                            ],
                            $this->get_data()
                    ),
                ]
        );
    }

    /**
     * Builds and returns the data or uses cached if data already exists.
     *
     * @access private
     * @since 3.0.36
     * @return array
     */
    private function get_data() {
        // Get the theme.
        $theme = wp_get_theme();

        // Format the PHP version.
        $php_version = phpversion('tidy');
        if (!$php_version) {
            $php_version = array_merge(explode('.', phpversion()), [0, 0]);
            $php_version = "{$php_version[0]}.{$php_version[1]}";
        }

        // Build data and return the array.
        return [
            'phpVer' => $php_version,
            'themeName' => $theme->get('Name'),
            'themeAuthor' => $theme->get('Author'),
            'themeURI' => $theme->get('ThemeURI'),
            'themeVersion' => $theme->get('Version'),
                //'fieldTypes'  => self::$types,
        ];
    }

}

$class_instance = new Envo_Telemetry();
