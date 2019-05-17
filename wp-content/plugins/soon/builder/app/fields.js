var soonGroups = (function(soonAdditions){

    var ZONES = [1,2,3,3.5,4,5,5.5,6,7,8,9,9.5,10,11,12,-11,-10,-9,-8,-7,-6,-5,-4,-3.5,-3,-1];

    var FONTS = [
        {
            'label':'Averia Serif Libre',
            'value':'Averia+Serif+Libre:300'
        },
        {
            'label':'Comfortaa',
            'value':'Comfortaa'
        },
        {
            'label':'Chivo',
            'value':'Chivo'
        },
        {
            'label':'Chewy',
            'value':'Chewy'
        },
        {
            'label':'Droid Serif',
            'value':'Droid+Serif'
        },
        {
            'label':'Dosis',
            'value':'Dosis:300'
        },
        {
            'label':'Lato',
            'value':'Lato:300'
        },
        {
            'label':'Lilita One',
            'value':'Lilita+One'
        },
        {
            'label':'Lora',
            'value':'Lora'
        },
        {
            'label':'Mountains of Christmas',
            'value':'Mountains+of+Christmas'
        },
        {
            'label':'Montserrat',
            'value':'Montserrat'
        },
        {
            'label':'Noto Serif',
            'value':'Noto+Serif'
        },
        {
            'label':'Oswald',
            'value':'Oswald'
        },
        {
            'label':'Oswald Light',
            'value':'Oswald:300'
        },
        {
            'label':'Open Sans',
            'value':'Open+Sans:300'
        },
        {
            'label':'Play',
            'value':'Play'
        },
        {
            'label':'Poiret One',
            'value':'Poiret+One'
        },
        {
            'label':'Port Lligat Slab',
            'value':'Port+Lligat+Slab'
        },
        {
            'label':'PT Sans',
            'value':'PT+Sans'
        },
        {
            'label':'Quicksand',
            'value':'Quicksand'
        },
        {
            'label':'Roboto',
            'value':'Roboto:100'
        },
        {
            'label':'Roboto Condensed',
            'value':'Roboto+Condensed:700'
        },
        {
            'label':'Roboto Slab',
            'value':'Roboto+Slab:300'
        },
        {
            'label':'Source Code Pro',
            'value':'Source+Code+Pro:200'
        },
        {
            'label':'Source Sans Pro',
            'value':'Source+Sans+Pro:300'
        },
        {
            'label':'Squada One',
            'value':'Squada+One'
        }
    ];

    // other methods
    var joinFieldValues = function(character) {
        return function(values){
            return values.filter(function(value){return value.length;}).join(character);
        };
    };

    var splitFieldValue = function(character) {
        return function(value) {
            return value.split(character);
        }
    };

    var setDependency = function(id,value){
        return {
            'id':id,
            'value':value
        }
    };

    var getField = function(id,label,attr,value,transform,dep) {
        return {
            'id':id,
            'label':label,
            'attr':attr,
            'value':value,
            'transform':transform,
            'dep':dep
        }
    };

    var getSelectField= function(id,label,attr,options,dep) {
        var field = getField(id,label,attr,null,null,dep);
        field.type = 'select';
        field.options = options;
        return field;
    };

    var getCheckboxList = function(id,label,attr,options,value,dep) {
        var field = getField(id,label,attr,value,joinFieldValues(','),dep);
        field.type = 'checkboxes';
        field.options = options;
        return field;
    };

    var getInputField = function(id,label,attr,type,value,transform,properties,dep) {

        var field = getField(id,label,attr,value,transform,dep);

        // type
        field.type = type;

        // set additional properties
        field.props = properties;

        return field;
    };

    var getMergedField = function(id,legend,attr,fields,value,transform,dep) {
        var field = getField(id,legend,attr,value,transform,dep);
        field.subfields = fields;
        return field;
    };

    var getTimezoneField = function(id,label,attr,dep) {
        return getSelectField(id,label,attr,[
                {
                    'label':'Auto',
                    'value':''
                },
                {
                    'label':'UTC',
                    'value':'Z'
                }
            ].concat(
                ZONES.map(function(zone){
                    var d = zone >= 0 ? '+' : '-';
                    zone = Math.abs(zone);
                    var h = Math.floor(zone);
                    var m = (zone%1) ===.5 ? '30' : '00';
                    return {
                        'label':d + ' ' + h + ':' + m,
                        'value':d + (h < 10 ? '0' + h : h) + ':' + m
                    }
                })
            )
        ,dep);
    };

    var getISODateField = function(id,label,attr,value,dep) {
        return getMergedField(
            id,
            label,
            attr,
            [
                getInputField(id + '_date','Date',null,'date'),
                getInputField(id + '_time','Time',null,'time',null,null,{'step':1}),
                getTimezoneField(id + '_zone','Zone')
            ],
            value,
            function(values){return values[0] + 'T' + values[1] + (values[2] || '')},
            dep
        );
    };

    var getColorField = function(id,label,attr,value,dep) {
        return getMergedField(
            id,
            label,
            attr,
            [
                getInputField(
                    id + '_color',
                    'Color',
                    null,
                    'color'
                ),
                getInputField(
                    id + '_opacity',
                    'Opacity',
                    null,
                    'number',
                    null,
                    null,
                    {'max':1,'step':.1,'min':0}
                )
            ],
            value,
            null,
            dep
        )
    };

    var getGradientField = function(id,label,attr,value,transform,dep) {
        return getMergedField(
            id,
            label,
            attr,
            [
                getInputField(
                    id + '_color_from',
                    'Color',
                    null,
                    'color',
                    value[0]
                ),
                getInputField(
                    id + '_color_to',
                    'Color',
                    null,
                    'color',
                    value[1]
                )
            ],
            value,
            transform,
            dep
        )
    };

    var getLabelsField = function(id,label,attr,singular,plural,dep) {
        return getMergedField(id,label,attr,
            [
                getInputField(id + '_singular',singular,null,'text',null,null,{'placeholder':singular}),
                getInputField(id + '_plural',plural,null,'text',null,null,{'placeholder':plural})
            ],
            null,
            joinFieldValues(','),
            dep
        )
    };

    var dueDate = new Date();
        dueDate.setMonth(dueDate.getMonth() + 6);
    var dueDateISO = dueDate.toISOString().split('T');
    var sinceDate = new Date();
        sinceDate.setYear(sinceDate.getFullYear() - 5);
    var sinceDateISO = sinceDate.toISOString().split('T');
    var nowDate = new Date();
    var nowDateISO = nowDate.toISOString().split('T');

    var groups = [
        {
            id:'moment',
            label:'Moment',
            fields:[
                getSelectField('counter-type','Type','_type',[
                    {
                        value:'count-down',
                        label:'Count down to moment'
                    },
                    {
                        value:'count-up',
                        label:'Count up from moment'
                    },
                    {
                        value:'count-day',
                        label:'Count down to time of day'
                    },
                    {
                        value:'count-amount',
                        label:'Count down amount'
                    },
                    {
                        value:'clock',
                        label:'Clock'
                    }
                ]),
                getISODateField('due-iso','Due','due',[dueDateISO[0],'12:00:00'],
                    setDependency('counter-type','count-down')
                ),
                getISODateField('since-iso','Since','since',[sinceDateISO[0],'12:00:00'],
                    setDependency('counter-type','count-up')
                ),

                getMergedField('due-day','Count towards','due',
                    [
                        getSelectField('due-day_name','Day',null,[
                            {
                                label:'Each day at',
                                value:'each'
                            },
                            {
                                label:'Monday at',
                                value:'monday'
                            },
                            {
                                label:'Tuesday at',
                                value:'tuesday'
                            },
                            {
                                label:'Wednesday at',
                                value:'wednesday'
                            },
                            {
                                label:'Thursday at',
                                value:'thursday'
                            },
                            {
                                label:'Friday at',
                                value:'friday'
                            },
                            {
                                label:'Saturday at',
                                value:'saturday'
                            },
                            {
                                label:'Sunday',
                                value:'sunday'
                            }
                        ]),
                        getInputField('due-day_time','Time',null,'time','12:00:00',null,{'step':1}),
                        getTimezoneField('due-day_zone','Zone'),
                        getSelectField('due-day_cycle','Reset',null,[
                            {
                                label:'Wait',
                                value:'wait'
                            },
                            {
                                label:'Loop',
                                value:'loop'
                            }
                        ])
                    ],
                    null,
                    function(values) {

                        var interval = (values[0] === 'each' ? '' : values[0] + ' ') + 'at ' + values[1];

                        if (values[2] == 'loop' || values[3] == 'loop') {
                            interval = 'reset ' + interval;
                        }

                        if (values[2] && values[2] !== 'wait' && values[2]!=='loop') {
                             interval += ' zone ' + values[2];
                        }

                        return interval.trim();

                    },
                    setDependency('counter-type','count-day')
                ),
                getMergedField('due-amount','Amount','due',
                    [
                        getInputField(
                            'due-amount_amount',
                            'Amount',
                            null,
                            'number',
                            5,
                            null,
                            {'max':60,'step':1,'min':0}
                        ),
                        getSelectField('due-amount_unit','Type',null,[,
                            {
                                value:'hours',
                                label:'Hour(s)'
                            },
                            {
                                value:'minutes',
                                label:'Minute(s)'
                            },
                            {
                                value:'seconds',
                                label:'Second(s)'
                            }
                        ])
                    ],
                    null,
                    function(values) { return 'in ' + values.join(' '); },
                    setDependency('counter-type',['count-amount'])
                ),

                getSelectField('session', 'Link to session', '@session',[
                    {
                        value:'true',
                        label:'True'
                    },
                    {
                        value:'false',
                        label:'False'
                    }
                ], setDependency('counter-type', 'count-amount')),

                getSelectField('now-type','Count From','_live',[
                    {
                        value:'current',
                        label:'Current time'
                    },
                    {
                        value:'fixed',
                        label:'Fixed moment'
                    }
                ],setDependency('counter-type',['count-up','count-down'])),
                getISODateField('now-fixed','Fixed Moment','now',[nowDateISO[0],'12:00'],setDependency('now-type','fixed')),

                getSelectField('complete-handler','On Complete','_complete',[
                    {
                        value:'',
                        label:'Do nothing'
                    },
                    {
                        value:'hide',
                        label:'Hide'
                    },
                    {
                        value:'redirect',
                        label:'Redirect to URL'
                    },
                    {
                        value:'javascript',
                        label:'Run JavaScript function'
                    }
                ]),

                getInputField(
                    'complete-handler-redirect',
                    'URL',
                    '@complete-handler-redirect',
                    'text',null,null,{'placeholder':'http://...'},
                    setDependency('complete-handler','redirect')),

                getInputField(
                    'complete-handler-javascript',
                    'Function name',
                    '@complete-handler-javascript',
                    'text',null,null,{'placeholder':'function_name'},
                    setDependency('complete-handler','javascript')),

                getInputField(
                    'complete-handler-hide',
                    '',
                    '@complete-handler-hide',
                    'hidden','auto',null,null,
                    setDependency('complete-handler','hide')),

                getInputField(
                    'complete-handler-callback',
                    '',
                    'event-complete',
                    'hidden','soonCompleteCallback',null,null,setDependency('complete-handler',['hide','redirect','javascript']))

            ]
        },
        {
            id:'layout',
            label:'Layout',
            fields:[
                getSelectField('layout-type','Layout Type','layout',[
                    {
                        label:'Group',
                        value:'group'
                    },
                    {
                        label:'Inline',
                        value:'inline'
                    }
                ]),
                getSelectField('whitespace', 'Whitespace', '_whitespace',[
                    {
                        label:'Default',
                        value:''
                    },
                    {
                        label:'Custom',
                        value:'custom'
                    }
                ]),
                getInputField(
                    'whitespace-top',
                    'Whitespace top',
                    '$whitespace-top',
                    'number',
                    null,
                    null,
                    {'max':100,'step':.1,'min':0,'placeholder':'Top'},
                    setDependency('whitespace','custom')
                ),
                getInputField(
                    'whitespace-right',
                    'Whitespace right',
                    '$whitespace-right',
                    'number',
                    null,
                    null,
                    {'max':100,'step':.1,'min':0,'placeholder':'Right'},
                    setDependency('whitespace','custom')
                ),
                getInputField(
                    'whitespace-bottom',
                    'Whitespace bottom',
                    '$whitespace-bottom',
                    'number',
                    null,
                    null,
                    {'max':100,'step':.1,'min':0,'placeholder':'Bottom'},
                    setDependency('whitespace','custom')
                ),
                getInputField(
                    'whitespace-left',
                    'Whitespace left',
                    '$whitespace-left',
                    'number',
                    null,
                    null,
                    {'max':100,'step':.1,'min':0,'placeholder':'Left'},
                    setDependency('whitespace','custom')
                ),
                getSelectField('max-scale','Maximum Scale','scale-max',[
                    {
                        label:'Default',
                        value:''
                    },
                    {
                        label:'Fill',
                        value:'fill'
                    },
                    {
                        label:'XXL',
                        value:'xxl'
                    },
                    {
                        label:'XL',
                        value:'xl'
                    },
                    {
                        label:'L',
                        value:'l'
                    },
                    {
                        label:'M',
                        value:'m'
                    },
                    {
                        label:'S',
                        value:'s'
                    },
                    {
                        label:'XS',
                        value:'xs'
                    },
                    {
                        label:'XXS',
                        value:'xxs'
                    }
                ]),
                getSelectField('max-scale-hide','Hide Algorithm','scale-hide',[
                    {
                        label:'Default',
                        value:''
                    },
                    {
                        label:'No hiding',
                        value:'none'
                    },
                    {
                        label:'Empty values only',
                        value:'empty'
                    }
                ]),
                getCheckboxList('date-format','Format','format',[
                        {
                            value:'y',
                            label:'Years'
                        },
                        {
                            value:'M',
                            label:'Months'
                        },
                        {
                            value:'w',
                            label:'Weeks'
                        },
                        {
                            value:'d',
                            label:'Days'
                        },
                        {
                            value:'h',
                            label:'Hours'
                        },
                        {
                            value:'m',
                            label:'Minutes'
                        },
                        {
                            value:'s',
                            label:'Seconds'
                        },
                        {
                            value:'ms',
                            label:'Milliseconds'
                        }
                    ],
                    ['d','h','m','s']
                ),

                getSelectField('character-separation','Separate Characters','separate-chars',[
                    {label:'Yes',value:''},
                    {label:'No',value:'false'}
                ]),

                getSelectField('value-cascading','Cascade Values','cascade',[
                    {label:'Yes',value:''},
                    {label:'No',value:'false'}
                ]),

                getSelectField('padding','Padding','padding',[
                    {label:'Yes',value:''},
                    {label:'No',value:'false'}
                ]),

                getInputField('year-padding','Year Padding','padding-years','text',null,null,{'placeholder':'0'},
                    setDependency('date-format','y')),
                getInputField('month-padding','Month Padding','padding-months','text',null,null,{'placeholder':'00'},
                    setDependency('date-format','M')),
                getInputField('week-padding','Week Padding','padding-weeks','text',null,null,{'placeholder':'00'},
                    setDependency('date-format','w')),
                getInputField('day-padding','Day Padding','padding-days','text',null,null,{'placeholder':'000'},
                    setDependency('date-format','d')),
                getInputField('hour-padding','Hour Padding','padding-hours','text',null,null,{'placeholder':'00'},
                    setDependency('date-format','h')),
                getInputField('minute-padding','Minute Padding','padding-minutes','text',null,null,{'placeholder':'00'},
                    setDependency('date-format','m')),
                getInputField('second-padding','Second Padding','padding-seconds','text',null,null,{'placeholder':'00'},
                    setDependency('date-format','s')),
                getInputField('millisecond-padding','Milliseconds Padding','padding-milliseconds','text',null,null,{'placeholder':'000'},
                    setDependency('date-format','ms'))

                ,
                getSelectField('group-spacing','Spacing','layout',[
                    {
                        label:'Default',
                        value:''
                    },
                    {
                        label:'Overlap',
                        value:'overlap'
                    },
                    {
                        label:'Adjacent',
                        value:'adjacent'
                    },
                    {
                        label:'Tight',
                        value:'tight'
                    },
                    {
                        label:'Spacey',
                        value:'spacey'
                    }
                ],setDependency('layout-type','group')),
                getSelectField('separator-character','Separator','separator',[
                    {
                        label:'None',
                        value:''
                    },
                    {
                        label:':',
                        value:':'
                    },
                    {
                        label:'/',
                        value:'/'
                    },
                    {
                        label:'.',
                        value:'.'
                    }
                ],setDependency('layout-type','group')),
                getSelectField('reflection-state','Reflection','reflect',[
                    {
                        label:'No',
                        value:''
                    },
                    {
                        label:'Yes',
                        value:'true'
                    }
                ]),
                getSelectField('reflection-strength','Reflection Strength','$reflection-strength',[
                    {
                        label:'Default',
                        value:''
                    },
                    {
                        label:'20%',
                        value:'.2'
                    },
                    {
                        label:'40%',
                        value:'.4'
                    },
                    {
                        label:'60%',
                        value:'.6'
                    },
                    {
                        label:'80%',
                        value:'.8'
                    },
                    {
                        label:'100%',
                        value:'1'
                    }
                ],setDependency('reflection-state',true))
            ]
        },

        {
            id:'styles',
            label:'Styles',
            fields:[

                getColorField('background-color','Background Color','$background-color',['#ffffff',1]),

                getInputField(
                    'background-image',
                    'Background Image URL',
                    '$background-image',
                    'text',
                    null,
                    null,
                    {'placeholder':'http://.../image.jpg'}
                ),

                getSelectField(
                    'background-position',
                    'Background Image Position',
                    '$background-position',
                    [
                        {
                            'label':'Top',
                            'value':'top'
                        },
                        {
                            'label':'Bottom',
                            'value':'bottom'
                        },
                        {
                            'label':'Left',
                            'value':'left'
                        },
                        {
                            'label':'Right',
                            'value':'right'
                        }
                    ]
                ),

                getColorField('text-color','Text Color','$text-color',['#333333',1]),

                getSelectField('label-color-options','Label Color','_label-color-options',[
                    {
                        'label':'Same as Text',
                        'value':''
                    },
                    {
                        'label':'Custom',
                        'value':'custom'
                    }
                ]),

                getColorField('custom-label-color','Custom Label Color','$label-color',['#b0b0b0',1],
                    setDependency('label-color-options','custom')),

                getSelectField('separator-color','Separator Color','_separator-style',[
                    {
                        'label':'Same as Text',
                        'value':''
                    },
                    {
                        'label':'Custom',
                        'value':'custom'
                    }
                ],setDependency('separator-character',function(value){return value.length > 0;})),

                getColorField('custom-separator-color','Custom Separator Color','$separator-color',['#95C4E1',1],
                    setDependency('separator-color','custom')),

                getSelectField('font-family-type','Font setup','_font-family',[
                    {
                        'label':'Page Default',
                        'value':''
                    },
                    {
                        'label':'Popular',
                        'value':'list'
                    },
                    {
                        'label':'Custom Google WebFont',
                        'value':'custom'
                    }
                ]),

                getSelectField('font-family-list','Font','$font-family',FONTS,
                    setDependency('font-family-type','list')),

                getMergedField('font-family-custom','Google WebFont','$font-family',
                    [
                        getInputField(
                            'font-family-name',
                            'Name',
                            null,
                            'text',
                            null,
                            null,
                            {'placeholder':'Font Name'}
                        ),

                        getSelectField(
                            'font-family-weight','Weight',null,[
                            {
                                label:'Default',
                                value:''
                            },
                            {
                                label:'100',
                                value:':100'
                            },
                            {
                                label:'200',
                                value:':200'
                            },
                            {
                                label:'300',
                                value:':300'
                            },
                            {
                                label:'400',
                                value:':400'
                            },
                            {
                                label:'500',
                                value:':500'
                            },
                            {
                                label:'600',
                                value:':600'
                            },
                            {
                                label:'700',
                                value:':700'
                            },
                            {
                                label:'800',
                                value:':800'
                            },
                            {
                                label:'900',
                                value:':900'
                            }
                        ]),

                        getSelectField(
                            'font-family-variant','Variant',null,
                            [
                                {
                                    label:'Regular',
                                    value:''
                                },
                                {
                                    label:'Italic',
                                    value:'italic'
                                }
                            ]
                        )
                    ],
                    null,
                    function(values) {
                        return typeof values.join === 'function' ? values.join('').replace(/\s/,'+') : values;
                    },
                    setDependency('font-family-type',['custom'])
                )
            ]
        },

        {
            id:'faces',
            label:'Faces',
            fields:[
                getSelectField('face-type','Type','face',[
                    {
                        value:'slot',
                        label:'Slot'
                    },
                    {
                        value:'flip',
                        label:'Flip'
                    },
                    {
                        value:'matrix',
                        label:'Dot Matrix'
                    },
                    {
                        value:'text',
                        label:'Text'
                    }
                ])
            ]
        },
        {
            // slot
            id:'slot',
            dep:setDependency('face-type','slot'),
            fields:[
                getSelectField('slot-animation-type','Animation Type','face',[
                    {
                        'label':'Fade',
                        'value':''
                    },
                    {
                        'label':'Slide',
                        'value':'slide'
                    },
                    {
                        'label':'Roll',
                        'value':'roll'
                    },
                    {
                        'label':'Rotate',
                        'value':'rotate'
                    },
                    {
                        'label':'Doctor',
                        'value':'doctor'
                    }
                ]),
                getSelectField('slot-animation-direction','Animation Direction','face',[
                    {
                        'label':'Default',
                        'value':''
                    },
                    {
                        'label':'Up',
                        'value':'up'
                    },
                    {
                        'label':'Down',
                        'value':'down'
                    },
                    {
                        'label':'Left',
                        'value':'left'
                    },
                    {
                        'label':'Right',
                        'value':'right'
                    }
                ],setDependency('slot-animation-type',['slide','roll'])),
                getSelectField('slot-rotate-animation-direction','Animation Direction','face',[
                    {
                        'label':'Default',
                        'value':''
                    },
                    {
                        'label':'Left',
                        'value':'left'
                    },
                    {
                        'label':'Right',
                        'value':'right'
                    }
                ],setDependency('slot-animation-type','rotate')),
                getSelectField('slot-animation-speed','Animation Speed','face',[
                    {
                        'label':'Default',
                        'value':''
                    },
                    {
                        'label':'Fast',
                        'value':'fast'
                    },
                    {
                        'label':'Faster',
                        'value':'faster'
                    }
                ]),
                getSelectField('slot-shadow','Shadow','face',[
                    {
                        'label':'None',
                        'value':''
                    },
                    {
                        'label':'Soft',
                        'value':'shadow-soft'
                    },
                    {
                        'label':'Hard',
                        'value':'shadow-hard'
                    },
                    {
                        'label':'Glow',
                        'value':'glow'
                    }
                ])
            ]
        },
        {
            // flip
            id:'flip',
            dep:setDependency('face-type','flip'),
            fields:[
                getSelectField('flip-color','Colors','face',[
                    {
                        'label':'Default',
                        'value':''
                    },
                    {
                        'label':'Light',
                        'value':'color-light'
                    },
                    {
                        'label':'Dark',
                        'value':'color-dark'
                    },
                    {
                        'label':'Custom',
                        'value':'flip-colors-custom'
                    }
                ]),

                getInputField('flip-background-color','Background Color','$flip-background-color','color','#95C4E1',null,null,
                    setDependency('flip-color','flip-colors-custom')),
                getInputField('flip-text-color','Character Color','$flip-text-color','color','#ffffff',null,null,
                    setDependency('flip-color','flip-colors-custom')),

                getSelectField('flip-shadow','Shadow','face',[
                    {
                        'label':'None',
                        'value':''
                    },
                    {
                        'label':'Soft',
                        'value':'shadow-soft'
                    },
                    {
                        'label':'Hard',
                        'value':'shadow-hard'
                    }
                ]),
                getSelectField('flip-animation-speed','Animation speed','face',[
                    {
                        'label':'Default',
                        'value':''
                    },
                    {
                        'label':'Fast',
                        'value':'fast'
                    },
                    {
                        'label':'Faster',
                        'value':'faster'
                    }
                ]),
                getSelectField('flip-corners','Corners','face',[
                    {
                        'label':'Default',
                        'value':''
                    },
                    {
                        'label':'Sharp',
                        'value':'corners-sharp'
                    },
                    {
                        'label':'Round',
                        'value':'corners-round'
                    }
                ])
            ]
        },
        {
            // dot matrix
            id:'matrix',
            dep:setDependency('face-type','matrix'),
            fields:[

                getSelectField('dot-matrix-resolution','Matrix Resolution','face',[
                    {
                        'label':'Default',
                        'value':''
                    },
                    {
                        'label':'5x7',
                        'value':'5x7'
                    },
                    {
                        'label':'3x5',
                        'value':'3x5'
                    }
                ]),

                getSelectField('dot-matrix-spacing','Dot Spacing','face',[
                        {
                            'label':'Default',
                            'value':''
                        },
                        {
                            'label':'Tight',
                            'value':'tight'
                        },
                        {
                            'label':'Spacey',
                            'value':'spacey'
                        }
                    ]
                ),
                getSelectField('dot-matrix-dot-shape','Dot Shape','face',[
                        {
                            'label':'Default',
                            'value':''
                        },
                        {
                            'label':'Square',
                            'value':'dot-square'
                        },
                        {
                            'label':'Round',
                            'value':'dot-round'
                        }
                    ]
                ),
                getSelectField('dot-matrix-animation-direction','Animation','face',[
                    {
                        'label':'Fade',
                        'value':''
                    },
                    {
                        'label':'Color',
                        'value':'animate-color'
                    },
                    {
                        'label':'Slide up',
                        'value':'slide up'
                    },
                    {
                        'label':'Slide right',
                        'value':'slide right'
                    },
                    {
                        'label':'Slide down',
                        'value':'slide down'
                    },
                    {
                        'label':'Slide left',
                        'value':'slide left'
                    }
                ]),
                getSelectField('dot-matrix-shadow-type','Shadow','face',[
                    {
                        'label':'None',
                        'value':''
                    },
                    {
                        'label':'Soft',
                        'value':'shadow-soft'
                    },
                    {
                        'label':'Hard',
                        'value':'shadow-hard'
                    },
                    {
                        'label':'Glow',
                        'value':'glow'
                    }
                ]),

                getSelectField('dot-matrix-dot-on-color-type','Dot On Type','_matrix_on_color',[
                    {
                        'label':'Default',
                        'value':'single'
                    },
                    {
                        'label':'Gradient to Right',
                        'value':'gradient-ltr'
                    },
                    {
                        'label':'Gradient to Bottom',
                        'value':'gradient-ttb'
                    }
                ]),

                getColorField('dot-matrix-dot-on-color-solid','Solid Color','$dot-on-color',['#95C4E1',1],
                    setDependency('dot-matrix-dot-on-color-type','single')),
                getGradientField('dot-matrix-dot-on-color-gradient-to-right','Gradient To Right','$dot-on-gradient-ltr',
                    [['#A847FF'],['#95C4E1']],
                    null,setDependency('dot-matrix-dot-on-color-type','gradient-ltr')),
                getGradientField('dot-matrix-dot-on-color-gradient-to-bottom','Gradient To Bottom','$dot-on-gradient-ttb',
                    [['#A847FF'],['#95C4E1']],
                    null,setDependency('dot-matrix-dot-on-color-type','gradient-ttb')),

                getSelectField('dot-matrix-dot-off-color-type','Dot Off Type','_matrix_off_color',[
                    {
                        'label':'Default',
                        'value':'single'
                    },
                    {
                        'label':'Gradient to Right',
                        'value':'gradient-ltr'
                    },
                    {
                        'label':'Gradient to Bottom',
                        'value':'gradient-ttb'
                    }
                ]),

                getColorField('dot-matrix-dot-off-color-solid','Solid Color','$dot-off-color',['#ffffff',0],
                    setDependency('dot-matrix-dot-off-color-type','single')),
                getGradientField('dot-matrix-dot-off-gradient-to-right','Gradient To Right','$dot-off-gradient-ltr',
                    [['#A847FF'],['#95C4E1']],
                    null,setDependency('dot-matrix-dot-off-color-type','gradient-ltr')),
                getGradientField('dot-matrix-dot-off-gradient-to-bottom','Gradient To Bottom','$dot-off-gradient-ttb',
                    [['#A847FF'],['#95C4E1']],
                    null,setDependency('dot-matrix-dot-off-color-type','gradient-ttb'))
            ]
        },

        {
            id:'visual',
            label:'Visuals',
            dep:setDependency('layout-type','group'),
            fields:[
                getSelectField('visual-type','Type','visual',[
                    {
                        label:'None',
                        value:''
                    },
                    {
                        label:'Ring',
                        value:'ring'
                    },
                    {
                        label:'Fill',
                        value:'fill'
                    }
                ])
            ]
        },
        {
            id:'ring',
            dep:setDependency('visual-type','ring'),
            fields:[
                getSelectField('ring-cap-style','Cap','visual',[
                    {
                        'label':'Straight',
                        'value':''
                    },
                    {
                        'label':'Round',
                        'value':'cap-round'
                    }
                ]),

                getSelectField('ring-invert','Invert','visual',[
                    {
                        'label':'No',
                        'value':''
                    },
                    {
                        'label':'Yes',
                        'value':'invert'
                    }
                ]),

                getSelectField('ring-direction','Direction','visual',[
                    {
                        'label':'Clockwise',
                        'value':''
                    },
                    {
                        'label':'Counterclockwise',
                        'value':'flip'
                    }
                ]),

                getSelectField('ring-progress-color-type','Foreground Color Type','_ring-progress-color-type',[
                    {
                        'label':'Solid',
                        'value':'solid'
                    },
                    {
                        'label':'Gradient',
                        'value':'gradient'
                    }
                ]),

                getColorField(
                    'ring-progress-color-solid',
                    'Foreground Color',
                    '$ring-progress-color-solid',
                    ['#95C4E1',1],
                    setDependency('ring-progress-color-type','solid')),

                getGradientField(
                    'ring-progress-color-gradient',
                    'Foreground Gradient',
                    'visual',
                    [['#A847FF'],['#95C4E1']],
                    function(values){return 'progressgradient-' + values.join('_').replace(/#/g,''); },
                    setDependency('ring-progress-color-type','gradient')),

                getSelectField('ring-progress-color-gradient-type','Foreground Gradient Direction','visual',[
                        {
                            'label':'Along Ring',
                            'value':''
                        },
                        {
                            'label':'Vertical',
                            'value':'progressgradienttype-vertical'
                        },
                        {
                            'label':'Horizontal',
                            'value':'progressgradienttype-horizontal'
                        }
                    ],
                    setDependency('ring-progress-color-type','gradient')
                ),

                getSelectField('ring-background-color-type','Background Color Type','_ring-background-color-type',[
                    {
                        'label':'Solid',
                        'value':'solid'
                    },
                    {
                        'label':'Gradient',
                        'value':'gradient'
                    }
                ]),

                getColorField('ring-background-color',
                    'Background Color',
                    '$ring-background-color-solid',
                    ['#f0f0f0',1],
                    setDependency('ring-background-color-type','solid')),

                getGradientField(
                    'ring-background-color-gradient',
                    'Background Gradient',
                    'visual',
                    [['#f0f0f0'],['#e0e0e0']],
                    function(values){return 'ringgradient-' + values.join('_').replace(/#/g,'');},
                    setDependency('ring-background-color-type','gradient')),

                getSelectField('ring-background-color-gradient-type','Background Gradient Direction','visual',[
                        {
                            'label':'Along Ring',
                            'value':''
                        },
                        {
                            'label':'Vertical',
                            'value':'ringgradienttype-vertical'
                        },
                        {
                            'label':'Horizontal',
                            'value':'ringgradienttype-horizontal'
                        }
                    ],
                    setDependency('ring-background-color-type','gradient')
                ),

                getSelectField('ring-progress-shadow','Foreground Shadow','visual',[
                    {
                        'label':'None',
                        'value':''
                    },
                    {
                        'label':'Soft',
                        'value':'shadow-soft-progress'
                    },
                    {
                        'label':'Hard',
                        'value':'shadow-hard-progress'
                    },
                    {
                        'label':'Glow',
                        'value':'glow-progress'
                    }
                ]),
                getSelectField('ring-background-shadow','Background Shadow','visual',[
                    {
                        'label':'None',
                        'value':''
                    },
                    {
                        'label':'Soft',
                        'value':'shadow-soft-background'
                    },
                    {
                        'label':'Hard',
                        'value':'shadow-hard-background'
                    },
                    {
                        'label':'Glow',
                        'value':'glow-background'
                    }
                ]),

                getSelectField('ring-width','Width','visual',[
                    {
                        'label':'Default',
                        'value':''
                    },
                    {
                        'label':'Thin',
                        'value':'width-thin'
                    },
                    {
                        'label':'Thick',
                        'value':'width-thick'
                    },
                    {
                        'label':'Custom',
                        'value':'ring-width-custom'
                    }
                ]),

                getInputField('ring-progress-width','Foreground Width','$ring-progress-width','number',
                    5,null,
                    {'step':1,'min':0,'max':1000},
                    setDependency('ring-width','ring-width-custom')
                ),

                getInputField('ring-background-width','Background Width','$ring-background-width','number',
                    5,null,
                    {'step':1,'min':0,'max':1000},
                    setDependency('ring-width','ring-width-custom')
                ),

                getSelectField('ring-align','Ring align','visual',[
                    {
                        'label':'Top',
                        'value':''
                    },
                    {
                        'label':'Center',
                        'value':'align-center'
                    },
                    {
                        'label':'Bottom',
                        'value':'align-bottom'
                    },
                    {
                        'label':'Inside',
                        'value':'align-inside'
                    }
                ],setDependency('ring-width','ring-width-custom')),

                getInputField('ring-offset','Offset','visual','number',
                    null,function(value){return 'offset-' + value},
                    {'placeholder':'0','step':.1,'min':0,'max':100}
                ),
                getInputField('ring-length','Length','visual','number',
                    null,function(value){return 'length-' + value},
                    {'placeholder':'100','step':.1,'min':0,'max':100}
                ),
                getInputField('ring-gap','Gap','visual','number',
                    null,function(value){return 'gap-' + value},
                    {'placeholder':'0','step':.1,'min':0,'max':50}
                )
            ]
        },

        {
            id:'fill',
            dep:setDependency('visual-type','fill'),
            fields:[
                getSelectField('fill-direction','Direction','visual',[
                    {
                        'label':'Default',
                        'value':''
                    },
                    {'label':'To Top','value':'to-top'},
                    {'label':'To Top Right','value':'to-top-right'},
                    {'label':'To Right','value':'to-right'},
                    {'label':'To Bottom Right','value':'to-bottom-right'},
                    {'label':'To Bottom','value':'to-bottom'},
                    {'label':'To Bottom Left','value':'to-bottom-left'},
                    {'label':'To Left','value':'to-left'},
                    {'label':'To Top Left','value':'to-top-left'}
                ]),
                getSelectField('fill-corner-style','Corners','visual',[
                    {
                        'label':'Default',
                        'value':''
                    },
                    {
                        'label':'Sharp',
                        'value':'corners-sharp'
                    },
                    {
                        'label':'Round',
                        'value':'corners-round'
                    }
                ]),
                getColorField('fill-background-color','Background Color','$fill-background-color',['#f0f0f0',1]),
                getColorField('fill-foreground-color','Foreground Color','$fill-foreground-color',['#95C4E1',1])
            ]
        },

        {
            id:'labels',
            label:'Labels',
            fields:[
                getSelectField('labels-singular','Label Singular','singular',[
                    {
                        label:'No',
                        value:''
                    },
                    {
                        label:'Yes',
                        value:'true'
                    }
                ]),
                getSelectField('labels-casing','Label Casing','layout',[
                    {
                        label:'Default',
                        value:''
                    },
                    {
                        label:'Lower',
                        value:'label-lowercase'
                    },
                    {
                        label:'Upper',
                        value:'label-uppercase'
                    }
                ]),
                getSelectField('labels-size','Label Size','layout',[
                    {
                        label:'Default',
                        value:''
                    },
                    {
                        label:'Small',
                        value:'label-small'
                    },
                    {
                        label:'Big',
                        value:'label-big'
                    }
                ]),
                getSelectField('labels-position','Label Position','layout',[
                    {
                        label:'Below',
                        value:''
                    },
                    {
                        label:'Above',
                        value:'label-above'
                    },
                    {
                        label:'Hidden',
                        value:'label-hidden'
                    }
                ],setDependency('layout-type','group')),

                getLabelsField('year-labels','Year Labels','labels-years','Year','Years',setDependency('date-format','y')),
                getLabelsField('month-labels','Month Labels','labels-months','Month','Months',setDependency('date-format','M')),
                getLabelsField('week-labels','Week Labels','labels-weeks','Week','Weeks',setDependency('date-format','w')),
                getLabelsField('day-labels','Day Labels','labels-days','Day','Days',setDependency('date-format','d')),
                getLabelsField('hour-labels','Hour Labels','labels-hours','Hour','Hours',setDependency('date-format','h')),
                getLabelsField('minute-labels','Minute Labels','labels-minutes','Minute','Minutes',setDependency('date-format','m')),
                getLabelsField('second-labels','Second Labels','labels-seconds','Second','Seconds',setDependency('date-format','s')),
                getLabelsField('millisecond-labels','Millisecond Labels','labels-milliseconds','Millisecond','Milliseconds',setDependency('date-format','ms'))

            ]
        },

        {
            id:'Advanced',
            label:'Advanced',
            fields:[
                {
                    'id':'css-overrides',
                    'attr':'$css-overrides',
                    'label':'CSS Overrides',
                    'type':'textarea',
                    'placeholder':'#my-soon-counter { ... }'
                }
            ]
        }

    ];

    if (soonAdditions) {
        soonAdditions(groups);
    }

    return groups;

}(window['soonAdditions']));