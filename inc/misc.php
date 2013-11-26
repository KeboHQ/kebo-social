<?php
/**
 * Misc functions.
 */

if ( ! defined( 'KBSO_VERSION' ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    die;
}

/**
 * Dashboard Widgets
 */

/**
 * Render Dashboard Widgets
 */
function kbso_dashboard_widget_render( $title, $content, $sortable = false ) {
    
    ?>
    <div class="dashboard-box">

        <div class="dash-header">

            <h3><?php echo esc_html( $title ); ?></h3>

        </div>

        <div class="dash-content">

            <?php echo $content; ?>

        </div>

    </div>
    <?php
    
}

/**
 * RSS Feed - Plugin News
 */
function kbso_dashboard_introduction() {
    
    // Begin Output Buffering
    ob_start();
    
    ?>
    <p><?php _e('Welcome to Kebo Social, we hope you enjoy using the plugin', 'kbso'); ?></p>
    
    <ul>
        <li><a href="#" title=""><?php _e('Getting Started Guide', 'kbso'); ?></a></li>
        <li><a href="#" title=""><?php _e('Tips & Tricks', 'kbso'); ?></a></li>
        <li><a href="#" title=""><?php _e('Documentation', 'kbso'); ?></a></li>
    </ul>
    <?php
    
    // End Output Buffering and Clear Buffer
    $output = ob_get_contents();
    ob_end_clean();
    
    return $output;
    
}

/**
 * RSS Feed - Plugin News
 */
function kbso_dashboard_news_feed() {

    $url = 'http://kebopowered.com/category/kebo-social/feed/';
    
    $rss = fetch_feed( $url );

    if ( ! is_wp_error( $rss ) ) {

        // Figure out how many total items there are, but limit it to 5. 
        $maxitems = $rss->get_item_quantity(5);

        // Build an array of all the items, starting with element 0 (first element).
        $rss_items = $rss->get_items(0, $maxitems);
        
    }
    
    // Begin Output Buffering
    ob_start();
    
    ?>
    <ul class="knews-posts">
        
        <?php if ( $maxitems == 0 ) : ?>
        
            <li><?php _e('Sorry, no Posts found.', 'kbso'); ?></li>
            
        <?php else : ?>
            
            <?php // Loop through each feed item and display each item as a hyperlink. ?>
            <?php foreach ( $rss_items as $item ) : ?>
                
                <li>
                    <a href="<?php echo esc_url($item->get_permalink()); ?>" title="<?php printf(__('Posted %s', 'kbso'), $item->get_date('j F Y | g:i a')); ?>">
                        <?php echo esc_html( $item->get_title() . ' - ' . $item->get_date('jS M') ); ?>
                    </a>
                </li>
                
            <?php endforeach; ?>
                
        <?php endif; ?>
                
    </ul>
    <?php
    
    // End Output Buffering and Clear Buffer
    $output = ob_get_contents();
    ob_end_clean();
    
    return $output;
    
}


/**
 * Facebook Functions
 */

/*
 * Returns Status date formatted for Display
 */
if ( ! function_exists( 'kbso_status_display_date' ) ) {

    function kbso_status_display_date( $status ) {

        $format = get_option( 'date_format' );
        
        $date = $status['updated_time'];
        
        // Prepare Date Formats
        if ( date('Ymd') == date( 'Ymd', strtotime( $date ) ) ) {

            // Covert created at date into timeago format
            $display_date = human_time_diff( date( 'U', strtotime( $date ) ), current_time( 'timestamp', $gmt = 1 ) );
            
        } else {

            // Convert created at date into easily readable format.
            $display_date = date_i18n( $format, strtotime( $date ) );
            
        }
        
        return $display_date;
        
    }

}

/**
 * Twitter Functions
 */

/*
 * Returns Tweet date formatted for Display
 */
if ( ! function_exists( 'kbso_tweet_display_date' ) ) {

    function kbso_tweet_display_date( $tweet ) {

        $format = get_option( 'date_format' );
        
        $date = ( ! empty( $tweet['retweeted_status'] ) ) ? $tweet['retweeted_status']['created_at'] : $tweet['created_at'];
        
        // Prepare Date Formats
        if ( date('Ymd') == date( 'Ymd', strtotime( $date ) ) ) {

            // Covert created at date into timeago format
            $display_date = human_time_diff( date( 'U', strtotime( $date ) ), current_time( 'timestamp', $gmt = 1 ) );
            
        } else {

            // Convert created at date into easily readable format.
            $display_date = date_i18n( $format, strtotime( $date ) );
            
        }
        
        return $display_date;
        
    }

}

/*
 * Returns Tweet ID
 */
if ( ! function_exists( 'kbso_tweet_id' ) ) {

    function kbso_tweet_id( $tweet ) {
        
        $tweet_id = ( ! empty( $tweet['retweeted_status'] ) ) ? $tweet['retweeted_status']['id_str'] : $tweet['id_str'];
        
        return $tweet_id;
        
    }

}

/*
 * Returns Tweet Name
 */
if ( ! function_exists( 'kbso_tweet_name' ) ) {

    function kbso_tweet_name( $tweet ) {

        $name = ( ! empty( $tweet['retweeted_status'] ) ) ? $tweet['retweeted_status']['user']['name'] : $tweet['user']['name'];
        
        return $name;
        
    }

}

/*
 * Returns Tweet Screen Name
 */
if ( ! function_exists( 'kbso_tweet_screen_name' ) ) {

    function kbso_tweet_screen_name( $tweet ) {

        $screen_name = ( ! empty( $tweet['retweeted_status'] ) ) ? $tweet['retweeted_status']['user']['screen_name'] : $tweet['user']['screen_name'];
        
        return $screen_name;
        
    }

}

/*
 * Returns Tweet Profile Image
 */
if ( ! function_exists( 'kbso_tweet_profile_image' ) ) {

    function kbso_tweet_profile_image( $tweet ) {

        if ( ! empty( $tweet['retweeted_status'] ) ) {
            
            $profile_image = ( is_ssl() ) ? $tweet['retweeted_status']['user']['profile_image_url_https'] : $tweet['retweeted_status']['user']['profile_image_url'];
            
        } else {
            
            $profile_image = ( is_ssl() ) ? $tweet['user']['profile_image_url_https'] : $tweet['user']['profile_image_url'];
            
        }
        
        return $profile_image;
        
    }

}

/*
 * Returns Tweet Text
 */
if ( ! function_exists( 'kbso_tweet_text' ) ) {

    function kbso_tweet_text( $tweet ) {

        $text = ( ! empty( $tweet['retweeted_status'] ) ) ? $tweet['retweeted_status']['text'] : $tweet['text'];
        
        return $text;
        
    }

}

/*
 * Returns Tweet UTC Offset
 */
if ( ! function_exists( 'kbso_tweet_offset' ) ) {

    function kbso_tweet_offset( $tweet ) {

        $offset = ( ! empty( $tweet['retweeted_status'] ) ) ? $tweet['retweeted_status']['user']['utc_offset'] : $tweet['user']['utc_offset'];
        
        return $offset;
        
    }

}

