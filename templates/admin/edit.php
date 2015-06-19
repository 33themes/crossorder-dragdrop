<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div id="crossorder" class="wrap edit">
    <?php if ( $this->load( $_GET['slug'] ) ): $this->the_filter(); ?>


        <h1><?php _e('Edit','crossorder');?>: <?php echo $this->the_name(); ?> </h1>

        <a class="button" href="<?php echo $this->_link_filter_order($_GET['slug']); ?>">ORDER</a>


        <form method="post" action="<?php echo $this->_link_filter_update( $this->the_slug() ); ?>">
            <p>
                <label><?php _e('name');?>:</label>
                <br>
                <textarea class="input" name="name" placeholder="my first name" style="width:100%"><?php echo $this->the_name();?></textarea>
            </p> 
            <p>
                <label><?php _e('Description');?>:</label>
                <br>
                <textarea class="input" name="description" placeholder="filter by tag" style="width:100%"><?php echo $this->the_description();?></textarea>
            </p>

            <?php for( $i = 1; $i <= 5; $i++ ): $this->load_params(); ?>

            <fieldset>
                <legend><?php _e('Filter params','crossorder');?> <?php echo $i; ?></legend>

                <div class="new">

                    <?php if( defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE ): ?>
                    <p><label>Blog ID: <input name="p<?php echo $i; ?>_blog_id" placeholder="filter by blog id (Network)" type="text" value="<?php echo $this->the_param($i, 'blog_id');?>"></label> <small>Use "," to separate</small></p>
                    <?php endif; ?>

                    <p><label>Tag: <input name="p<?php echo $i; ?>_tag" placeholder="filter by tag" type="text" value="<?php echo $this->the_param($i, 'tag');?>"></label> <small>Use "," to separate</small></p>
                    <p><label>CPT: <input name="p<?php echo $i; ?>_post_type" placeholder="filter by post type (CPT)" type="text" value="<?php echo $this->the_param($i, 'post_type');?>"></label> <small>Use "," to separate</small></p>
                    <p><label>Category: <input name="p<?php echo $i; ?>_cat" placeholder="ID of the categories to filter" type="text" value="<?php echo $this->the_param($i, 'cat');?>"></label> <small>Use "," to separate</small></p>
                    <p>
                        <label>Taxonomy:
                            <input name="p<?php echo $i; ?>_taxonomy" placeholder="Name of the taxonomy" type="text" value="<?php echo $this->the_param($i, 'taxonomy');?>">
                            <input name="p<?php echo $i; ?>_taxonomy_term" placeholder="Term to search in the taxonomy" type="text" value="<?php echo $this->the_param($i, 'taxonomy_term');?>">
                        </label>
                        <small>Use "," to separate</small></p>
                    <p>
                        <label>Sticky: <select name="p<?php echo $i; ?>_stiky" placeholder="ID of the categories to filter" type="">
                                    <option value="">No</option>
                                    <option value="1" <?php echo ( $this->the_param($i, 'stiky') > 0 ? 'selected' : '' );?>>Yes</option>
                                </select>
                        </label>
                        <small>If you put "yes", we filter the stiky post</small>
                    </p>
                </div>
            </fieldset>

            <?php endfor; ?>

            <p><input class="button button-primary button-large" type="submit" value="<?php _e('Save','crossorder') ?>"> <a class="button" href="<?php echo $this->_link_filter_order($_GET['slug']); ?>">ORDER</a></p>

        </form>

    <?php else: ?>

    <?php _e('Filter don\'t exists','crossorder'); ?>

    <?php endif; ?>
</div>
