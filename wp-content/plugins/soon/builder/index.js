(function () {

    if (tinymce.majorVersion === '4') {

        tinymce.PluginManager.add('soon', function (editor, url) {

            editor.addButton('soon_counters', {

                icon: 'soon',
                onclick: function () {

                    editor.windowManager.open({
                        title: 'Soon Counters',
                        body: [
                            {
                                type: 'listbox',
                                name: 'counters',
                                label: 'Counters',
                                values: soonCounterData
                            },
                            {
                                type: 'fieldset',
                                label: 'Date overrides',
                                items: [
                                    {
                                        type: 'label',
                                        multiline: true,
                                        onPostRender: function () {
                                            this.getEl().innerHTML = 'Use these optional fields to override the due or since date<br> per counter, this allows you to use a single counter template<br> with multiple dates.';
                                        },
                                        text: ''
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'due',
                                        label: 'Due Date',
                                        tooltip: 'Due date in ISO Format',
                                        onPostRender: function () {
                                            this.getEl().setAttribute('placeholder', 'e.g. 2015-05-28T05:15');
                                        }
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'since',
                                        label: 'Since Date',
                                        tooltip: 'Since date in ISO Format',
                                        onPostRender: function () {
                                            this.getEl().setAttribute('placeholder', 'e.g. 2015-05-28T05:15');
                                        }
                                    }
                                ]
                            }
                        ],
                        onsubmit: function (e) {

                            var props = [];
                            props.push('name="' + e.data.counters + '"');

                            if (e.data.due) {
                                props.push('due="' + e.data.due + '"');
                            }

                            if (e.data.since) {
                                props.push('since="' + e.data.since + '"');
                            }

                            editor.insertContent('[soon ' + props.join(' ') + '][/soon]');
                        }

                    });
                }

            });

        });
        return;
    }

    tinymce.create('tinymce.plugins.Soon', {

        init: function (ed, url) {

            ed.addButton('soon_counters', {
                title: 'Soon',
                cmd: 'get_shortcode',
                image: url + '/../icon.png'
            });

            ed.addCommand('get_shortcode', function () {
                var name = prompt('Enter the name of the counter you want to insert.');
                ed.execCommand('mceInsertContent', 0, '[soon name="' + name + '"] [/soon]');
            });

        },

        createControl: function (n, cm) {
            return null;
        },

        getInfo: function () {
            return {
                longname: 'Soon',
                author: 'pqina',
                authorurl: 'https://pqina.nl',
                infourl: 'https://pqina.nl',
                version: '1.12.1'
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('soon', tinymce.plugins.Soon);

})();