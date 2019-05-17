<style>

    #wpcontent {
        padding-left:0;
    }

    .builder-code {
        left: 15rem;
        right: 15rem;
    }

    .builder-form {
        max-height: 500px;
    }

    .builder {
        direction:ltr;
    }

</style>

<div class="builder">

    <div class="builder-demo"></div>

    <form class="builder-presets" action="/"></form>

    <form class="builder-form" action="/">

        <h1></h1>

        <div class="btn-group"></div>

        <div class="inner"></div>

    </form>

</div>
<script>

    var soonServerTime = '<?php echo date('c');?>';

    var soonId = 'my-soon-counter';

    var soonOptionModifier = function(options) {
        if (options['now'] === '{{ date }}') {
            options['now'] = soonServerTime;
        }
    };

    var soonAdditions = function(groups){

        // server time field
        groups[0].fields[6].options.push({
            'label':'Server Time',
            'value':'server'
        });

        groups[0].fields.push({
            attr:'now',
            dep:{
                id:'now-type',
                value:'server'
            },
            id:'now-server',
            label:'',
            value:'{{ date }}',
            type:'hidden'
        });

    };

    var soonGenerateSnippets = function(data) {
        var i=0,l=data.custom.length;
        for(;i<l;i++) {
            data.custom[i].snippet = soonBuilder.getSnippetByValues(data.custom[i].values);
        }
    };

    var soonGeneratorFormatter = function(container,source,attributes) {

        var due = attributes['due'];
        var since = attributes['since'];
        var time = '';

        if (due) {
            time = ' due="' + due + '"';
        }
        if (since) {
            time = ' since="' + since + '"';
        }

        var name = document.getElementById('presets').value;
        container.innerHTML = '[soon name="'+ name + '"' + time + '][/soon]';

    };

    var soonStorage = {

        save:function(data,cb) {

            soonGenerateSnippets(data);

            jQuery.ajax({
                type:'post',
                url:ajaxurl,
                data:{
                    'action':'soon_storage_save',
                    'data':JSON.stringify(data)
                },
                success:function(){
                    cb();
                }
            });

        },

        load:function(cb){
            jQuery.ajax({
                url:ajaxurl,
                data:{
                    'action':'soon_storage_load'
                },
                success:function(data){
                    try {
                        data = JSON.parse(data);
                    }
                    catch(e){}
                    cb(data);
                }
            });

        }

    };

    var soonInit = function() {

        var presets = document.getElementById('presets');
        var selected = presets.selectedIndex;
        var custom = presets.children[0];
        var customCount = custom ? custom.children.length-1 : 0;
        var isPreset = selected > customCount;

        document.querySelector('.btn-copy').style.display = isPreset ? 'none' : '';

    };

</script>