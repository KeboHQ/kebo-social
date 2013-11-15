<?php
/**
 * Facebook Widget
 * Supports...
 */

/**
 * Check a Facebook account exists.
 */
$connections = get_option( 'kebo_se_connections' );

$found = false;

/**
 * Search each connection looking for a twitter account.
 * We don't need to activate the Widget if there is no Twitter account.
 */
foreach ( $connections as $connection ) {

    if ( 'facebook' == strtolower( $connection['service'] ) ) {

        $found[] = $connection;
        
    }
    
}

/**
 * We only need Twitter connections now.
 */
$facebook_accounts = $found;

/**
 * Only register Widget if connection has been made to our Twitter App.
 */
if ( ! empty( $facebook_accounts ) ) {

    add_action( 'widgets_init', 'kbso_facebook_register_widget' );

    function kbso_facebook_register_widget() {

        register_widget( 'Kbso_Facebook_Widget' );
        
    }

}

class Kbso_Facebook_Widget extends WP_Widget {

    /**
     * Default Widget Options
     */
    public $default_options = array(
        'accounts' => null,
        'title' => null,
        'type' => 'statuses',
        'style' => 'list',
        'theme' => 'light',
        'conversations' => false,
        'count' => 5,
        'offset' => 0,
        'avatar' => false,
    );
    
    /**
     * Has the Tweet Intent javascript been printed?
     */
    static $printed_intent_js;
    
    /**
     * Has the Tweet Admin javascript been printed?
     */
    static $printed_admin_js;
    
    /**
     * Has the Tweet Media javascript been printed?
     * TODO: Include in Intent JS?
     */
    static $printed_media_js;
    
    /**
     * Setup the Widget
     */
    function Kbso_Facebook_Widget() {

        $widget_ops = array(
            'classname' => 'kbso_facebook_widget',
            'description' => __('Displays many types of Facebook data.', 'kbso')
        );

        $this->WP_Widget(
            false,
            __('Kebo Social - Facebook', 'kbso'),
            $widget_ops
        );
        
    }

    /**
     * Outputs Content
     */
    function widget( $args, $instance ) {

        $time_start = microtime(true);
        
        $instance = wp_parse_args( $instance, $this->default_options );
        
        $service = 'facebook';
        $type = $instance['type'];
        $accounts = array();
        
        //wp_enqueue_style( 'kebo-twitter-plugin' );
        //wp_enqueue_style( 'kbso-widgets-css' );
        
        // temp
        $instance['accounts'] = array( '598124749' );
        
        if ( is_array( $instance['accounts'] ) ) {
        
            foreach ( $instance['accounts'] as $account_id ) {

                $account = kebo_se_get_connection( $account_id, $service );
                
                $accounts[] = $account;
                
            }
            
            $data = new Kbso_Api;
            $data->set_service( $service );
            $data->set_type( $type );
            $data->set_accounts( $accounts );
            $data->set_options( $instance );

            $data = $data->get_data();
            
            /**
             * Check which type of Widget we need to output
             */
            if ( 'friends' == $instance['type'] ) {
                
                $this->output_friends( $instance, $data, $args );
                
            } else {
                
                $this->output_statuses( $instance, $data, $args );
                
            }
        
        } else {
            
            _e('You must select an account to begin showing Facebook data.', 'kbso');
            return;
            
        }
        
        $time_end = microtime( true );
        $time = $time_end - $time_start;

        echo "Rendered Widget in $time seconds\n";
        
    }
    
    /**
     * Outputs Friends List
     */
    function output_friends( $instance, $friends, $args ) {
        
        extract( $args, EXTR_SKIP );
        
        /**
         * Setup an instance of the View class.
         * Allow customization using a filter.
         */
        $view = new Kbso_View(
            apply_filters(
                'kbso_facebook_widget_view_dir',
                KBSO_PATH . 'views/facebook/friends',
                $widget_id
            )
        );
        
        /**
         * Prepare the HTML classes
         */
        $classes[] = 'kfriends';
        $classes[] = $instance['theme'];
        if ( is_rtl() ) {
            $classes[] = 'rtl';
        }
            
        $view
            ->set_view( 'friends' )
            ->set( 'widget_id', $widget_id )
            ->set( 'friends', $friends )
            ->set( 'classes', $classes )
            ->set( 'instance', $instance )
            ->set( 'before_widget', $before_widget )
            ->set( 'before_title', $before_title )
            ->set( 'title', $instance['title'] )
            ->set( 'after_title', $after_title )
            ->set( 'after_widget', $after_widget )
            ->set( 'view', $view )
            ->render();
        
    }
    
