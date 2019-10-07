<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//==============================
//   Customize Toolbat Colors
//==============================
function wewp_protected_customize_register($wp_customize){
	//===================
	//   Add Setiings
	//===================
	//Image
	$wp_customize-> add_setting('wewp_page_image',
		array(
			'type' => 'option',
			'transport' => 'refresh',

	));
	$wp_customize-> add_setting('wewp_page_image_width',
		array(
			'default'   => '200',
			'type' => 'option',
			'transport' => 'refresh',

	));
	$wp_customize-> add_setting('wewp_page_image_align',
		array(
			'default'   => 'center',
			'type' => 'option',
			'transport' => 'refresh',

	));
	//Title
	$wp_customize-> add_setting('wewp_page_title',
		array(
			'default'   => 'Content protected',
			'type' => 'option',
			'transport' => 'refresh',

	));
	$wp_customize-> add_setting('wewp_page_title_size',
		array(
			'default'   => '24',
			'type' => 'option',
			'transport' => 'refresh',

	));
	$wp_customize-> add_setting('wewp_page_title_align',
		array(
			'default'   => 'center',
			'type' => 'option',
			'transport' => 'refresh',

	));
	$wp_customize-> add_setting('wewp_form_page_title_color',
		array(
			'default'   => '#262626',
			'type' => 'option',
			'transport' => 'refresh',

	));
	//Form Settings
	$wp_customize-> add_setting('wewp_form_lable_on',
		array(
			'default'   => 'show',
			'type' => 'option',
			'transport' => 'refresh',

	));

	//Name Field

	$wp_customize-> add_setting('wewp_form_name_field_on',
		array(
			'default'   => 'show',
			'type' => 'option',
			'transport' => 'refresh',

	));
	$wp_customize-> add_setting('wewp_form_name_field_lable',
		array(
			'default'   => 'Name',
			'type' => 'option',
			'transport' => 'refresh',

	));
	$wp_customize-> add_setting('wewp_form_name_field_placeholder',
		array(
			'default'   => 'Name',
			'type' => 'option',
			'transport' => 'refresh',

	));
	//Email Filed
	$wp_customize-> add_setting('wewp_form_email_field_on',
		array(
			'default'   => 'show',
			'type' => 'option',
			'transport' => 'refresh',

	));
	$wp_customize-> add_setting('wewp_form_email_field_lable',
		array(
			'default'   => 'Email',
			'type' => 'option',
			'transport' => 'refresh',

	));
	$wp_customize-> add_setting('wewp_form_email_field_Placeholder',
		array(
			'default'   => 'Email',
			'type' => 'option',
			'transport' => 'refresh',

	));
	//Password Field
	$wp_customize-> add_setting('wewp_form_password_field_lable',
		array(
			'default'   => 'Password',
			'type' => 'option',
			'transport' => 'refresh',

	));
	$wp_customize-> add_setting('wewp_form_password_field_placeholder',
		array(
			'default'   => 'Password',
			'type' => 'option',
			'transport' => 'refresh',

	));
	//Submit Button
	$wp_customize-> add_setting('wewp_page_submit_text',
		array(
			'default'   => 'Send',
			'type' => 'option',
			'transport' => 'refresh',

	));
	$wp_customize-> add_setting('wewp_page_submit_button_font_size',
		array(
			'default'   => '24',
			'type' => 'option',
			'transport' => 'refresh',

	));
	$wp_customize-> add_setting('wewp_page_submit_button_align',
		array(
			'default'   => 'left',
			'type' => 'option',
			'transport' => 'refresh',

	));
	$wp_customize-> add_setting('wewp_form_page_Submit_Button_text_color',
		array(
			'default'   => '#ffffff',
			'type' => 'option',
			'transport' => 'refresh',

	));
	$wp_customize-> add_setting('wewp_form_page_Submit_Button_text_color_hover',
		array(
			'default'   => '#262626',
			'type' => 'option',
			'transport' => 'refresh',

	));

	$wp_customize-> add_setting('wewp_submit_button_background_color',
		array(
			'default'   => '#020202',
			'type' => 'option',
			'transport' => 'refresh',

	));

		$wp_customize-> add_setting('wewp_submit_button_background_color_hover',
		array(
			'default'   => '#ffffff',
			'type' => 'option',
			'transport' => 'refresh',

	));


	$wp_customize-> add_setting('wpspf_title_color',
		array(
			'default'   => '#262626',
			'type' => 'option',
			'transport' => 'refresh',

	));

	$wp_customize-> add_setting('wpspf_border_color',
		array(
			'default'   => '#ffffff',
			'type' => 'option',
			'transport' => 'refresh',

	));

	$wp_customize-> add_setting('wpspf_row_item',
		array(
			'default' => '33.3333333%',
			'type' => 'option',
			'transport' => 'refresh',

	));
	//========================
	//      Create Panel
	//========================
	$wp_customize-> add_panel('wewp_protected_panel',
		array(
			'title' 		=> __('Potected Page Settings', 'protected-page'),
			'description' 	=> __('Edit the layout of the Form', 'protected-page'),
			'priority' 		=> 30,

		));
	//======================
	//     Create Section
	//======================

	$wp_customize-> add_section('wewp_form_page_image_panel',
		array(
			'title' 	=> __('Page Image', 'protected-page'),
			'priority' 	=> 30,
			'panel' 	=> 'wewp_protected_panel',

	));
	$wp_customize-> add_section('wewp_form_page_title_panel',
		array(
			'title' 	=> __('Page Title', 'protected-page'),
			'priority' 	=> 31,
			'panel' 	=> 'wewp_protected_panel',

	));
	$wp_customize-> add_section('wewp_form_settings_panel',
		array(
			'title' 	=> __('Form Settings', 'protected-page'),
			'priority' 	=> 32,
			'panel' 	=> 'wewp_protected_panel',

	));
	$wp_customize-> add_section('wewp_form_name_field_panel',
		array(
			'title' 	=> __('Form - Name Filed', 'protected-page'),
			'priority' 	=> 33,
			'panel' 	=> 'wewp_protected_panel',

	));

	$wp_customize-> add_section('wewp_form_email_field_panel',
		array(
			'title' 	=> __('Form - Email Filed', 'protected-page'),
			'priority' 	=> 34,
			'panel' 	=> 'wewp_protected_panel',

	));

	$wp_customize-> add_section('wewp_form_password_field_panel',
		array(
			'title' 	=> __('Form - Password Filed', 'protected-page'),
			'priority' 	=> 35,
			'panel' 	=> 'wewp_protected_panel',

	));
	$wp_customize-> add_section('wewp_form_submit_button_panel',
		array(
			'title' 	=> __('Form Submit Button', 'protected-page'),
			'priority' 	=> 36,
			'panel' 	=> 'wewp_protected_panel',

	));
	//====================
	//     Add Control
	//====================

	/*=============
	* Image Upload
	===============*/
	$wp_customize-> add_control(new WP_Customize_Image_Control($wp_customize, 'wewp_page_image_control',
		array(
			'label'		=> __('Upload Image', 'protected-page'),
			'section'	=> 'wewp_form_page_image_panel',
			'settings'	=> 'wewp_page_image'


	) ) );
		$wp_customize->add_control( 'wewp_page_image_width_control', array(
 			 'type' => 'number',
 			 'section' => 'wewp_form_page_image_panel',
 			 'label' => __( 'Image Size', 'protected-page' ),
 			 'description' => __( 'Insert only the number, the value is in PX. (default is 200px)', 'protected-page' ),
 			 'input_attrs' => array(
   			 	'min' => 10,
 				   'step' => 1,
 		 		),
		     'section'	=> 'wewp_form_page_image_panel',
 			 'settings' => 'wewp_page_image_width'
) );
		$wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        'wewp_page_image_align_control',
        array(
            'label'          => __( 'Image Position', 'protected-page' ),
            'section'        => 'wewp_form_page_image_panel',
            'settings'       => 'wewp_page_image_align',
            'type'           => 'select',
            'choices'        => array(
                'left'   => __( 'Left', 'protected-page' ),
                'right'  => __( 'Right', 'protected-page' ),
                'center' => __('Center', 'protected-page')
            )
        )
    )
);

	/*===================
	*Form Title Text
	=====================*/
	$wp_customize-> add_control(new WP_Customize_Control($wp_customize, 'wewp_page_title_control',
		array(
			'label'		=> __('Page Title', 'protected-page'),
			'section'	=> 'wewp_form_page_title_panel',
			'settings'	=> 'wewp_page_title'


	) ) );
			$wp_customize->add_control( 'wewp_page_title_font_size_control', array(
 			 'type' => 'number',
 			 'section' => 'wewp_form_page_title_panel',
 			 'label' => __( 'Title Font Size', 'protected-page' ),
 			 'description' => __( 'Insert only the number, the value is in PX.', 'protected-page' ),
 			 'input_attrs' => array(
   			 	'min' => 0,
  				  'max' => 10000,
 				   'step' => 1,
 		 		),
		     'section'	=> 'wewp_form_page_title_panel',
 			 'settings' => 'wewp_page_title_size'
) );
		$wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        'wewp_page_title_align_control',
        array(
            'label'          => __( 'Title Position', 'protected-page' ),
            'section'        => 'wewp_form_page_title_panel',
            'settings'       => 'wewp_page_title_align',
            'type'           => 'select',
            'choices'        => array(
                'left'   => __( 'Left', 'protected-page' ),
                'right'  => __( 'Right', 'protected-page' ),
                'center' => __('Center', 'protected-page')
            )
        )
    )
);
		$wp_customize-> add_control(new WP_Customize_Color_Control($wp_customize, 'wewp_title_color_control',
		array(
			'label'		=> __('Title Text Color', 'protected-page'),
			'section'	=> 'wewp_form_page_title_panel',
			'settings'	=> 'wewp_form_page_title_color'


	) ) );
	/*============
	*Form settings
	==============*/
	$wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        'wewp_form_lable_on_control',
        array(
            'label'          => __( 'Show OR Hide Form Lables', 'protected-page' ),
            'section'        => 'wewp_form_settings_panel',
            'settings'       => 'wewp_form_lable_on',
            'type' => 'radio',
  				'choices' => array(
   					'show' => __('Show Lables', 'protected-page'),
    				'hide' => __('Hide Lables', 'protected-page'),
       			 )

	) ) );

		/*==================
		* Form Fields
		====================*/
		// Name Field
	$wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        'wewp_form_name_filed_on_control',
        array(
            'label'          => __( 'Show OR Hide Name Field', 'protected-page' ),
            'section'        => 'wewp_form_name_field_panel',
            'settings'       => 'wewp_form_name_field_on',
            'type' => 'radio',
  				'choices' => array(
   					'show' => __('Show Name Field', 'protected-page'),
    				'hide' => __('Hide Name Field', 'protected-page'),
       			 )

	) ) );
	$wp_customize-> add_control(new WP_Customize_Control($wp_customize, 'wewp_form_name_filed_control',
		array(
			'label'		=> __('Name Field Lable', 'protected-page'),
			'section'	=> 'wewp_form_name_field_panel',
			'settings'	=> 'wewp_form_name_field_lable'

	) ) );
	$wp_customize-> add_control(new WP_Customize_Control($wp_customize, 'wewp_form_name_filed_placeholder_control',
		array(
			'label'		=> __('Name Field Placeholder', 'protected-page'),
			'section'	=> 'wewp_form_name_field_panel',
			'settings'	=> 'wewp_form_name_field_placeholder'


	) ) );
	//Email Field

	$wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        'wewp_form_email_filed_on_control',
        array(
            'label'          => __( 'Show OR Hide Name Field', 'protected-page' ),
            'section'        => 'wewp_form_email_field_panel',
            'settings'       => 'wewp_form_email_field_on',
            'type' => 'radio',
  				'choices' => array(
   					'show' => __('Show Name Field', 'protected-page'),
    				'hide' => __('Hide Name Field', 'protected-page'),
       			 )

	) ) );
	$wp_customize-> add_control(new WP_Customize_Control($wp_customize, 'wewp_form_email_filed_lable_control',
		array(
			'label'		=> __('Email Field Lable', 'protected-page'),
			'section'	=> 'wewp_form_email_field_panel',
			'settings'	=> 'wewp_form_email_field_lable'


	) ) );
	$wp_customize-> add_control(new WP_Customize_Control($wp_customize, 'wewp_form_email_filed_placeholder_control',
		array(
			'label'		=> __('Email Field Placeholder', 'protected-page'),
			'section'	=> 'wewp_form_email_field_panel',
			'settings'	=> 'wewp_form_email_field_Placeholder'


	) ) );
	//Password Field
	$wp_customize-> add_control(new WP_Customize_Control($wp_customize, 'wewp_form_Password_filed_lable_control',
		array(
			'label'		=> __('Password Field Lable', 'protected-page'),
			'section'	=> 'wewp_form_password_field_panel',
			'settings'	=> 'wewp_form_password_field_lable'


	) ) );
	$wp_customize-> add_control(new WP_Customize_Control($wp_customize, 'wewp_form_Password_filed_placeholder_control',
		array(
			'label'		=> __('Password Field Placeholder', 'protected-page'),
			'section'	=> 'wewp_form_password_field_panel',
			'settings'	=> 'wewp_form_password_field_placeholder'


	) ) );

	//Submit Button
	$wp_customize-> add_control(new WP_Customize_Control($wp_customize, 'wewp_page_submit_control',
		array(
			'label'		=> __('Submit Button', 'protected-page'),
			'section'	=> 'wewp_form_submit_button_panel',
			'settings'	=> 'wewp_page_submit_text'


	) ) );
			$wp_customize->add_control( 'wewp_page_submit_button_font_size_control', array(
 			 'type' => 'number',
 			 'section' => 'wewp_form_submit_button_panel',
 			 'label' => __( 'Submit Button Font Size', 'protected-page' ),
 			 'description' => __( 'Insert only the number, the value is in PX.', 'protected-page' ),
 			 'input_attrs' => array(
   			 	'min' => 0,
  				  'max' => 10000,
 				   'step' => 1,
 		 		),
		     'section'	=> 'wewp_form_submit_button_panel',
 			 'settings' => 'wewp_page_submit_button_font_size'
) );
		$wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        'wewp_page_submit_button_align_control',
        array(
            'label'          => __( 'Submit Button Position', 'protected-page' ),
            'section'        => 'wewp_form_submit_button_panel',
            'settings'       => 'wewp_page_submit_button_align',
            'type'           => 'select',
            'choices'        => array(
                'left'   => __( 'Left', 'protected-page' ),
                'right'  => __( 'Right', 'protected-page' ),
                'center' => __('Center', 'protected-page')
            )
        )
    )
);
		$wp_customize-> add_control(new WP_Customize_Color_Control($wp_customize, 'wewp_submit_button_text_color_control',
		array(
			'label'		=> __('Submit Button Text Color', 'protected-page'),
			'section'	=> 'wewp_form_submit_button_panel',
			'settings'	=> 'wewp_form_page_Submit_Button_text_color'


	) ) );
		$wp_customize-> add_control(new WP_Customize_Color_Control($wp_customize, 'wewp_submit_button_text_color_hover_control',
		array(
			'label'		=> __('Submit Button Text Color Hover', 'protected-page'),
			'section'	=> 'wewp_form_submit_button_panel',
			'settings'	=> 'wewp_form_page_Submit_Button_text_color_hover'


	) ) );
    $wp_customize-> add_control(new WP_Customize_Color_Control($wp_customize, 'wewp_submit_button_background_color_control',
		array(
			'label'		=> __('Submit Button Background Color', 'protected-page'),
			'section'	=> 'wewp_form_submit_button_panel',
			'settings'	=> 'wewp_submit_button_background_color'


	) ) );
	$wp_customize-> add_control(new WP_Customize_Color_Control($wp_customize, 'wewp_submit_button_background_color_hover_control',
		array(
			'label'		=> __('Submit Button Background Color Hover', 'protected-page'),
			'section'	=> 'wewp_form_submit_button_panel',
			'settings'	=> 'wewp_submit_button_background_color_hover'


	) ) );
}

