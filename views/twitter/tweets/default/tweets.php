<?php
/**
 * Template file to show Twitter Feed
 */

/**
 * TODO: Check if I should re-define the $view instance each time? More confusing/less confusing, etc?
 */
?>

<?php echo apply_filters( 'kbso_twitter_tweets_before_widget', $before_widget, $instance, $widget_id ); ?>

<?php do_action( 'kbso_before_twitter_tweets', $tweets, $instance, $widget_id ); ?>

<?php

/**
 * If the Title has been set, output it.
 */
if ( ! empty( $title ) ) {

    /**
     * Already contains: $widget_id, $friends, $instance, $before $before_title, $title, $after_title
     */
    $view
        ->set_view( '_title' )
        ->render();
    
}

?>

<ul id="<?php echo $widget_id; ?>" class="<?php echo implode( ' ', $classes ); ?>">
    
    <?php
    /**
     * Loop through each Tweet and render contents.
     */
    foreach ( $tweets as $tweet ) {
        
        /**
         * Already contains: $widget_id, $friends, $instance
         */
        $view
            ->set_view( '_tweet' )
            ->set( 'tweet', $tweet )
            ->set( 'tweet_id', kbso_tweet_id( $tweet ) )
            ->set( 'display_date', kbso_tweet_display_date( $tweet ) )
            ->set( 'name', kbso_tweet_name( $tweet ) )
            ->set( 'screen_name', kbso_tweet_screen_name( $tweet ) )
            ->set( 'profile_image', kbso_tweet_profile_image( $tweet ) )
            ->set( 'text', kbso_tweet_text( $tweet ) )
            ->set( 'offset', kbso_tweet_offset( $tweet ) )
            ->render();
                
    }
    
    ?>
    
</ul><!-- .ktweets -->

<?php do_action( 'kbso_after_twitter_tweets', $tweets, $instance, $widget_id ); ?>

<?php echo apply_filters( 'kbso_twitter_tweets_after_widget', $after_widget, $instance, $widget_id ); ?>