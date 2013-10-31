<?php
/**
 * View File - Facebook Friend Content
 */
?>

<li class="kfriend">

    <?php do_action( 'kbso_before_facebook_friends_friend', $friend, $instance, $widget_id ); ?>
    
    <a href="<?php echo esc_url( 'https://facebook.com/profile.php?id=' . $friend['id'] ); ?>" title="<?php echo esc_attr( $friend['name'] ); ?>" target="_blank">
        <img alt="<?php echo esc_attr( $friend['name'] ); ?>" src="<?php echo esc_url( $profile_image ); ?>" />
    </a>
    
    <?php do_action( 'kbso_after_facebook_friends_friend', $friend, $instance, $widget_id ); ?>

</li>