    /**
     * Outputs Statuses
     */
    function output_statuses( $instance, $statuses, $args ) {
        
        extract( $args, EXTR_SKIP );
        
        /**
         * Setup an instance of the View class.
         * Allow customization using a filter.
         */
        $view = new Kbso_View(
            apply_filters(
                'kbso_facebook_widget_view_dir',
                KBSO_PATH . 'views/facebook/statuses',
                $widget_id
            )
        );
        
        /**
         * Prepare the HTML classes
         */
        $classes[] = 'kstatuses';
        $classes[] = $instance['theme'];
        if ( is_rtl() ) {
            $classes[] = 'rtl';
        }
            
        $view
            ->set_view( 'statuses' )
            ->set( 'widget_id', $widget_id )
            ->set( 'statuses', $statuses )
            ->set( 'classes', $classes )
            ->set( 'instance', $instance )
            ->set( 'before_widget', $before_widget )
            ->set( 'before_title', $before_title )
            ->set( 'title', $instance['title'] )
            ->set( 'after_title', $after_title )
            ->set( 'after_widget', $after_widget )
            ->set( 'view', $view )
            ->render();
        
    }

    /*
     * Outputs Options Form
     */
    function form( $instance ) {

        // Add defaults.
        $instance = wp_parse_args( $instance, $this->default_options );

        /*
         * Output Relevant Script in the Footer.
         */
        add_action( 'admin_print_footer_scripts', array( $this, 'print_admin_js' ) );
        
        $connections = get_option( 'kebo_se_connections' );
        $user_id = get_current_user_id();
        $counter = 0;
        
        foreach ( $connections as $connection ) {

            if ( 'facebook' == strtolower( $connection['service'] ) && ( $user_id == $connection['user_id'] || 1 == $connection['shared'] ) ) {

                $facebook_accounts[] = $connection;
                $counter++;
                
            }

        }
        
        ?>

        <?php if ( ! empty( $facebook_accounts ) ) { ?>
        <label for="<?php echo $this->get_field_id('accounts'); ?>">
            <p>
                <?php _e('Accounts', 'kbso'); ?>:
                <select style="width: 100%;" size="<?php echo ( 3 < $counter ) ? '4' : $counter ; ?>" id="<?php echo $this->get_field_id('accounts') ?>" name="<?php echo $this->get_field_name('accounts'); ?>[]" multiple="multiple">
                    <?php
                    foreach ( $facebook_accounts as $account ) {
                        
                        $selected = false;
                        
                        if ( is_array( $instance['accounts'] ) ) {
                            
                            foreach ( $instance['accounts'] as $account_id ) {

                                if ( $account['account_id'] == $account_id ) {
                                    $selected = true;
                                }

                            }
                            
                        }
                        
                        ?>
                        <option value="<?php echo $account['account_id']; ?>"<?php if ( true == $selected ) { echo ' selected="selected"'; } ?>>@<?php echo $account['account_name']; ?></option>
                        <?php
                        
                    }
                    ?>
                </select>
            </p>
        </label>
        <?php } ?>

        <label for="<?php echo $this->get_field_id('title'); ?>">
            <p><?php _e('Title', 'kbso'); ?>: <input style="width: 100%;" type="text" value="<?php echo $instance['title']; ?>" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>"></p>
        </label>

        <label for="<?php echo $this->get_field_id('type'); ?>">
            <p>
                <?php _e('Type', 'kbso'); ?>:
                <select style="width: 100%;" id="<?php echo $this->get_field_id('type') ?>" name="<?php echo $this->get_field_name('type'); ?>">
                    <option value="statuses"<?php if ( 'statuses' == $instance['type'] ) { echo ' selected="selected"'; } ?>><?php _e('Statuses', 'kbso'); ?></option>
                    <option value="friends"<?php if ( 'friends' == $instance['type'] ) { echo ' selected="selected"'; } ?>><?php _e('Friends', 'kbso'); ?></option>
                </select>
                <span class="howto"><?php _e('Please choose a type of Widget to see more options.', 'kbso'); ?></span>
            </p>
        </label>

        <div class="feed-container<?php echo ( isset( $instance['type'] ) ) ? ' ' . $instance['type'] : ''; ?>">

        <label for="<?php echo $this->get_field_id('display'); ?>">
            <p>
                <?php _e('Display', 'kebo_twitter'); ?>:
                <select style="width: 100%;" id="<?php echo $this->get_field_id('display') ?>" name="<?php echo $this->get_field_name('display'); ?>">
                    <option value="tweets" <?php
                    if ('tweets' == $instance['display']) {
                        echo 'selected="selected"';
                    }
                    ?>><?php _e('Tweets', 'kebo_twitter'); ?></option>
                    <option value="retweets" <?php
                    if ('retweets' == $instance['display']) {
                        echo 'selected="selected"';
                    }
                    ?>><?php _e('Re-Tweets', 'kebo_twitter'); ?></option>
                    <option value="all" <?php
                    if ('all' == $instance['display']) {
                        echo 'selected="selected"';
                    }
                    ?>><?php _e('All Tweets', 'kebo_twitter'); ?></option>
                </select>
                <span class="howto">Explanation text</span>
            </p>
        </label>

        <label for="<?php echo $this->get_field_id('style'); ?>">
            <p>
                <?php _e('Style', 'kebo_twitter'); ?>:
                <select style="width: 100%;" id="<?php echo $this->get_field_id('style') ?>" name="<?php echo $this->get_field_name('style'); ?>">
                    <option value="list" <?php if ( 'list' == $instance['style'] ) { echo 'selected="selected"'; } ?>><?php _e('List', 'kbso'); ?></option>
                    <option value="slider" <?php if ( 'slider' == $instance['style'] ) { echo 'selected="selected"'; } ?>><?php _e('Slider', 'kbso'); ?></option>
                </select>
            </p>
        </label>

        <label for="<?php echo $this->get_field_id('theme'); ?>">
            <p>
                <?php _e('Theme', 'kebo_twitter'); ?>:
                <select style="width: 100%;" id="<?php echo $this->get_field_id('theme') ?>" name="<?php echo $this->get_field_name('theme'); ?>">
                    <option value="light" <?php
                    if ('light' == $instance['theme']) {
                        echo 'selected="selected"';
                    }
                    ?>><?php _e('Light', 'kebo_twitter'); ?></option>
                    <option value="dark" <?php
                    if ('dark' == $instance['theme']) {
                        echo 'selected="selected"';
                    }
                    ?>><?php _e('Dark', 'kebo_twitter'); ?></option>
                </select>
            </p>
        </label>

        <label for="<?php echo $this->get_field_id('count'); ?>">
            <p><?php _e('Number Of Tweets', 'kebo_twitter'); ?>: <input style="width: 28px;" type="text" value="<?php echo $instance['count']; ?>" name="<?php echo $this->get_field_name('count'); ?>" id="<?php echo $this->get_field_id('count'); ?>"> <span><?php _e('Range 1-50', 'kebo_twitter') ?></span></p>
        </label>

        <label for="<?php echo $this->get_field_id('avatar'); ?>">
            <p><input style="width: 28px;" type="checkbox" value="true" name="<?php echo $this->get_field_name('avatar'); ?>" id="<?php echo $this->get_field_id('avatar'); ?>" <?php
                if ('avatar' == $instance['avatar']) {
                    echo 'checked="checked"';
                }
                ?>> <?php _e('Show profile image?', 'kebo_twitter'); ?> </p>
        </label>

        <label for="<?php echo $this->get_field_id('conversations'); ?>">
            <p><input style="width: 28px;" type="checkbox" value="true" name="<?php echo $this->get_field_name('conversations'); ?>" id="<?php echo $this->get_field_id('conversations'); ?>" <?php
                if ('true' == $instance['conversations']) {
                    echo 'checked="checked"';
                }
                ?>> <?php _e('Show conversations?', 'kebo_twitter'); ?> </p>
        </label>

        <label for="<?php echo $this->get_field_id('media'); ?>">
            <p><input style="width: 28px;" type="checkbox" value="true" name="<?php echo $this->get_field_name('media'); ?>" id="<?php echo $this->get_field_id('media'); ?>" <?php
                      if ('true' == $instance['media']) {
                          echo 'checked="checked"';
                      }
                      ?>> <?php _e('Show media? (only Lists)', 'kebo_twitter'); ?> </p>
        </label>
            
        </div>

        <?php
    }

