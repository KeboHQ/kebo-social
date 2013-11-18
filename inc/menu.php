<?php
/**
 * Kebo Plugin CP Menu Code.
 */

if ( ! defined( 'KBSO_VERSION' ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    die;
}

if ( ! function_exists( 'kebo_se_plugin_menu' ) && ! function_exists( 'kebo_se_dashboard_page' ) && ! function_exists( 'kebo_se_connections_page' ) && ! function_exists( 'kebo_se_sharing_page' ) ):

    function kbso_plugin_menu() {

        add_menu_page(
                __('Dashboard', 'kbso'), // Page Title
                __('Kebo Social', 'kbso'), // Menu Title
                'edit_others_posts', // Capability ** Let Editors See It **
                'kbso-dashboard', // Menu Slug
                'kbso_dashboard_page', // Render Function
                null, // Icon URL
                '99.00018384' // Menu Position (use decimals to ensure no conflicts
        );

        /*
         * Plugin Dashboard Page
         */
        add_submenu_page(
                'kbso-dashboard', // Parent Page Slug
                __('Dashboard', 'kbso'), // Name of Page
                __('Dashboard', 'kbso'), // Label in Menu
                'manage_options', // Capability Required
                'kbso-dashboard', // Menu Slug, used to uniquely identify the page
                'kbso_dashboard_page' // Function that renders the options page
        );

        /*
         * Plugin Social Connections Page
         */
        add_submenu_page(
                'kbso-dashboard', // Parent Page Slug
                __('Connections', 'kbso'), // Name of Page
                __('Connections', 'kbso'), // Label in Menu
                'edit_others_posts', // Capability Required ** Let Editors See It **
                'kbso-connections', // Menu Slug, used to uniquely identify the page
                'kbso_connections_page' // Function that renders the options page
        );
        
        /*
         * Plugin Sharing Page
         */
        add_submenu_page(
                'kbso-dashboard', // Parent Page Slug
                __('Sharing', 'kbso'), // Name of Page
                __('Sharing', 'kbso'), // Label in Menu
                'manage_options', // Capability Required
                'kbso_sharing', // Menu Slug, used to uniquely identify the page
                'kbso_sharing_page' // Function that renders the options page
        );
        
    }
    add_action('admin_menu', 'kbso_plugin_menu');

    function kbso_dashboard_page() {
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        
        /*
         * Global Dashboad Object/Class and Prepare Widgets to be Rendered.
         */
        global $dashboard;
        $dashboard->prepare_widgets();
        ?>

        <div class="wrap">
            <h2><?php _e( 'Kebo Social - Dashboard', 'kbso' ); ?></h2>
            <?php settings_errors(); ?>
            
            <p><?php _e('This is your Kebo SE Dashboard.' , 'kbso'); ?></p>

            <div id="kebo-wrap" class="kebo-dash kebo" data-user_id="<?php echo get_current_user_id(); ?>">

                <div class="row">

                    <div id="main-panel" class="small-12 large-12 columns">
                        
                        
                            
                    </div><!-- .small-12 .large-12 .columns -->
                        
                </div><!-- .row -->
                
                <div class="row">

                    <div id="sort1" class="small-12 large-6 columns">
                        
                        <?php do_action( 'kbso_dashboard_col', 1 ); ?>
                        
                            
                            
                    </div><!-- .small-12 .large-6 .columns -->
                    
                    <div id="sort2" class="small-12 large-6 columns">
                        
                        <?php do_action( 'kbso_dashboard_col', 2 ); ?>
                        
                        
                            
                    </div><!-- .small-12 .large-6 .columns -->
                        
                </div><!-- .row -->

            </div> <!-- .kebo-wrap .kebo -->
            
        </div><!-- .wrap -->
        <?php
        
        }

        function kbso_connections_page() {
            
            if ( ! current_user_can( 'edit_others_posts' ) ) {
                wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
            }
            
            ?>
            
            <div class="wrap">
                <h2><?php _e('Kebo Social - Connections', 'kbso'); ?></h2>
                <?php settings_errors(); ?>
                
                <p>This the the social connections page.</p>

                <div class="kebo-wrap kebo">

                    <div class="row">

                        <div class="small-12 large-6 columns">

                            <h3 class="title"><?php _e('Your Connections', 'kbso'); ?></h3>

                            <p><?php _e('You can view, edit and remove your connections to social services below. Shared connections will be available to other users.', 'kbso'); ?></p>

                            <div class="panel">

                                <div class="social-connections">

                                <?php
                                
                                /*
                                 * Output Relevant Social Connections
                                 */
                                kebo_se_print_connections();
                                
                                ?>

                                </div><!-- .social-connections -->

                            </div><!-- .panel -->

                        </div><!-- .small-12 .large-6 .columns -->

                        <div class="small-12 large-6 columns">

                            <h3 class="title"><?php _e('Connect to Services', 'kbso'); ?></h3>

                            <p><?php _e('Connect your blog to popular social networking sites and automatically share new posts with your friends. You can make a connection for just yourself or for all users on your blog.', 'kbso'); ?></p>

                            <div class="panel">

                                <div class="services-list">
                                    
                                    <a class="social-link twitter" title="Connect to Twitter" href="http://auth.kebopowered.com/kbs_twitter/?origin=<?php echo admin_url('admin.php?page=kbso-connections') ?>&_wpnonce=<?php echo wp_create_nonce( 'kebo-new-connection' ); ?>"><i class="icon-twitter"></i><?php _e('Twitter', 'kbso'); ?></a>

                                    <a class="social-link facebook" title="Connect to Facebook" href="http://auth.kebopowered.com/kbs_facebook/?origin=<?php echo admin_url('admin.php?page=kbso-connections') ?>&_wpnonce=<?php echo wp_create_nonce( 'kebo-new-connection' ); ?>"><i class="icon-facebook"></i><?php _e('Facebook', 'kbso'); ?></a>

                                    <a class="social-link google" title="Connect to Google" href="http://auth.kebopowered.com/google/?origin=<?php echo admin_url('admin.php?page=kbso-connections') ?>&_wpnonce=<?php echo wp_create_nonce( 'kebo-new-connection' ); ?>"><i class="icon-google-plus"></i><?php _e('Google', 'kbso'); ?></a>

                                    <a class="social-link linkedin" title="Connect to LinkedIn" href="http://auth.kebopowered.com/linkedin/?origin=<?php echo admin_url('admin.php?page=kbso-connections') ?>&_wpnonce=<?php echo wp_create_nonce( 'kebo-new-connection' ); ?>"><i class="icon-linkedin"></i><?php _e('LinkedIn', 'kbso'); ?></a>

                                    <a class="social-link pinterest" title="Connect to Pinterest" href="http://auth.kebopowered.com/pinterest/?origin=<?php echo admin_url('admin.php?page=kbso-connections') ?>&_wpnonce=<?php echo wp_create_nonce( 'kebo-new-connection' ); ?>"><i class="icon-pinterest"></i><?php _e('Pinterest', 'kbso'); ?></a>

                                    <a class="social-link tumblr" title="Connect to Tumblr" href="http://auth.kebopowered.com/tumblr/?origin=<?php echo admin_url('admin.php?page=kbso-connections') ?>&_wpnonce=<?php echo wp_create_nonce( 'kebo-new-connection' ); ?>"><i class="icon-tumblr"></i><?php _e('Tumblr', 'kbso'); ?></a>

                                    <a class="social-link instagram" title="Connect to Instagram" href="http://auth.kebopowered.com/instagram/?origin=<?php echo admin_url('admin.php?page=kbso-connections') ?>&_wpnonce=<?php echo wp_create_nonce( 'kebo-new-connection' ); ?>"><i class="icon-instagram"></i><?php _e('Instagram', 'kbso'); ?></a>

                                    <a class="social-link flickr" title="Connect to Flickr" href="http://auth.kebopowered.com/flickr/?origin=<?php echo admin_url('admin.php?page=kbso-connections') ?>&_wpnonce=<?php echo wp_create_nonce( 'kebo-new-connection' ); ?>"><i class="icon-flickr"></i><?php _e('Flickr', 'kbso'); ?></a>
                                    
                                </div><!-- .services-list -->

                            </div><!-- .panel -->

                        </div><!-- .small-12 .large-6 .columns -->

                    </div><!-- .row -->

                </div><!-- .kebo-wrap .kebo -->

            </div><!-- .wrap -->
            <?php
        }
        
        function kbso_sharing_page() {
            
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
            }
            
            ?>
            
            <div class="wrap">
                <h2><?php _e( 'Kebo Social - Sharing', 'kbso' ); ?></h2>
                
                <p><?php _e( 'This the the sharing page.', 'kbso' ); ?></p>

                <div class="kebo-wrap kebo">

                    <div class="row">

                        <div id="sort-container" class="small-12 large-12 columns">

                            <h3 class="title"><?php _e( 'Social Share Links', 'kbso' ); ?></h3>
                            
                            <p>These are the links available which help your blog visitors to share your work across the internet. Move the relevant buttons into the box below to display them on your site.</p>
                            
                            <div class="panel">

                                <ul id="share-links" class="connectedSortable Sortable">
                                    
                                    <li class="sortable"><a class="social-link twitter" href="#"><i class="icon-twitter"></i>Twitter</a></li>
                                    <li class="sortable"><a class="social-link facebook" href="#"><i class="icon-facebook"></i>Facebook</a></li>
                                    <li class="sortable"><a class="social-link google" href="#"><i class="icon-google-plus"></i>Google</a></li>
                                    <li class="sortable"><a class="social-link linkedin" href="#"><i class="icon-linkedin"></i>LinkedIn</a></li>
                                    <li class="sortable"><a class="social-link pinterest" href="#"><i class="icon-pinterest"></i>Pinterest</a></li>
                                    <li class="sortable"><a class="social-link tumblr" href="#"><i class="icon-tumblr"></i>Tumblr</a></li>
                                    
                                </ul>

                            </div><!-- .panel -->
                            
                            <div class="panel">

                                <ul id="share-links-selected" class="connectedSortable Sortable" style="min-height: 40px;">
                                    
                                </ul>

                            </div><!-- .panel -->
                            
                            <script type="text/javascript">
                                
                                jQuery( document ).ready(function() {
                                    
                                    jQuery( "#share-links, #share-links-selected" ).sortable({
                                        
                                        connectWith: ".connectedSortable",
                                        placeholder: "sortable-placeholder",
                                        dropOnEmpty: true,
                                        start: function( event, ui ) {
                                            
                                            ui.placeholder.height( ui.helper.outerHeight() - 2 );
                                            ui.placeholder.width( ui.helper.outerWidth() -2 );
                                            
                                        },
                                        update: function( event, ui ) {
                                            
                                            // do AJAX update
                                            
                                        }
                                        
                                    }).disableSelection();
                                    
                                });
                                
                            </script>

                        </div><!-- .small-12 .large-12 .columns -->

                        <div class="small-12 large-12 columns">
                            
                            <?php settings_errors(); ?>

                            <form method="post" action="options.php">
                                <?php
                                settings_fields( 'kbso_options' );
                                do_settings_sections( 'kbso_sharing' );
                                submit_button();
                                $options = kbso_get_plugin_options();
                                print_r( $options );
                                ?>
                            </form>

                            <p></p>
                            
                            <div class="panel">

                                

                            </div><!-- .panel -->

                        </div><!-- .small-12 .large-12 .columns -->

                    </div><!-- .row -->

                </div><!-- .kebo-wrap .kebo -->

            </div><!-- .wrap -->
            <?php
        }

endif;