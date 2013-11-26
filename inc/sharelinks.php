<?php

/**
 * Social Share Links
 */

if ( ! defined( 'KBSO_VERSION' ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    die;
}

/**
 * If the Share Links feature has been activated it, hook the feature in.
 */
$options = kbso_get_plugin_options();

if ( 'yes' == $options['share_links_activate_feature'] ) {
    
    add_filter( 'the_content', 'kbso_add_share_links', 95 );
    
}

/**
 * Adds Social Share links below Blog Post Content.
 */
function kbso_add_share_links( $content ) {
    
    global $post;

    $options = kbso_get_plugin_options();

    if ( in_array( $post->post_type, $options['share_links_post_types'] ) && is_single() ) {

        $theme = $options['share_links_theme'];
        
        /**
         * Setup an instance of the View class.
         * Allow customization using a filter.
         */
        $view = new Kbso_View(
            apply_filters(
                'kbso_sharelinks_view_dir',
                KBSO_PATH . 'views/sharelinks/' . $theme
            )
        );
        
        /**
         * Prepare the HTML classes
         */
        $classes[] = 'ksharelinks';
        $classes[] = $options['share_links_theme'];
        if ( is_rtl() ) {
            $classes[] = 'rtl';
        }
        
        add_post_meta( $post->ID, '_kbso_share_counts', array( 'expiry' => time() ), true );
        
        $title = urlencode( get_the_title() );
        $permalink = urlencode( get_permalink() );
        $summary = urlencode( wp_trim_words( strip_tags( get_the_content( $post->ID ) ), 50 ) );
        $site_name = urlencode( get_bloginfo( 'name' ) );
        $post_thumnail_url = urlencode( wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ) );
        $counts = get_post_meta( $post->ID, '_kbso_share_counts', true );
        
        $all_links = array(
            'facebook' => array(
                'name' => 'facebook',
                'label' => 'Facebook',
                'href' => 'http://www.facebook.com/sharer.php?u=' . $permalink . '&t=' . $title . ''
            ),
            'twitter' => array(
                'name' => 'twitter',
                'label' => 'Twitter',
                'href' => 'http://twitter.com/share?text=' . $title . '&url=' . $permalink . ''
            ),
            'googleplus' => array(
                'name' => 'googleplus',
                'label' => 'Google+',
                'href' => 'https://plus.google.com/share?url=' . $permalink . ''
            ),
            'linkedin' => array(
                'name' => 'linkedin',
                'label' => 'LinkedIn',
                'href' => 'http://www.linkedin.com/shareArticle?mini=true&url=' . $permalink . '&title=' . $title . '&summary=' . $summary . '&source=' . $site_name . ''
            ),
            'pinterest' => array(
                'label' => 'Pinterest',
                'name' => 'pinterest',
                'href' => 'http://pinterest.com/pin/create/button/?url=' . $permalink . '&media=' . $post_thumnail_url . '&description=' . $title . '&is_video=false'
            ),
            'tumblr' => array(
                'name' => 'tumblr',
                'label' => 'Tumblr',
                'href' => 'https://www.tumblr.com/share/link?url=' . $permalink . '&name=' . $title . '&description=' . $summary . ''
            ),
            'reddit' => array(
                'name' => 'reddit',
                'label' => 'Reddit',
                'href' => 'http://www.reddit.com/submit?title=' . $title . '&url=' . $permalink . ''
            ),
            'stumbleupon' => array(
                'name' => 'stumbleupon',
                'label' => 'StumbleUpon',
                'href' => 'http://www.stumbleupon.com/submit?url=' . $permalink . '&title=' . $title . ''
            ),
            'digg' => array(
                'name' => 'digg',
                'label' => 'Digg',
                'href' => 'http://digg.com/submit?url=' . $permalink . '&title=' . $title . ''
            ),
            'delicious' => array(
                'name' => 'delicious',
                'label' => 'Delicious',
                'href' => 'https://delicious.com/save?v=5&noui&jump=close&url=' . $permalink . '&title=' . $title . ''
            ),
        );
        
        apply_filters( 'kbso_sharelinks_links', $all_links );
        
        $selected_links = array();
        
        foreach ( kbso_share_links_order('selected') as $link ) {
            
            if ( isset( $all_links[ $link ] ) ) {
                
                $selected_links[ $link ] = $all_links[ $link ];
                
            }
            
        }
        
        $links = $view
            ->set_view( 'links' )
            ->set( 'classes', $classes )
            ->set( 'post_type', $post->post_type )
            ->set( 'options', $options )
            ->set( 'permalink', $permalink )
            ->set( 'title', $title )
            ->set( 'summary', $summary )
            ->set( 'site_name', $site_name )
            ->set( 'post_thumnail_url', $post_thumnail_url )
            ->set( 'links', $selected_links )
            ->set( 'counts', $counts )
            ->set( 'view', $view )
            ->retrieve();
        
        /**
         * Add the Links HTML to the end of the Content.
         */
        $content = $content . $links;
        
        wp_enqueue_style( 'kbso-sharelinks-css' );
        
    }
    
    wp_enqueue_script( 'jquery' );
    add_action( 'wp_footer', 'kbso_share_links_js_print' );
    
    add_action( 'shutdown', 'kbso_share_links_update_counts' );
    
    return $content;
    
}

