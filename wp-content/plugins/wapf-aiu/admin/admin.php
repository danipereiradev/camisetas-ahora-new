<?php

use SW_WAPFAIU\Admin\Updater;

$wapfaiu_updater = new Updater();
$wapfaiu_notices = [];

// Add new section to backend WooCommerce settings
add_action('wapf/admin/settings_sections', function($sections) {
	
	$sections['wapf-aiu'] = __('Image Upload','wapf-aiu');
	
	return $sections;

});

// Fill section with settings
add_action('wapf/admin/settings_wapf-aiu', function($settings) {
	
	$license_info = Updater::get_license_info();
	$has_license = $license_info != null;
			
	$settings[] = [
		'name'  => __( 'License key', 'wapf-aiu' ),
		'type'	=> 'wapf_license_key',
		'license_id' => 'wapfaiu_license',
		'has_license' => $has_license,
	];

	if($has_license) {

		$settings[] = [
			'name' => __( 'Image editor settings', 'wapf-aiu' ),
			'type' => 'title',
		];

		$settings[] = [
			'name'     => __( 'Enable image editor', 'wapf-aiu' ),
			'id'       => 'wapf_aiu_editor',
			'type'     => 'checkbox',
			'default'  => 'yes',
			'desc'     => __( "Enable the image editor", 'wapf-aiu' ),
			'desc_tip' => __( "Only enable this when you're planning to use the image editor. Enabling this loads a few extra scripts on your site's frontend.", 'wapf-aiu' )
		];

        $settings[] = [
            'name'     => __( 'Cropping quality', 'wapf-aiu' ),
            'id'       => 'wapf_aiu_crop',
            'type'     => 'number',
            'default'  => '0.88',
            'desc' => __( "A Number between 0 and 1 indicating the image quality when cropping files that support lossy compression (such as jpeg or webp). Warning: a higher number means a larger (than original) file size.", 'wapf-aiu' ),
            'custom_attributes' => [
                'min' => '0',
                'max' => '1',
                'step'=> '0.01'
            ]
        ];

		$settings[] = [
			'name'    => __( 'Image editor theme', 'wapf-aiu' ),
			'id'      => 'wapf_aiu_theme',
			'type'    => 'select',
			'default' => 'dark',
			'options' => [
				'dark'  => __( 'Dark', 'wapf-aiu' ),
				'light' => __( 'Light', 'wapf-aiu' )
			],
		];

		$settings[] = [
			'name'     => __( 'Show image thumbnails', 'wapf-aiu' ),
			'id'       => 'wapf_aiu_show_thumbs',
			'type'     => 'checkbox',
			'default'  => 'no',
			'desc'     => __( "Show uploaded image thumbnails on cart, checkout, and order screens.", 'wapf-aiu' ),
		];

		$settings[] = [
			'name'     => __( 'Image thumbnail size', 'wapf-aiu' ),
			'id'       => 'wapf_aiu_thumb_size',
			'type'     => 'number',
			'default'  => 100,
			'desc'     => __( "The maximum thumbnail width, in pixels.", 'wapf-aiu' ),
		];

        $settings[] = [
            'name'     => __( 'Enable PDF page count', 'wapf-aiu' ),
            'id'       => 'wapf_aiu_pdf_count',
            'type'     => 'checkbox',
            'default'  => 'no',
            'desc'     => __( "Enable counting the pages of an uploaded PDF in formulas.", 'wapf-aiu' ),
        ];

	}

	$settings[] = [
		'type' => 'sectionend',
	];
				
	return $settings;

});

// View for custom setting
add_filter(  'wapf/admin_setting_template_path', function( $path, $type ) {

    if( $type !== 'img_editor' ) return $path;

    return dirname( __FILE__ ) . '/img-editor.php';

}, 10, 2 );

// Licensing and admin notifications
add_action('init', function() {
	
	global $wapfaiu_updater;
	global $wapfaiu_notices;
	
	$nonce = isset($_POST['_wapfaiu_license_nonce']) ? $_POST['_wapfaiu_license_nonce'] : false;
	if(!$nonce) return;
	
	if(isset($_REQUEST['wapfaiu_license_activate']) && wp_verify_nonce($nonce,'wapfaiu_license_activate')  ) {
		$activated = $wapfaiu_updater->activate_license();

		$notice = $activated === true ? __('License activated.','wapf-aiu') : $activated;
		$wapfaiu_notices[] = [
			'class' => $activated === true ? 'success' : 'error',
			'message' => __($notice, 'wapf-aiu')
		];
	}

	if(isset($_REQUEST['wapfaiu_license_activate']) && wp_verify_nonce($nonce,'wapfaiu_license_deactivate')){
		$deactivated = $wapfaiu_updater->deactivate_license();
		$wapfaiu_notices[] = [
			'class' => 'success',
			'message' => __('License deactivated','wapf-aiu')
		];
	}

});

