(function(){

    var G_POSITION = [],
        q = void 0,
        r = !0,
        t = null,
        u = !1,

        captureMode = false,

        /** @const */
        R_SPACE = /\s+/g,

        expando = 'FreeDrag' + (new Date).getTime();

    /**
     * 获取浏览器宽高
     * @returns {{winWidth: (number|*|Number), winHeight: (number|*|Number)}}
     */
    function findDimensions(){

        //获取窗口宽度
        if (window.innerWidth){
            winWidth = window.innerWidth;
        } else if ((document.body) && (document.body.clientWidth)){
            winWidth = document.body.clientWidth;
        }

        //获取窗口高度
        if (window.innerHeight){
            winHeight = window.innerHeight;
        }else if ((document.body) && (document.body.clientHeight)){
            winHeight = document.body.clientHeight;
        }

        //通过深入Document内部对body进行检测，获取窗口大小
        if (document.documentElement  && document.documentElement.clientHeight && document.documentElement.clientWidth){
            winHeight = document.documentElement.clientHeight;
            winWidth = document.documentElement.clientWidth;
        }

        return {
            winWidth : winWidth,
            winHeight : winHeight
        }

    }

    /**
     * 对象的合成
     * @param dst 原始对象
     * @param src 参数对象
     * @returns {*}
     * @private
     */
    function _extend(dst, src) {
        if (dst && src) {
            for (var key in src) {
                if (src.hasOwnProperty(key)) {
                    dst[key] = src[key];
                }
            }
        }
        return dst;
    }

    /**
     * 给元素添加/移除class
     * @param el
     * @param name
     * @param state
     * @private
     */
    function _toggleClass(el, name, state) {
        if (el && name) {
            if (el.classList) {
                el.classList[state ? 'add' : 'remove'](name);
            }
            else {
                var className = (' ' + el.className + ' ').replace(R_SPACE, ' ').replace(' ' + name + ' ', ' ');
                el.className = (className + (state ? ' ' + name : '')).replace(R_SPACE, ' ');
            }
        }
    }

    /**
     * 处理元素css样式
     * @param el
     * @param prop
     * @param val
     * @returns {*}
     * @private
     */
    function _css(el, prop, val) {
        var style = el && el.style;

        if (style) {
            if (val === void 0) {
                if (document.defaultView && document.defaultView.getComputedStyle) {
                    val = document.defaultView.getComputedStyle(el, '');
                }
                else if (el.currentStyle) {
                    val = el.currentStyle;
                }

                return prop === void 0 ? val : val[prop];
            }
            else {
                if (!(prop in style)) {
                    prop = '-webkit-' + prop;
                }

                style[prop] = val + (typeof val === 'string' ? '' : 'px');
            }
        }
    }

    /**
     * 绑定事件
     * @param obj
     * @param action
     * @param d
     * @private
     */
    var _on = function(obj, action, d){
        obj.attachEvent ? obj.attachEvent("on" + action,
            function(action) {
                d.call(obj, action)
            }) : obj.addEventListener && obj.addEventListener(action, d , u)
    };

    /**
     * 取消绑定事件
     * @param el
     * @param event
     * @param fn
     * @private
     */
    function _off(el, event, fn) {
        el.removeEventListener(event, fn, captureMode);
    }

    /**
     * 判断两个dom对象是否重叠
     * @param elem1 对象1
     * @param elem2 对象2
     * @returns bool
     */
    var is_cross = function(elem1,elem2) {

        var o_top = parseInt(elem1.top);
        var o_left = parseInt(elem1.left);
        var o_width = parseInt(elem1.width);
        var o_height = parseInt(elem1.height);

        var y = parseInt(elem2.top);
        var x = parseInt(elem2.left);
        var w = parseInt(elem2.width);
        var h = parseInt(elem2.height);

        var is_doublication = false;

        //判断是否有重叠区域（判断一个元素的四个点是否在另一个元素内部）
        var compare_left_top = x >= o_left && x <= o_left+o_width && y >= o_top && y <= o_top+o_height,
            compare_right_top = x+w >= o_left && x+w <= o_left+o_width && y >= o_top && y <= o_top+o_height,
            compare_left_bottom = x >= o_left && x <= o_left+o_width && y+h >= o_top && y+h <= o_top+o_height,
            compare_right_bottom = x+w >= o_left && x+w <= o_left+o_width && y+h >= o_top && y+h <= o_top+o_height;
        if( compare_left_top || compare_right_top || compare_left_bottom || compare_right_bottom ){
            is_doublication = true;
        }
        var drag_left_top = o_left >= x && o_left <= x+w && o_top >= y && o_top <= y+h,
            drag_right_top = o_left+o_width >= x && o_left+o_width <= x+w && o_top >= y && o_top <= y+h,
            drag_left_bottom = o_left >= x && o_left <= x+w && o_top+o_height >= y && o_top+o_height <= y+h,
            drag_right_bottom = o_left+o_width >= x && o_left+o_width <= x+w && o_top+o_height >= y && o_top+o_height <= y+h;
        if( drag_left_top || drag_right_top || drag_left_bottom || drag_right_bottom ){
            is_doublication = true;
        }

        //判断两个元素是否交叉成十字型
        var cross_1 = o_top <= y && o_top+o_height >= y+h && o_left>= x && o_left <= x+w && o_left+o_width >= x && o_left+o_width <= x+w;
        var cross_2 = o_top >= y && o_top+o_height <= y+h && o_left<= x && o_left+o_width >= x+w;
        if( cross_1 || cross_2 ){
            is_doublication = true;
        }

        return is_doublication;
    }


    /**
     * @class  FreeDrag
     * @param  {HTMLElement}  el
     * @param  {Object}  [options]
     */
    var FreeDrag = function(el,options) {
        if (!(el && el.nodeType && el.nodeType === 1)) {
            throw 'Sortable: `el` must be HTMLElement, and not ' + {}.toString.call(el);
        }

        var defaults = {
            animation:500, //number 单位：ms，定义排序动画的时间
            x_interval:10, //x轴方向上元素之间的间隔
            y_interval:10, //Y轴方向上元素之间的间隔
            dragClass: "" //拖动元素的class
        };

        this.el = el; // root element
        this.options = options = _extend( defaults, options );

        // Export instance
        el[expando] = this;

        //设置元素索引
        var lis = document.querySelectorAll(options.dragNodeClass);
        var lis_length = lis.length;
        if( lis_length > 0 ){
            for(var i=0; i<lis_length; i++){
                lis[i].setAttribute('data-index',i);
                lis[i].style.zIndex = 1;
            }
        }

        // Bind all private methods
        for (var fn in this) {
            if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
                this[fn] = this[fn].bind(this);
            }
        }

        // Bind events
        _on(el, 'mousedown', this._onTapStart);
        _on(el, 'touchstart', this._onTapStart);
    }

    FreeDrag.prototype = {

        constructor: FreeDrag,

        createObj: function() {

            var _this = this,
                el = _this.el,
                options = _this.options;

            var data_index = document.querySelectorAll(options.dragNodeClass).length;

            this.c_op = document.createElement('div');
            el.appendChild(this.c_op);
            _toggleClass(this.c_op,options.dragNodeClass.replace('.',''),true);
            _toggleClass(this.c_op,'react-item-create',true);
            this.c_op.setAttribute('data-index', data_index );
            _css(this.c_op, 'top', _this.handle_node.offsetTop);
            _css(this.c_op, 'left', _this.handle_node.offsetLeft);
            _css(this.c_op, 'width', _this.handle_node.offsetWidth);
            _css(this.c_op, 'height', _this.handle_node.offsetHeight);
            _css(this.c_op, 'background-color', el.style.backgroundColor);
            _css(this.c_op, 'opacity', '0.5');
            _css(this.c_op, 'position', 'absolute');

        },

        selected: function() {

            var _this = this,
                handle_node = this.handle_node,
                options = _this.options;

            var list = document.querySelectorAll(options.dragNodeClass);
            var list_length = list.length;
            if( list_length > 0 ){
                for(var i=0; i<list_length; i++){
                    _toggleClass(list[i],options.dragClass,false);
                    _toggleClass(list[i],'react-dragging',false);
                }
            }
            if( handle_node ){
                _toggleClass(handle_node,options.dragClass,true);
                _toggleClass(handle_node,'react-dragging',true);
            }
        },

        match: function() {

            var options = this.options,
                target = this.target;

            this.match_mode = 0;
            this.handle_node = null;

            if( target.className.search(options.draggable.replace('.','')) !== -1 ){
                this.match_mode = 1;
            } else if ( target.className.search(options.scaleable.replace('.','')) !== -1 ){
                this.match_mode = 2;
            }

            if( this.match_mode == 0 ){
                return;
            }

            var handle_node = target; //从当前元素开始向上遍历校验，寻找符合的元素
            while(1){
                if(
                    this.match_mode == 1
                    &&
                    handle_node.className.search(options.dragNodeClass.replace('.','')) !== -1
                    ||
                    this.match_mode == 2
                    &&
                    handle_node.className.search(options.scaleNodeClass.replace('.','')) !== -1
                ){
                    break;
                }
                if( !handle_node.parentNode ){
                    handle_node = null;
                    break;
                }
                handle_node = handle_node.parentNode;
            }

            this.handle_node = handle_node;

        },

        _onTapStart:function(evt) {

            var _this = this,
                el = this.el,
                touch = evt.touches && evt.touches[0],
                target = (touch || evt).target;

            //父级元素的行为跳过
            if( el === target  ){
                return false;
            }

            //初始化变量
            this._nulling();

            this.target = target;

            evt.preventDefault();
            evt.stopPropagation();

            this._lastX = (touch || evt).clientX;
            this._lastY = (touch || evt).clientY;

            //清空之前的绑定事件
            _this._offUpEvents();

            //匹配符合条件的元素
            this.match();

            if( this.match_mode == 0 ){ //没有任何绑定事件的元素跳过
                return false;
            }

            this._preDragStart(evt);

        },

        _preDragStart:function(/** Event|TouchEvent */evt){

            var _this = this,
                el = this.el,
                options = this.options,
                touch = evt.touches && evt.touches[0],
                target = (touch || evt).target,
                limit_offsetWidth,
                limit_offsetHeight;

            this.origin = {
                left:target.offsetLeft,
                top:target.offsetTop,
                width:target.offsetWidth,
                height:target.offsetHeight
            };

            limit_offsetWidth = el.offsetWidth;
            limit_offsetHeight = el.offsetHeight;
            this.maxLeft = limit_offsetWidth - target.offsetWidth;
            this.maxTop = limit_offsetHeight - target.offsetHeight;

            if( this.match_mode == 2 ){ //缩放

                this.disx = _this._lastX - target.offsetLeft;
                this.disy = _this._lastY - target.offsetTop;

                this.handleOrigin = {
                    left:this.handle_node.offsetLeft,
                    top:this.handle_node.offsetTop,
                    width:this.handle_node.offsetWidth,
                    height:this.handle_node.offsetHeight
                };
            } else { //拖动
                this.disx = _this._lastX - this.handle_node.offsetLeft;
                this.disy = _this._lastY - this.handle_node.offsetTop;
            }

            //创建临时元素，用于计算定位
            _this.createObj();

            //设置拖拽目标选中class
            _this.selected();

            //拖拽的元素或者缩放的父元素，设置拖拽class，用于自动重排
            _toggleClass(this.handle_node,'react-dragging',false);
            _toggleClass(this.handle_node,'react-dragging',true);

            //绑定移动事件
            _on(el, 'mousemove', _this._onTouchMove);
            _on(el, 'mouseup', _this._onDrop);
            _on(el, 'touchmove', _this._onTouchMove);
            _on(el, 'touchend', _this._onDrop);
            _on(el, 'touchcancel', _this._onDrop);

            this._dispatchEvent('start');

        },

        _onTouchMove: function (/**TouchEvent*/evt) {

            var	touch = evt.touches ? evt.touches[0] : evt,
                target = this.target,
                c_op = this.c_op,
                match_mode = this.match_mode,
                handle_node = this.handle_node,
                options = this.options;

            if( match_mode == 0 || !handle_node ){ //没有任何绑定事件的元素跳过
                return false;
            }

            this.is_move = 1;

            evt.preventDefault();
            evt.stopPropagation();

            var sildLeft = evt.clientX - this.disx;
            var slidTop = evt.clientY - this.disy;

            if( sildLeft <= 0 ){
                sildLeft = 0;
            }
            if( sildLeft >= this.maxLeft ){
                sildLeft = this.maxLeft;
            }
            if( slidTop <= 0 ){
                slidTop = 0;
            }
            if( slidTop >= this.maxTop ){
                slidTop = this.maxTop;
            }

            if( match_mode == 1 ){
                _css(handle_node,'left',sildLeft);
                _css(handle_node,'top',slidTop);
                _css(c_op,'top',slidTop);
                _css(c_op,'left',sildLeft);
            } else {
                _css(target,'left',sildLeft);
                _css(target,'top',slidTop);
                _css(handle_node,'width', (target.offsetLeft + target.offsetWidth) );
                _css(handle_node,'height', (target.offsetTop + target.offsetHeight) );
                _css(c_op,'left',handle_node.offsetLeft);
                _css(c_op,'top',handle_node.offsetTop);
                _css(c_op,'width', (target.offsetLeft + target.offsetWidth) );
                _css(c_op,'height', (target.offsetTop + target.offsetHeight) );
            }

            this.layout();
            this._dispatchEvent('move');

        },

        _onDrop:function(evt) {

            evt.preventDefault();
            evt.stopPropagation();

            var el = this.el,
                handle_node = this.handle_node,
                c_op = this.c_op;

            this.Animation(handle_node,c_op.offsetTop,c_op.offsetLeft);

            this._offUpEvents();

            if( this.is_move ){
                this._dispatchEvent('end');
            }

            this._nulling();
        },

        _offUpEvents: function () {
            _off(this.el, 'mousemove', this._onTouchMove);
            _off(this.el, 'mouseup', this._onDrop);
            _off(this.el, 'touchmove', this._onTouchMove);
            _off(this.el, 'touchend', this._onDrop);
            _off(this.el, 'touchcancel', this._onDrop);
        },

        position: function() {

            var el = this.el,
                target = this.target,
                options = this.options;

            var posCompare = function(p1, p2) {
                if (p1.top > p2.top) {
                    return true;
                } else if (p1.top == p2.top) {
                    return (p1.left > p2.left);
                } else {
                    return false;
                }
            }
            var sortPos = function (arry) {
                var len = arry.length;
                for (var i = 0; i < len - 1; i++) {
                    for (var j = 0; j < len - 1 - i; j++) {
                        if (posCompare(arry[j], arry[j + 1])) {
                            var tmp = arry[j];
                            arry[j] = arry[j + 1];
                            arry[j+1] = tmp;
                            arry[j].elem.setAttribute('data-index',j);
                            arry[j+1].elem.setAttribute('data-index',j+1);
                        }
                    }
                }
                return arry;
            }

            G_POSITION = [];
            var lis = el.querySelectorAll(options.dragNodeClass);
            var lis_length = lis.length;
            if( lis_length == 0 ){return false;}
            for(var i=0; i<lis_length; i++){
                lis[i].setAttribute('data-index',i);
                var position = {
                    elem:lis[i],
                    left:lis[i].offsetLeft,
                    top:lis[i].offsetTop,
                    width:lis[i].offsetWidth,
                    height:lis[i].offsetHeight
                };
                G_POSITION.push(position);
            }
            G_POSITION = sortPos(G_POSITION);
        },

        layout: function() {
            new flowLayout(this);
        },

        _dispatchEvent: function (name) {

            if( !this.el ){
                return;
            }

            var evt = document.createEvent('Event'),
                el = this.el,
                options = this.options,
                onName = 'on' + name.charAt(0).toUpperCase() + name.substr(1);

            evt.initEvent(name, true, true);

            evt.match_mode = this.match_mode;
            evt.handle_node = this.handle_node;
            evt.target = this.target;

            el.dispatchEvent(evt);

            if (options[onName]) {
                options[onName].call(this, evt);
            }
        },

        Animation: function(elem,top,left){

            if( !top && !left ){
                return;
            }


            if( top ){
                _css(elem,'top',top);
            }
            if( left ){
                _css(elem,'left',left);
            }


            /**
            if( elem === this.c_op ){ //临时生成的辅助元素直接定位
                if( top ){
                    _css(elem,'top',top);
                }
                if( left ){
                    _css(elem,'left',left);
                }
                return;
            }

            startMove(elem,{
                top: top || elem.offsetTop,
                left: left || elem.offsetLeft
            });

            function startMove(obj, pos, onEnd){

                //获取最终样式
                function getStyle(obj, attr){
                    return parseFloat(obj.currentStyle ? obj.currentStyle[attr] : getComputedStyle(obj, null)[attr]);
                }

                function doMove(obj, pos, onEnd){
                    var iCurL = getStyle(obj, "left");
                    var iCurT = getStyle(obj, "top");
                    var iSpeedL = (pos.left - iCurL) / 3;
                    var iSpeedT = (pos.top - iCurT) / 3;
                    iSpeedL = iSpeedL > 0 ? Math.ceil(iSpeedL) : Math.floor(iSpeedL);
                    iSpeedT = iSpeedT > 0 ? Math.ceil(iSpeedT) : Math.floor(iSpeedT);
                    if (pos.left == iCurL && pos.top == iCurT) {
                        clearInterval(obj.timer);
                        onEnd && onEnd();
                    } else {
                        obj.style.left = iCurL + iSpeedL + "px";
                        obj.style.top = iCurT + iSpeedT + "px";
                    }
                }
                clearInterval(obj.timer);
                obj.timer = setInterval(function (){
                    doMove(obj, pos, onEnd);
                }, 20);
            }
             **/

        },

        _nulling:function() {

            G_POSITION = [];

            this._lastX = '';
            this._lastY = '';
            this.handleOrigin = null;
            this.match_mode = 0;
            this.handle_node = null;
            this.is_move = 0;
            this.target = '';
            this.origin = {};
            this.maxLeft = 0;
            this.maxTop = 0;
            this.disx = 0;
            this.disy = 0;
            this.c_op = '';
            this.selected();

            //删除临时元素
            var list = this.el.querySelectorAll('.react-item-create');
            var list_length = list.length;
            if( list_length > 0 ){
                for(var i=0; i<list_length; i++){
                    this.el.removeChild(list[i]);
                }
            }

        },

        destroy: function () {
            var el = this.el;

            el[expando] = null;

            _off(el, 'mousedown', this._onTapStart);
            _off(el, 'touchstart', this._onTapStart);

            this._onDrop();

            this.el = el = null;
        }

    }


    /**
     * 流式布局处理
     * @class  flowLayout
     * @param FDOBJ  FreeDrag对象
     */
    var flowLayout = function( FDOBJ ) {
        this.FD = FDOBJ;

        var el = FDOBJ.el,
            options = FDOBJ.options;

        var list = el.querySelectorAll(options.dragNodeClass);
        var list_length = list.length;
        if( list_length > 0 ){
            for(var i=0; i<list_length; i++){
                _toggleClass(list[i],'react-moved-item',false);
            }
        }

        // Bind all private methods
        for (var fn in this) {
            if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
                this[fn] = this[fn].bind(this);
            }
        }

        this.optimize();
    }
    flowLayout.prototype.optimize = function() {

        var FD = this.FD;

        FD.position();

        var lis_length = G_POSITION.length;
        if( lis_length == 0 ){return false;}

        //循环校验每个元素是否还存在重叠的元素
        for( var i=0; i < lis_length; i++ ){
            G_POSITION[i] = {
                elem:G_POSITION[i].elem,
                top:G_POSITION[i].elem.offsetTop,
                left:G_POSITION[i].elem.offsetLeft,
                width:G_POSITION[i].elem.offsetWidth,
                height:G_POSITION[i].elem.offsetHeight
            };
            var cross_list = this.cross_get(G_POSITION[i],G_POSITION[i].elem);
            if( cross_list.length > 0 ){
                this.cross_handle(G_POSITION[i],cross_list[0]);
            }
        }

        this.space();
    }
    flowLayout.prototype.cross_handle = function(p1, p2) {

        //若两个元素都经过处理的，则跳过
        if( p1.elem.className.search("react-moved-item") != -1 && p2.elem.className.search("react-moved-item") != -1 ){
            return false;
        }

        var FD = this.FD,
            c_op = FD.c_op,
            match_mode = FD.match_mode,
            target = FD.target,
            targetOrigin = FD.origin,
            handleOrigin = FD.handleOrigin,
            xGap = FD.options.x_interval,
            yGap = FD.options.y_interval;

        var OA={},OB={};

        if(
            p1.elem.className.search("react-dragging") != -1
            ||
            p1.elem.className.search("react-moved-item") != -1
            ||
            p1.elem.className.search("react-item-create") != -1
        ){
            OA = p1;
            OB = p2;
        }
        else
        {
            OA = p2;
            OB = p1;
        }

        var a_x_min = OA.elem.offsetLeft;
        var a_x_max = OA.elem.offsetLeft+OA.elem.offsetWidth;
        var a_y_min = OA.elem.offsetTop;
        var a_y_max = OA.elem.offsetTop+OA.elem.offsetHeight;

        var b_x_min = OB.elem.offsetLeft;
        var b_x_max = OB.elem.offsetLeft+OB.elem.offsetWidth;
        var b_y_min = OB.elem.offsetTop;
        var b_y_max = OB.elem.offsetTop+OB.elem.offsetHeight;

        var case_flg = 0;

        if( a_y_min <= b_y_min ){

            if( a_x_max > b_x_min && a_x_max <= (b_x_min+20) ){
                case_flg = 1;
            } else if ( a_x_max > (b_x_min+20) && a_x_max < (b_x_max-20) ){
                case_flg = 2;
            } else if( a_x_max >= (b_x_max-20) && a_x_max < b_x_max ){
                case_flg = 3;
            } else if ( a_x_min>=(b_x_max-20) && a_x_min <= b_x_max ){
                case_flg = 4;
            } else if ( a_x_min > (b_x_min+20) && a_x_min < (b_x_max-20) ){
                case_flg = 5;
            } else if ( a_x_min > b_x_min && a_x_min <= (b_x_min+20) ){
                case_flg = 6;
            }

        } else {

            if( a_y_min < (b_y_min+20) ){
                case_flg = 11;
            } else if ( a_y_min >= (b_y_min+20) ){
                case_flg = 12;
            }

        }

        switch( case_flg ){

            case 2:
            case 3:
            case 4:
            case 5:
            case 11: //A元素在上，B元素在下
                var new_top = a_y_max+10;
                _toggleClass(OB.elem,'react-moved-item',true);
                G_POSITION[OB.elem.getAttribute('data-index')].top = new_top;
                FD.Animation(OB.elem,new_top);
                break;

            case 12: //B元素在上，A元素在下
                var new_top = b_y_max+10;
                _toggleClass(OB.elem,'react-moved-item',true);
                G_POSITION[c_op.getAttribute('data-index')].top = new_top;
                FD.Animation(c_op,new_top);
                break;

            default: //移动元素回归

                if(  OA.elem.className.search("react-dragging") != -1 ){ //拖拽的元素

                    var origin = match_mode==1 ? targetOrigin : handleOrigin;
                    var handleObj = match_mode==1 ? FD.handle_node : c_op;

                    if( (origin.top+origin.height) < b_y_min ){
                        var new_top = (b_y_min-yGap) < (origin.top+origin.height) ? origin.top : (b_y_min - origin.height - yGap);
                    } else if ( origin.top > b_y_max ){
                        var new_top = (b_y_max+yGap) > origin.top ? origin.top : (b_y_max+yGap);
                    } else {
                        var new_top = origin.top;
                    }

                    if( (origin.left+origin.width) < b_x_min ){
                        var new_left = (b_x_min-xGap) < (origin.left+origin.width) ? origin.left : (b_x_min - origin.width - xGap);
                    } else if ( origin.left > b_x_max ){
                        var new_left = (b_x_max+xGap) > origin.left ? origin.left : b_x_max + xGap;
                    } else {
                        var new_left = origin.left;
                    }

                    G_POSITION[handleObj.getAttribute('data-index')].top = new_top;
                    G_POSITION[handleObj.getAttribute('data-index')].left = new_left;
                    FD.Animation(handleObj,new_top,new_left);

                } else { //移动之后的元素

                    var new_top = a_y_max+yGap;
                    G_POSITION[OB.elem.getAttribute('data-index')].top = new_top;
                    FD.Animation(OB.elem,new_top);

                }

        }

        return case_flg;
    }
    /**
     * 获取重叠的元素
     * @param pos position中的对象
     * @param Filter1 需要过滤的对象
     * @param Filter2 需要过滤的对象
     * @returns {Array}
     */
    flowLayout.prototype.cross_get = function(pos,Filter1,Filter2) {
        var cross_obj_list = [];

        var length = G_POSITION.length;

        for(var i=0; i<length; i++){

            if( Filter1===G_POSITION[i].elem || Filter2===G_POSITION[i].elem ){
                continue;
            }

            if(
                pos.elem.className.search("react-dragging") != -1 && G_POSITION[i].elem.className.search("react-item-create") != -1
                ||
                G_POSITION[i].elem.className.search("react-dragging") != -1 && pos.elem.className.search("react-item-create") != -1
            ){
                continue;
            }

            if( is_cross(pos,G_POSITION[i]) ){
                cross_obj_list.push(G_POSITION[i]);
            }

        }

        return cross_obj_list;
    }
    /**
     * 从顶部依次往下进行移动，若某个位置没有交叉元素，则记录当前位置信息
     * @param pos 需要移动的元素位置信息
     * @return {Array}
     */
    flowLayout.prototype.movingY = function(pos) {
        var matched = [];
        var end_top = pos.top+pos.height;
        for(var i=0; i<=end_top; i++){
            pos.top = i;
            var crossed_num = 0;
            for( var j=0; j<G_POSITION.length; j++ ){
                if(
                    pos.elem == G_POSITION[j].elem
                    ||
                    pos.elem.className.search("react-dragging") !== -1
                    &&
                    G_POSITION[j].elem.className.search("react-item-create") !== -1
                ){
                    continue;
                }
                if( is_cross(pos,G_POSITION[j]) ){
                    crossed_num++;
                }
            }
            if( crossed_num == 0 ){
                matched.push(i);
            }
        }
        return matched;
    }
    flowLayout.prototype.movingX = function(pos) {
        var matched = [];
        var end_left = pos.left+pos.width;
        for(var i=0; i<=end_left; i++){
            pos.left = i;
            var crossed_num = 0;
            for( var j=0; j<G_POSITION.length; j++ ){
                if(
                    pos.elem == G_POSITION[j].elem
                    ||
                    pos.elem.className.search("react-dragging") !== -1
                    &&
                    G_POSITION[j].elem.className.search("react-item-create") !== -1
                ){
                    continue;
                }
                if( is_cross(pos,G_POSITION[j]) ){
                    crossed_num++;
                }
            }
            if( crossed_num == 0 ){
                matched.push(i);
            }
        }
        return matched;
    }
    /**
     * 空白区域的处理
     */
    flowLayout.prototype.space = function() {

        var FD = this.FD;

        FD.position();

        var yGap = this.FD.options.y_interval;
        var xGap = this.FD.options.x_interval;

        //Y轴方向的移动检查
        for( var j=0; j<G_POSITION.length; j++  ){
            if(
                G_POSITION[j].elem.className.search("react-item-create") !== -1
            ){
                continue;
            }
            var matched = this.movingY(G_POSITION[j]);
            if( matched.length > 0 ){
                var new_top = (matched[matched.length-1] - matched[0]) > yGap ? (matched[0]+yGap) : G_POSITION[j].top ;
                G_POSITION[j].top = new_top;
                if( G_POSITION[j].elem.className.search("react-dragging") !== -1 ){
                    FD.Animation(FD.c_op,new_top);
                } else {
                    FD.Animation(G_POSITION[j].elem, new_top);
                }

            }
        }

        //X轴方向的移动检查
        /**
        for( var j=0; j<G_POSITION.length; j++  ){
            if(
                G_POSITION[j].elem.className.search("react-item-create") !== -1
            ){
                continue;
            }
            var matched = this.movingX(G_POSITION[j]);
            if( matched.length > 0 ){
                var new_left = (matched[matched.length-1] - matched[0]) > xGap ? (matched[0]+xGap) : G_POSITION[j].left ;
                G_POSITION[j].left = new_left;
                if( G_POSITION[j].elem.className.search("react-dragging") !== -1 ){
                    FD.Animation(FD.c_op, null, new_left);
                } else {
                    FD.Animation(G_POSITION[j].elem, null, new_left);
                }
            }
        }
         **/

    }

    /**
     * Create FreeDrag instance
     * @param {HTMLElement}  el
     * @param {Object}    [options]
     */
    FreeDrag.create = function (el, options) {
        return new FreeDrag(el, options);
    };

    /**
     * Create multi FreeDrag instance
     * @param elems dom对象列表
     * @param options
     * @returns {FreeDrag}
     */
    FreeDrag.MultiCreate = function (elems, options) {
        var len = elems.length;
        if( len == 0 ){
            return false;
        }
        for(var i=0; i<len; i++){
            new FreeDrag(elems[i], options);
        }
    };

    window.FreeDrag = FreeDrag;

})();