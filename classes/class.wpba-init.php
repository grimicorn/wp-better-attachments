<?php
if ( ! class_exists('WPBA_Init' ) ) :
/**
 * This class triggers functions that run during activation/deactivation & uninstallation
 */
class WPBA_Init
{
    // Set this to true to get the state of origin, so you don't need to always uninstall during development.
    const STATE_OF_ORIGIN = false;


    function __construct( $case = false )
    {
        if ( ! $case )
            wp_die( 'Busted! You should not call this class directly', 'Doing it wrong!' );

        switch( $case )
        {
            case 'activate' :
                // add_action calls and else
                # @example:
                add_action( 'init', array( &$this, 'activate_cb' ) );
                break;

            case 'deactivate' :
                // reset the options
                # @example:
                add_action( 'init', array( &$this, 'deactivate_cb' ) );
                break;

            case 'uninstall' :
                // delete the tables
                # @example:
                add_action( 'init', array( &$this, 'uninstall_cb' ) );
                break;
        }
    }

    /**
     * Set up tables, add options, etc. - All preparation that only needs to be done once
     */
    function on_activate()
    {
        new WPBA_Init( 'activate' );
    }

    /**
     * Do nothing like removing settings, etc.
     * The user could reactivate the plugin and wants everything in the state before activation.
     * Take a constant to remove everything, so you can develop & test easier.
     */
    function on_deactivate()
    {
        $case = 'deactivate';
        if ( STATE_OF_ORIGIN )
            $case = 'uninstall';

        new WPBA_Init( $case );
    }

    /**
     * Remove/Delete everything - If the user wants to uninstall, then he wants the state of origin.
     *
     * Will be called when the user clicks on the uninstall link that calls for the plugin to uninstall itself
     */
    function on_uninstall()
    {
        // important: check if the file is the one that was registered with the uninstall hook (function)
        if ( __FILE__ != WP_UNINSTALL_PLUGIN )
            return;

        new WPBA_Init( 'uninstall' );
    }

    function activate_cb()
    {
        // Stuff like adding default option values to the DB
        wp_die( '<h1>This is run on <code>init</code> during activation.</h1>', 'Activation hook example' );
    }

    function deactivate_cb()
    {
        // if you need to output messages in the 'admin_notices' field, do it like this:
        $this->error( "Some message.<br />" );
        // if you need to output messages in the 'admin_notices' field AND stop further processing, do it like this:
        $this->error( "Some message.<br />", TRUE );
        // Stuff like remove_option(); etc.
        wp_die( '<h1>This is run on <code>init</code> during deactivation.</h1>', 'Deactivation hook example' );
    }

    function uninstall_cb()
    {
        // Stuff like delete tables, etc.
        wp_die( '<h1>This is run on <code>init</code> during uninstallation</h1>', 'Uninstallation hook example' );
    }
    /**
     * trigger_error()
     *
     * @param (string) $error_msg
     * @param (boolean) $fatal_error | catched a fatal error - when we exit, then we can't go further than this point
     * @param unknown_type $error_type
     * @return void
     */
    function error( $error_msg, $fatal_error = false, $error_type = E_USER_ERROR )
    {
        if( isset( $_GET['action'] ) && 'error_scrape' == $_GET['action'] )
        {
            echo "{$error_msg}\n";
            if ( $fatal_error )
                exit;
        }
        else
        {
            trigger_error( $error_msg, $error_type );
        }
    }
}
endif;