// Display notices (if any)
add_action('admin_notices', function() {
	
	global $wapfaiu_notices;
	
	foreach( $wapfaiu_notices as $notice ) {
		echo '<div class="notice is-dismissible notice-' . esc_html($notice['class']) . '"><p>' . esc_html($notice['message']) . '</p></div>';
	}

});

// Add options to the file field.
add_filter('wapf/field_options', function($options) {
	
	foreach($options as $field_name => &$the_options) {
		
		if( $field_name !== 'file' ) {
			continue;
		}
		
		array_splice($the_options, 1, 0, [[

			'type'                  => 'number',
			'id'                    => 'min_filecount',
			'show_if'               => 'multiple',
			'default'               => 0,
			'label'                 => __('Minimum file count','wapf-aiu'),
			'description'           => __('How many files should be uploaded at minimum?', 'wapf-aiu')
		]]);
		
		array_splice($the_options, 2, 0, [[

			'type'                  => 'number',
			'id'                    => 'max_filecount',
			'show_if'               => 'multiple',
			'default'               => -1,
			'label'                 => __('Maximum file count','wapf-aiu'),
			'description'           => __('How many files can be uploaded at maximum? Set "-1" for unlimited', 'wapf-aiu')
		]]);
		
		
		array_splice($the_options, 4, 0, [[
			'type'                  => 'number',
			'id'                    => 'minsize',
			'default'               => 0,
			'label'                 => __('Minimum file size (MB)','wapf-aiu'),
			'description'           => __('The minimum needed filesize of 1 file in megabytes.', 'wapf-aiu')
		]]);
		
		$the_options[] = [
			'type'                  => 'number',
			'id'                    => 'minwidth',
			'default'               => 0,
			'label'                 => __('Minimum width','wapf-aiu'),
			'description'           => __('The minimum required image width in pixels.', 'wapf-aiu'),
            'postfix'               => __('px', 'wapf-aiu')
		];

        $the_options[] = [
			'type'                  => 'number',
			'id'                    => 'minheight',
			'default'               => 0,
			'label'                 => __('Minimum height','wapf-aiu'),
			'description'           => __('The minimum required image height in pixels.', 'wapf-aiu'),
            'postfix'               => __('px', 'wapf-aiu')
		];

        $the_options[] = [
            'type'                  => 'number',
            'id'                    => 'maxwidth',
            'default'               => 0,
            'label'                 => __('Maximum width','wapf-aiu'),
            'description'           => __('The maximum allowed image width in pixels.', 'wapf-aiu'),
            'postfix'               => __('px', 'wapf-aiu')
        ];

        $the_options[] = [
            'type'                  => 'number',
            'id'                    => 'maxheight',
            'default'               => 0,
            'label'                 => __('Maximum height','wapf-aiu'),
            'description'           => __('The maximum allowed image height in pixels.', 'wapf-aiu'),
            'postfix'               => __('px', 'wapf-aiu')
        ];

        $the_options[] = [
			'type'                  => 'true-false',
			'id'                    => 'auto_resize',
			'default'               => 0,
			'label'                 => __('Automatically resize','wapf-aiu'),
			'note'           		=> __('Larger images will automatically resize to the maximum. Leave either the maximum width or height setting above empty to maintain aspect ratio. If both are set, the image will be cropped to the exact size.', 'wapf-aiu')
		];

		if( get_option('wapf_aiu_editor','yes') === 'yes' ) {

            $available_ratios = apply_filters('wapf_available_ratios', [
                ''			    => __('None (freeform)', 'wapf-aiu'),
                '1'   		    => __('Square (1:1)','wapf-aiu'),
                '0.5'           => __('1:2','wapf-aiu'),
                '0.33333333'    => __('1:3','wapf-aiu'),
                '2'             => __('2:1','wapf-aiu'),
                '0.66667'       => __('2:3','wapf-aiu'),
                '1.5'   	    => __('3:2','wapf-aiu'),
                '0.75'   	    => __('3:4','wapf-aiu'),
                '0.6'   	    => __('3:5','wapf-aiu'),
                '1.33333333'	=> __('4:3','wapf-aiu'),
                '0.8'	        => __('4:5','wapf-aiu'),
                '1.25'		    => __('5:4','wapf-aiu'),
                '0.5625'        => __('9:16','wapf-aiu'),
                '0.46153846153'	=> __('9:19.5 (6:13)','wapf-aiu'),
                '1.77778'	    => __('16:9','wapf-aiu'),
                '1.6'		    => __('16:10','wapf-aiu'),
            ] );

            $the_options[] = [
                'type'                  => 'img_editor',
                'label'                 => __('Enable image editor','wapf-aiu'),
                'description'           => __('Enables users to edit their uploaded images.', 'wapf-aiu'),
                'ratios'                => $available_ratios,
            ];
			
		}
				
	}			
	
	return $options;
	
});
