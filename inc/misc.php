<?php
/**
 * Misc functions.
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

