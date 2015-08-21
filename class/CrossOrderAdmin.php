<?php

class CrossOrderAdmin extends CrossOrder_Common {

    private $role_order = 'edit_pages';
    private $role_edit = 'manage_options';

    public function __construct($type = 'admin') {
        $_style = TTTINC_CROSSORDER.'/crossorder-dragdrop.php';

        add_action('admin_menu', array( &$this, 'admin_menu' ));
        wp_enqueue_script( 'jquery-simple-datetimepicker', plugins_url('/templates/admin/js/jquery.simple-dtpicker.js',$_style), array( 'jquery' )  );
        wp_enqueue_script( 'CrossOrder', plugins_url('/templates/admin/js/crossorder.js', $_style), array( 'jquery','jquery-ui-core','jquery-ui-widget','jquery-ui-mouse','jquery-ui-sortable','jquery-simple-datetimepicker' )  );
        wp_enqueue_style( 'jquery-simple-datetimepicker', plugins_url('/templates/admin/css/jquery.simple-dtpicker.css', $_style) );
        wp_enqueue_style( 'CrossOrder', plugins_url('/templates/admin/css/crossorder.css', $_style) );
    }
    
    function admin_menu() {
        add_menu_page( 'CrossOrder', __('CrossOrder','crossorder') , $this->role_order, 'crossorder-menu', array( &$this, 'actions' ) );
        add_submenu_page( 'crossorder-menu', __('Manager','crossorder') , 'Manager', $this->role_edit, 'crossorder-manager', array( &$this, 'manager' ) );
    }

    function removekey( $name ) {
        delete_option( self::okey.'_'.$name );
    }

    function _link_create() {
        return '?'.http_build_query(array(
            'page' => $_GET['page'],
            'action' => 'create'
        ));
    }

    function _link_filter_edit( $slug ) {
        return '?'.http_build_query(array(
            'page' => $_GET['page'],
            'action' => 'edit',
            'slug' => $slug
        ));
    }

    function _link_filter_order( $slug ) {
        return '?'.http_build_query(array(
            'page' => $_GET['page'],
            'action' => 'order',
            'slug' => $slug
        ));
    }

    function _link_filter_update( $slug ) {
        return '?'.http_build_query(array(
            'page' => $_GET['page'],
            'action' => 'edit',
            'slug' => $slug
        ));
    }

    function _link_filter_remove( $slug ) {
        return '?'.http_build_query(array(
            'page' => $_GET['page'],
            'action' => 'remove',
            'slug' => $slug
        ));
    }


    function loop_args() {

        $this->load_params();

        foreach ( $this->params as $param ) {
            unset( $_query );

            foreach ( $param as $key => $value ) {
                if (preg_match('/^\s*$/', trim($value) ))  continue;

                if ( $key == 'taxonomy' && !empty($param['taxonomy_term']) ) {
                    if ( is_numeric( $param['taxonomy_term'] ) ) {
                        $_query['tax_query'][] = array(
                            'taxonomy' => $value,
                            'field' => 'term_id',
                            'terms' => array($param['taxonomy_term']),
                        );
                    }
                    elseif ( strpos(',',$params['taxonomy_term']) !== false ) {
                        $params['taxonomy_term'] = preg_replace('/\s*,\s*/','', $params['taxonomy_term']);
                        $_query['tax_query'][] = array(
                            'taxonomy' => $value,
                            'field' => 'term_id',
                            'terms' => split(',',$param['taxonomy_term']),
                        );
                    }
                    else {
                        $_query['tax_query'][] = array(
                            'taxonomy' => $value,
                            'field' => 'slug',
                            'terms' => $param['taxonomy_term'],
                        );
                    }
                }
                elseif (preg_match('/stiky/i',$key)) {
                    if (preg_match('/^(yes|1)$/i',$value)) {
                        $_query[ 'post__in' ] = get_option( 'sticky_posts' );
                    }
                }
                else
                    $_query[ $key ] = $value;
            }

            if (isset($_query['tax_query'])) {
                // $_query['tax_query']['relation'] = 'AND';
                unset($_query['taxonomy_term']);
            }


            if (is_array($_query)) {
                $_query['posts_per_page'] = 150;
                $_args[] = $_query;
            }
        }





        return $_args;
    }

