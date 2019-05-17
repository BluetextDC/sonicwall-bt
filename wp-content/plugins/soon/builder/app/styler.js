var soonStyler = (function(soonId){

    var getColorBetween = function (color1, color2, percent) {

        var newColor = {};

        function makeChannel(a, b) {
            return(a + Math.round((b-a)*percent));
        }

        function makeColorPiece(num) {
            num = Math.min(num, 255);   // not more than 255
            num = Math.max(num, 0);     // not less than 0
            var str = num.toString(16);
            if (str.length < 2) {
                str = '0' + str;
            }
            return(str);
        }

        return('#' +
            makeColorPiece(makeChannel(color1.r, color2.r)) +
            makeColorPiece(makeChannel(color1.g, color2.g)) +
            makeColorPiece(makeChannel(color1.b, color2.b))
        );

    };

    var hexToRgb = function(hex) {
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    };

    var rgbToHex = function(c) {
        return (c.r).toString(16) + (c.g).toString(16) + (c.b).toString(16);
    };

    var getRGBAValue = function(values) {
        var color = hexToRgb(values[0]);
        if (typeof values[1] === 'undefined') {
            color.a = 1;
        }
        else {
            color.a = parseFloat(values[1]);
        }
        return color;
    };

    var getRGBAString = function(value) {
        var c = getRGBAValue(value);
        return 'rgba(' + c.r + ',' + c.g + ',' + c.b + ',' + c.a + ')';
    };

    var select = function(target,properties) {
        return '\n#' + soonId + ' ' + (target ? target + ' ' : '') + '{' + properties.join('') + '}';
    };

    var prop = function(property,values) {
        return values.map(function(value){return property + ':' + value + ';';}).join('');
    };

    var hexColor = function(property,hex) {
        return prop(property,[hex]);
    };

    var color = function(property,color) {
        var values = [color[0]];
        if (color[1]!=='1') {
            values.push(getRGBAString(color));
        }
        return prop(property,values);
    };

    var dotGradient = function(selector,colors,interval) {

        var from = hexToRgb(colors[0]);
        var to = hexToRgb(colors[1]);

        // calculate in betweens
        var steps = [];

        var i=0,l=interval,s=1/(l-1),p=0;
        for(;i<l;i++) {
            steps[i] = getColorBetween(from,to,p);
            p += s;
        }

        return steps.map(function(step,index){
            return select(selector.replace('$index',index+1),[hexColor('background-color',step)]);
        }).join('');

    };

    var rules = {
        '$background-color':function(value) {

            // background
            var selectors = select(null,[color('background-color',value)]);

            // reflection
            selectors += select('.soon-reflection',[
                prop('background-color',[value[0]]),
                prop('background-image',['linear-gradient(' + value[0] + ' 25%,' + getRGBAString([value[0],0]) + ')'])
            ]);

            return selectors;
        },
        '$text-color':function(value) {
            return select(null,[color('color',value)]);
        },
        '$label-color':function(value) {
            return select('.soon-label',[color('color',value)]);
        },
        '$separator-color':function(value) {
            return select('.soon-separator',[color('color',value)]);
        },
        '$ring-background-color-solid':function(value) {
            return select('.soon-ring-progress',[color('background-color',value)]);
        },
        '$ring-progress-color-solid':function(value) {
            return select('.soon-ring-progress',[color('color',value)]);
        },
        '$ring-progress-width':function(value) {
            value = (1/40) * value;
            return select('.soon-ring-progress',[prop('border-top-width',[value + 'em'])])
        },
        '$ring-background-width':function(value) {
            value = (1/40) * value;
            return select('.soon-ring-progress',[prop('border-bottom-width',[value + 'em'])])
        },
        '$flip-background-color':function(value) {
            return select('.soon-flip-face',[hexColor('background-color',value)]) + select('.soon-flip-fallback',[hexColor('background-color',value)]);
        },
        '$flip-text-color':function(value) {
            return select('.soon-flip-face',[hexColor('color',value)]) + select('.soon-flip-fallback',[hexColor('color',value)]);
        },
        '$reflection-strength':function(value) {
            return select('.soon-reflection',[prop('opacity',[value])]);
        },
        '$dot-on-color':function(value) {
            return select('.soon-matrix-dot[data-state=\'1\']',[color('background-color',value)]);
        },
        '$dot-off-color':function(value) {
            return select('.soon-matrix-dot[data-state=\'0\']',[color('background-color',value)]);
        },
        '$dot-on-gradient-ltr':function(value){
            return dotGradient('.soon-matrix-dot[data-state=\'1\']:nth-child($index)',value,5);
        },
        '$dot-on-gradient-ttb':function(value){
            return dotGradient('.soon-matrix-row:nth-child($index) .soon-matrix-dot[data-state=\'1\']',value,7);
        },
        '$dot-off-gradient-ltr':function(value){
            return dotGradient('.soon-matrix-dot[data-state=\'0\']:nth-child($index)',value,5);
        },
        '$dot-off-gradient-ttb':function(value){
            return dotGradient('.soon-matrix-row:nth-child($index) .soon-matrix-dot[data-state=\'0\']',value,7);
        },
        '$fill-foreground-color':function(value) {
            return select('.soon-fill-progress',[color('background-color',value)]);
        },
        '$fill-background-color':function(value) {
            return select('.soon-fill-inner',[color('background-color',value)]);
        },
        '$font-family':function(value) {

            var parts = value.split(':');

            var family = parts[0];
            var variant = parts[1] || '';

            var font = family.replace(/\+/g, ' ');

            var weight = variant.match(/[\d]+/);
                weight = weight ? weight[0] : 'normal';

            var italic = weight ? /italic/.test(variant) ? 'italic' : 'normal' : 'normal';

            return [
                select(null,[
                    prop('font-family',['"' + font + '",sans-serif']),
                    prop('font-weight',[weight]),
                    prop('font-style',[italic])
                ])
            ];

        },
        '$css-overrides':function(value) {
            return '\n' + value;
        },
        '$background-image':function(value) {
            return select(null,[
                prop('background-image',['url(' + value + ')']),
                prop('background-size',['cover'])
            ]);
        },
        '$background-position':function(value) {
            return select(null,[
                prop('background-position',[value])
            ]);
        },
        '$whitespace-top':function(value){
            return select(null,[prop('padding-top',[value + 'rem'])]);
        },
        '$whitespace-right':function(value){
            return select(null,[prop('padding-right',[value + 'rem'])]);
        },
        '$whitespace-bottom':function(value){
            return select(null,[prop('padding-bottom',[value + 'rem'])]);
        },
        '$whitespace-left':function(value){
            return select(null,[prop('padding-left',[value + 'rem'])]);
        }
    };

    return {

        getStyles:function(options) {

            var styles = options
                .map(function(option){
                    var rule = rules[option.attr];
                    if (rule) {
                        return rule(option.value);
                    }
                    return '';
                })
                .join('');

            return styles.trim();

        }
    }

}(soonId));