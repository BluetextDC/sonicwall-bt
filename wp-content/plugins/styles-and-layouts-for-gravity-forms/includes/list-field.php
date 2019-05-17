<?php
//form fields section

$wp_customize->add_section( 'gf_stla_form_id_list_field' , array(
    'title' => 'List Field',
    'panel' => 'gf_stla_panel',
  ) );

 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-table][background-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[list-field-table][background-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Table background Color' ),
      'section' => 'gf_stla_form_id_list_field',
    )
  )
);

//   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-table][margin]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[list-field-table][margin]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
//     'label' => __( 'Table Margin' ),
//    'input_attrs' => array(
//     'placeholder' => 'Example: 5px 10px 5px 10px'
//   )
//   )
// );

// font style buttons
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-heading][font-style]' , array(
	'default'     => '',
	'transport'   => 'postMessage',
	'type' => 'option'
) );

$wp_customize->add_control( new Stla_Font_Style_Option ( $wp_customize, 'gf_stla_form_id_'.$current_form_id.'[list-field-heading][font-style]', array(
	'label'	      =>  'Heading Font Style',
	'section'     => 'gf_stla_form_id_list_field',
	'type'        => 'font_style',
	'choices'     => $font_style_choices,
) ) );

 /* Start of Section */
 //label
 $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[list-field-heading][font-size-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Heading Font Size' ),
      'section' => 'gf_stla_form_id_list_field',
      'settings' => array(),
    )
  )

);
 /* for pc*/
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-heading][font-size]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

   $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[list-field-heading][font-size]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px '
  )
  )
  )
);
  /* for_tablet*/
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-heading][font-size-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

   $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[list-field-heading][font-size-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px '
  )
  )
  )
);
  
  /* for_phone*/
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-heading][font-size-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

   $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[list-field-heading][font-size-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px '
  )
  )
  )
);

/* Start of Section */
 //Line height label
 $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[list-field-heading][line-height-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Heading Line Height' ),
      'section' => 'gf_stla_form_id_list_field',
      'settings' => array(),
    )
  )

);
 /* for pc*/
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-heading][line-height]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[list-field-heading][line-height]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);
  /* for_tablet*/
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-heading][line-height-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[list-field-heading][line-height-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);

  
  /* for mobile*/
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-heading][line-height-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[list-field-heading][line-height-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);


/*start of Section */
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-heading][font-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );


  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[list-field-heading][font-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Heading Font Color' ),
      'section' => 'gf_stla_form_id_list_field',
    )
  )
);

 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-heading][background-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[list-field-heading][background-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Heading background Color' ),
      'section' => 'gf_stla_form_id_list_field',
    )
  )
);

// $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-heading][text-align]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[list-field-heading][text-align]',   array(
//     'type' => 'select',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
//     'label' => __( 'Heading Position' ),
//     'choices'        => $align_pos,
//   )
// );

// font align style buttons
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-heading][text-align]' , array(
	'default'     => '',
	'transport'   => 'postMessage',
	'type' => 'option'
) );

$wp_customize->add_control( new Stla_Text_Alignment_Option ( $wp_customize, 'gf_stla_form_id_'.$current_form_id.'[list-field-heading][text-align]', array(
	'label'	      =>  'Heading Alignment',
	'section'     => 'gf_stla_form_id_list_field',
	'type'        => 'text_alignment',
	'choices'     => $align_pos,
) ) );


//    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-heading][padding]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[list-field-heading][padding]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
//     'label' => __( 'Heading Padding' ),
//    'input_attrs' => array(
//     'placeholder' => 'Example: 5px 10px 5px 10px'
//   )
//   )
// );

$wp_customize->add_control(
	new WP_Customize_Label_Only(
	  $wp_customize, // WP_Customize_Manager
	  'gf_stla_form_id_'.$current_form_id.'[list-field-heading][padding-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Heading Padding' ),
		'section' => 'gf_stla_form_id_list_field',
		'settings' => array(),
	  )
	)
  );
  stla_margin_padding_controls( $wp_customize, $current_form_id, 'gf_stla_form_id_list_field', 'list-field-heading', 'padding' );

// font style buttons
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-cell][font-style]' , array(
	'default'     => '',
	'transport'   => 'postMessage',
	'type' => 'option'
) );

$wp_customize->add_control( new Stla_Font_Style_Option ( $wp_customize, 'gf_stla_form_id_'.$current_form_id.'[list-field-cell][font-style]', array(
	'label'	      =>  'Cell Font Style',
	'section'     => 'gf_stla_form_id_list_field',
	'type'        => 'font_style',
	'choices'     => $font_style_choices,
) ) );

 /* Start of Section */
 //label
 $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[list-field-cell][font-size-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Cell Font Size' ),
      'section' => 'gf_stla_form_id_list_field',
      'settings' => array(),
    )
  )

);
 /* for pc*/
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-cell][font-size]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  
  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[list-field-cell][font-size]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);
  /* for_tablet*/
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-cell][font-size-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

   $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[list-field-cell][font-size-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px '
  )
   )
  )
);
  
  /* for_phone*/
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-cell][font-size-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

   $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[list-field-cell][font-size-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px '
  )
   )
  )
);

/* Start of Section */
 //Line height label
 $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[list-field-cell][line-height-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Cell Line Height' ),
      'section' => 'gf_stla_form_id_list_field',
      'settings' => array(),
    )
  )

);
 /* for pc*/
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-cell][line-height]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[list-field-cell][line-height]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);
  /* for_tablet*/
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-cell][line-height-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[list-field-cell][line-height-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);

  
  /* for mobile*/
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-cell][line-height-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[list-field-cell][line-height-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);

 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-cell][background-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[list-field-cell][background-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Cell background Color' ),
      'section' => 'gf_stla_form_id_list_field',
    )
  )
);

 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-cell][font-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[list-field-cell][font-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Cell Text Font Color' ),
      'section' => 'gf_stla_form_id_list_field',
    )
  )
);

// $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-cell][text-align]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[list-field-cell][text-align]',   array(
//     'type' => 'select',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
//     'label' => __( 'Cell Text Alignment' ),
//     'choices'        => $align_pos,
//   )
// );

// font align style buttons
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-cell][text-align]' , array(
	'default'     => '',
	'transport'   => 'postMessage',
	'type' => 'option'
) );

$wp_customize->add_control( new Stla_Text_Alignment_Option ( $wp_customize, 'gf_stla_form_id_'.$current_form_id.'[list-field-cell][text-align]', array(
	'label'	      =>  'Cell Font Alignment',
	'section'     => 'gf_stla_form_id_list_field',
	'type'        => 'text_alignment',
	'choices'     => $align_pos,
) ) );

//    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[list-field-cell-container][padding]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[list-field-cell-container][padding]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_list_field', // Required, core or custom.
//     'label' => __( 'Cell Padding' ),
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
	  'gf_stla_form_id_'.$current_form_id.'[list-field-cell-container][padding-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Cell Padding' ),
		'section' => 'gf_stla_form_id_list_field',
		'settings' => array(),
	  )
	)
  );
  stla_margin_padding_controls( $wp_customize, $current_form_id, 'gf_stla_form_id_list_field', 'list-field-cell-container', 'padding' );
