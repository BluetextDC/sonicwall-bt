var soonRenderer = (function(soonId,soonOptionModifier,Soon){

    if (!Soon){return;}

    var capitaliseFirstLetter = function(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    };

    var transformPropertyName = function(name) {
        return name.split('-').map(function(part,index){
            return index > 0 ? capitaliseFirstLetter(part) : part;
        }).join('');
    };

    var timer = null;

    // get test color value
    var testNode = document.createElement('div');
    testNode.style.color = 'transparent';
    var testColor = window.getComputedStyle(testNode).getPropertyValue('color');

    // expose api
    return {

        render:function(attributes,styles,font) {

            // stop the timer
            clearTimeout(timer);



            // remove previous counter
            var wrapper = document.querySelector('.builder-demo');

            var soonNode = wrapper.querySelector('div');
            if (soonNode) {
                if (soonNode.classList.contains('soon')) {
                    Soon.destroy(soonNode);
                }
                wrapper.firstChild.parentNode.removeChild(soonNode);
            }

            // set new styles
            var styleText = document.createTextNode(styles);
            var style = wrapper.querySelector('style');
            if (!style) {
                style = document.createElement('style');
                style.type = 'text/css';
                wrapper.appendChild(style);
                style.appendChild(styleText);
            }
            else {
                style.replaceChild(styleText,style.firstChild);
            }

            // do data transforms
            var options = {};
            for(var attr in attributes) {
                if (!attributes.hasOwnProperty(attr)){continue;}
                options[transformPropertyName(attr)] = attributes[attr];
            }

            // modify options
            if (soonOptionModifier){
                soonOptionModifier(options);
            }

            // create new countdown
            var node = document.createElement('div');
            node.id = soonId;
            wrapper.appendChild(node);

            // wrapped in timeout as browser might need a
            // little bit of time to parse styles...
            this._create(node,options,font);

        },

        _loadFont:function(font) {

            if (!font) {
                return;
            }

            // setup google fonts
            if (!window.WebFont) {
                window.WebFontConfig = {
                    google: { families: [font] }
                };
                (function() {
                    var wf = document.createElement('script');
                    wf.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
                    wf.type = 'text/javascript';
                    wf.async = 'true';
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(wf, s);
                })();
            }
            else {
                window.WebFont.load({
                    google:{
                        families: [font]
                    }
                });
            }

        },

        _create:function(node,options,font) {

            var color = window.getComputedStyle(node).getPropertyValue('color');
            if (color !== testColor) {
                Soon.create(node,options);

                // load font
                this._loadFont(font);
            }
            else {
                var self = this;
                setTimeout(function() {
                    self._create(node, options);
                },10);
            }

        }

    }

}(soonId,window['soonOptionModifier'],Soon));