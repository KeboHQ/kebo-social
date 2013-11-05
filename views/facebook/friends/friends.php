<?php
/**
 * Template file to show Twitter Feed
 */

/**
 * TODO: Check if I should re-define the $view instance each time? More confusing/less confusing, etc?
 */
?>

<?php echo $before_widget; ?>

<?php do_action( 'kbso_before_facebook_friends', $friends, $instance, $widget_id ); ?>

<?php

/**
 * If the Title has been set, output it.
 */
if ( ! empty( $title ) ) {

    /**
     * Already contains: $widget_id, $friends, $instance, $before, $before_title, $title, $after_title
     */
    $view
        ->set_view( '_title' )
        ->render();
    
}

?>

<ul class="<?php echo implode( ' ', $classes ); ?>">
    
    <?php
    /**
     * Loop through each Tweet and render contents.
     */
    foreach ( $friends as $friend ) {
        
        $profile_image = $friend['picture']['data']['url'] ;
        
        /**
         * Already contains: $widget_id, $friends, $instance
         */
        $view
            ->set_view( '_friend' )
            ->set( 'friend', $friend )
            ->set( 'profile_image', $profile_image )
            ->render();
                
    }
    
    ?>
    
</ul><!-- .kfriends -->

<?php do_action( 'kbso_after_facebook_friends', $friends, $instance, $widget_id ); ?>

<?php echo $after_widget; ?>
