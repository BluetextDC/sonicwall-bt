
/**
 * Preset Configuration
 */
var soonPresets = (function(){

    var newYork = [
        {"id":"max-scale","attr":"scale-max","value":"xl"},
        {"id":"group-spacing","attr":"layout","value":"tight"},
        {"id":"separator-character","attr":"separator","value":":"},
        {"id":"background-color","attr":"$background-color","value":["#2b2b2b","1"]},
        {"id":"background-image","attr":"$background-image","value":"../media/city.jpg"},
        {"id":"background-position","attr":"$background-position","value":"bottom"},
        {"id":"text-color","attr":"$text-color","value":["#ffffff","1"]},
        {"id":"font-family-type","attr":"_font-family","value":"list"},
        {"id":"font-family-list","attr":"$font-family","value":"Source+Sans+Pro:300"},
        {"id":"face-type","attr":"face","value":"text"},
        {"id":"whitespace","attr":"_whitespace","value":"custom"},
        {"id":"whitespace-top","attr":"$whitespace-top","value":"12"},
        {"id":"whitespace-right","attr":"$whitespace-right","value":"1"},
        {"id":"whitespace-bottom","attr":"$whitespace-bottom","value":"2"},
        {"id":"whitespace-left","attr":"$whitespace-left","value":"1"},
        {"id":"labels-size","attr":"layout","value":"label-small"},
        {"id":"css-overrides","attr":"$css-overrides","value":"#my-soon-counter {\n    text-shadow:0 0 .125em rgba(255,255,255,.5);\n}\n#my-soon-counter .soon-label {\n    font-weight: bold;\n    text-align: left;\n    margin-left:.5em;\n    margin-top:-1.5em;\n    font-size:.2em;\n}"}
    ];


    var lighthouse = [
        {"id":"group-spacing","attr":"layout","value":"tight"},
        {"id":"separator-character","attr":"separator","value":"/"},
        {"id":"background-color","attr":"$background-color","value":["#efe09b","1"]},
        {"id":"background-image","attr":"$background-image","value":"../media/lighthouse.jpg"},
        {"id":"background-position","attr":"$background-position","value":"top"},
        {"id":"text-color","attr":"$text-color","value":["#222657","1"]},
        {"id":"label-color-options","attr":"_label-color-options","value":"custom"},
        {"id":"custom-label-color","attr":"$label-color","value":["#823953","1"]},
        {"id":"font-family-type","attr":"_font-family","value":"list"},
        {"id":"font-family-list","attr":"$font-family","value":"Port+Lligat+Slab"},
        {"id":"face-type","attr":"face","value":"slot"},
        {"id":"whitespace","attr":"_whitespace","value":"custom"},
        {"id":"whitespace-top","attr":"$whitespace-top","value":"1"},
        {"id":"whitespace-right","attr":"$whitespace-right","value":"3"},
        {"id":"whitespace-bottom","attr":"$whitespace-bottom","value":"13"},
        {"id":"whitespace-left","attr":"$whitespace-left","value":"3"},
        {"id":"slot-animation-type","attr":"face","value":"roll"},
        {"id":"labels-casing","attr":"layout","value":"label-uppercase"},
        {"id":"labels-size","attr":"layout","value":"label-small"},
        {"id":"labels-position","attr":"layout","value":"label-above"}
    ];

    var reflectedBlue = [
        {"id":"separator-character","attr":"separator","value":"."},
        {"id":"reflection-state","attr":"reflect","value":true},
        {"id":"background-color","attr":"$background-color","value":["#5fc6fd","1"]},
        {"id":"text-color","attr":"$text-color","value":["#ffffff","1"]},
        {"id":"separator-color","attr":"_separator-style","value":"custom"},
        {"id":"custom-separator-color","attr":"$separator-color","value":["#1e99dd","1"]},
        {"id":"face-type","attr":"face","value":"slot"},
        {"id":"slot-animation-type","attr":"face","value":"slide"},
        {"id":"labels-position","attr":"layout","value":"label-hidden"}
    ];

    var flipper = [
        {"id":"background-color","attr":"$background-color","value":["#ffffff","1"]},
        {"id":"text-color","attr":"$text-color","value":["#929292","1"]},
        {"id":"face-type","attr":"face","value":"flip"},
        {"id":"flip-color","attr":"face","value":"color-dark"},
        {"id":"flip-corners","attr":"face","value":"corners-sharp"},
        {"id":"labels-casing","attr":"layout","value":"label-lowercase"},
        {"id":"labels-size","attr":"layout","value":"label-small"}
    ];

    var flipperWhite = [
        {"id":"background-color","attr":"$background-color","value":["#db4b3e","1"]},
        {"id":"text-color","attr":"$text-color","value":["#030303",".25"]},
        {"id":"font-family-type","attr":"_font-family","value":"list"},
        {"id":"font-family-list","attr":"$font-family","value":"Oswald"},
        {"id":"face-type","attr":"face","value":"flip"},
        {"id":"flip-color","attr":"face","value":"color-light"},
        {"id":"flip-shadow","attr":"face","value":"shadow-soft"},
        {"id":"flip-animation-speed","attr":"face","value":"fast"},
        {"id":"flip-corners","attr":"face","value":"corners-round"},
        {"id":"labels-casing","attr":"layout","value":"label-uppercase"}
    ];

    var flipperMerged = [
        {"id":"character-separation","attr":"separate-chars","value":false},
        {"id":"group-spacing","attr":"layout","value":"tight"},
        {"id":"reflection-state","attr":"reflect","value":true},
        {"id":"reflection-strength","attr":"$reflection-strength","value":".2"},
        {"id":"background-color","attr":"$background-color","value":["#d4d4d4","1"]},
        {"id":"background-image","attr":"$background-image","value":"../media/desert.jpg"},
        {"id":"background-position","attr":"$background-position","value":"bottom"},
        {"id":"text-color","attr":"$text-color","value":["#605951","1"]},
        {"id":"face-type","attr":"face","value":"flip"},
        {"id":"whitespace","attr":"_whitespace","value":"custom"},
        {"id":"whitespace-top","attr":"$whitespace-top","value":"1"},
        {"id":"whitespace-right","attr":"$whitespace-right","value":"1"},
        {"id":"whitespace-bottom","attr":"$whitespace-bottom","value":"8"},
        {"id":"whitespace-left","attr":"$whitespace-left","value":"1"},
        {"id":"flip-color","attr":"face","value":"color-dark"},
        {"id":"flip-corners","attr":"face","value":"corners-sharp"},
        {"id":"labels-size","attr":"layout","value":"label-small"},
        {"id":"labels-position","attr":"layout","value":"label-above"}
    ];

    var oranges = [
        {"id":"background-color","attr":"$background-color","value":["#ffffff","1"]},{"id":"text-color","attr":"$text-color","value":["#412d13","1"]},{"id":"label-color-options","attr":"_label-color-options","value":"custom"},{"id":"custom-label-color","attr":"$label-color","value":["#fa6c00","1"]},{"id":"font-family","attr":"$font-family","value":"Source+Sans+Pro:300"},{"id":"face-type","attr":"face","value":"slot"},{"id":"slot-animation-type","attr":"face","value":"roll"},{"id":"slot-animation-direction","attr":"face","value":"left"},{"id":"slot-animation-speed","attr":"face","value":"fast"},{"id":"visual-type","attr":"visual","value":"ring"},{"id":"ring-cap-style","attr":"visual","value":"cap-round"},{"id":"ring-invert","attr":"visual","value":"invert"},{"id":"ring-progress-color-type","attr":"_ring-progress-color-type","value":"gradient"},{"id":"ring-progress-color-gradient","attr":"visual","value":["#fb801b","#f1d935"]},{"id":"ring-background-color-type","attr":"_ring-background-color-type","value":"solid"},{"id":"ring-background-color","attr":"$ring-background-color-solid","value":["#f0f0f0","1"]},{"id":"ring-width","attr":"visual","value":"ring-width-custom"},{"id":"ring-progress-width","attr":"$ring-progress-width","value":"4"},{"id":"ring-background-width","attr":"$ring-background-width","value":"2"}
    ];

    var appleWatch = [
        {"id":"background-color","attr":"$background-color","value":["#000000","1"]},
        {"id":"text-color","attr":"$text-color","value":["#ffffff","1"]},
        {"id":"label-color-options","attr":"_label-color-options","value":"custom"},
        {"id":"custom-label-color","attr":"$label-color","value":["#c4c4c4","1"]},
        {"id":"font-family-type","attr":"_font-family","value":"list"},
        {"id":"font-family-list","attr":"$font-family","value":"Comfortaa"},
        {"id":"face-type","attr":"face","value":"slot"},
        {"id":"visual-type","attr":"visual","value":"ring"},
        {"id":"ring-cap-style","attr":"visual","value":"cap-round"},
        {"id":"ring-progress-color-type","attr":"_ring-progress-color-type","value":"gradient"},
        {"id":"ring-background-color-type","attr":"_ring-background-color-type","value":"solid"},
        {"id":"ring-width","attr":"visual","value":"ring-width-custom"},
        {"id":"ring-progress-width","attr":"$ring-progress-width","value":"14"},
        {"id":"ring-background-width","attr":"$ring-background-width","value":"13"},
        {"id":"ring-gap","attr":"visual","value":"0"},
        {"id":"labels-casing","attr":"layout","value":"label-uppercase"},
        {"id":"labels-size","attr":"layout","value":"label-small"}
    ];

    var futuristic = [
        {"id":"background-color","attr":"$background-color","value":["#ffffff","1"]},
        {"id":"text-color","attr":"$text-color","value":["#6e6e6e","1"]},
        {"id":"label-color-options","attr":"_label-color-options","value":"custom"},
        {"id":"custom-label-color","attr":"$label-color","value":["#c5c5c5","1"]},
        {"id":"font-family-type","attr":"_font-family","value":"list"},
        {"id":"font-family-list","attr":"$font-family","value":"Quicksand"},
        {"id":"face-type","attr":"face","value":"slot"},
        {"id":"slot-animation-type","attr":"face","value":"roll"},
        {"id":"visual-type","attr":"visual","value":"ring"},
        {"id":"ring-invert","attr":"visual","value":"invert"},
        {"id":"ring-progress-color-type","attr":"_ring-progress-color-type","value":"gradient"},
        {"id":"ring-progress-color-gradient","attr":"visual","value":["#a65fff","#659ee1"]},
        {"id":"ring-background-color-type","attr":"_ring-background-color-type","value":"solid"},
        {"id":"ring-background-color","attr":"$ring-background-color-solid","value":["#e7e7e7","1"]},
        {"id":"ring-width","attr":"visual","value":"width-thin"},
        {"id":"ring-gap","attr":"visual","value":"2"},
        {"id":"labels-casing","attr":"layout","value":"label-uppercase"},
        {"id":"labels-size","attr":"layout","value":"label-big"},
        {"id":"day-labels","attr":"labels-days","value":["D"]},
        {"id":"hour-labels","attr":"labels-hours","value":["H"]},
        {"id":"minute-labels","attr":"labels-minutes","value":["M"]},
        {"id":"second-labels","attr":"labels-seconds","value":["S"]}
    ];

    var isolation = [
        {"id":"group-spacing","attr":"layout","value":"tight"},
        {"id":"background-color","attr":"$background-color","value":["#183216","1"]},
        {"id":"text-color","attr":"$text-color","value":["#77d671","1"]},
        {"id":"label-color-options","attr":"_label-color-options","value":"custom"},
        {"id":"custom-label-color","attr":"$label-color","value":["#356b2d","1"]},
        {"id":"font-family-type","attr":"_font-family","value":"list"},
        {"id":"font-family-list","attr":"$font-family","value":"Source+Sans+Pro:300"},
        {"id":"face-type","attr":"face","value":"text"},
        {"id":"visual-type","attr":"visual","value":"ring"},
        {"id":"ring-progress-color-type","attr":"_ring-progress-color-type","value":"solid"},
        {"id":"ring-progress-color-solid","attr":"$ring-progress-color-solid","value":["#80e67a","1"]},
        {"id":"ring-background-color-type","attr":"_ring-background-color-type","value":"solid"},
        {"id":"ring-background-color","attr":"$ring-background-color-solid","value":["#295224","1"]},
        {"id":"ring-width","attr":"visual","value":"ring-width-custom"},
        {"id":"ring-progress-width","attr":"$ring-progress-width","value":"8"},
        {"id":"ring-background-width","attr":"$ring-background-width","value":"2"},
        {"id":"ring-align","attr":"visual","value":"align-bottom"},
        {"id":"ring-gap","attr":"visual","value":"1"},
        {"id":"labels-casing","attr":"layout","value":"label-lowercase"}
    ];

    var greenSquad = [
        {"id":"background-color","attr":"$background-color","value":["#262e47","1"]},
        {"id":"text-color","attr":"$text-color","value":["#ffffff","1"]},
        {"id":"label-color-options","attr":"_label-color-options","value":"custom"},
        {"id":"custom-label-color","attr":"$label-color","value":["#4c5c81","1"]},
        {"id":"font-family-type","attr":"_font-family","value":"list"},
        {"id":"font-family-list","attr":"$font-family","value":"Squada+One"},
        {"id":"face-type","attr":"face","value":"slot"},
        {"id":"slot-shadow","attr":"face","value":"glow"},
        {"id":"visual-type","attr":"visual","value":"ring"},
        {"id":"ring-cap-style","attr":"visual","value":"cap-round"},
        {"id":"ring-progress-color-type","attr":"_ring-progress-color-type","value":"solid"},
        {"id":"ring-progress-color-solid","attr":"$ring-progress-color-solid","value":["#00fa91","1"]},
        {"id":"ring-background-color-type","attr":"_ring-background-color-type","value":"solid"},
        {"id":"ring-background-color","attr":"$ring-background-color-solid","value":["#ffffff","1"]},
        {"id":"ring-progress-shadow","attr":"visual","value":"glow-progress"},
        {"id":"ring-background-shadow","attr":"visual","value":"glow-background"},
        {"id":"ring-width","attr":"visual","value":"ring-width-custom"},
        {"id":"ring-progress-width","attr":"$ring-progress-width","value":"3"},
        {"id":"ring-background-width","attr":"$ring-background-width","value":"4"},
        {"id":"ring-align","attr":"visual","value":"align-center"},
        {"id":"ring-gap","attr":"visual","value":"2.5"},
        {"id":"labels-casing","attr":"layout","value":"label-lowercase"}
    ];

    var matrixSunny = [
        {"id":"background-color","attr":"$background-color","value":["#fffaf3","1"]},
        {"id":"text-color","attr":"$text-color","value":["#6e6e6e","1"]},
        {"id":"label-color-options","attr":"_label-color-options","value":"custom"},
        {"id":"custom-label-color","attr":"$label-color","value":["#ffe02b","1"]},
        {"id":"font-family-type","attr":"_font-family","value":"list"},
        {"id":"font-family-list","attr":"$font-family","value":"Quicksand"},
        {"id":"face-type","attr":"face","value":"matrix"},
        {"id":"dot-matrix-animation-direction","attr":"face","value":"slide up"},
        {"id":"dot-matrix-dot-on-color-type","attr":"_matrix_on_color","value":"gradient-ttb"},
        {"id":"dot-matrix-dot-on-color-gradient-to-bottom","attr":"$dot-on-gradient-ttb","value":["#fb1a1b","#ffdf42"]},
        {"id":"dot-matrix-dot-off-color-type","attr":"_matrix_off_color","value":"single"},
        {"id":"dot-matrix-dot-off-color-solid","attr":"$dot-off-color","value":["#ffffff","0"]},
        {"id":"labels-casing","attr":"layout","value":"label-uppercase"},
        {"id":"labels-size","attr":"layout","value":"label-small"}
    ];

    var matrixNeon = [
        {"id":"background-color","attr":"$background-color","value":["#484745","1"]},
        {"id":"text-color","attr":"$text-color","value":["#6e6e6e","1"]},
        {"id":"label-color-options","attr":"_label-color-options","value":"custom"},
        {"id":"custom-label-color","attr":"$label-color","value":["#353535","1"]},
        {"id":"font-family-type","attr":"_font-family","value":"list"},
        {"id":"font-family-list","attr":"$font-family","value":"Chivo"},
        {"id":"face-type","attr":"face","value":"matrix"},
        {"id":"dot-matrix-spacing","attr":"face","value":"spacey"},
        {"id":"dot-matrix-dot-shape","attr":"face","value":"dot-square"},
        {"id":"dot-matrix-dot-on-color-type","attr":"_matrix_on_color","value":"gradient-ttb"},
        {"id":"dot-matrix-dot-on-color-gradient-to-bottom","attr":"$dot-on-gradient-ttb","value":["#5afbb7","#08f5ff"]},
        {"id":"dot-matrix-dot-off-color-type","attr":"_matrix_off_color","value":"single"},
        {"id":"dot-matrix-dot-off-color-solid","attr":"$dot-off-color","value":["#0b2051","0"]},
        {"id":"labels-casing","attr":"layout","value":"label-uppercase"},
        {"id":"labels-size","attr":"layout","value":"label-small"}
    ];

    var glowRing = [
        {"id":"group-spacing","attr":"layout","value":"overlap"},
        {"id":"background-color","attr":"$background-color","value":["#f13446","1"]},
        {"id":"text-color","attr":"$text-color","value":["#ffffff","1"]},
        {"id":"font-family-type","attr":"_font-family","value":"list"},
        {"id":"font-family-list","attr":"$font-family","value":"Quicksand"},
        {"id":"face-type","attr":"face","value":"slot"},
        {"id":"slot-animation-type","attr":"face","value":"doctor"},
        {"id":"slot-shadow","attr":"face","value":"glow"},
        {"id":"visual-type","attr":"visual","value":"ring"},
        {"id":"ring-progress-color-type","attr":"_ring-progress-color-type","value":"solid"},
        {"id":"ring-progress-color-solid","attr":"$ring-progress-color-solid","value":["#ffffff","1"]},
        {"id":"ring-background-color-type","attr":"_ring-background-color-type","value":"solid"},
        {"id":"ring-background-color","attr":"$ring-background-color-solid","value":["#ffffff",".2"]},
        {"id":"ring-progress-shadow","attr":"visual","value":"glow-progress"},
        {"id":"ring-width","attr":"visual","value":"width-thin"},
        {"id":"ring-offset","attr":"visual","value":"65"},
        {"id":"ring-length","attr":"visual","value":"70"},
        {"id":"labels-casing","attr":"layout","value":"label-lowercase"},
        {"id":"css-overrides","attr":"$css-overrides","value":"#my-soon-counter {\n    background-image:linear-gradient(30deg, #F13B6F,#FC9E2C);\n}\n#my-soon-counter .soon-label {\n    text-shadow:0 0 .25rem rgba(255,255,255,.75);\n}"}
    ];

    var squareBlue = [
        {"id":"reflection-state","attr":"reflect","value":true},
        {"id":"background-color","attr":"$background-color","value":["#153255","1"]},
        {"id":"text-color","attr":"$text-color","value":["#ffffff","1"]},
        {"id":"label-color-options","attr":"_label-color-options","value":"custom"},
        {"id":"custom-label-color","attr":"$label-color","value":["#0d2949","1"]},
        {"id":"font-family-type","attr":"_font-family","value":"list"},
        {"id":"font-family-list","attr":"$font-family","value":"Oswald"},
        {"id":"face-type","attr":"face","value":"slot"},
        {"id":"slot-animation-type","attr":"face","value":"slide"},
        {"id":"slot-animation-direction","attr":"face","value":"up"},
        {"id":"slot-shadow","attr":"face","value":"glow"},
        {"id":"visual-type","attr":"visual","value":"fill"},
        {"id":"fill-direction","attr":"visual","value":"to-top"},
        {"id":"fill-corner-style","attr":"visual","value":"corners-sharp"},
        {"id":"fill-background-color","attr":"$fill-background-color","value":["#ffffff","0"]},
        {"id":"fill-foreground-color","attr":"$fill-foreground-color","value":["#1e4a7f","1"]},
        {"id":"labels-casing","attr":"layout","value":"label-uppercase"}
    ];

    return [
        {
            name:'Blank Canvas',
            values:[]
        },
        {
            name:'New York',
            values:newYork
        },
        {
            name:'Lighthouse',
            values:lighthouse
        },
        {
            name:'Reflected Blue',
            values:reflectedBlue
        },
        {
            name:'Basic Flipper',
            values:flipper
        },
        {
            name:'Bright Flipper',
            values:flipperWhite
        },
        {
            name:'Panel Flipper',
            values:flipperMerged
        },
        {
            name:'Square Blue',
            values:squareBlue
        },
        {
            name:'Oranges',
            values:oranges
        },
        {
            name:'Glow',
            values:glowRing
        },
        {
            name:'Apple Watch Red',
            values:appleWatch.concat([
                {"id":"ring-progress-color-gradient","attr":"visual","value":["#ff1000","#fc07ba"]},
                {"id":"ring-background-color","attr":"$ring-background-color-solid","value":["#470e1d","1"]}
            ])
        },
        {
            name:'Apple Watch Blue',
            values:appleWatch.concat([
                {"id":"ring-progress-color-gradient","attr":"visual","value":["#00fff6","#075fff"]},
                {"id":"ring-background-color","attr":"$ring-background-color-solid","value":["#162453","1"]}
            ])
        },
        {
            name:'Apple Watch Green',
            values:appleWatch.concat([
                {"id":"ring-progress-color-gradient","attr":"visual","value":["#98fc04","#f3fc02"]},
                {"id":"ring-background-color","attr":"$ring-background-color-solid","value":["#304407","1"]}
            ])
        },
        {
            name:'Alien Isolation',
            values:isolation
        },
        {
            name:'Futuristic',
            values:futuristic
        },
        {
            name:'Green Squad',
            values:greenSquad
        },
        {
            name:'Sunny Matrix',
            values:matrixSunny
        },
        {
            name:'Neon Matrix',
            values:matrixNeon
        }
    ]

}());
