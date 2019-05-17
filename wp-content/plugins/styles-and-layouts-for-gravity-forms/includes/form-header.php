<?php

 $wp_customize->add_section( 'gf_stla_form_id_form_header' , array(
    'title' => 'Form Header',
    'panel' => 'gf_stla_panel',
  ) );

 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[form-header][background-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[form-header][background-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Header Background Color' ),
      'section' => 'gf_stla_form_id_form_header',
    )
  )
);

//Label
$wp_customize->add_control(
	new WP_Customize_Label_Only(
	  $wp_customize, // WP_Customize_Manager
	  'gf_stla_form_id_'.$current_form_id.'[form-header][border-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Header Border' ),
		'section' => 'gf_stla_form_id_form_header',
		'settings' => array(),
	  )
	)
  );

  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[form-header][border-size]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[form-header][border-size]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_form_header', // Required, core or custom.
    'label' => __( 'Size' ),
   'input_attrs' => array(
    'placeholder' => 'Example: 4px or 10%'
  )
  )
);

$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[form-header][border-type]' , array(
  'default'     => 'solid',
  'transport'   => 'postMessage',
  'type' => 'option'
) );

$wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[form-header][border-type]',   array(
'type' => 'select',
'priority' => 10, // Within the section.
'section' => 'gf_stla_form_id_form_header', // Required, core or custom.
'label' => __( 'Type' ),
'choices'        => $border_types,
)
);

 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[form-header][border-radius]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[form-header][border-radius]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_form_header', // Required, core or custom.
    'label' => __( 'Radius' ),
   'input_attrs' => array(
    'placeholder' => 'Example: 4px or 10%'
  )
  )
);
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[form-header][border-color]' , array(
  //'default'     => '#000000', Removed border color
  'transport'   => 'postMessage',
  'type' => 'option'
) );

$wp_customize->add_control(
new WP_Customize_Color_Control(
$wp_customize, // WP_Customize_Manager
'gf_stla_form_id_'.$current_form_id.'[form-header][border-color]', // Setting id
array( // Args, including any custom ones.
  'label' => __( 'Header Border Color' ),
  'section' => 'gf_stla_form_id_form_header',
)
)
);

// Start of Section
//Label
$wp_customize->add_control(
	new WP_Customize_Label_Only(
	  $wp_customize, // WP_Customize_Manager
	  'gf_stla_form_id_'.$current_form_id.'[form-header][margin-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Header Margin' ),
		'section' => 'gf_stla_form_id_form_header',
		'settings' => array(),
	  )
	)
  );

  stla_margin_padding_controls( $wp_customize, $current_form_id, 'gf_stla_form_id_form_header', 'form-header', 'margin' );

// Start of Section
//Label
$wp_customize->add_control(
	new WP_Customize_Label_Only(
	  $wp_customize, // WP_Customize_Manager
	  'gf_stla_form_id_'.$current_form_id.'[form-header][padding-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Header Padding' ),
		'section' => 'gf_stla_form_id_form_header',
		'settings' => array(),
	  )
	)
  );
  stla_margin_padding_controls( $wp_customize, $current_form_id, 'gf_stla_form_id_form_header', 'form-header', 'padding' );


//    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[form-header][margin]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[form-header][margin]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_form_header', // Required, core or custom.
//     'label' => __( 'Margin' ),
//    'input_attrs' => array(
//     'placeholder' => 'Example: 5px 10px 5px 10px'
//   )
//   )
// );

//    $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[form-header][padding]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[form-header][padding]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_form_header', // Required, core or custom.
//     'label' => __( 'Padding' ),
//    'input_attrs' => array(
//     'placeholder' => 'Example: 5px 10px 5px 10px'
//   )
//   )
// );