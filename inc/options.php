<?php
/*
 * Options Page
 */

if ( ! defined( 'KBSO_VERSION' ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    die;
}

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
            'kbso_share_links_general', // Unique identifier for the settings section
            __('General Options', 'kbso'), // Section title
            '__return_false', // Section callback (we don't want anything)
            'kbso-sharing' // Menu slug
    );
    
    /**
     * Field - Activate Feature
     */
    add_settings_field(
            'share_links_activate_feature', // Unique identifier for the field for this section
            __('Activate Feature', 'kbso'), // Setting field label
            'kbso_options_render_switch', // Function that renders the settings field
            'kbso-sharing', // Menu slug
            'kbso_share_links_general', // Settings section.
            array( // Args to pass to render function
                'name' => 'share_links_activate_feature',
                'help_text' => __('Turns the feature on or off.', 'kbso')
            ) 
    );
    
    /**
     * Field - Text Intro
     */
    add_settings_field(
            'share_links_intro_text', // Unique identifier for the field for this section
            __('Intro Text', 'kbso'), // Setting field label
            'kbso_options_render_text_input', // Function that renders the settings field
            'kbso-sharing', // Menu slug
            'kbso_share_links_general', // Settings section.
            array( // Args to pass to render function
                'name' => 'share_links_intro_text',
                'help_text' => __('Text to display before the Share Links.', 'kbso')
            )
    );
    
    /**
     * Field - Post Types
     */
    add_settings_field(
            'share_links_post_types', // Unique identifier for the field for this section
            __('Post Types', 'kbso'), // Setting field label
            'kbso_options_render_post_type_checkboxes', // Function that renders the settings field
            'kbso-sharing', // Menu slug
            'kbso_share_links_general', // Settings section.
            array( 'name' => 'share_links_post_types' ) // Args to pass to render function
    );
    
    /**
     * Section - Share Links
     */
    add_settings_section(
            'kbso_share_links_visual', // Unique identifier for the settings section
            __('Visual Options', 'kbso'), // Section title
            '__return_false', // Section callback (we don't want anything)
            'kbso-sharing' // Menu slug
    );
    
    /**
     * Field - Post Types
     */
    add_settings_field(
            'share_links_link_content', // Unique identifier for the field for this section
            __('Link Content', 'kbso'), // Setting field label
            'kbso_options_render_link_content', // Function that renders the settings field
            'kbso-sharing', // Menu slug
            'kbso_share_links_visual', // Settings section.
            array( // Args to pass to render function
                'name' => 'share_links_link_content',
                'help_text' => __('Choose which parts of the Share Link you want visible. One of icon and name are required.', 'kbso')
            )
    );
    
    /**
     * Field - Theme
     */
    add_settings_field(
            'share_links_theme', // Unique identifier for the field for this section
            __('Theme', 'kbso'), // Setting field label
            'kbso_options_render_theme_dropdown', // Function that renders the settings field
            'kbso-sharing', // Menu slug
            'kbso_share_links_visual', // Settings section.
            array( 'name' => 'share_links_theme' ) // Args to pass to render function
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
        // Section - Share Links - General
        'share_links_activate_feature' => 'no',
        'share_links_intro_text' => null,
        // Section - Share Links - Visual
        'share_links_link_content' => array( 'icon', 'name', 'count' ),
        'share_links_theme' => 'default',
        'share_links_post_types' => array( 'post' ),
    );

    $defaults = apply_filters( 'kbso_get_plugin_options', $defaults );

    $options = wp_parse_args( $saved, $defaults );
    $options = array_intersect_key( $options, $defaults );

    return $options;
    
}

function kbso_options_render_switch( $args ) {
    
    $options = kbso_get_plugin_options();
    
    $name = esc_attr( $args['name'] );
    
    $help_text = ( $args['help_text'] ) ? esc_html( $args['help_text'] ) : null;
    
    ?>
    <div class="switch options">
    <?php
    foreach ( kbso_options_radio_buttons() as $button ) {
    $counter++;
    ?>
        <input id="x<?php echo $counter; ?>" type="radio" name="kbso_plugin_options[<?php echo $name; ?>]" value="<?php echo esc_attr( $button['value'] ); ?>" <?php checked( $options[ $name ], $button['value'] ); ?> />
        <label for="x<?php echo $counter; ?>"><?php echo $button['label']; ?></label>
    <?php
    }
    ?>
    <span></span>
    </div>
    <?php if ( $help_text ) { ?>
        <span class="howto"><?php echo esc_html( $help_text ); ?></span>
    <?php } ?>
    <?php
}

/**
 * Renders the text input setting field.
 */
function kbso_options_render_text_input( $args ) {
    
    $options = kbso_get_plugin_options();
    
    $name = esc_attr( $args['name'] );
    
    $help_text = ( $args['help_text'] ) ? esc_html( $args['help_text'] ) : null;
        
    ?>
    <label class="description" for="<?php echo $name; ?>">
    <input type="text" name="kbso_plugin_options[<?php echo $name; ?>]" id="<?php echo $name; ?>" value="<?php echo esc_attr( $options[ $name ] ); ?>" />
    </label>
    <?php if ( $help_text ) { ?>
        <span class="howto"><?php echo esc_html( $help_text ); ?></span>
    <?php } ?>
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

    return apply_filters( 'kbso_options_radio_buttons', $radio_buttons );
    
}

