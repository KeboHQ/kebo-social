<?php
/**
 * View File - Twitter Feed Tweet Content
 */
?>

<li class="ktweet" data-id="<?php echo esc_attr( kbso_tweet_id( $tweet ) ); ?>">

    <?php do_action( 'kbso_before_twitter_tweets_tweet', $tweet, $instance, $widget_id ); ?>
    
    <div class="kheader">

        <a class="kname" href="<?php echo esc_url( 'https://twitter.com/' . $screen_name ); ?>" target="_blank">
            <?php echo esc_html( $name ); ?>
        </a>

        <a class="kdate" href="<?php echo esc_url( 'https://twitter.com/' . $screen_name . '/status/' . $tweet_id ); ?>" target="_blank">
            <time title="<?php echo esc_attr__( 'Time posted: ', 'kbso' ) . date_i18n( 'dS M Y H:i:s', strtotime( $tweet['created_at'] ) + $offset ); ?>" datetime="<?php echo date_i18n( 'c', strtotime( $tweet['created_at'] ) + $offset ); ?>" aria-label="<?php esc_attr_e('Posted on ', 'kbso'); ?><?php echo date_i18n( 'dS M Y H:i:s', strtotime( $tweet['created_at'] ) + $offset ); ?>"><?php echo esc_html( $display_date ); ?></time>
        </a>

        <a class="kscreen" href="<?php echo esc_url( 'https://twitter.com/' . $screen_name ); ?>" target="_blank">
            @<?php echo esc_html( $screen_name ); ?>
        </a>

    </div>

    <div class="ktext">

        <?php if ( true == $instance['avatar'] ) { ?>
        
        <a class="kavatar" href="<?php echo esc_url ( 'https://twitter.com/' . $screen_name ); ?>">

            <img src="<?php echo esc_url( $profile_image ); ?>" />

        </a>
        
        <?php } ?>

        <?php echo wp_kses_post( $text ); ?>

    </div>

    <div class="kfooter">

        <?php if ( ! empty( $tweet['entities']['media'] ) && true == $instance['media'] ) : ?>
            <a class="ktogglemedia kclosed" href="#" data-id="<?php echo esc_attr( $tweet_id ); ?>"><span class="kshow" title="<?php esc_attr_e('View Media', 'kbso'); ?>"><?php _e('View Media', 'kbso'); ?></span><span class="khide" title="<?php esc_attr_e('Hide Media', 'kbso'); ?>"><?php _e('Hide Media', 'kbso'); ?></span></a>
        <?php endif; ?>

        <a class="kreply" title="<?php esc_attr_e('Reply', 'kbso'); ?>" href="<?php echo esc_url( 'https://twitter.com/intent/tweet?in_reply_to=' . $tweet_id ); ?>"></a>
        <a class="kretweet" title="<?php esc_attr_e('Re-Tweet', 'kbso'); ?>" href="<?php echo esc_url( 'https://twitter.com/intent/retweet?tweet_id=' . $tweet_id ); ?>"></a>
        <a class="kfavorite" title="<?php esc_attr_e('Favorite', 'kbso'); ?>" href="<?php echo esc_url( 'https://twitter.com/intent/favorite?tweet_id=' . $tweet_id ); ?>"></a>

    </div>
    
    <?php
    /**
     * Check for Media attached to the Tweet and display.
     */
    if ( ! empty( $tweet['entities']['media'] ) && true == $instance['media'] ) {
    
        $view
            ->set_view( '_media' )
            ->set( 'instance', $instance )
            ->set( 'tweet', $tweet )
            ->render();
        
    }
    ?>
    
    <?php do_action( 'kbso_after_twitter_tweets_tweet', $tweet, $instance, $widget_id ); ?>

</li>