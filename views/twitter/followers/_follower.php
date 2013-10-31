<?php
/**
 * View File - Twitter Feed Tweet Content
 */
?>

<li class="kfollower">

    <?php do_action( 'kbso_before_twitter_followers_follower', $follower, $instance, $widget_id ); ?>
    
    <a href="<?php echo esc_url( 'https://twitter.com/' . $follower['screen_name'] ); ?>" title="<?php echo esc_attr( $follower['name'] . ' @' . $follower['screen_name'] ); ?>" target="_blank">
        <img alt="<?php echo esc_attr( $follower['name'] ); ?>" src="<?php echo esc_url( $profile_image ); ?>" />
    </a>
    
    <?php do_action( 'kbso_after_twitter_followers_follower', $follower, $instance, $widget_id ); ?>

</li>