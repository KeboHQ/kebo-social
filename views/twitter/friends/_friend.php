<?php
/**
 * View File - Twitter Feed Tweet Content
 */
?>

<li class="kfriend">

    <?php do_action( 'kbso_before_twitter_friends_friend', $friend, $instance, $widget_id ); ?>
    
    <a href="<?php echo esc_url( 'https://twitter.com/' . $friend['screen_name'] ); ?>" title="<?php echo esc_attr( $friend['name'] . ' @' . $friend['screen_name'] ); ?>" target="_blank">
        <img alt="<?php echo esc_attr( $friend['name'] ); ?>" src="<?php echo esc_url( $profile_image ); ?>" />
    </a>
    
    <?php do_action( 'kbso_after_twitter_friends_friend', $friend, $instance, $widget_id ); ?>

</li>