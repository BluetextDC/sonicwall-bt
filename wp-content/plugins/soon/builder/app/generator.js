
var soonGenerator = (function(formatter){

    // code popup
    var popover = document.createElement('div');
    popover.className = 'builder-code';
    document.body.appendChild(popover);

    var btnClose = document.createElement('button');
    btnClose.addEventListener('click',function(){
        popover.setAttribute('data-state','off');
    });
    btnClose.innerHTML = '&times;';
    popover.appendChild(btnClose);

    // area
    var pre = document.createElement('pre');
    pre.innerHTML = '<code class="html"></code>';
    popover.appendChild(pre);

    // add reset button
    var buttons = document.querySelector('.btn-group');
    var code = document.createElement('button');
    code.addEventListener('click',function(e){
        e.preventDefault();
        e.stopPropagation();
        popover.setAttribute('data-state','on');
    });
    code.setAttribute('type','button');
    code.className = 'btn-copy';
    code.textContent = 'Get Snippet';
    buttons.appendChild(code);

    return {

        generate:function(attributes,styles,scripts) {

            // to string
            var attributeString = '';
            for(var attr in attributes) {
                if (!attributes.hasOwnProperty(attr)){continue;}
                attributeString += 'data-' + attr + '="' + attributes[attr] + '"\n     ';
            }

            // set styles
            var source = '';

            // scripts
            if (scripts.length) {
                source += '<script type="text/javascript">\n' + scripts + '\n<\/script>\n';
            }

            // styles
            source += '<style type="text/css">\n' + styles + '\n<\/style>\n';

            // set html
            source += '<div class="soon" id="' + soonId + '"\n     ' + attributeString.trim() +'>\n<\/div>';

            // set code
            soonGeneratorFormatter(pre.firstChild,source,attributes);

            return source;
        }

    };

}(soonGeneratorFormatter));
