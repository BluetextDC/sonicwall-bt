<?php
//submit button
/* Start of Section */
$wp_customize->add_section( 'gf_stla_form_id_submit_button' , array(
    'title' => 'Submit Button',
    'panel' => 'gf_stla_panel',
  ) );

// $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][button-align]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[submit-button][button-align]',   array(
//     'type' => 'select',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
//     'label' => __( 'Button Position' ),
//     'choices'        => $align_pos,
//   )
// );


// font align style buttons
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][button-align]' , array(
	'default'     => '',
	'transport'   => 'postMessage',
	'type' => 'option'
) );

$wp_customize->add_control( new Stla_Text_Alignment_Option ( $wp_customize, 'gf_stla_form_id_'.$current_form_id.'[submit-button][button-align]', array(
	'label'	      =>  'Button Alignment',
	'section'     => 'gf_stla_form_id_submit_button',
	'type'        => 'text_alignment',
	'choices'     => $align_pos,
) ) );

/* Start of Section */
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][button-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[submit-button][button-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Button Color' ),
      'section' => 'gf_stla_form_id_submit_button',
    )
  )
);

/* Start of Section */
     $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][hover-color]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[submit-button][hover-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Hover Color' ),
      'section' => 'gf_stla_form_id_submit_button',
    )
  )
);

// font style buttons
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][font-style]' , array(
	'default'     => '',
	'transport'   => 'postMessage',
	'type' => 'option'
) );

$wp_customize->add_control( new Stla_Font_Style_Option ( $wp_customize, 'gf_stla_form_id_'.$current_form_id.'[submit-button][font-style]', array(
	'label'	      =>  'Font Style',
	'section'     => 'gf_stla_form_id_submit_button',
	'type'        => 'font_style',
	'choices'     => $font_style_choices,
) ) );

/* Start of Section */
//label
 $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[submit-button][font-size-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Font Size' ),
      'section' => 'gf_stla_form_id_submit_button',
      'settings' => array(),
    )
  )

);
/* for pc */
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][font-size]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[submit-button][font-size]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 24px '
  )
  )
  )
);

/* for tablet */
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][font-size-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[submit-button][font-size-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 24px '
  )
  )
  )
);

/* for mobile */
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][font-size-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[submit-button][font-size-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 24px '
  )
  )
  )
);

/* Start of Section */
 //Line height label
 $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[submit-button][line-height-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Line Height' ),
      'section' => 'gf_stla_form_id_submit_button',
      'settings' => array(),
    )
  )

);
 /* for pc*/
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][line-height]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[submit-button][line-height]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);
  /* for_tablet*/
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][line-height-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[submit-button][line-height-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);

  
  /* for mobile*/
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][line-height-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[submit-button][line-height-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 40px'
  )
  )
  )
);


/* Start of Section */
     $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][font-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[submit-button][font-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Font Color' ),
      'section' => 'gf_stla_form_id_submit_button',
    )
  )
);

/* Start of Section */
     $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][font-hover-color]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[submit-button][font-hover-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Hover Font Color' ),
      'section' => 'gf_stla_form_id_submit_button',
    )
  )
);

/* Start of Section */
//label
 $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[submit-button][max-width-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Width' ),
      'section' => 'gf_stla_form_id_submit_button',
      'settings' => array(),
    )
  )

);

/* for pc */
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][max-width]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[submit-button][max-width]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 50px'
  )
  )
  )
);
  /* for tablet */
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][max-width-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[submit-button][max-width-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 50px'
  )
  )
  )
);

  /* for mobile */
  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][max-width-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[submit-button][max-width-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 50px'
  )
  )
  )
);
/* Start of Section */
//label
 $wp_customize->add_control(
  new WP_Customize_Label_Only(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[submit-button][height-label-only]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Height' ),
      'section' => 'gf_stla_form_id_submit_button',
      'settings' => array(),
    )
  )

);

  /* for pc */
  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][height]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Desktop_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[submit-button][height]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 50px '
  )
  )
  )
);
  /* for tablet */
  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][height-tab]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Tab_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[submit-button][height-tab]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 50px '
  )
  )
  )
);
   /* for mobile */
  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][height-phone]' , array(
      'default'     => '',
      'transport'   => 'refresh',
      'type' => 'option'
  ) );

  $wp_customize->add_control(new Stla_Mobile_Text_Input_Option( $wp_customize,'gf_stla_form_id_'.$current_form_id.'[submit-button][height-phone]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
    'label' => __( '' ),
   'input_attrs' => array(
    'placeholder' => 'Ex: 50px '
  )
  )
  )
);

