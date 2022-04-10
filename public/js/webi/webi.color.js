(function(){

    /**
     * webiColor, JavaScript Color Picker
     *
     * @version 1.0
     * @author  zhoulaiyin
     * @created 2018-05-04
     */

    var q = void 0,
        r = !0,
        t = null,
        u = !1;

    var webiColor = {};

    webiColor.bindClass = 'WeBIColor'; //通过class进行批量绑定
    webiColor.binding = true; //开启自动绑定
    webiColor.color_database = []; //存放维护的颜色代码值数组
    webiColor.hsv = [0, 0, 1]; // read-only  0-6, 0-1, 0-1
    webiColor.rgb = [1, 1, 1]; // read-only  0-1, 0-1, 0-1
    webiColor.minH = 0; // read-only  0-6
    webiColor.maxH = 6; // read-only  0-6
    webiColor.minS = 0; // read-only  0-1
    webiColor.maxS = 1; // read-only  0-1
    webiColor.minV = 0; // read-only  0-1
    webiColor.maxV = 1; // read-only  0-1
    webiColor.pickerPosition = 'top'; // left | right | top | bottom
    webiColor.pickerSmartPosition = true; // automatically adjust picker position when necessary
    webiColor.pickerFace = 10; // px
    webiColor.pickerInset = 1; // px
    webiColor.pickerW = 290; //拾取器默认宽度
    webiColor.pickerH = 180; //拾取器默认高度
    webiColor.pickerZIndex = 10000; //拾取器默认层级
    webiColor.op_flg = 0; //操作标志：0:无操作 1:选择颜色 2.清空颜色
    webiColor.picker_owner = ''; //拾色器操作对象

    //JS处理ajax请求
    webiColor.ajax_get = function(url, fn){
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200 || xhr.status == 304) {
                if( typeof xhr.responseText == 'string'){
                    var dt = JSON.parse(xhr.responseText);
                } else {
                    var dt = xhr.responseText;
                }
                fn.call(this,dt);
            }
        };
        xhr.send();
    };

    //绑定事件
    webiColor.eventBind = function(obj, action, d){
        obj.attachEvent ? obj.attachEvent("on" + action,
            function(action) {
                d.call(obj, action)
            }) : obj.addEventListener && obj.addEventListener(action, d , u)
    };

    webiColor.init = function() {
        webiColor.createPicker();
        webiColor.getColor();
        if(webiColor.binding) {
            webiColor.bind();
        }
    };
    //创建拾色器HTML结构对象
    webiColor.createPicker = function() {
        webiColor.picker = {
            boxM : document.createElement('div'),
            boxCont : document.createElement('div'),
            btn : document.createElement('button'),
            btnSpan : document.createElement('span'),
            header : document.createElement('div'),
            headerTitle : document.createElement('div'),
            headerTitleSpan : document.createElement('span'),
            headerTitleSpanI : document.createElement('i'),
            body : document.createElement('div'),
            bodyOneDiv : document.createElement('div'),
            bodyTwoDiv : document.createElement('div'),
            bodyPickerDiv : document.createElement('div')
        };

        webiColor.picker.headerTitleSpan.innerText = '颜色拾取';

        webiColor.picker.boxM.appendChild(webiColor.picker.boxCont);
        webiColor.picker.boxCont.appendChild(webiColor.picker.btn);
        webiColor.picker.btn.appendChild(webiColor.picker.btnSpan);
        webiColor.picker.boxCont.appendChild(webiColor.picker.header);
        webiColor.picker.header.appendChild(webiColor.picker.headerTitle);
        webiColor.picker.headerTitle.appendChild(webiColor.picker.headerTitleSpan);
        webiColor.picker.headerTitleSpan.appendChild(webiColor.picker.headerTitleSpanI);
        webiColor.picker.boxCont.appendChild(webiColor.picker.body);
        webiColor.picker.body.appendChild(webiColor.picker.bodyOneDiv);
        webiColor.picker.bodyOneDiv.appendChild(webiColor.picker.bodyTwoDiv);
        webiColor.picker.bodyTwoDiv.appendChild(webiColor.picker.bodyPickerDiv);
    };
    //获取维护的色彩代码
    webiColor.getColor = function(){
        webiColor.ajax_get(
            '/webi/color/get',
            webiColor.jsonBack
        );
    };
    webiColor.jsonBack = function (o) {
        webiColor.color_database = o.data;
    };

    webiColor.bind = function() {
        var e = document.getElementsByTagName('input');
        for(var i=0; i<e.length; i+=1) {
            if( e[i].className.search(webiColor.bindClass) != -1 ){
                webiColor.color(e[i]);
            }
        }
    };

    //手工绑定
    webiColor.use = function(target) {
        if( typeof target == 'string'){
            if( !document.getElementById(target) ){
                return false;
            }
            var target_obj = document.getElementById(target);
        } else {
            var target_obj = target;
        }
        webiColor.color(target_obj);
    };

    webiColor.getElementPos = function(e) {
        var e1=e, e2=e;
        var x=0, y=0;
        if(e1.offsetParent) {
            do {
                x += e1.offsetLeft;
                y += e1.offsetTop;
            } while(e1 = e1.offsetParent);
        }
        while((e2 = e2.parentNode) && e2.nodeName.toUpperCase() !== 'BODY') {
            x -= e2.scrollLeft;
            y -= e2.scrollTop;
        }
        return [x, y];
    };
    webiColor.getElementSize = function(e) {
        return [e.offsetWidth, e.offsetHeight];
    };

    webiColor.getRelMousePos = function(e) {
        var x = 0, y = 0;
        if (!e) { e = window.event; }
        if (typeof e.offsetX === 'number') {
            x = e.offsetX;
            y = e.offsetY;
        } else if (typeof e.layerX === 'number') {
            x = e.layerX;
            y = e.layerY;
        }
        return { x: x, y: y };
    };

    webiColor.getViewPos = function() {
        if(typeof window.pageYOffset === 'number') {
            return [window.pageXOffset, window.pageYOffset];
        } else if(document.body && (document.body.scrollLeft || document.body.scrollTop)) {
            return [document.body.scrollLeft, document.body.scrollTop];
        } else if(document.documentElement && (document.documentElement.scrollLeft || document.documentElement.scrollTop)) {
            return [document.documentElement.scrollLeft, document.documentElement.scrollTop];
        } else {
            return [0, 0];
        }
    };
    webiColor.getViewSize = function() {
        if(typeof window.innerWidth === 'number') {
            return [window.innerWidth, window.innerHeight];
        } else if(document.body && (document.body.clientWidth || document.body.clientHeight)) {
            return [document.body.clientWidth, document.body.clientHeight];
        } else if(document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
            return [document.documentElement.clientWidth, document.documentElement.clientHeight];
        } else {
            return [0, 0];
        }
    };

    webiColor.rgbToHex = function(rgb) {
        var rRgba = /rgba?\((\d{1,3}),(\d{1,3}),(\d{1,3})(,([.\d]+))?\)/,
        r, g, b, a,
        rsa = rgb.replace(/\s+/g, "").match(rRgba);
        if (rsa) {
            r = (+rsa[1]).toString(16);
            r = r.length == 1 ? "0" + r : r;
            g = (+rsa[2]).toString(16);
            g = g.length == 1 ? "0" + g : g;
            b = (+rsa[3]).toString(16);
            b = b.length == 1 ? "0" + b : b;
            a = (+(rsa[5] ? rsa[5] : 1)) * 100
            return {hex: "#" + r + g + b, alpha: Math.ceil(a)};
        } else {
            return {hex: rgb, alpha: 100};
        }
    };
    webiColor.hexToRgba = function(hex, al) {
        var hexColor = /^#/.test(hex) ? hex.slice(1) : hex,
            alp = hex === 'transparent' ? 0 : Math.ceil(al),
            r, g, b;
        hexColor = /^[0-9a-f]{3}|[0-9a-f]{6}$/i.test(hexColor) ? hexColor : 'fffff';
        if (hexColor.length === 3) {
            hexColor = hexColor.replace(/(\w)(\w)(\w)/gi, '$1$1$2$2$3$3');
        }
        r = hexColor.slice(0, 2);
        g = hexColor.slice(2, 4);
        b = hexColor.slice(4, 6);
        r = parseInt(r, 16);
        g = parseInt(g, 16);
        b = parseInt(b, 16);
        return {
            hex: '#' + hexColor,
            alpha: alp,
            rgba: 'rgba(' + r + ', ' + g + ', ' + b + ', ' + (alp / 100).toFixed(2) + ')'
        };
    };

    webiColor.color = function(target) {

        this.fromString = function(hex, flags) {
            var m = hex.match(/^\W*([0-9A-F]{3}([0-9A-F]{3})?)\W*$/i);
            if(!m) {
                return false;
            } else {
                if(m[1].length === 6) { // 6-char notation
                    this.fromRGB(
                        parseInt(m[1].substr(0,2),16) / 255,
                        parseInt(m[1].substr(2,2),16) / 255,
                        parseInt(m[1].substr(4,2),16) / 255,
                        flags
                    );
                } else { // 3-char notation
                    this.fromRGB(
                        parseInt(m[1].charAt(0)+m[1].charAt(0),16) / 255,
                        parseInt(m[1].charAt(1)+m[1].charAt(1),16) / 255,
                        parseInt(m[1].charAt(2)+m[1].charAt(2),16) / 255,
                        flags
                    );
                }
                return true;
            }
        };

        this.fromRGB = function(r, g, b, flags) { // null = don't change
            if(r !== null) { r = Math.max(0.0, Math.min(1.0, r)); }
            if(g !== null) { g = Math.max(0.0, Math.min(1.0, g)); }
            if(b !== null) { b = Math.max(0.0, Math.min(1.0, b)); }

            var hsv = RGB_HSV(
                r===null ? this.rgb[0] : r,
                g===null ? this.rgb[1] : g,
                b===null ? this.rgb[2] : b
            );
            if(hsv[0] !== null) {
                this.hsv[0] = Math.max(0.0, this.minH, Math.min(6.0, this.maxH, hsv[0]));
            }
            if(hsv[2] !== 0) {
                this.hsv[1] = hsv[1]===null ? null : Math.max(0.0, this.minS, Math.min(1.0, this.maxS, hsv[1]));
            }
            this.hsv[2] = hsv[2]===null ? null : Math.max(0.0, this.minV, Math.min(1.0, this.maxV, hsv[2]));

            // update RGB according to final HSV, as some values might be trimmed
            var rgb = HSV_RGB(this.hsv[0], this.hsv[1], this.hsv[2]);
            this.rgb[0] = rgb[0];
            this.rgb[1] = rgb[1];
            this.rgb[2] = rgb[2];

            this.updateOwner.style.backgroundColor = '#'+this.toString();
            this.updateOwner.style.color =
                0.213 * this.rgb[0] +
                0.715 * this.rgb[1] +
                0.072 * this.rgb[2]
                < 0.5 ? '#FFF' : '#000';
        };

        this.toString = function() {
            return (
                (0x100 | Math.round(255*this.rgb[0])).toString(16).substr(1) +
                (0x100 | Math.round(255*this.rgb[1])).toString(16).substr(1) +
                (0x100 | Math.round(255*this.rgb[2])).toString(16).substr(1)
            );
        };

        this.showPicker = function(){

            this.onImmediateChange = this.picker_owner.getAttribute('onchange'); // onchange callback (can be either string or function)
            this.op_flg = 0;

            if( webiColor.color_database.length == 0 ){
                webiColor.getColor();
                return false;
            }

            var tp = webiColor.getElementPos(this.picker_owner); // target pos
            var ts = webiColor.getElementSize(this.picker_owner); // target size
            var vs = webiColor.getViewSize(); // view size
            var x = tp[0]+10;
            var y = tp[1] + ts[1] + 10;
            if( (vs[0] - x) < this.pickerW ){
                x = (vs[0] - this.pickerW)-20;
            }
            if( (vs[1] - y) < this.pickerH ){
                y = tp[1] - this.pickerH - 10;
            }
            drawPicker(x, y);
        };

        function drawPicker(x, y) {

            var p = webiColor.picker;

            // picker
            p.boxM.className = 'ant-modal';
            p.boxM.style.width = webiColor.pickerW + 'px';
            p.boxM.style.height = webiColor.pickerH + 'px';
            p.boxM.style.margin = '0px';
            p.boxM.style.left = x+'px';
            p.boxM.style.top = y+'px';
            p.boxM.style.zIndex = webiColor.pickerZIndex;
            p.boxM.style.transformOrigin = '330px 408px 0px';

            //content
            p.boxCont.className = 'ant-modal-content';

            //content-button
            p.btn.className = 'ant-modal-close';
            //content-button-span
            p.btnSpan.className = 'ant-modal-close-x layui-icon layui-icon-close';

            //content-header
            p.header.className = 'ant-modal-header';
            p.headerTitle.className = 'ant-modal-title';
            p.headerTitleSpanI.className = 'layui-icon layui-icon-delete';
            p.headerTitleSpanI.style.cursor = 'pointer';
            p.headerTitleSpanI.style.color = 'rgb(16, 142, 233)';
            p.headerTitleSpanI.style.marginLeft = '5px';

            //content-body
            p.body.className = 'ant-modal-body';
            p.bodyTwoDiv.style.display = 'block';
            p.bodyTwoDiv.style.paddingTop = '10px';
            p.bodyPickerDiv.className = 'circle-picker';
            p.bodyPickerDiv.style.width = '100%';
            p.bodyPickerDiv.style.display = 'flex';
            p.bodyPickerDiv.style.flexWrap = 'wrap';
            p.bodyPickerDiv.style.marginRight = '-14px';
            p.bodyPickerDiv.style.marginBottom = '-14px';

            p.bodyPickerDiv.innerHTML = '';

            //贴上颜色代码
            for( k in webiColor.color_database ){

                var span_m = document.createElement('span');
                var span_m_div = document.createElement('div');
                var span_m_div_span = document.createElement('span');
                var span_m_div_span_div = document.createElement('div');

                span_m.appendChild(span_m_div);
                span_m_div.appendChild(span_m_div_span);
                span_m_div_span.appendChild(span_m_div_span_div);

                span_m.setAttribute('data-color',webiColor.color_database[k]);

                span_m_div.style.width = '28px';
                span_m_div.style.height = '28px';
                span_m_div.style.marginRight = '14px';
                span_m_div.style.marginBottom = '14px';
                span_m_div.style.transform = 'scale(1)';
                span_m_div.style.transition = 'transform 100ms ease 0s';

                span_m_div_span_div.title = '#'+webiColor.color_database[k];
                span_m_div_span_div.className = 'webi-picker';
                span_m_div_span_div.style.background = 'transparent none';
                span_m_div_span_div.style.backgroundRepeat = 'repeat scroll';
                span_m_div_span_div.style.backgroundSize = '0% 0%';
                span_m_div_span_div.style.width = '100%';
                span_m_div_span_div.style.height = '100%';
                span_m_div_span_div.style.cursor = 'pointer';
                span_m_div_span_div.style.position = 'relative';
                span_m_div_span_div.style.outline = 'medium none currentcolor';
                span_m_div_span_div.style.borderRadius = '50%';
                if( webiColor.picker_owner.value == span_m_div_span_div.title ){
                    span_m_div_span_div.style.boxShadow = span_m_div_span_div.title + ' 0px 0px 0px 4px inset';
                } else {
                    span_m_div_span_div.style.boxShadow = span_m_div_span_div.title + ' 0px 0px 0px 14px inset';
                }
                span_m_div_span_div.style.transition = 'box-shadow 100ms ease 0s';

                p.bodyPickerDiv.appendChild(span_m);

                webiColor.eventBind(span_m_div_span_div,'click',selectColor);

            }

            document.getElementsByTagName('body')[0].appendChild(p.boxM);
            webiColor.eventBind(p.btn,'click',hidePicker);
            webiColor.eventBind(p.headerTitleSpanI,'click',delColor);
        }

        function selectColor(ev){
            var event = ev || event;

            //设置选中效果
            var list = webiColor.picker.bodyPickerDiv.querySelectorAll('.webi-picker');
            var list_length = list.length;
            for ( var k=0; k<list_length;k++){
                list[k].className = list[k].className.replace(/selected/g,'');
                if( list[k] === event.target ){
                    list[k].style.boxShadow = list[k].title + ' 0px 0px 0px 4px inset';
                } else {
                    list[k].style.boxShadow = list[k].title + ' 0px 0px 0px 14px inset';
                }
            }
            webiColor.op_flg = 1;
            webiColor.selected = event.target;
            importColor();
        }

        function importColor() {

            if( webiColor.op_flg == 0 ){return false;}

            switch ( parseInt(webiColor.op_flg) ){

                case 1:

                    var pre_color_code =  webiColor.rgbToHex(webiColor.picker_owner.style.backgroundColor).hex;
                    webiColor.picker_owner.style.backgroundColor = webiColor.selected.title;
                    webiColor.picker_owner.value = webiColor.selected.title;

                    if( pre_color_code.toUpperCase() != webiColor.selected.title.toUpperCase() ){
                        dispatchImmediateChange('importColor-1');
                    }

                    break;

                case 2:

                    var list = webiColor.picker.bodyPickerDiv.querySelectorAll('.webi-picker');
                    var list_length = list.length;
                    for ( var k=0; k<list_length;k++){
                        list[k].className = list[k].className.replace(/selected/g,'');
                        list[k].style.boxShadow = list[k].title + ' 0px 0px 0px 14px inset';
                    }

                    var pre_color_code =  webiColor.rgbToHex(webiColor.picker_owner.style.backgroundColor).hex;

                    webiColor.picker_owner.style.backgroundColor = '';
                    webiColor.picker_owner.value = '';

                    if( pre_color_code != '' ){
                        dispatchImmediateChange('importColor-2');
                    }
                    break;

                default:
                    return false;

            }

            setTimeout(function () {
                webiColor.op_flg = 0;
                delete webiColor.selected;
            },100);
            webiColor.picker_owner.focus();
            return true;
        }

        function hidePicker() {
            removePicker();
        }

        function removePicker() {
            webiColor.picker_owner = '';
            document.getElementsByTagName('body')[0].removeChild(webiColor.picker.boxM);
        }

        function delColor() {
            webiColor.op_flg = 2;
            importColor();
        }

        function dispatchImmediateChange(func) {
            //console.log(new Date().getTime()+'--'+func);
            if (webiColor.onImmediateChange) {
                eval(webiColor.onImmediateChange).call(this,webiColor.picker_owner);
            }
        }

        function HSV_RGB(h, s, v) {
            if(h === null) { return [ v, v, v ]; }
            var i = Math.floor(h);
            var f = i%2 ? h-i : 1-(h-i);
            var m = v * (1 - s);
            var n = v * (1 - s*f);
            switch(i) {
                case 6:
                case 0: return [v,n,m];
                case 1: return [n,v,m];
                case 2: return [m,v,n];
                case 3: return [m,n,v];
                case 4: return [n,m,v];
                case 5: return [v,m,n];
            }
        }

        function RGB_HSV(r, g, b) {
            var n = Math.min(Math.min(r,g),b);
            var v = Math.max(Math.max(r,g),b);
            var m = v - n;
            if(m === 0) { return [ null, 0, v ]; }
            var h = r===n ? 3+(b-g)/m : (g===n ? 5+(r-b)/m : 1+(g-r)/m);
            return [ h===6?0:h, m/v, v ];
        }

        function blurTarget() {
            setTimeout(function () {
                if( !importColor() ){
                    hidePicker();
                }
            },105);
        }

        webiColor.eventBind(target, 'focus', function(ev) {
            var event = ev || event;
            if( webiColor.picker_owner !== event.target ){
                if( webiColor.picker_owner != '' ){
                    removePicker();
                }
                webiColor.picker_owner = event.target;
                webiColor.showPicker();
            }
        });
        webiColor.eventBind(target, 'blur', function(ev) {
            var event = ev || event;
            webiColor.blur_obj = event.target;
            blurTarget();
        });

        //输入框内容变更，进行内容校验，符合颜色代码值，进行颜色更换
        var updateField = function(ev) {
            if( webiColor.op_flg != 0 ){return false;}
            var event = ev || event;
            webiColor.updateOwner = event.target;
            webiColor.fromString(webiColor.updateOwner.value, 1<<0);
            dispatchImmediateChange('updateField');
        };
        webiColor.eventBind(target, 'keyup', updateField);
        webiColor.eventBind(target, 'input', updateField);
    };

    webiColor.init();

    window.webiColor = webiColor;

})();