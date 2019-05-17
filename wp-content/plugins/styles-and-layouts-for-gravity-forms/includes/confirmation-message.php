<?php
/* Start of Section */
$wp_customize->add_section( 'gf_stla_form_id_confirmation_message' , array(
    'title' => 'Confirmation Message',
    'panel' => 'gf_stla_panel',
  ) );

//Label
  $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[confirmation-message][max-width-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Width' ),
      'section' => 'gf_stla_form_id_confirmation_message',
      'settings' => array(),
    )
  )

);

/* for pc */
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][max-width]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[confirmation-message][max-width]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 400px'
  )
  )
  )
);
/* for tablet */
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][max-width-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[confirmation-message][max-width-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 400px'
  )
  )
  )
);

/* for mobile */
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][max-width-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[confirmation-message][max-width-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 400px'
  )
  )
  )
);

// /* Start of Section */
// $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][text-align]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[confirmation-message][text-align]',   array(
//     'type' => 'select',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
//     'label' => __( 'Text Alignment' ),
//     'choices'        => $align_pos,
//   )
// );

// font align style buttons
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][text-align]' , array(
	'default'     => '',
	'transport'   => 'postMessage',
	'type' => 'option'
) );

$wp_customize->add_control( new Stla_Text_Alignment_Option ( $wp_customize, 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][text-align]', array(
	'label'	      =>  'Font Alignment',
	'section'     => 'gf_stla_form_id_confirmation_message',
	'type'        => 'text_alignment',
	'choices'     => $align_pos,
) ) );

// font style buttons
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][font-style]' , array(
	'default'     => '',
	'transport'   => 'postMessage',
	'type' => 'option'
) );

$wp_customize->add_control( new Stla_Font_Style_Option ( $wp_customize, 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][font-style]', array(
	'label'	      =>  'Font Style',
	'section'     => 'gf_stla_form_id_confirmation_message',
	'type'        => 'font_style',
	'choices'     => $font_style_choices,
) ) );

  /* Start of Section */
  //Label
  $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[confirmation-message][font-size-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Font Size' ),
      'section' => 'gf_stla_form_id_confirmation_message',
      'settings' => array(),
    )
  )

);
  /* for pc */
   $wp_customize->add_setting('gf_stla_form_id_'.$current_form_id.'[confirmation-message][font-size]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[confirmation-message][font-size]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);
/* for tablet */
    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][font-size-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[confirmation-message][font-size-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);

/* for mobile */
    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][font-size-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[confirmation-message][font-size-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);

/* Start of Section */
 //Line height label
 $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[confirmation-message][line-height-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Line Height' ),
      'section' => 'gf_stla_form_id_confirmation_message',
      'settings' => array(),
    )
  )

);
 /* for pc*/
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][line-height]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[confirmation-message][line-height]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);
  /* for_tablet*/
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][line-height-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[confirmation-message][line-height-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);

  
  /* for mobile*/
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][line-height-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[confirmation-message][line-height-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);
/* Start of Section */
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][font-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[confirmation-message][font-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Font Color' ),
      'section' => 'gf_stla_form_id_confirmation_message',
    )
  )
);



  /* Start of Section */

  //Label
$wp_customize->add_control(
	new WP_Customize_Label_Only(
	  $wp_customize, // WP_Customize_Manager
	  'gf_stla_form_id_'.$current_form_id.'[confirmation-message][border-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Confirmation Border' ),
		'section' => 'gf_stla_form_id_confirmation_message',
		'settings' => array(),
	  )
	)
  );

  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][border-size]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[confirmation-message][border-size]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
    'label' => __( 'Size' ),
   'input_attrs' => array(
    'placeholder' => 'Example: 4px or 10%'
  )
  )
);

  /* Start of Section */
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][border-type]' , array(
      'default'     => 'solid',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[confirmation-message][border-type]',   array(
    'type' => 'select',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
    'label' => __( 'Type' ),
    'choices'        => $border_types,
  )
);

//  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][border-color]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control(
//   new WP_Customize_Color_Control(
//     $wp_customize, // WP_Customize_Manager
//     'gf_stla_form_id_'.$current_form_id.'[confirmation-message][border-color]', // Setting id
//     array( // Args, including any custom ones.
//       'label' => __( 'Border Color' ),
//       'section' => 'gf_stla_form_id_confirmation_message',
//     )
//   )
// );

//  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][border-radius]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[confirmation-message][border-radius]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
//     'label' => __( 'Border Radius' ),
//    'input_attrs' => array(
//     'placeholder' => 'Example: 4px or 10%'
//   )
//   )
// );

/* Start of Section */
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][background-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[confirmation-message][background-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Background Color' ),
      'section' => 'gf_stla_form_id_confirmation_message',
    )
  )
);


//   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][margin]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[confirmation-message][margin]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
//     'label' => __( 'Margin' ),
//    'input_attrs' => array(
//     'placeholder' => 'Example: 5px 10px 5px 10px'
//   )
//   )
// );
/* Start of Section */
//    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[confirmation-message][padding]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[confirmation-message][padding]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_confirmation_message', // Required, core or custom.
//     'label' => __( 'Padding' ),
//    'input_attrs' => array(
//     'placeholder' => 'Example: 5px 10px 5px 10px'
//   )
//   )
// );

// Start of Section
//Label
$wp_customize->add_control(
	new WP_Customize_Label_Only(
	  $wp_customize, // WP_Customize_Manager
	  'gf_stla_form_id_'.$current_form_id.'[confirmation-message][padding-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Confirmation Padding' ),
		'section' => 'gf_stla_form_id_confirmation_message',
		'settings' => array(),
	  )
	)
  );
  stla_margin_padding_controls( $wp_customize, $current_form_id, 'gf_stla_form_id_confirmation_message', 'confirmation-message', 'padding' );