    function loop() {
        global $blog_id;

        unset( $_return );

        foreach( $this->loop_args() as $args ) {
    
            if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE) {
                if (!isset($args['blog_id']) || $args['blog_id'] <= 0 )
                    $args['blog_id'] = get_current_blog_id();

                switch_to_blog($args['blog_id']);
            }

            $_query = new WP_Query( $args );

            while ( $_query->have_posts() ) {

                $_query->the_post();
                $_id = $_query->post->ID;

                if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE) {
                    if ($args['blog_id'] > 0) {
                        $_id = $args['blog_id'].'-'.$_query->post->ID;
                    }
                }

                $_return[ $_id ] = (object) $_query->post;

                if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE) {
                    if ($args['blog_id'] > 0) {
                        $_return[ $_id ]->blog_id = $args['blog_id'];
                    }
                }

            }

            $this->restore_current_blog(); 

        }

        $this->loop = $_return;

        return $_return;
    }

    function have_loop() {
        if (count($this->loop) > 0) return true;

        $this->restore_current_blog(); 

        return false;
    }

    function the_loop() {
        global $post;
        $this->loop_post = array_shift($this->loop);
 
        if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE)
            switch_to_blog($this->the_loop_blog_id());

        $post = get_post($this->the_loop_id());
        $post = $this->loop_post;

        $s = get_post_type_object($post->post_type);
        $this->loop_post->_edit_link = get_admin_url().sprintf($s->_edit_link, $post->ID).'&action=edit';


            $this->loop_post->blog_id = get_current_blog_id();

        $this->loop_post->blog_name = get_bloginfo('name');

        setup_postdata( $post );
    }

    function the_loop_title() {
        return $this->loop_post->post_title;
    }
    function the_loop_id() {
        return $this->loop_post->ID;
    }
    function the_loop_post_type() {
        return $this->loop_post->post_type;
    }
    function the_loop_blog_id() {
        return $this->loop_post->blog_id;
    }
    function the_loop_blog_name() {
        return $this->loop_post->blog_name;
    }

    function manager() {
        switch( $_GET['action'] ) {
            case 'create':
                return $this->_action_create();
            case 'remove':
                return $this->_action_remove();
            case 'edit':
                return $this->_action_edit();
            case 'order':
                return $this->_action_order();
            default:
                return $this->_action_manager();
        }

        return false;    
    }

    function _error() {
        $e = new WP_Error('broke', __("You do not have sufficient permissions to access this page."));
        echo $e->get_error_message();
        die();
    }

    function actions() {
        switch( $_GET['action'] ) {
            case 'order':
                return $this->_action_order();
            default:
                return $this->_action_main();
        }

        return false;
    }

    function _action_manager() {
        if ( ! current_user_can( $this->role_edit ) ) return $this->_error();
        require_once(TTTINC_CROSSORDER.'/templates/admin/manager.php');
    }

    function _action_main() {
        if ( ! current_user_can( $this->role_order ) ) return $this->_error();
        require_once(TTTINC_CROSSORDER.'/templates/admin/main.php');
    }

    function _action_remove() {
        if ( ! current_user_can( $this->role_edit ) ) return $this->_error();
        $this->do_remove( $_GET['slug'] );
        $this->_action_manager();
    }

    function _action_create() {
        if ( ! current_user_can( $this->role_edit ) ) return $this->_error();
        $this->do_create( $_POST['name'] );
        $this->_action_manager();
    }

    function _action_edit() {
        if ( ! current_user_can( $this->role_edit ) ) return $this->_error();

        $this->_action_filter_update();
        require_once(TTTINC_CROSSORDER.'/templates/admin/edit.php');
    }

    function _action_order() {
        if ( ! current_user_can( $this->role_order ) ) return $this->_error();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ref = 'crossorder_'.$_GET['slug'];
            $this->restore_current_blog();


            if (empty($_POST['new_order'])) {
                $this->remove_bkey('order_'.$_GET['slug']);
            }
            else {
                $s = preg_split('/,/',$_POST['new_order']);
                $this->do_order( $s );

                foreach( $s as $value) {

                    list($blog_id,$post_id) = $this->_get_blog_post_id($value);

                    if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE) {
                        switch_to_blog($blog_id);
                    }

                    $gmt = get_option( 'gmt_offset' );
                    $gmt_string = '+0000';
                    
                    $refFrom = $_POST['date_from_'.$blog_id.'_'.$post_id];
                    $refTo = $_POST['date_to_'.$blog_id.'_'.$post_id];

                    if ( isset($refFrom) && isset($refTo) ) {

                        $from = DateTime::createFromFormat('Y/m/d H:iP',$refFrom.$gmt_string );
                        $to = DateTime::createFromFormat('Y/m/d H:iP',$refTo.$gmt_string );

                        $m = array(
                            'from_orig' => $refFrom,
                            'from' => $from->format('U'),
                            'to_orig' => $refTo,
                            'to' => $to->format('U')
                        );

                        delete_post_meta( $post_id, $ref );
                        add_post_meta( $post_id, $ref, $m );
                    }
                    else {
                        delete_post_meta( $post_id, $ref );
                    }

                }
            }
        }

        $this->restore_current_blog();

        require_once(TTTINC_CROSSORDER.'/templates/admin/order.php');
    }

    function do_order( $order ) {
        $this->bkey('order_'.$_GET['slug'], $order );
    }

    function do_remove( $slug ) {
        $filters = $this->bkey('filters');
        unset( $filters[ $slug ] );
        $filters = $this->bkey('filters', $filters);

        $this->removekey('filters_'.$slug );
        $this->removekey('order_'.$slug );
    }

    function do_create( $name ) {
        $filters = $this->load();
        $_slug = $this->_slug($name);

        if (is_array($filters) && array_key_exists( $_slug, $filters )) {
            echo __( sprintf('Error: The "%s" filter all ready exists!',$name) ,'crossorder' );
            return false;
        }

        $filters[ $_slug ] = array('slug'=> $_slug, 'name' => $name );

        $this->bkey('filters', $filters);
    }

    function _action_filter_update() {
        if (!is_array($_POST)) return false;

        $_slug = $_GET['slug'];

        $filters = $this->load();

        if (isset($_POST['name']))
            $filters[ $_slug ]['name'] = $_POST['name'];

        if (isset($_POST['description']))
            $filters[ $_slug ]['description'] = $_POST['description'];

        $this->bkey('filters', $filters);

        foreach( $_POST as $key => $value ) {
            if (!preg_match('/^p(\d+)_([\w\_]+)$/',$key, $regs)) continue;
            $_id = $regs[1];
            $_name = $regs[2];

            $_filter_params[ $_id ][ $_name] = $value;
        }

        $this->bkey('filters_'.$_slug, $_filter_params);
    }
}
