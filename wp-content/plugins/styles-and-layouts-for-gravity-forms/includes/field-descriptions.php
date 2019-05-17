<?php
//form  section

/* Start of Section */
$wp_customize->add_section( 'gf_stla_form_id_field_descriptions' , array(
    'title' => 'Field Description',
    'panel' => 'gf_stla_panel',
  ) );

//  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][text-align]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[field-descriptions][text-align]',   array(
//     'type' => 'select',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_field_descriptions', // Required, core or custom.
//     'label' => __( 'Description Font Alignment' ),
//     'choices'        => $align_pos,
//   )
// );
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][display]' , array(
  'default'     => false,
  'transport'   => 'refresh',
  'type' => 'option'
) );

$wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[field-descriptions][display]',   array(
'type' => 'checkbox',
'priority' => 10, // Within the section.
'section' => 'gf_stla_form_id_field_descriptions', // Required, core or custom.
'label' => __( 'Hide Descriptions' ),
)
);


// font align style buttons
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][text-align]' , array(
	'default'     => '',
	'transport'   => 'postMessage',
	'type' => 'option'
) );

$wp_customize->add_control( new Stla_Text_Alignment_Option ( $wp_customize, 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][text-align]', array(
	'label'	      =>  'Description Font Alignment',
	'section'     => 'gf_stla_form_id_field_descriptions',
	'type'        => 'text_alignment',
	'choices'     => $align_pos,
) ) );


// font style buttons
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][font-style]' , array(
	'default'     => '',
	'transport'   => 'postMessage',
	'type' => 'option'
) );

$wp_customize->add_control( new Stla_Font_Style_Option ( $wp_customize, 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][font-style]', array(
	'label'	      =>  'Font Style',
	'section'     => 'gf_stla_form_id_field_descriptions',
	'type'        => 'font_style',
	'choices'     => $font_style_choices,
) ) );

/* Start of Section */


//label
 $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[field-descriptions][font-size-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Font Size' ),
      'section' => 'gf_stla_form_id_field_descriptions',
      'settings' => array(),
    )
  )

);

 /* for pc */
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][font-size]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[field-descriptions][font-size]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_field_descriptions', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px '
  )
  )
  )
);
 /* for tab */
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][font-size-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[field-descriptions][font-size-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_field_descriptions', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px '
  )
  )
  )
);

  /* for mobile */
  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][font-size-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[field-descriptions][font-size-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_field_descriptions', // Required, core or custom.
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
    'gf_stla_form_id_'.$current_form_id.'[field-descriptions][line-height-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Line Height' ),
      'section' => 'gf_stla_form_id_field_descriptions',
      'settings' => array(),
    )
  )

);
 /* for pc*/
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][line-height]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[field-descriptions][line-height]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_field_descriptions', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);
  /* for_tablet*/
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][line-height-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[field-descriptions][line-height-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_field_descriptions', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);

  
  /* for mobile*/
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][line-height-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[field-descriptions][line-height-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_field_descriptions', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);

/* Start of Section */
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][font-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[field-descriptions][font-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Label Font Color' ),
      'section' => 'gf_stla_form_id_field_descriptions',
    )
  )
);

 
//   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][margin]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[field-descriptions][margin]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_field_descriptions', // Required, core or custom.
//     'label' => __( 'Margin' ),
//    'input_attrs' => array(
//     'placeholder' => 'Example: 5px 10px 5px 10px'
//   )
//   )
// );


// /* Start of Section */
//    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[field-descriptions][padding]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[field-descriptions][padding]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_field_descriptions', // Required, core or custom.
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
	  'gf_stla_form_id_'.$current_form_id.'[field-descriptions][padding-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Description Padding' ),
		'section' => 'gf_stla_form_id_field_descriptions',
		'settings' => array(),
	  )
	)
  );
  stla_margin_padding_controls( $wp_customize, $current_form_id, 'gf_stla_form_id_field_descriptions', 'field-descriptions', 'padding' );