/**
 * Returns an array of radio options for Yes/No.
 */
function kbso_options_link_content_options() {
    
    $check_boxes = array(
        'icon' => array(
            'value' => 'icon',
            'label' => __('Icon', 'kbso')
        ),
        'name' => array(
            'value' => 'name',
            'label' => __('Name', 'kbso')
        ),
        'count' => array(
            'value' => 'count',
            'label' => __('Count', 'kbso')
        ),
    );

    return apply_filters( 'kbso_options_link_content_options', $check_boxes );
    
}

/**
 * Renders the radio options setting field.
 */
function kbso_options_render_link_content( $args ) {
    
    $options = kbso_get_plugin_options();
    
    $name = esc_attr( $args['name'] );
    
    $help_text = ( $args['help_text'] ) ? esc_html( $args['help_text'] ) : null;
    
    foreach ( kbso_options_link_content_options() as $checkbox ) {
        
        ?>
        <label for="<?php echo $name; ?>[<?php echo $checkbox['value']; ?>]">
        <input type="checkbox" id="<?php echo $name; ?>[<?php echo $checkbox['value']; ?>]" name="kbso_plugin_options[<?php echo $name; ?>][]" value="<?php echo $checkbox['value']; ?>" <?php checked( true, in_array( $checkbox['value'], $options[ $name ] ) ); ?> />
        <?php echo esc_html( $checkbox['label'] ); ?>
        </label>
        <?php
        
    }
    if ( $help_text ) { ?>
        <span class="howto"><?php echo esc_html( $help_text ); ?></span>
    <?php }
    
}

/**
 * Returns an array of select inputs for the Theme dropdown.
 */
function kbso_options_theme_select_dropdown() {
    
    $dropdown = array(
        'default' => array(
            'value' => 'default',
            'label' => __('Default', 'kbso')
        ),
        'flat' => array(
            'value' => 'flat',
            'label' => __('Flat', 'kbso')
        ),
        'gradient' => array(
            'value' => 'gradient',
            'label' => __('Gradient', 'kbso')
        ),
    );

    return apply_filters( 'kbso_options_theme_dropdown', $dropdown );
    
}

/**
 * Renders the Theme dropdown.
 */
function kbso_options_render_theme_dropdown( $args ) {
    
    $options = kbso_get_plugin_options();
    
    $name = esc_attr( $args['name'] );
    
    ?>
    <select id="<?php echo $name; ?>[<?php echo $dropdown['value']; ?>]" name="kbso_plugin_options[<?php echo $name; ?>]">
    <?php
    foreach ( kbso_options_theme_select_dropdown() as $dropdown ) {
        
        ?>
        <option value="<?php echo esc_attr( $dropdown['value'] ); ?>" <?php selected( $dropdown['value'], $options[ $name ] ); ?>>
            <?php echo esc_html( $dropdown['label'] ); ?>
        </option>
        <?php
        
    }
    ?>
    </select>    
    <?php
        
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
    $post_types = get_post_types( $args, 'objects' );
    
    foreach ( $post_types as $post_type ) {
        
        ?>
        <label for="<?php echo $name; ?>[<?php echo $post_type->name; ?>]">
        <input type="checkbox" id="<?php echo $name; ?>[<?php echo $post_type->name; ?>]" name="kbso_plugin_options[<?php echo $name; ?>][]" value="<?php echo $post_type->name; ?>" <?php checked( true, in_array( $post_type->name, $options[ $name ] ) ); ?> />
        <?php echo esc_html( $post_type->labels->name ); ?>
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
    
    if ( isset( $input['share_links_activate_feature'] ) && array_key_exists( $input['share_links_activate_feature'], kbso_options_radio_buttons() ) ) {
        $output['share_links_activate_feature'] = $input['share_links_activate_feature'];
    }
    
    if ( isset( $input['share_links_intro_text'] ) && ! empty( $input['share_links_intro_text'] ) ) {
	$output['share_links_intro_text'] = sanitize_title( $input['share_links_intro_text'] );
    }
    
    if ( isset( $input['share_links_link_content'] ) && ! empty( $input['share_links_link_content'] ) ) {
        
        if ( in_array( 'icon', $input['share_links_link_content'] ) || in_array( 'name', $input['share_links_link_content'] ) ) {
            
            $output['share_links_link_content'] = $input['share_links_link_content'];
            
        } else {
            
            $input['share_links_link_content'][] = 'icon';
            
            $output['share_links_link_content'] = $input['share_links_link_content'];
            
            add_settings_error(
                'kbso-options-sharelinks',
                esc_attr( 'settings_updated' ),
                __('Link Content - You must select at least one from icon and name.', 'kbso'),
                'error'
            );
            
        }
        
    }
    
    if ( isset( $input['share_links_post_types'] ) ) {
        $output['share_links_post_types'] = esc_html( $input['share_links_post_types'] );
    }
    
    if ( isset( $input['share_links_theme'] ) && array_key_exists( $input['share_links_theme'], kbso_options_theme_select_dropdown() ) ) {
        $output['share_links_theme'] = $input['share_links_theme'];
    }
    
    // Combine Inputs with currently Saved data, for multiple option page compability
    $options = wp_parse_args( $input, $options );
    
    return apply_filters( 'kbso_plugin_options_validate', $options, $output );
    
}