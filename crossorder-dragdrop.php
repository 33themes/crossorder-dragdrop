<?php
/*
Plugin Name: CrossOrder Drag&Drop
Plugin URI: http://www.kreait-dev.com
Description: Drag & Drop the content from different sources (cpt, post, ..) in one loop
Version: 0.8
Author: Kreait-Dev
Author URI: http://www.kreait-dev.com
License: GPL2
*/

function crossorder_init () {
    global $CrossOrder;

    $s = load_plugin_textdomain( 'crossorder', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

    require_once(dirname(__FILE__).'/crossorder-common.class.php');
    require_once(dirname(__FILE__).'/crossorder-admin.class.php');
    require_once(dirname(__FILE__).'/crossorder.class.php');

    if ( is_admin() )
        $CrossOrderAdmin = new CrossOrderAdmin('admin');
    else
        $CrossOrder = new CrossOrder('front');

}

add_action('init', 'crossorder_init', 10);

?>
