var soonBuilder = (function($,groups,styler,renderer,generator,presets,storage,initCb){
    
        window['soonCompleteCallback'] = function() {};
    
        var colorInputSupported = (function(){
            var i = document.createElement('input');
            i.setAttribute('type', 'color');
            return i.type !== 'text';
        }());
    
        var dateInputSupported = (function(){
            var i = document.createElement('input');
            i.setAttribute('type', 'date');
            return i.type !== 'text';
        }());
    
        var timeInputSupported = (function(){
            var i = document.createElement('input');
            i.setAttribute('type', 'time');
            return i.type !== 'text';
        }());
    
    
        // convert font family entries
        var Convertor = {
    
            convert:function(o,n,obj) {
                if (o === '1.4.0' && n === '1.7.0') {
                    return this.convert140To170(obj);
                }
                return obj;
            },
    
            convert140To170:function(obj) {
    
                obj.custom = obj.custom.map(function(entry){
    
                    var used = entry.values.filter(function(value){
                        return value.attr === '$font-family';
                    });
    
                    if (used.length) {
                        used[0].id = 'font-family-list';
                        entry.values.push(
                            {"id":"font-family-type","attr":"_font-family","value":"list"}
                        );
                    }
    
                    return entry;
    
                });
    
                obj.version = '1.7.0';
    
                return obj;
            }
    
        };
    
        /**
         * DataManager Obj
         */
        var DataManager = {
    
            _data:{
                version:'1.7.0',
                active:0,
                custom:[
                    {
                        name:'Default',
                        values:[]
                    }
                ]
            },
            _presets:presets,
    
            init:function(cb) {
    
                var self = this;
    
                storage.load(function(data){
    
                    // if no data received or is old data entry
                    if(!data || !data.version){cb();return;}
    
                    // continue
                    try {
                        self._data = Convertor.convert(data.version,DataManager._data.version,data);
                    }
                    catch(e) {
                        self._data = data;
                    }
    
                    // ready
                    cb();
                });
    
            },
    
            _getEntries:function(){
                return this._data.custom.concat(this._presets);
            },
    
            _isPreset:function(entry) {
                return this._presets.indexOf(entry)!==-1;
            },
    
            isPresetSelected:function() {
                return this._isPreset(this.getActiveEntry());
            },
    
            addEntryWithName:function(name,values,cb) {
    
                // if name already exists stop here
                if (this._data.custom.filter(function(entry){return entry.name === name}).length) {
                    cb('duplicate');
                    return;
                }
    
                // create new custom entry
                this._data.custom.push({
                    name:name,
                    values:values
                });
    
                // select the new entry
                this._data.active = this._data.custom.length-1;
    
                // store new state
                this.save(null,cb);
    
                return true;
            },
    
            removeEntry:function(cb) {
    
                // cannot reset presets
                if (this.isPresetSelected()) {return;}
    
                // define next index to be selected
                // - if last item is selected move to last item minus 1
                var nextActive;
                if (this._data.active === this._data.custom.length-1) {
                    nextActive = Math.max(0,this._data.custom.length-2);
                }
                else {
                    nextActive = this._data.active;
                }
    
                // delete the current entry
                this._data.custom.splice(this._data.active,1);
    
                this._data.active = nextActive;
    
                this.save(null,cb);
    
            },
    
            setActiveIndex:function(index,cb) {
                this._data.active = index;
                this.load(index,FieldManager.setFieldValues);
                this.save(null,cb);
            },
    
            getActiveIndex:function() {
                return this._data.active;
            },
    
            getActiveEntry:function() {
                return this.getEntryByIndex(this.getActiveIndex());
            },
    
            getEntryByIndex:function(index) {
                return this._getEntries()[index];
            },
    
            getEntryList:function(){
                return [
                    {
                        name:'Custom',
                        items:this._data.custom
                    },
                    {
                        name:'Presets',
                        items:this._presets
                    }
                ]
            },
    
            // saves presets
            save:function(values,cb) {
    
                // set values for active preset
                if (values) {
                    var entry = this.getActiveEntry();
                        entry.values = values;
                }
    
                // store current data
                storage.save(this._data,cb);
            },
    
            // load data by preset index
            load:function(index,cb) {
    
                // get data object from presets
                var entry = this.getEntryByIndex(index || this._data.active);
    
                // if no entry found, stop here
                if(!entry) {
                    return;
                }
    
                // load data object to fields
                cb(entry.values);
    
            }
            
        };
    
    
        
        var FieldManager = {
    
            _fields:[],
            _hash:[],
            
            setFields:function(fields) {
    
                var i= 0,l = fields.length;
                for(;i<l;i++) {
                    this._hash[fields[i].id] = fields[i];
                }
                this._fields = fields;
    
            },
            
            setFieldValues:function(values) {
                var i= 0,l=values.length;
                if (!l){return;}
                FieldManager._fields.forEach(function(field){
                    for (i=0;i<l;i++) {
                        if (field.id === values[i].id) {
                            field.value = values[i].value;
                        }
                    }
                });
            },
    
            getFieldById:function(id){
                return this._hash[id];
            },
    
            getFieldsByAttr:function(attr) {
                return this._fields.filter(function(field){
                    return field.attr === attr;
                });
            },
    
            toValueObjects:function() {
    
                // convert to field <> value objects
                var values = this._fields.map(function(field) {
                    return {
                        'id':field.id,
                        'attr':field.attr,
                        'value':getFieldValue(field)
                    };
                })
    
                // remove invalid fields
                .filter(function(value){
                    return isValidFieldData(value.value,value.id);
                });
    
                return values;
            },
    
            getFieldDependency:function(field) {
                if (field.dep) {
                    return this.getFieldById(field.dep.id);
                }
                return null;
            }
    
        };
        
    
        var isValidFieldData = function(value,fieldId) {
    
            var field = FieldManager.getFieldById(fieldId);
    
            var node = document.getElementById(field.id);
            if (node && node.offsetWidth ===0) {
                return false;
            }
    
            if (typeof value === 'string') {
                return value.length > 0;
            }
    
            return !(typeof value === 'undefined' || value === null)
        };
    
        var isPrivateField = function(field) {
            return field.attr.charAt(0) === '_';
        };
    
        var isCSSField = function(field) {
            return field.attr.charAt(0) === '$';
        };
    
        // returns field HTML
        var getFieldHTML = function(field) {
        
            // correct due-day fields
            if (field.id === 'due-day' && field.value && field.value.length === 3) {
                field.value.splice(2, 0, undefined);
            }
            // get unique id for each field
            //field.id = 'field_' + uid++;
    
            // render field group
            if (field.fields) {
                return '<fieldset class="group'+ (field.label ? '' : ' no-legend') +'" id="' + field.id + '">' + (field.label ? '<legend>' + field.label +' </legend>' : '') + field.fields.map(getFieldHTML).join('') + '</fieldset>';
            }
    
            // render sub fields
            if (field.subfields) {
                return '<fieldset class="form-item" id="' + field.id + '"><legend>' + field.label + '</legend><div>' + field.subfields.map(function(subfield,index){
    
                    // store field id
                    //subfield.id = field.id + '_' + index;
    
                    // set value
                    if (field.value) {
                        subfield.value = field.value[index];
                    }
    
                    // get field html
                    return getFieldHTML(subfield);
    
                }).join('') + '</div></fieldset>';
            }
    
            // render base label
            var label = '<div class="form-item" id="' + field.id + '"><label for="field_' + field.id + '">' + field.label +'</label>';
            var base = 'id="field_' + field.id + '" name="' + field.id + '"';
            var options;
    
            // render textarea
            if (field.type === 'textarea') {
                return label + '<textarea ' + base + ' placeholder="' + field.placeholder + '">' + (field.value || '') + '</textarea>';
            }
    
            // render select
            if (field.type === 'select') {
                options = field.options.map(function(option){
                    return '<option value="' + option.value + '"' + (field.value + '' === option.value ? ' selected'  : '') + '>' + option.label + '</option>';
                }).join('');
                return label + '<select ' + base + '>' + options + '</select></div>';
            }
    
            // checkbox list
            if (field.type === 'checkboxes') {
                options = field.options.map(function(option){
                    return '<label class="checkable"><input type="checkbox" value="' + option.value + '"' + (field.value.indexOf(option.value) !== -1 ? ' checked'  : '') + '>' + option.label + '</label>';
                }).join('');
                return '<fieldset class="form-item" id="' + field.id + '"><legend>' + field.label + '</legend><div>' + options + '</div></fieldset>';
            }
    
            // get custom properties
            var prop,props = '';
            for(prop in field.props) {
                props+= prop + '="' + field.props[prop] + '" ';
            }
    
            // return the actual field
            return label + '<input type="' + (field.type || 'text') + '"' + (field.value ? ' value="' + field.value + '" ' : '') + '' + base + ' ' + props.trim() + '/></div>';
    
        };
    
        // creates a new counter with a given name
        var createCounterWithValues = function(values,cb) {
    
            // get name
            var name = prompt('Please enter a name for your counter.');
            name = name ? name.trim() : name;
            if (!name || !name.length) {
                alert('Name cannot be empty.');
                return false;
            }
    
            // add entry
            DataManager.addEntryWithName(name,values,function(state){
    
                if (state === 'duplicate') {
                    alert('A counter with the name "' + name + '" already exists.');
                }
    
                cb(state !== 'duplicate');
    
            });
    
        };
    
    
    
    
        var PresetSelect = {
    
            _root:null,
    
            init:function() {
    
                this._root = document.querySelector('.builder-presets');
                this._root.className = 'builder-presets';
    
                // or
                var presetLabel = document.createElement('label');
                presetLabel.className = 'or';
                presetLabel.textContent = 'Select a counter preset';
                presetLabel.setAttribute('for','presets');
                this._root.appendChild(presetLabel);
    
                // create preset dropdown
                var select = document.createElement('select');
                select.id = 'presets';
                var entries = DataManager.getEntryList();
    
                // setup select options
                select.innerHTML = entries.map(function(group){
                    if (!group.items.length) {return null;}
                    return '<optgroup label="' + group.name + '">' + group.items.map(function(item){
                        return '<option value="' + item.name + '">' + item.name + '</option>';
                    }).join('') + '</optgroup>';
                }).join('');
    
                // set selected index
                select.selectedIndex = DataManager.getActiveIndex();
    
                // add to the root form node
                this._root.appendChild(select);
    
                // handle preset change
                select.addEventListener('change',function(){
                    DataManager.setActiveIndex(select.selectedIndex,function(){
                        document.location.reload(false);
                    });
                });
    
                // or
                var or = document.createElement('span');
                or.className = 'or';
                or.textContent = 'or';
                this._root.appendChild(or);
    
                var create = document.createElement('button');
                create.setAttribute('type','button');
                create.className = 'btn-primary';
                create.textContent= 'Create a new Counter';
                create.addEventListener('click',function(e){
    
                    e.preventDefault();
                    e.stopPropagation();
    
                    createCounterWithValues([],function(success){
                        if (success) {
                            document.location.reload(false);
                        }
                    });
    
                    return false;
    
                });
                this._root.appendChild(create);
    
                if (initCb) {
                    initCb();
                }
            },
    
            getElement:function(){
    
                return this._root;
    
            }
    
        };
    
    
        var polyfill = function(){
    
            // load polyfills
            if (!colorInputSupported) {
    
                $('input[type=color]').each(function(){
    
                    var self = $(this);
                    self.spectrum({
                        preferredFormat: 'hex',
                        clickoutFiresChange:true
                    });
    
                    self.on('move.spectrum', function(e, color) {
    
                        // set new value
                        self.val(color.toHexString());
    
                        // changed
                        change();
    
                    });
    
                });
    
            }
    
            if (!dateInputSupported) {
    
                $('input[type=date]').each(function(){
    
                    var self = $(this);
                    self.datetimepicker({
                        timepicker:false,
                        format:'Y-m-d',
                        onChangeDateTime:function(dp,$input){
                            change();
                        }
                    });
    
                });
    
            }
    
            if (!timeInputSupported) {
    
                $('input[type=time]').each(function(){
    
                    var self = $(this);
                    self.datetimepicker({
                        datepicker:false,
                        format:'H:i:s',
                        onChangeDateTime:function(dp,$input){
                            change();
                        }
                    });
    
                });
    
            }
    
        };
    
    
        // initializes the form
        var init = function(){
    
            // get fields from groups
            var fields = [];
            groups.forEach(function(group){
                fields = fields.concat(group.fields);
            });
            FieldManager.setFields(fields);
    
            // load data
            DataManager.init(function(){
    
                DataManager.load(null,FieldManager.setFieldValues);
    
                var entry = DataManager.getActiveEntry();
    
                // set title
                document.querySelector('h1').textContent = entry.name;
    
                // get group
                var btnGroup = document.querySelector('.btn-group');
    
                // build form
                var form = document.querySelector('.builder-form .inner');
                var changeTimeout = null;
                form.innerHTML = groups.map(getFieldHTML).join('');
                form.addEventListener('change',function(){
                    clearTimeout(changeTimeout);
                    changeTimeout = setTimeout(function(){
                        change();
                    },50);
                });
    
                // add save button
                var save = document.createElement('button');
                save.textContent = 'Save Changes';
                save.className = 'btn-primary btn-save';
                save.style.display = DataManager.isPresetSelected() ? 'none' : '';
                save.addEventListener('click',function(e){
                    e.preventDefault();
                    e.stopPropagation();
    
                    if (!window.confirm('Are you sure you want to save your changes.')){
                        return;
                    }
    
                    // map to values and filter empties
                    var values = FieldManager.toValueObjects();
    
                    // remember these values
                    DataManager.save(values,function(){
    
                        // not always necessary but results in 'saved' feeling
                        document.location.reload(false);
    
                    });
    
                    return false;
                });
                btnGroup.appendChild(save);
    
    
                // add clone button
                var duplicate = document.createElement('button');
                duplicate.textContent = 'Duplicate';
                duplicate.addEventListener('click',function(e){
                    e.preventDefault();
                    e.stopPropagation();
    
                    // map to values and filter empties
                    var values = FieldManager.toValueObjects();
    
                    createCounterWithValues(values,function(success){
                        if (success) {
                            document.location.reload(false);
                        }
                    });
    
                    return false;
                });
                btnGroup.appendChild(duplicate);
    
                /*
                // add reset button
                var reset = document.createElement('button');
                reset.addEventListener('click',function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    if (!window.confirm('Are you sure you want to reset this item.')){return;}
                    DataManager.resetEntry();
                    document.location.reload(false);
                });
                reset.setAttribute('type','button');
                reset.textContent = 'Reset';
                reset.style.display = DataManager.isPresetSelected() ? 'none' : '';
                btnGroup.appendChild(reset);
                */
    
    
                // add remove button
                var remove = document.createElement('button');
                remove.textContent= 'Remove';
                remove.className = 'btn-remove';
                remove.style.display = DataManager.isPresetSelected() ? 'none' : '';
                remove.addEventListener('click',function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    if (!window.confirm('Are you sure you want to remove this item.')){return;}
                    DataManager.removeEntry(function(){
                        document.location.reload(false);
                    });
                    return false;
                });
                btnGroup.appendChild(remove);
    
    
                // add preset select
                PresetSelect.init();
    
                // load possible polyfills
                polyfill();
    
                // trigger change to update form layout
                change();
    
            });
    
    
        };
    
        //
        var isDefined = function(value) {
    
            // if is empty string
            if (typeof value === 'string') {
                return value.length > 0;
            }
    
            // if not is empty array
            if (value && value.length) {
                return value.length > 0;
            }
    
            // if is
            return typeof value !== 'undefined' && value !== null;
        };
    
        // returns field values
        var getFieldValue = function(field) {
    
            var value;
    
            if (field.subfields) {
    
                value = field.subfields.map(function(field){
                    return getFieldValue(field);
                });
    
                value = value.filter(function(val){
                    return isDefined(val);
                });
    
                if (value.length===0) {
                    return null;
                }
    
                return value;
            }
    
            var node = getFieldNode(field);
            if (node){
    
                // handle checkboxes
                if (field.type === 'checkboxes') {
                    var checked = Array.prototype.slice.call(node.querySelectorAll('input:checked'));
                    return checked.map(function(box){
                        return box.value;
                    });
                }
    
                // handle textarea
                if (field.type === 'textarea') {
                    value = node.querySelector('textarea').value;
                    if (!value.length) {
                        return null;
                    }
                }
    
                // handle select
                else if (field.type === 'select') {
                    value = node.querySelector('select').value;
    
                    if (!value.length) {
                        return null;
                    }
    
                    if (value === 'false') {
                        value = false;
                    }
    
                    if (value === 'true') {
                        value = true;
                    }
                }
                else {
                    value = node.querySelector('input').value;
                    if (!value.length) {
                        return null;
                    }
                }
    
                return value;
            }
            return null;
    
        };
    
        var getFieldNode = function(field) {
            return document.getElementById(field.id);
        };
    
        var hideField = function(field) {
            var node = getFieldNode(field);
            node.style.display = 'none';
        };
    
        var showField = function(field) {
            var node = getFieldNode(field);
            node.style.display = '';
        };
    
    
    
        // toggle field visibility
        var toggleFields = function(field) {
    
            if (field.fields) {
                field.fields.forEach(toggleFields);
            }
    
            // get dependency
            var dependency = FieldManager.getFieldDependency(field);
    
            // not dependent on other fields, so should always be visible
            if (!dependency) {
                showField(field);
                return;
            }
    
            // determine if should be shown or hidden
            var expected = field.dep.value;
            var value = getFieldValue(dependency);
            var show = false;
            if (value !== null) {
    
                if (typeof expected === 'boolean') {
                    show = value === expected;
                }
                else if (typeof expected === 'string') {
                    show = value.indexOf(expected) !== -1;
                }
                else if (typeof expected === 'function') {
                    show = expected(value);
                }
                else {
                    show = expected.filter(function(v){
                        return value.indexOf(v) !== -1;
                    }).length >= 1;
                }
    
            }
    
            if (show) {
                showField(field);
            }
            else {
                hideField(field);
            }
        };
    
        var getCodeForValues = function(values) {
    
            // get attribute object from values
            var attributes = [];
            values.filter(function(item){
                return item.attr.charAt(0)!== '_' && item.attr.charAt(0)!=='$' && item.attr.charAt(0)!=='@'
                })
                .forEach(function(item){
                    var field = FieldManager.getFieldById(item.id);
                    var attr = item.attr;
                    var value = field.transform ? field.transform(item.value) : item.value;
                    attributes[attr] = attributes[attr] ? attributes[attr] + ' ' + value : value;
                });
    
            // styler
            var styles = styler.getStyles(
                values.filter(function(item){return item.attr.indexOf('$') === 0})
                    .map(function(item){
    
                        var field = FieldManager.getFieldById(item.id);
                        if (field.transform) {
                            item.value = field.transform(item.value);
                        }
                        return item;
    
                    })
            );
    
            // generate scripts
            var scripts = values
                .filter(function(value){return value.attr.indexOf('$font-family')===0 || value.attr.indexOf('@')===0;})
                .map(function(value){
    
                    var script = '';
    
                    switch(value.attr) {
                        case '$font-family': {
    
                            script+= "WebFontConfig={google:{families:['" + value.value + "']}};";
                            script+= "(function(){";
                            script+= "var wf=document.createElement('script');";
                            script+= "wf.src=('https:'==document.location.protocol?'https':'http')+'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';";
                            script+= "wf.type='text/javascript';";
                            script+= "wf.async='true';";
                            script+= "var s=document.getElementsByTagName('head')[0];";
                            script+= "s.appendChild(wf);";
                            script+= "})()";
    
                        }
                            break;
                        case '@session': {
                            if (value.value === true) {
    
                                script += '(function(){';
                                script += 'var id = "my-soon-counter";';
                                script += 'document.addEventListener("DOMContentLoaded", function() {';
                                script += 'var counter = document.getElementById(id);';
                                script += 'var due = counter.getAttribute("data-due");';
                                script += 'var duration = due.match(/([\\d]+)[\\s]+([a-z]+)/i);';
                                script += 'var amount = parseInt(duration[1], 10);';
                                script += 'var units = duration[2];';
                    
                                script += 'var ms = 0;';
                                script += 'if (units.indexOf("hour")!==-1) {';
                                script += '    ms = amount * 1000 * 60 * 60;';
                                script += '}';
                                script += 'else if (units.indexOf("minute")!==-1) {';
                                script += '    ms = amount * 1000 * 60;';
                                script += '}';
                                script += 'else if (units.indexOf("second") !== -1) {';
                                script += '    ms = amount * 1000 * 60;';
                                script += '}';
                
                                script += 'var offset = new Date( localStorage.getItem( id + "-countdown-offset") || new Date() );';
                                
                                script += 'localStorage.setItem( id + "-countdown-offset", offset);';
                                
                                script += 'var deadline = new Date( offset.setMilliseconds( offset.getMilliseconds() + ms ) );';
                    
                                script += 'var iso = Soon.toISODate(deadline);';
                    
                                script += 'if (!counter.hasAttribute("data-initialized")) {';
                                script += '    counter.setAttribute("data-due", iso);';
                                script += '    Soon.create(counter);';
                                script += '}';
                                script += 'else {';
                                script += '    Soon.setOption(counter, "due", iso);';
                                script += '}';
                                script += '});';
    
                                script += '}())';
                            }
                        }
                            break;
                        case '@complete-handler-hide': {
                            script+= 'function soonCompleteCallback(){document.getElementById("' + soonId + '").style.display = "none";}';
                        }
                            break;
                        case '@complete-handler-redirect': {
                            script+= 'function soonCompleteCallback(){window.location = "' + value.value + '"}';
                        }
                            break;
                        case '@complete-handler-javascript': {
                            script+= 'function soonCompleteCallback(){ ' + value.value + '();}';
                        }
                            break;
    
                    }
                    return script;
    
                }).join(';');
    
            return {
                attr:attributes,
                styles:styles,
                scripts:scripts
            }
    
        };
    
        // triggered on value change
        var change = function(){
    
            // toggle field visibility
            groups.forEach(toggleFields);
    
            // map to values and filter empties
            var values = FieldManager.toValueObjects();
    
            // get
            var code = getCodeForValues(values);
    
            // get font
            var font = values.filter(function(item){return item.attr === '$font-family'});
            font = font && font.length ? font[0].value : null;
    
            // render
            renderer.render(code.attr,code.styles,font);
    
            // generate code
            generator.generate(code.attr,code.styles,code.scripts);
    
        };
    
        window.addEventListener('load',init());
    
        return {
            getSnippetByValues:function(values) {
    
                // get
                var code = getCodeForValues(values);
    
                // generate code
                return generator.generate(code.attr,code.styles,code.scripts);
    
            }
        }
    
    }(jQuery,soonGroups,soonStyler,soonRenderer,soonGenerator,soonPresets,soonStorage,soonInit));