<?php
//form placeholders section uses refresh method
/* Start of Section */
$wp_customize->add_section( 'gf_stla_form_id_placeholders' , array(
    'title' => 'Placeholders',
    'panel' => 'gf_stla_panel',
  ) );

// font style buttons
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[placeholders][font-style]' , array(
	'default'     => '',
	'transport'   => 'refresh',
	'type' => 'option'
) );

$wp_customize->add_control( new Stla_Font_Style_Option ( $wp_customize, 'gf_stla_form_id_'.$current_form_id.'[placeholders][font-style]', array(
	'label'	      =>  'Font Style',
	'section'     => 'gf_stla_form_id_placeholders',
	'type'        => 'font_style',
	'choices'     => $font_style_choices,
) ) );

 //label
 $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[placeholders][font-size-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Font Size' ),
      'section' => 'gf_stla_form_id_placeholders',
      'settings' => array(),
    )
  )

);
/* for pc */
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[placeholders][font-size]' , array(
    'default'     => '',
    'transport'   => 'refresh',
    'type' => 'option'
  ) );

$wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize, 'gf_stla_form_id_'.$current_form_id.'[placeholders][font-size]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_placeholders', // Required, core or custom.
    'label' => __( '' ),
    'input_attrs' => array(
      'placeholder' => 'Ex: 40px'
    )
)
  )
);
/* for tablet */
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[placeholders][font-size-tab]' , array(
    'default'     => '',
    'transport'   => 'refresh',
    'type' => 'option'
  ) );

$wp_customize->add_control( new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[placeholders][font-size-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_placeholders', // Required, core or custom.
    'label' => __( '' ),
    'input_attrs' => array(
      'placeholder' => 'Ex: 40px'
    )
    )
  )
);

/* for mobile */
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[placeholders][font-size-phone]' , array(
    'default'     => '',
    'transport'   => 'refresh',
    'type' => 'option'
  ) );

$wp_customize->add_control( new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[placeholders][font-size-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_placeholders', // Required, core or custom.
    'label' => __( '' ),
    'input_attrs' => array(
      'placeholder' => 'Ex: 40px'
    )
    )
  )
);
// line-height  not need for placeholders
/* Start of Section */
 //Line height label
//  $wp_customize->add_control(
//   new WP_Customize_Label_Only(
//     $wp_customize, // WP_Customize_Manager
//     'gf_stla_form_id_'.$current_form_id.'[placeholders][line-height-label-only]', // Setting id
//     array( // Args, including any custom ones.
//       'label' => __( 'Line Height' ),
//       'section' => 'gf_stla_form_id_placeholders',
//       'settings' => array(),
//     )
//   )

// );
//  /* for pc*/
//  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[placeholders][line-height]' , array(
//       'default'     => '',
//       'transport'   => 'refresh',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[placeholders][line-height]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_placeholders', // Required, core or custom.
//     'label' => __( '' ),
//    'input_attrs' => array(
//     'placeholder' => 'Ex: 40px'
//   )
//   )
//   )
// );
//   /* for_tablet*/
//    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[placeholders][line-height-tab]' , array(
//       'default'     => '',
//       'transport'   => 'refresh',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[placeholders][line-height-tab]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_placeholders', // Required, core or custom.
//     'label' => __( '' ),
//    'input_attrs' => array(
//     'placeholder' => 'Ex: 40px'
//   )
//   )
//   )
// );

  
  /* for mobile*/
//    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[placeholders][line-height-phone]' , array(
//       'default'     => '',
//       'transport'   => 'refresh',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[placeholders][line-height-phone]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_placeholders', // Required, core or custom.
//     'label' => __( '' ),
//    'input_attrs' => array(
//     'placeholder' => 'Ex: 40px'
//   )
//   )
//   )
// );

/* Start of Section */
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[placeholders][font-color]' , array(
    'default'     => '',
    'transport'   => 'refresh',
    'type' => 'option'
  ) );

$wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[placeholders][font-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Font Color' ),
      'section' => 'gf_stla_form_id_placeholders',
    )
  )
);

// /* Start of Section */
// $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[placeholders][padding]' , array(
//     'default'     => '',
//     'transport'   => 'refresh',
//     'type' => 'option'
//   ) );

// $wp_customize->add_control( 'gf_stla_form_id_'.$current_form_id.'[placeholders][padding]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_placeholders', // Required, core or custom.
//     'label' => __( 'Padding' ),
//     'input_attrs' => array(
//       'placeholder' => 'Example: 5px 10px 5px 10px'
//     )
//   )
// );

// Start of Section
//Label
$wp_customize->add_control(
	new WP_Customize_Label_Only(
	  $wp_customize, // WP_Customize_Manager
	  'gf_stla_form_id_'.$current_form_id.'[placeholders][padding-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Placeholders Padding' ),
		'section' => 'gf_stla_form_id_placeholders',
		'settings' => array(),
	  )
	)
  );
  stla_margin_padding_controls( $wp_customize, $current_form_id, 'gf_stla_form_id_placeholders', 'placeholders', 'padding' );