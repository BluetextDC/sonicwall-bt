<?php
  /* Start of Section */
$wp_customize->add_section( 'gf_stla_form_id_error_message' , array(
    'title' => 'Error Message',
    'panel' => 'gf_stla_panel',
  ) );

//label
 $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[error-message][max-width-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Width' ),
      'section' => 'gf_stla_form_id_error_message',
      'settings' => array(),
    )
  )

);

/* for pc */
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[error-message][max-width]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[error-message][max-width]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_error_message', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 400px'
  )
  )
  )
);
/* for tablet */
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[error-message][max-width-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[error-message][max-width-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_error_message', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 400px'
  )
  )
  )
);

/* for mobile */
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[error-message][max-width-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[error-message][max-width-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_error_message', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 400px'
  )
  )
  )
);
//   /* Start of Section */
// $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[error-message][text-align]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[error-message][text-align]',   array(
//     'type' => 'select',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_error_message', // Required, core or custom.
//     'label' => __( 'Text Alignment' ),
//     'choices'        => $align_pos,
//   )
// );


// font align style buttons
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[error-message][text-align]' , array(
	'default'     => '',
	'transport'   => 'postMessage',
	'type' => 'option'
) );

$wp_customize->add_control( new Stla_Text_Alignment_Option ( $wp_customize, 'gf_stla_form_id_'.$current_form_id.'[error-message][text-align]', array(
	'label'	      =>  'ERROR MESSAGE Font Alignment',
	'section'     => 'gf_stla_form_id_error_message',
	'type'        => 'text_alignment',
	'choices'     => $align_pos,
) ) );


//    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[error-message][font-size]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[error-message][font-size]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_error_message', // Required, core or custom.
//     'label' => __( 'Font Size' ),
//    'input_attrs' => array(
//     'placeholder' => 'Example: 40px'
//   )
//   )
// );

// $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[error-message][font-color]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control(
//   new WP_Customize_Color_Control(
//     $wp_customize, // WP_Customize_Manager
//     'gf_stla_form_id_'.$current_form_id.'[error-message][font-color]', // Setting id
//     array( // Args, including any custom ones.
//       'label' => __( 'Font Color' ),
//       'section' => 'gf_stla_form_id_error_message',
//     )
//   )
// );
    /* Start of Section */
//Label
$wp_customize->add_control(
	new WP_Customize_Label_Only(
	  $wp_customize, // WP_Customize_Manager
	  'gf_stla_form_id_'.$current_form_id.'[error-message][border-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Error Message Border' ),
		'section' => 'gf_stla_form_id_error_message',
		'settings' => array(),
	  )
	)
  );

  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[error-message][border-size]' , array(
      'default'     => '1px',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[error-message][border-size]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_error_message', // Required, core or custom.
    'label' => __( 'Size' ),
   'input_attrs' => array(
    'placeholder' => 'Example: 4px or 10%'
  )
  )
);

  /* Start of Section */
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[error-message][border-type]' , array(
      'default'     => 'solid',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[error-message][border-type]',   array(
    'type' => 'select',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_error_message', // Required, core or custom.
    'label' => __( 'Type' ),
    'choices'        => $border_types,
  )
);

//  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[error-message][border-color]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control(
//   new WP_Customize_Color_Control(
//     $wp_customize, // WP_Customize_Manager
//     'gf_stla_form_id_'.$current_form_id.'[error-message][border-color]', // Setting id
//     array( // Args, including any custom ones.
//       'label' => __( 'Border Color' ),
//       'section' => 'gf_stla_form_id_error_message',
//     )
//   )
// );

//  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[error-message][border-radius]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[error-message][border-radius]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_error_message', // Required, core or custom.
//     'label' => __( 'Border Radius' ),
//    'input_attrs' => array(
//     'placeholder' => 'Example: 4px or 10%'
//   )
//   )
// );

  /* Start of Section */
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[error-message][background-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[error-message][background-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Background Color' ),
      'section' => 'gf_stla_form_id_error_message',
    )
  )
);


//   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[error-message][margin]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[error-message][margin]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_error_message', // Required, core or custom.
//     'label' => __( 'Margin' ),
//    'input_attrs' => array(
//     'placeholder' => 'Example: 5px 10px 5px 10px'
//   )
//   )
// );

//   /* Start of Section */
//    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[error-message][padding]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[error-message][padding]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_error_message', // Required, core or custom.
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
	  'gf_stla_form_id_'.$current_form_id.'[error-message][padding-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Error message Padding' ),
		'section' => 'gf_stla_form_id_error_message',
		'settings' => array(),
	  )
	)
  );
  stla_margin_padding_controls( $wp_customize, $current_form_id, 'gf_stla_form_id_error_message', 'error-message', 'padding' );