/**
 * Outputs Share Links Javascript
 */
function kbso_share_links_js_print() {
    
    ?>
    <script type="text/javascript">
        
        //<![CDATA[
        jQuery(document).ready(function() {
            
            jQuery( '.ksharelinks ul li a' ).click(function(e) {

                // Prevent Click from Reloading page
                e.preventDefault();

                var khref = jQuery(this).attr('href');
                window.open( khref, 'window', 'width=600, height=400, top=0, left=0');

            });
            
        });
        //]]>
        
    </script>
    <?php
    
}

/**
 * Prepare Share Link Order
 */
function kbso_share_links_order( $type = 'selected' ) {
    
    $all_links = array(
        'twitter', 'facebook', 'linkedin', 'googleplus', 'pinterest', 'tumblr', 'reddit', 'stumbleupon', 'digg', 'delicious'
    );
    
    $selected = get_option( 'kbso_sharelink_order' );
    
    if ( 'selected' == $type ) {
        
        return $selected;
        
    } else {
        
        foreach ( $all_links as $link ) {
            
            if ( ! in_array( $link, $selected ) ) {
                
                $remaining[] = $link; 
                
            }
            
        }
        
        return $remaining;
        
    }
    
    return $links;
    
}

/**
 * Updates the Share Link Counts after the page has been rendered.
 */
function kbso_share_links_update_counts() {
    
    global $post;
    
    $options = kbso_get_plugin_options();
    
    $permalink = get_permalink();
    
    $counts = get_post_meta( $post->ID, '_kbso_share_counts', true );
    
    if ( $counts['expiry'] < time() ) {
    
        $twitter = kbso_update_twitter_count( $permalink );

        if ( isset( $twitter->count ) ) {

            $counts['twitter'] = $twitter->count;

        }

        $facebook = kbso_update_facebook_count( $permalink );

        if ( isset( $facebook->shares ) ) {

            $counts['facebook'] = $facebook->shares;

        }
        
        $googleplus = kbso_update_googleplus_count( $permalink );

        if ( $googleplus ) {

            $counts['googleplus'] = $googleplus;

        }
        
        $linkedin = kbso_update_linkedin_count( $permalink );

        if ( $linkedin->count ) {

            $counts['linkedin'] = $linkedin->count;

        }
        
        $pinterest = kbso_update_pinterest_count( $permalink );

        if ( $pinterest ) {

            $counts['pinterest'] = $pinterest;

        }
        
        $stumbleupon = kbso_update_stumbleupon_count( $permalink );
        
        if ( $stumbleupon ) {

            $counts['stumbleupon'] = $stumbleupon;

        }
        
        $delicious = kbso_update_delicious_count( $permalink );
        
        if ( $delicious ) {

            $counts['delicious'] = $delicious;

        }

        $counts['expiry'] = time() + ( 5 * MINUTE_IN_SECONDS );
        $counts['expiry'] = time();

        if ( ! update_post_meta( $post->ID, '_kbso_share_counts', $counts ) ) {

            add_post_meta( $post->ID, '_kbso_share_counts', $counts, true );

        }
    
    }
    
}

