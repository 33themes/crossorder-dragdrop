<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<?php global $post; ?>

<?php
if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE) {
    restore_current_blog();
}
?>

<div id="crossorder" class="wrap order">

    <?php if ( $this->load( $_GET['slug'] ) ): $this->the_filter(); ?>

        <h1><?php _e('Filter','crossorder');?>: <?php echo $this->the_name(); ?></h1>
        
        <p><?php echo $this->the_description(); ?></p>


        <form id="crossorder_form_order" method="post" action="<?php echo $this->_link_filter_order( $this->the_slug() ); ?>">

        <input type="hidden" name="new_order" value="">

        <?php if ( $this->loop() ):  ?>
        <div id="crossorder_orig">
            <h3 class="title"><?php _e('Content available','crossorder'); ?></h3>
            <ul class="connectedSortable">
            <?php while( $this->have_loop() ): $this->the_loop(); ?>
                <?php 
                // global $post;
                // $post = get_post( $this->the_loop_id() );
                // setup_postdata( $this->post ); 
                ?>
                <li>
                    <span class="blog_id">
                        <a href="#">
                        <?php echo $this->the_loop_blog_id(); ?>
                        </a>
                    </span>

                    <span class="id"><a href="<?php the_permalink(); ?>" target="_blank"><?php echo $this->the_loop_id(); ?></a></span>
                    <span class="post_type"><?php echo $this->the_loop_post_type(); ?></span>
                    <?php if ( is_sticky( $this->the_loop_id() ) ): ?>
                        <span class="sticky">sticky</span>
                    <?php endif; ?>
                    

                    
                    <?php if ( ( $status = get_post_status( get_the_ID() ) ) != 'publish' ): ?>
                        <span class="status"><?php echo get_post_status( get_the_ID() ); ?></span>
                    <?php endif; ?>
                    
                    <?php if ( get_the_date('U') > time() ): ?>
                        <span class="status schedule"><?php the_time( get_option('date_format') ); ?></span>
                    <?php endif; ?>

                    <span class="title"><?php echo $this->the_loop_title(); ?></span>
                </li>
                <?php
                if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE)
                    restore_current_blog();
                ?>
            <?php endwhile; ?>
            </ul>
        </div>

        <?php else: ?>
            <?php _e('Empty results from the filter','crossorder'); ?>
        <?php endif; ?>


        <?php if ( $WP_query = $this->order( $this->the_slug(), false ) ):  ?>
        <div id="crossorder_order">
            <h3 class="title"><?php _e('Selected content','crossorder'); ?></h3>
            <ul class="connectedSortable">
            <?php while( $WP_query->have_posts() ): $WP_query->the_post(); ?>
                <li>
                    <span class="blog_id">
                        <a href="#">
                            <?php echo $WP_query->post->blog_id; ?>
                        </a>
                    </span>

                    <span class="id">
                        <a href="<?php the_permalink(); ?>" target="_blank">
                            <?php echo $WP_query->post->ID; ?>
                        </a>
                    </span>

                    <span class="post_type"><?php echo get_post_type( get_the_ID() ); ?></span>
                    <?php if ( is_sticky( get_the_ID() ) ): ?>
                        <span class="sticky">sticky</span>
                    <?php endif; ?>

                    <?php if ( ( $status = get_post_status( get_the_ID() ) ) != 'publish' ): ?>
                        <span class="status"><?php echo get_post_status( get_the_ID() ); ?></span> 
                    <?php endif; ?>
                    
                    <?php if ( get_the_date('U') > time() ): ?>
                        <span class="status schedule"><?php the_time( get_option('date_format') ); ?></span>

                    <?php endif; ?>

                    
                    <?php if ( $WP_query->post->_crossorder_in_date ): ?>
                        <span class="status indate"><?php _e('In date'); ?></span>
                    <?php endif; ?>

                    <span class="status program <?php echo ( ( $WP_query->post->_crossorder_from > 0 || $WP_query->post->_crossorder_to > 0 ) ? 'active' : '' ); ?>">
                        <span class="show"><?php _e('Set dates'); ?></span>

                        <span class="selectors hidden">
                            <span><?php _e('From','crossorder'); ?>:</span>
                            <input type="text" class="crosstime datatime1" name="date_from_<?php echo $WP_query->post->blog_id; ?>_<?php the_ID(); ?>" value="<?php echo ( $WP_query->post->_crossorder_from > 0 ? date('Y/m/d H:i', $WP_query->post->_crossorder_from) : '' ); ?>">
                            <span><?php _e('To','crossorder'); ?>:</span>
                            <input type="text" class="crosstime datatime2" name="date_to_<?php echo $WP_query->post->blog_id; ?>_<?php the_ID(); ?>" value="<?php echo ( $WP_query->post->_crossorder_to > 0 ? date('Y/m/d H:i', $WP_query->post->_crossorder_to) : '' ); ?>">
                        </span>
                        <span class="close hidden">X</span>
                    </span>

                    <span class="title"><?php the_title(); ?></span>
                </li>
            <?php endwhile; ?>
            </ul>
        </div>
        <?php endif; ?>


        <p><input class="button" type="submit" value="Send and save"></p>

        </form>


    <?php else: ?>

    <?php _e('Filters empty','crossorder'); ?>

    <?php endif; ?>
</div>
