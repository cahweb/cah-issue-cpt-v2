<?php
/**
 * Plugin Name: CAH Issue Custom Post Type
 * Description: Custom Post type to handle issues of a publication, often containing various Articles. Designed to work interoperably with the Article Custom Post Type v2.0
 * Author: Mike W. Leavitt
 * Version: 2.0.0
 */

defined( 'ABSPATH' ) || exit( "No direct access plzthx" );

define( 'CAH_ISSUE_2__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CAH_ISSUE_2__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CAH_ISSUE_2__PLUGIN_FILE', __FILE__ );

require_once 'includes/cah-issue-setup.php';
require_once 'includes/cah-issue-editor.php';

if( !function_exists( 'cah_issue_2_plugin_activate' ) ) {
    function cah_issue_2_plugin_activate() {
        CAH_IssueSetup::register_issue();
        flush_rewrite_rules();
    }
}
register_activation_hook( __FILE__ , 'cah_issue_2_plugin_activate' );


if( !function_exists( 'cah_issue_2_plugin_deactivate' ) ) {
    function cah_issue_2_plugin_deactivate() {
        flush_rewrite_rules();
    }
}
register_deactivation_hook( __FILE__ , 'cah_issue_2_plugin_deactivate' );

add_action( 'plugins_loaded', array( 'CAH_IssueSetup', 'setup' ) );

if( !function_exists( 'numberToRomanRepresentation' ) ) {
    function numberToRomanRepresentation( $number ) {
        $dict = array(
            'M'  => 1000,
            'CM' => 900,
            'D'  => 500,
            'CD' => 400,
            'C'  => 100,
            'XC' => 90,
            'L'  => 50,
            'XL' => 40,
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
            'I'  => 1
        );

        $retval = '';

        while( $number > 0 ) {
            foreach( $dict as $roman => $int ) {
                if( $number >= $int ) {
                    $number -= $int;
                    $retval .= $roman;
                    break;
                }
            }
        }

        return $retval;
    }
}
?>