function kbso_update_twitter_count( $permalink ) {
    
    $permalink = 'http://www.codedevelopr.com';
    
    $url = 'http://urls.api.twitter.com/1/urls/count.json?url=';
    
    $response = file_get_contents( esc_url_raw( $url . $permalink ) );
    
    if ( $response ) {
        
        return json_decode( $response );
        
    } else {
        
        return null;
        
    }
    
}

function kbso_update_facebook_count( $permalink ) {
    
    $permalink = 'http://www.codedevelopr.com';
    
    $url = 'http://graph.facebook.com/?id=';
    
    $response = file_get_contents( esc_url_raw( $url . $permalink ) );
    
    if ( $response ) {
        
        return json_decode( $response );
        
    } else {
        
        return null;
        
    }
    
}

function kbso_update_linkedin_count( $permalink ) {
    
    $permalink = 'http://www.codedevelopr.com';
    
    $url = 'http://www.linkedin.com/countserv/count/share?format=json&url=';
    
    $response = file_get_contents( esc_url_raw( $url . $permalink ) );
    
    if ( $response ) {
        return json_decode( $response );
    } else {
        return null;
    }
    
}

function kbso_update_pinterest_count( $permalink ) {
    
    $permalink = 'http://www.facebook.com';
    
    $url = 'http://api.pinterest.com/v1/urls/count.json?url=';
    
    $response = file_get_contents( esc_url_raw( $url . $permalink ) );
        
    if ( $response ) {
        
        $response = preg_replace( '/.+?({.+}).+/','$1', $response );

        $json = json_decode( $response );

        return $json->count !== '-' ? $json->count : null;
    
    } else {
        
        return null;
    
    }
    
}

function kbso_update_googleplus_count( $permalink ) {
        
    $permalink = 'https://www.khanacademy.org/';
    
    $url = 'https://clients6.google.com/rpc';

    $args = array(
        'method'    => 'POST',
        'sslverify' => false,
        'timeout'   => 5,
        'headers'   => array( 'Content-Type' => 'application/json' ),
        'body'      => '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $permalink . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]'
    );
    
    $response = wp_remote_request( esc_url_raw( $url ), $args );

    if ( is_wp_error( $response ) || '400' <= $response['response']['code'] ) {
        
        return null;
        
    } else {
        
        $data = json_decode( $response['body'], true );

        if ( isset( $data[0]['result']['metadata']['globalCounts']['count'] ) ) {
            
            return $data[0]['result']['metadata']['globalCounts']['count'];
        
        } else {
            
            return null;
            
        }
        
    }
    
}

function kbso_update_stumbleupon_count( $permalink ) {
    
    $permalink = 'http://www.facebook.com';
    
    $url = 'http://www.stumbleupon.com/services/1.01/badge.getinfo?url=';
    
    $response = file_get_contents( $url . $permalink );
  
    if ( $response ) {
        
        $data = json_decode( $response, true );
        
        if ( isset( $data['result']['views'] ) ) {
        
            $count = $data['result']['views'];
            return $count;
        
        } else {
            
            return null;
            
        }
    
    } else {
        
    return null;
    
    }
    
}

function kbso_update_delicious_count( $permalink ) {
    
    $permalink = 'http://www.facebook.com';
    
    $url = 'http://feeds.delicious.com/v2/json/urlinfo/data?url=';
    
    $response = file_get_contents( $url . $permalink );

    if ( $response ) {
        
        $data = json_decode( $response, true );
        
        if ( isset( $data[0]['total_posts'] ) ) {

            $count = $data[0]['total_posts'];
            return $count;
        
        } else {
            
            return null;
            
        }
    
    } else {
        
    return null;
    
    }
    
}