    /*
     * Validates and Updates Options
     */

    function update( $new_instance, $old_instance ) {

        $instance = array();

        // Use old figures in case they are not updated.
        $instance = $old_instance;

        $instance['accounts'] = $new_instance['accounts'];
        
        // Update text inputs and remove HTML.
        $instance['title'] = wp_filter_nohtml_kses($new_instance['title']);
        $instance['style'] = wp_filter_nohtml_kses($new_instance['style']);
        $instance['theme'] = wp_filter_nohtml_kses($new_instance['theme']);
        $instance['avatar'] = wp_filter_nohtml_kses($new_instance['avatar']);
        $instance['conversations'] = wp_filter_nohtml_kses($new_instance['conversations']);
        $instance['media'] = wp_filter_nohtml_kses($new_instance['media']);
        $instance['display'] = wp_filter_nohtml_kses($new_instance['display']);
        $instance['type'] = wp_filter_nohtml_kses($new_instance['type']);

        // Check 'count' is numeric.
        if (is_numeric($new_instance['count'])) {

            // If 'count' is above 50 reset to 50.
            if (50 <= intval($new_instance['count'])) {
                $new_instance['count'] = 50;
            }

            // If 'count' is below 1 reset to 1.
            if (1 >= intval($new_instance['count'])) {
                $new_instance['count'] = 1;
            }

            // Update 'count' using intval to remove decimals.
            $instance['count'] = intval($new_instance['count']);
        }

        return $instance;
    }
    
