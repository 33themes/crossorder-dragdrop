<?php

class CrossOrder_Common {

    const okey = 'crossorder';

    function __construct( $type = 'front' ) {

    }

    function restore_current_blog() {

        global $switched;
        global $blog_id;

        if (!defined('WP_ALLOW_MULTISITE') || !WP_ALLOW_MULTISITE)
            return false;

        restore_current_blog();

        if ($switched == false)
            return false;

        $_id = $GLOBALS['_wp_switched_stack'][0];


        if (!$_id) {
            $_id = get_current_blog_id();
        }

        switch_to_blog($_id);
    }

    function bkey( $name, $value = null ) {
        if ( $value !== null ) {
            delete_option( self::okey.'_'.$name );
            add_option( self::okey.'_'.$name, $value );
        }
        return get_option( self::okey.'_'.$name );
    }

    function remove_bkey($name) {
        delete_option( self::okey.'_'.$name );
    }

    function _slug( $name ) {
        $name = preg_replace('/\s+/','_',$name);
        $name = mb_strtolower($name);
        return $name;
    }

    function load( $slug = false ) {
        $this->restore_current_blog();

        $this->filters = $this->bkey('filters');

        if (empty($this->filters)) return false;

        if ( $slug === false ) {
            return $this->filters;
        }


        if (isset($this->filters[$slug])) {
            $filter[ $slug ] = $this->filters[ $slug ];
            $this->filters = $filter;

            return $this->filters;
        }


        return false;
    }

    function have_filters() {
        if (is_array($this->filters) && count($this->filters) > 0) return true;

        return false;
    }

    function the_filter() {
        $this->filter = array_shift($this->filters);
    }

    function _the_base( $key ) {
        if (!is_array($this->filter)) return false;

        if (array_key_exists( $key, $this->filter))
            return $this->filter[ $key ];
        
        return false;
    }

    private function _filter_exists($key) {
        $this->filters = $this->bkey('filters');
        if (isset($this->filters[$key]))
            return $this->filters[$key];

        return false;        
    }

    public function filter_exists($key) {
        if (is_object($this))
            return $this->_filter_exists($key);
        else {
            $a = new CrossOrder_Common();
            return $a->_filter_exists($key);
        }
    }

    function _get_blog_post_id( $value ) {

        if ( strpos($value,'-') === false ) {
            $_blog_id = get_current_blog_id();
            $_post_id = $value;
        }
        else {
            $s = split('-',$value);
            $_blog_id = $s[0];
            $_post_id = $s[1];
        }

        return array($_blog_id,$_post_id);
    }

    function the_slug() { return $this->_the_base('slug'); }
    function the_name() { return $this->_the_base('name'); }
    function the_description() { return $this->_the_base('description'); }

    function load_params() {
        $this->params = (array) $this->bkey('filters_'.$this->filter['slug']);
        return $this->params;
    }

    function the_param( $i, $key ) {
        return $this->params[$i][$key];
    }

    function order( $slug = false, $date_filter = true ) {

        $this->restore_current_blog();

        unset( $_return );

        if ($slug === false) $slug = $_GET['slug'];


        $this->filter_params = (array) $this->bkey('filters_'.$slug);
        $_order = (array) $this->bkey('order_'.$slug);
        if (count($_order) <= 0) return false;

        global $wp_post_types;

        $_query = (object) array(
            'post_count' => 0
        );


        foreach($_order as $key => $value) {

            list($_blog_id,$_post_id) = $this->_get_blog_post_id($value);

            // $options_cpts_obj = get_post_types(array(
            //     'public' => true,
            //     '_builtin' => false
            // ), 'names');

            // foreach ($options_cpts_obj as $cpt) {
            //     $options_cpts[] = $cpt;
            // }
            // $options_cpts[] = 'post';
            // $options_cpts[] = 'produkt';
            
            
            if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE) {
                switch_to_blog($_blog_id);
            }

            add_filter( 'pre_get_posts', array( $this, _get_posts_types ) );

            $_custom_query = new WP_Query(array(
                // 'post_type' => $options_cpts,
                'post__in' => array($_post_id),
                'posts_per_page' => -1,
                'ignore_sticky_posts' => 1
            ));
            remove_filter( 'pre_get_posts', array( $this, _get_posts_types ) );

            if (count($_custom_query->posts) <= 0)
                continue;

            $_post = array_shift($_custom_query->posts);
            unset($_custom_query);

            $_post->blog_id = (int) $_blog_id;
            $ref = 'crossorder_'.$slug;
            $_post->_crossorder_date = get_post_meta( $_post->ID , $ref, true );

            $this->posts[] = $_post;

        }

        $this->restore_current_blog();

        $this->posts = $this->reindex( $_order, $slug, $date_filter );

        $this->post_count = count( $this->posts );

        return $this;
    }

    function have_posts() {
        if (count($this->posts) > 0) return true;

        $this->restore_current_blog();

        wp_reset_query();
        wp_reset_postdata();

        return false;
    }

    function the_post() {
        $this->post = array_shift($this->posts);

        global $post;
        
        if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE) {
            switch_to_blog($this->post->blog_id);
        }

        $this->post->blog_name = get_bloginfo('name');
        $post = $this->post;

        if (is_admin()) {
            $s = get_post_type_object($post->post_type);
            $post->_edit_link = get_admin_url().sprintf($s->_edit_link, $post->ID).'&action=edit';
        }
        setup_postdata( $post );
    }

    function reindex( $_order, $slug, $date_filter ) {
        $_posts = $this->posts;

        foreach ($_order as $value) {

            list($_blog_id,$_post_id) = $this->_get_blog_post_id($value);
            
            foreach ( $_posts as $id => $post ) {

                if ($post->blog_id == $_blog_id && $post->ID == $_post_id) {

                    $post->_crossorder_in_date = false;
                    // $ref = 'crossorder_'.$slug;
                    // $s = get_post_meta( $post->ID , $ref, true );
                    $s = $post->_crossorder_date;
                    
                    if ( !empty($s['from']) && $s['from'] > 0 && $s['to'] > 0 ) {
                        $time = (int) time() + ( get_option( 'gmt_offset' ) * 3600 );

                        if ( $time >= (int) $s['from'] && $time < (int) $s['to'] ) {
                            $post->_crossorder_in_date = true;
                        }
                        elseif ( $date_filter == true ) {
                            continue;
                        }

                        $post->_crossorder_from = (int) $s['from'];
                        $post->_crossorder_to = (int) $s['to'];
                        $post->_crossorder = $s;
                    }

                    $new[] = $post;
                    break;
                }
            }
        }


        return $new;
                
    }

    
    function _get_posts_types ($query) {
        //if ( !is_home() ) return $query;
        
        global $wp_post_types;
        
        if (is_array($this->filter_params)) {
            foreach($this->filter_params as $p) {
                if (isset($p['post_type']))
                    $options_cpts[] = $p['post_type'];
            }
        }
        $options_cpts[] = 'post';
        $options_cpts = array_unique($options_cpts);

        $query->set( 'post_type', $options_cpts );
        
        return $query;
    }

}



class CrossOrder extends CrossOrder_common {
}
