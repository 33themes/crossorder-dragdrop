<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="wrap">
    <h1><?php _e('Plugin title','crossorder');?></h1>

    <h2><?php _e('Filters available','crossorder');?></h2>


    <?php if ( $this->load() ): ?>
    <table class="wp-list-table widefat fixed posts">
        <thead>
            <tr class="">
                <th scope="col" class="manage-column column-title sortable desc"><span>Name</span></th>
                <th scope="col" class="manage-column column-title sortable desc"><span>Order</span></th>
            </tr>
        </thead>
        <tbody>
            <?php while( $this->have_filters() ): $this->the_filter(); ?>
            <tr class="alternate">
                <td class="column-title">
                    <?php echo $this->the_name(); ?>
                    <br>
                    <small><?php echo $this->the_description(); ?></small>
                </td>
                <td class="column-order"><a href="<?php echo $this->_link_filter_order( $this->the_slug() );?>">ORDER</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>

    <?php _e('Filters empty','crossorder'); ?>

    <?php endif; ?>
</div>