    static function print_intent_js() {
        
        if ( true === self::$printed_intent_js ) {
            return;
        }
        
        self::$printed_intent_js = true;
        
        // Begin Output Buffering
        ob_start();
        ?>

        <script type="text/javascript">
    
            //<![CDATA[
            jQuery( '.ktweet .kfooter a:not(.ktogglemedia)' ).click(function(e) {

                // Prevent Click from Reloading page
                e.preventDefault();

                var href = jQuery(this).attr('href');
                window.open( href, 'twitter', 'width=600, height=400, top=0, left=0');

            });
            //]]>
            
        </script>

        <?php
        // End Output Buffering and Clear Buffer
        $output = ob_get_contents();
        ob_end_clean();
        
        echo $output;
        
    }
    
    static function print_admin_js() {
        
        if ( true === self::$printed_admin_js ) {
            return;
        }
        
        self::$printed_admin_js = true;
        
        // Begin Output Buffering
        ob_start();
        ?>

        <script type="text/javascript">
            //<![CDATA[
            jQuery('[id^="widget-kbso_twitter_widget-"][id$="-type"]').change( function() {
                
                // Get the currently selected value
                var kselected = jQuery(this).val();
                // Add this value to the Widget contain container
                jQuery(this).parent().parent().parent().children('.feed-container').eq(0).removeClass('tweets followers friends').addClass( kselected );
                
            });
            //]]>
        </script>
        
        <style type="text/css">
            
            .widget-content .feed-container {
                display: none;
            }
            .widget-content .feed-container.tweets {
                display: block;
            }
            
        </style>

        <?php
        // End Output Buffering and Clear Buffer
        $output = ob_get_contents();
        ob_end_clean();
        
        echo $output;
        
    }

}

