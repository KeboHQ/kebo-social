<?php
/**
 * Template file to show Twitter Feed
 */
?>

<?php echo $before_widget; ?>

<?php do_action( 'kbso_before_facebook_statuses', $statuses, $instance, $widget_id ); ?>

<?php

/**
 * If the Title has been set, output it.
 */
if ( ! empty( $title ) ) {

    /**
     * Already contains: $widget_id, $statuses, $instance, $before, $before_title, $title, $after_title
     */
    $view
        ->set_view( '_title' )
        ->render();
    
}

?>

<ul class="<?php echo implode( ' ', $classes ); ?>">
    
    <?php
    /**
     * Loop through each Status and render contents.
     */
    foreach ( $statuses as $status ) {
        
        /**
         * Already contains: $widget_id, $statuses, $instance
         */
        $view
            ->set_view( '_status' )
            ->set( 'status', $status )
            ->render();
                
    }
    
    ?>
    
</ul><!-- .kstatuses -->

<?php do_action( 'kbso_after_facebook_statuses', $statuses, $instance, $widget_id ); ?>

<?php echo $after_widget; ?>
