<?php
/**
 * View File - Twitter Feed Tweet Content
 */
?>

<li class="kstatus" data-id="<?php esc_attr_e( $status['id'], 'kbso' ); ?>">

    <?php do_action( 'kbso_before_facebook_statuses_status', $status, $instance, $widget_id ); ?>
    
    <div class="kheader">
        
        <a class="kavatar" style="float: left;" href="<?php echo esc_url( 'https://facebook.com/profile.php?id=' . $status['from']['id'] ); ?>" title="<?php echo esc_attr( $status['from']['name'] ); ?>" target="_blank">
            <img src="<?php echo esc_url( 'http://graph.facebook.com/' . $status['from']['id'] . '/picture?type=square' ); ?>" alt="Profile Image" />
        </a>
        
        <a class="kname" href="<?php echo esc_url( 'https://facebook.com/profile.php?id=' . $status['from']['id'] ); ?>" title="<?php echo esc_attr( $status['from']['name'] ); ?>" target="_blank">
            <?php echo $status['from']['name']; ?>
        </a>
        
        <time class="kdate">
            <?php echo esc_html( kbso_status_display_date( $status ) ); ?>
        </time>
        
    </div>
    
    <div class="ktext">
        
        <?php echo wp_kses_post( $status['message'] ); ?>
        
    </div>
    
    <div class="kfooter">
        
        <a class="klike" href="<?php echo esc_url_raw( 'https://www.facebook.com/sharer/sharer.php?u=' . 'http://peterbooker.com' ); ?>" title="<?php _e( 'Like this status', 'kbso' ); ?>" target="_blank">
            <?php _e( 'Like', 'kbso' ); ?>
        </a>
        
        <a class="kcomments" href="<?php echo esc_url_raw( 'https://www.facebook.com/sharer/sharer.php?u=' . 'http://peterbooker.com' ); ?>" title="<?php _e( 'Comment on this status', 'kbso' ); ?>" target="_blank">
            <?php echo sprintf( _n( '1 Comment', '%s Comments', count( $status['comments']['data'] ), 'kbso' ), count( $status['comments']['data'] ) ); ?>
        </a>
        
        <a class="kshare" href="<?php echo esc_url_raw( 'https://www.facebook.com/sharer/sharer.php?u=' . 'http://peterbooker.com' ); ?>" title="<?php _e( 'Share this status', 'kbso' ); ?>" target="_blank">
            <?php _e( 'Share', 'kbso' ); ?>
        </a>
        
    </div>
    
    <?php do_action( 'kbso_after_facebook_statuses_status', $status, $instance, $widget_id ); ?>

</li>