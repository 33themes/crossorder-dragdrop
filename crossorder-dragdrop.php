<?php
/*
Plugin Name: CrossOrder Drag&Drop
Plugin URI: http://www.33themes.com
Description: Drag & Drop the content from different sources (cpt, post, ..) in one loop
Version: 0.9
Author: 33themes
Author URI: http://www.33themes.com
License: GPL2
*/

define('TTTINC_CROSSORDER', dirname(__FILE__) );
define('TTTVERSION_CROSSORDER', 0.9 );

function ttt_autoload_crossorder( $class ) {
    if ( 0 !== strpos( $class, 'CrossOrder' ) )
        return;

    if ($class == 'CrossOrder') $class = 'CrossOrder_Common';
    
    $file = TTTINC_CROSSORDER . '/class/' . $class . '.php';

    if (is_file($file)) {
        require_once $file;
        return true;
    }
    
    throw new Exception("Unable to load $class at ".$file);
}

if ( function_exists( 'spl_autoload_register' ) ) {
    spl_autoload_register( 'ttt_autoload_crossorder' );
} else {
    require_once TTTINC_CROSSORDER . '/class/CrossOrder_Common.php';
    require_once TTTINC_CROSSORDER . '/class/CrossOrder_Admin.php';
    require_once TTTINC_CROSSORDER . '/class/CrossOrder.php';
}

function crossorder_init () {
    global $CrossOrder;

    $s = load_plugin_textdomain( 'crossorder', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
    if ( !is_admin() ) {
        $CrossOrder = new CrossOrder('front');
    }
    else {
        $CrossOrderAdmin = new CrossOrderAdmin('admin');
    }
}

add_action('init', 'crossorder_init');