add_action('customize_register', 'wewp_protected_customize_register');
//==========================
//    Output Costomize CSS
//==========================
function wewp_cusomize_css(){ ?>

<style type="text/css">
	.protected-page-image img{

		width: <?php echo get_option('wewp_page_image_width', '200'); ?>px ;
	}
	.protected-page-image {

		text-align:<?php echo get_option('wewp_page_image_align', 'center');?>;
	}
	.entry-header{
		text-align: <?php echo get_option('wewp_page_title_align', 'center');?>;
	}
	.entry-title{
		font-size: <?php echo get_option('wewp_page_title_size', '24'); ?>px ;
		color: <?php echo get_option('wewp_form_page_title_color', '#262626') ?>;
	}
	.protected-form-submit{
		text-align:<?php echo get_option('wewp_page_submit_button_align', 'center');?>;

	}
	.protected-form-submit input[type=submit]{
	color: <?php echo get_option('wewp_form_page_Submit_Button_text_color', '#ffffff') ?>;
	background:<?php echo get_option('wewp_submit_button_background_color', '#262626') ?>;
	font-size: <?php echo get_option('wewp_page_submit_button_font_size', '24'); ?>px ;
	border-color: <?php echo get_option('wewp_form_page_Submit_Button_text_color', '#ffffff') ?>;
	}
	.protected-form-submit input[type=submit]:hover{
	color: <?php echo get_option('wewp_form_page_Submit_Button_text_color_hover', '#262626') ?>;
	background:<?php echo get_option('wewp_submit_button_background_color_hover', '#ffffff') ?>;
	border-color: <?php echo get_option('wewp_form_page_Submit_Button_text_color_hover', '#262626') ?>;
	}

</style>

<?php }

add_action('wp_head', 'wewp_cusomize_css');
