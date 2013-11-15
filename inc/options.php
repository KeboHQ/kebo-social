<?php
/*
 * Options Page
 */

/**
 * Register the form setting for our kebo_options array.
 */
function kbso_plugin_options_init() {
    
    // Get Options
    $options = kbso_get_plugin_options();
    
    register_setting(
            'kbso_options', // Options group
            'kbso_plugin_options', // Database option
            'kbso_plugin_options_validate' // The sanitization callback,
    );

    /**
     * Section - Share Links
     */
    add_settings_section(
            'kbso_share_links', // Unique identifier for the settings section
            __('Share Link Options', 'kbso'), // Section title
            '__return_false', // Section callback (we don't want anything)
            'kbso_sharing' // Menu slug
    );
    
    /**
     * Field - Text Intro
     */
    add_settings_field(
            'share_links_text_intro', // Unique identifier for the field for this section
            __('Intro Text', 'kbso'), // Setting field label
            'kbso_options_render_text_input', // Function that renders the settings field
            'kbso_sharing', // Menu slug
            'kbso_share_links', // Settings section.
            array( 'name' => 'share_links_text_intro' ) // Args to pass to render function
    );
    
    /**
     * Field - Post Types
     */
    add_settings_field(
            'share_links_link_display', // Unique identifier for the field for this section
            __('Link Display', 'kbso'), // Setting field label
            'kbso_options_render_radio_buttons', // Function that renders the settings field
            'kbso_sharing', // Menu slug
            'kbso_share_links', // Settings section.
            array( 'name' => 'share_links_link_display' ) // Args to pass to render function
    );
    
    /**
     * Field - Post Types
     */
    add_settings_field(
            'share_links_post_types', // Unique identifier for the field for this section
            __('Post Types', 'kbso'), // Setting field label
            'kbso_options_render_post_type_checkboxes', // Function that renders the settings field
            'kbso_sharing', // Menu slug
            'kbso_share_links', // Settings section.
            array( 'name' => 'share_links_post_types' ) // Args to pass to render function
    );

}
add_action( 'admin_init', 'kbso_plugin_options_init' );

/**
 * Change the capability required to save the 'kebo_base_options' options group.
 */
function kbso_plugin_option_capability( $capability ) {
    
    return 'manage_options';
    
}
add_filter('option_page_capability_kebo_options', 'kbso_plugin_option_capability');

/**
 * Returns the options array for kebo.
 */
function kbso_get_plugin_options() {
    
    $saved = (array) get_option( 'kbso_plugin_options' );
    
    $defaults = array(
        // Section - Share Links
        'share_links_text_intro' => null,
        'share_links_link_display' => true,
        'share_links_post_types' => array( 'post' ),
    );

    $defaults = apply_filters( 'kbso_get_plugin_options', $defaults );

    $options = wp_parse_args( $saved, $defaults );
    $options = array_intersect_key( $options, $defaults );

    return $options;
    
}

/**
 * Renders the text input setting field.
 */
function kbso_options_render_text_input( $args ) {
    
    $options = kbso_get_plugin_options();
    
    $name = esc_attr( $args['name'] );
        
    ?>
    <label class="description" for="<?php echo $name; ?>">
    <input type="text" name="kbso_plugin_options[<?php echo $name; ?>]" id="<?php echo $name; ?>" value="<?php echo esc_attr( $options[ $name ] ); ?>" />
    <?php esc_html_e( 'Share Text', 'kbso' ); ?>
    </label>
    <?php
        
}

/**
 * Returns an array of radio options for Yes/No.
 */
function kbso_options_radio_buttons() {
    
    $radio_buttons = array(
        'yes' => array(
            'value' => 'yes',
            'label' => __('On', 'kbso')
        ),
        'no' => array(
            'value' => 'no',
            'label' => __('Off', 'kbso')
        ),
    );

    return apply_filters('kbso_options_radio_buttons', $radio_buttons);
    
}

/**
 * Renders the radio options setting field.
 */
function kbso_options_render_radio_buttons( $args ) {
    
	$options = kbso_get_plugin_options();
        
        $name = esc_attr( $args['name'] );

	foreach ( kbso_options_radio_buttons() as $button ) {
	?>
	<label for="<?php echo $name; ?>">
            <input type="radio" name="kbso_plugin_options[<?php echo $name; ?>]" id="<?php echo $name; ?>" value="<?php echo esc_attr( $button['value'] ); ?>" <?php checked( $options[ $name ], $button['value'] ); ?> />
            <?php echo $button['label']; ?>
	</label>
	<?php
	}
}

/**
 * Renders the Post Type checkboxes.
 */
function kbso_options_render_post_type_checkboxes( $args ) {
    
    $options = kbso_get_plugin_options();
    
    $name = esc_attr( $args['name'] );
    
    $args = array(
        'public' => true,
    );
    $post_types = get_post_types( $args );
    
    foreach ( $post_types as $post_type ) {
        
        ?>
        <label for="<?php echo $name; ?>[<?php echo $post_type; ?>]">
        <?php echo esc_html( ucfirst( $post_type ) ); ?>
        <input type="checkbox" id="<?php echo $name; ?>[<?php echo $post_type; ?>]" name="kbso_plugin_options[<?php echo $name; ?>][]" value="<?php echo $post_type; ?>" <?php checked( $post_type, $options[ $name ] ); ?> />
        </label>
        <?php
        
    }
        
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 */
function kbso_plugin_options_validate( $input ) {
    
    $options = kbso_get_plugin_options();
    
    $output = array();
    
    if ( isset( $input['share_links_text_intro'] ) && ! empty( $input['share_links_text_intro'] ) ) {
	$output['share_links_text_intro'] = sanitize_title( $input['share_links_text_intro'] );
    }
    
    if ( isset( $input['share_links_link_display'] ) && array_key_exists( $input['share_links_link_display'], kbso_options_radio_buttons() ) ) {
        $output['share_links_link_display'] = $input['share_links_link_display'];
    }
    
    if ( isset( $input['share_links_post_types'] ) ) {
        $output['share_links_post_types'] = esc_html( $input['share_links_post_types'] );
    }
        
    //if ( isset( $input['feature_placeholder_message'] ) && ! empty( $input['feature_placeholder_message'] ) )
	//$output['feature_placeholder_message'] = wp_filter_post_kses( $input['feature_placeholder_message'] );
    
    //if ( isset( $input['feature_verification_bing_webmaster'] ) && ! empty( $input['feature_verification_bing_webmaster'] ) )
	//$output['feature_verification_bing_webmaster'] = wp_kses( $input['feature_verification_bing_webmaster'] , array ('') );
     
    //if ( isset( $input['feature_verification_google_analytics'] ) && ! empty( $input['feature_verification_google_analytics'] ) )
	//$output['feature_verification_google_analytics'] = esc_js( $input['feature_verification_google_analytics'] );
    
    // Combine Inputs with currently Saved data, for multiple option page compability
    $options = wp_parse_args( $input, $options );
    
    return apply_filters( 'kbso_plugin_options_validate', $options, $output );
}