//Label
$wp_customize->add_control(
	new WP_Customize_Label_Only(
	  $wp_customize, // WP_Customize_Manager
	  'gf_stla_form_id_'.$current_form_id.'[submit-button][border-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Button Border' ),
		'section' => 'gf_stla_form_id_submit_button',
		'settings' => array(),
	  )
	)
  );

  /* Start of Section */
  $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][border-size]' , array(
      'default'     => '0px',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[submit-button][border-size]',   array(
    'type' => 'text',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
    'label' => __( 'Size' ),
   'input_attrs' => array(
    'placeholder' => 'Example: 4px or 10%'
  )
  )
);

/* Start of Section */
   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][border-type]' , array(
      'default'     => 'solid',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[submit-button][border-type]',   array(
    'type' => 'select',
    'priority' => 10, // Within the section.
    'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
    'label' => __( 'Type' ),
    'choices'        => $border_types,
  )
);

/* Start of Section */
$wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][border-radius]' , array(
  'default'     => '',
  'transport'   => 'postMessage',
  'type' => 'option'
) );

$wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[submit-button][border-radius]',   array(
'type' => 'text',
'priority' => 10, // Within the section.
'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
'label' => __( 'Radius' ),
'input_attrs' => array(
'placeholder' => 'Example: 4px or 10%'
)
)
);

/* Start of Section */
 $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][border-color]' , array(
      'default'     => '',
      'transport'   => 'postMessage',
      'type' => 'option'
  ) );

  $wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize, // WP_Customize_Manager
    'gf_stla_form_id_'.$current_form_id.'[submit-button][border-color]', // Setting id
    array( // Args, including any custom ones.
      'label' => __( 'Border Color' ),
      'section' => 'gf_stla_form_id_submit_button',
    )
  )
);

// /* Start of Section */
//   $wp_customize->add_setting( 'gf_stla_form_id_'.$current_form_id.'[submit-button][margin]' , array(
//       'default'     => '',
//       'transport'   => 'postMessage',
//       'type' => 'option'
//   ) );

//   $wp_customize->add_control('gf_stla_form_id_'.$current_form_id.'[submit-button][margin]',   array(
//     'type' => 'text',
//     'priority' => 10, // Within the section.
//     'section' => 'gf_stla_form_id_submit_button', // Required, core or custom.
//     'label' => __( 'Margin' ),
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
	  'gf_stla_form_id_'.$current_form_id.'[submit-button][margin-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Button Margin' ),
		'section' => 'gf_stla_form_id_submit_button',
		'settings' => array(),
	  )
	)
  );

  stla_margin_padding_controls( $wp_customize, $current_form_id, 'gf_stla_form_id_submit_button', 'submit-button', 'margin' );

// Start of Section
//Label
$wp_customize->add_control(
	new WP_Customize_Label_Only(
	  $wp_customize, // WP_Customize_Manager
	  'gf_stla_form_id_'.$current_form_id.'[submit-button][padding-label-only]', // Setting id
	  array( // Args, including any custom ones.
		'label' => __( 'Button Padding' ),
		'section' => 'gf_stla_form_id_submit_button',
		'settings' => array(),
	  )
	)
  );
  stla_margin_padding_controls( $wp_customize, $current_form_id, 'gf_stla_form_id_submit_button', 'submit-button', 'padding' );

