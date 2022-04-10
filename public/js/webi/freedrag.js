(function(){

    var q = void 0,
        r = !0,
        t = null,
        u = !1,

    captureMode = false,

    /** @const */
    R_SPACE = /\s+/g;

    /**
     * 对象的合成
     * @param dst 原始对象
     * @param src 参数对象
     * @returns {*}
     * @private
     */
    window._extend = function(dst, src) {
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
    window._toggleClass = function(el, name, state) {
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
    window._css = function(el, prop, val) {
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
    window._on = function(obj, action, d){
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
    window._off = function(el, event, fn) {
        el.removeEventListener(event, fn, captureMode);
    }

})();

(function()  {

    /**
     * 坐标处理类
     * @param obj
     * @returns {Coords}
     * @constructor
     */
    var Coords = function (){}

    Coords.prototype = {

        constructor: Coords,

        sortPos: function (arry) {
            var posCompare = function(p1, p2) {
                if (p1.top > p2.top) {
                    return true;
                } else if (p1.top == p2.top) {
                    return (p1.left > p2.left);
                } else {
                    return false;
                }
            }
            var len = arry.length;
            for (var i = 0; i < len - 1; i++) {
                for (var j = 0; j < len - 1 - i; j++) {
                    if (posCompare(arry[j], arry[j + 1])) {
                        var tmp = arry[j];
                        arry[j] = arry[j + 1];
                        arry[j+1] = tmp;
                    }
                }
            }
            return arry;
        },

        /**
         *  获取DOM元素定位信息
         * @param el
         * @returns {{x1, y1, x2: *, y2: *, cx: *, cy: *, left, top, width: number, height: number}}
         */
        get: function(el) {
            var pos = {
                x1: el.offsetLeft,
                y1: el.offsetTop,
                x2: el.offsetLeft + el.offsetWidth,
                y2: el.offsetTop + el.offsetHeight,
                cx: el.offsetLeft + (el.offsetWidth / 2),
                cy: el.offsetTop + (el.offsetHeight / 2),
                width: el.offsetWidth,
                height: el.offsetHeight,
                elem: el
            };
            el['origion'] = pos;
            return pos;
        }

    }

    window.Coords = new Coords();

})();

(function(){

    /**
     *
     * @param FDOBJ FreeDrag对象
     * @constructor
     */
    var Collision = function(FDOBJ) {
        this.FD = FDOBJ;
    };

    Collision.prototype = {

        constructor: Collision,

        /**
         * 判断两个坐标元素是否重叠
         * @param p1
         * @param p2
         * @returns {boolean}
         */
        is_cross: function(p1,p2) {

            var o_top = parseInt(p1.y1);
            var o_left = parseInt(p1.x1);
            var o_width = parseInt(p1.width);
            var o_height = parseInt(p1.height);

            var y = parseInt(p2.y1);
            var x = parseInt(p2.x1);
            var w = parseInt(p2.width);
            var h = parseInt(p2.height);

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
        },

        /**
         * 计算重叠区域的坐标
         * @param a
         * @param b
         */
        calculate_overlapped_area_coords: function(a, b){
            var x1 = Math.max(a.x1, b.x1);
            var y1 = Math.max(a.y1, b.y1);
            var x2 = Math.min(a.x2, b.x2);
            var y2 = Math.min(a.y2, b.y2);
            return {
                left: x1,
                top: y1,
                width : (x2 - x1),
                height: (y2 - y1)
            };
        },

        /**
         * 计算重叠区域面积
         * @param coords
         * @returns {number}
         */
        calculate_overlapped_area: function(coords){
            return (coords.width * coords.height);
        },

        /**
         * 根据预设的规则，判断两个重叠元素的处理标志
         * @param a  Coords对象
         * @param b  Coords对象
         * @returns {number}
         */
        detect_from_overlapping: function(a, b) {

            var FD = this.FD,
                flg = 0;

            var A=null,B=null;

            if( FD.is_drager(a.elem) || FD.is_moved_node(a.elem) || FD.is_helper(a.elem) || FD.is_resized_node(a.elem) ){
                A = a;
                B = b;
            } else {
                A = b;
                B = a;
            }

            //基准元素的原始位置
            var origion = FD.is_drager(A.elem) ? FD.handleOrigin : A.elem.origion;

            if( FD.is_drager(A.elem) ){
                drager_handel();
            } else {
                moved_handel();
            }

            function x_satisfy(){
                if( A.x2 >= (B.x1+20) || A.x2 <= (B.x2-20) ){
                    return true;
                } else {
                    return false;
                }
            }

            function drager_handel(){
                if( A.y1 <= B.y1 )
                {

                    if( x_satisfy() ){  //达到了触发移动的边界

                        if ( (A.y2+10) >= B.y2 && Math.abs(origion.x1-A.x1) <= Math.abs(origion.y1-A.y1) ){
                            flg = 1; //垂直方向移动重叠，A的高度即将覆盖B的高度，B元素往上移动
                        } else {
                            flg = 2; //左右侧重叠。B元素往下移动
                        }

                    }

                }
                else
                {

                    if( x_satisfy() ){

                        if( A.y1 <= (B.y1+20) && Math.abs(origion.x1-A.x1) <= Math.abs(origion.y1-A.y1) ){
                            flg = 100;  //下方重叠，A元素上移，B元素移动到A的下面
                        } else if ( A.y1 <= (B.y1+20) ){
                            flg = 101;  //两侧侧重叠，B元素下移
                        } else if ( A.y1 > (B.y1+20) && A.x2 >= (B.x1+20) ){
                            flg = 102;  //两侧重叠，A元素下移
                        }

                    }

                }
            }

            function moved_handel(){
               flg = 1000;
            }

            return {
                A: A,
                B: B,
                flg: flg
            };

        },

        /**
         * 找出与坐标元素碰撞的元素
         * @param $source 坐标元素
         * @param player_data_coords 所有拖拽元素坐标集合
         * @returns {Array}
         */
        find_collisions: function($source, player_data_coords){

            var FD = this.FD;

            var colliders = [];
            var count = player_data_coords.length;

            for( var i=0; i < count; i++ ){

                player_data_coords[i] = Coords.get(player_data_coords[i].elem);

                if( FD.skip_check($source.elem,player_data_coords[i].elem) ) {
                    continue;
                }

                if( !this.is_cross($source, player_data_coords[i]) ){
                    continue;
                }

                var new_obj = _extend({},player_data_coords[i]);
                new_obj.area_coords = this.calculate_overlapped_area_coords($source, player_data_coords[i]);
                new_obj.area = this.calculate_overlapped_area(new_obj.area_coords);

                colliders.push(new_obj);
            }

            return colliders;
        },

        /**
         * 获取最靠近的冲突元素
         * @param player_data_coords 所有拖拽元素的坐标数据集合
         * @returns {*}
         */
        get_closest_colliders: function($source,player_data_coords){

            var colliders = this.find_collisions($source,player_data_coords);
            if( colliders.length == 0 ){
                return colliders;
            }

            colliders.sort(function(a, b) {
                return a.area < b.area
            });

            return colliders;

        }

    }

    window.Collision = Collision;

})();

(function(){

    var G_POSITION = [],
        expando = 'FreeDrag' + (new Date).getTime();

    var defaults = {
        mode:1, //模式： 1.任意拖拽布局  2.流式布局
        draggable: '.drag-item', //定义触发移动节点的class
        dragNodeClass: '.drag-grid', //定义触发移动时的拖动元素
        zoomable: '.zoom-item', //定义触发缩放节点的class
        zoomNodeClass: '.drag-grid', //定义触发缩放时的变更元素
        x_interval:10, //水平方向上元素之间的间隔
        y_interval:10, //垂直方向上元素之间的间隔
        draggingClass: "drag-selected" //被拖动元素的赋值的class
    };

    /**
     * @class  FreeDrag
     * @param  {HTMLElement}  el
     * @param  {Object}  [options]
     */
    var FreeDrag = function(el,options) {
        if (!(el && el.nodeType && el.nodeType === 1)) {
            throw 'FreeDrag: `el` must be HTMLElement, and not ' + {}.toString.call(el);
        }

        this.el = el; // root element
        this.options = _extend( defaults, options );

        // Export instance
        el[expando] = this;

        // Bind all private methods
        for (var fn in this) {
            if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
                this[fn] = this[fn].bind(this);
            }
        }

        // 绑定拖拽事件
        _on(el, 'mousedown', this._onTapStart);
        _on(el, 'touchstart', this._onTapStart);
    }

    FreeDrag.prototype = {

        constructor: FreeDrag,

        clone_helper: function() {

            var _this = this,
                el = _this.el,
                options = _this.options;

            this.$preview_holder = document.createElement('div');
            el.appendChild(this.$preview_holder);
            _toggleClass(this.$preview_holder,options.dragNodeClass.replace('.',''),true);
            _toggleClass(this.$preview_holder,'react-item-create',true);
            _css(this.$preview_holder, 'top', _this.handle_node.offsetTop);
            _css(this.$preview_holder, 'left', _this.handle_node.offsetLeft);
            _css(this.$preview_holder, 'width', _this.handle_node.offsetWidth);
            _css(this.$preview_holder, 'height', _this.handle_node.offsetHeight);
            _css(this.$preview_holder, 'background-color', el.style.backgroundColor);
            _css(this.$preview_holder, 'opacity', '0.5');
            _css(this.$preview_holder, 'position', 'absolute');
            _css(this.$preview_holder, 'z-index', '0');

        },
        remove_helper:function() {
            var list = this.el.querySelectorAll('.react-item-create');
            var list_length = list.length;
            if( list_length > 0 ){
                for(var i=0; i<list_length; i++){
                    this.el.removeChild(list[i]);
                }
            }
        },

        selected: function() {

            var handle_node = this.handle_node,
                options = this.options;

            var list = document.querySelectorAll(options.dragNodeClass);
            var list_length = list.length;
            if( list_length > 0 ){
                for(var i=0; i<list_length; i++){
                    _toggleClass(list[i],options.draggingClass,false);
                    _toggleClass(list[i],'react-dragging-item',false);
                    _toggleClass(list[i],'react-resized-item',false);
                }
            }
            if( handle_node ){
                _toggleClass(handle_node,options.draggingClass,true);
                var className = this.match_mode==2 ? 'react-resized-item' : 'react-dragging-item';
                _toggleClass(handle_node,className,true);
            }
        },

        skip_check: function(aElem,bElem) {
            if(
                aElem === bElem
                ||
                this.is_drager(aElem) && this.is_helper(bElem)
                ||
                this.is_drager(bElem) && this.is_helper(aElem)
                ||
                this.is_resized_node(aElem) && this.is_helper(bElem)
                ||
                this.is_resized_node(bElem) && this.is_helper(aElem)
            )
            {
                return true;
            }
            else
            {
                return false;
            }
        },

        is_drager: function(el) {
            if( el.className.search('react-dragging-item') !== -1 ){
                return true;
            } else {
                return false;
            }
        },
        is_moved_node: function(el) {
            if( el.className.search('react-moved-item') !== -1 ){
                return true;
            } else {
                return false;
            }
        },
        is_helper: function(el) {
            if( el.className.search('react-item-create') !== -1 ){
                return true;
            } else {
                return false;
            }
        },
        is_resized_node: function(el) {
            if( el.className.search('react-resized-item') !== -1 ){
                return true;
            } else {
                return false;
            }
        },
        is_drag_node: function(el) {
            var options = this.options;
            if( el.className.search(options.dragNodeClass.replace('.','')) !== -1 ){
                return true;
            } else {
                return false;
            }
        },
        is_zoom_node: function(el) {
            var options = this.options;
            if( el.className.search(options.zoomNodeClass.replace('.','')) !== -1 ){
                return true;
            } else {
                return false;
            }
        },
        is_dragable_node: function(el) {
            var options = this.options;
            if( el.className.search(options.draggable.replace('.','')) !== -1 ){
                return true;
            } else {
                return false;
            }
        },
        is_zoomable_node: function(el) {
            var options = this.options;
            if( el.className.search(options.zoomable.replace('.','')) !== -1 ){
                return true;
            } else {
                return false;
            }
        },

        /**
         * 匹配元素是否为拖拽或者缩放元素
         */
        scan_node: function() {

            var options = this.options,
                dragger = this.dragger;

            this.match_mode = 0;
            this.handle_node = null;

            if( this.is_dragable_node(dragger) ){
                this.match_mode = 1;
            } else if ( this.is_zoomable_node(dragger) ){
                this.match_mode = 2;
            }

            if( this.match_mode == 0 ){
                return;
            }

            var handle_node = dragger; //从当前元素开始向上遍历校验，寻找符合的元素
            while(1){
                if(
                    this.match_mode == 1 && this.is_drag_node(handle_node)
                    ||
                    this.match_mode == 2 && this.is_zoom_node(handle_node)
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

            this.dragger = target;

            evt.preventDefault();
            evt.stopPropagation();

            this._lastX = (touch || evt).clientX;
            this._lastY = (touch || evt).clientY;

            //清空之前的绑定事件
            _this._offUpEvents();

            //匹配符合条件的元素
            this.scan_node();

            if( this.match_mode == 0 ){ //没有任何绑定事件的元素跳过
                return false;
            }

            this._preDragStart(evt);

        },

        _preDragStart:function(/** Event|TouchEvent */evt) {

            var _this = this,
                el = this.el,
                options = this.options,
                touch = evt.touches && evt.touches[0],
                target = (touch || evt).target;

            this.original_coords = Coords.get(target);
            this.handleOrigin = Coords.get(this.handle_node);

            this.maxLeft = el.offsetWidth - target.offsetWidth;
            this.maxTop = el.offsetHeight - target.offsetHeight;

            if( this.match_mode == 2 ){ //缩放
                this.disx = _this._lastX - target.offsetLeft;
                this.disy = _this._lastY - target.offsetTop;
            } else { //拖动
                this.disx = _this._lastX - this.handle_node.offsetLeft;
                this.disy = _this._lastY - this.handle_node.offsetTop;
            }

            //创建临时元素，用于计算定位
            _this.clone_helper();

            //设置拖拽目标选中class
            _this.selected();

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
                dragger = this.dragger,
                $preview_holder = this.$preview_holder,
                match_mode = this.match_mode,
                handle_node = this.handle_node,
                options = this.options;

            if( match_mode == 0 || !handle_node ){ //没有任何绑定事件的元素跳过
                return false;
            }

            this.is_moving = 1;

            evt.preventDefault();
            evt.stopPropagation();

            var sildLeft = evt.clientX - this.disx;
            var slidTop = evt.clientY - this.disy;

            if( sildLeft <= 0 ){
                sildLeft = 0;
            } else if ( sildLeft >= this.maxLeft ){
                sildLeft = this.maxLeft;
            }

            if( slidTop <= 0 ){
                slidTop = 0;
            } else if ( slidTop >= this.maxTop ){
                slidTop = this.maxTop;
            }

            switch ( match_mode ){
                case 1: //拖拽
                        _css(handle_node,'left',sildLeft);
                        _css(handle_node,'top',slidTop);
                        _css($preview_holder,'top',slidTop);
                        _css($preview_holder,'left',sildLeft);
                    break;
                default: //缩放
                    _css(dragger,'left',sildLeft);
                    _css(dragger,'top',slidTop);
                    _css(handle_node,'width', (dragger.offsetLeft + dragger.offsetWidth) );
                    _css(handle_node,'height', (dragger.offsetTop + dragger.offsetHeight) );
                    _css($preview_holder,'left',handle_node.offsetLeft);
                    _css($preview_holder,'top',handle_node.offsetTop);
                    _css($preview_holder,'width', (dragger.offsetLeft + dragger.offsetWidth) );
                    _css($preview_holder,'height', (dragger.offsetTop + dragger.offsetHeight) );
            }

            this.layout();
            this._dispatchEvent('move');

        },

        _onDrop:function(evt) {

            evt.preventDefault();
            evt.stopPropagation();

            var el = this.el,
                handle_node = this.handle_node,
                c_op = this.$preview_holder;

            this.Animation(handle_node,c_op.offsetTop,c_op.offsetLeft);

            if( this.layoutObj ){
                this.layoutObj.check_all_module();
            }

            this._offUpEvents();

            if( this.is_moving ){
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
                options = this.options;

            G_POSITION = [];
            var lis = el.querySelectorAll(options.dragNodeClass);
            var lis_length = lis.length;
            if( lis_length == 0 ){return false;}
            for(var i=0; i<lis_length; i++){
                G_POSITION.push(Coords.get(lis[i]));
            }
            G_POSITION = Coords.sortPos(G_POSITION);
        },

        layout: function() {

            switch ( this.options.mode ){

                case 1:
                        return;
                    break;

                default:
                    this.layoutObj = new flowLayout(this);

            }

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
            evt.target = this.dragger;

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

        },

        _nulling:function() {
            G_POSITION = [];
            this.maxLeft = 0;
            this.maxTop = 0;
            this.disx = 0;
            this.disy = 0;
            this._lastX = '';
            this._lastY = '';
            this.handleOrigin = {};
            this.original_coords = {};
            this.match_mode = 0;
            this.is_moving = 0;
            this.handle_node = null;
            this.dragger = null;
            this.$preview_holder = null;
            this.selected();
            this.remove_helper();
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

        this.collision_api = new Collision(FDOBJ);

        this.init();

        FDOBJ.position();

        this.collision();
    }

    flowLayout.prototype.init = function() {

        var FD = this.FD,
            el = FD.el,
            options = FD.options;

        var list = el.querySelectorAll(options.dragNodeClass);
        var list_length = list.length;
        if( list_length > 0 ){
            for(var i=0; i<list_length; i++){
                _toggleClass(list[i],'react-moved-item',false);
            }
        }
    }

    flowLayout.prototype.collision = function(){
        var FD = this.FD;

        var lis_length = G_POSITION.length;

        //循环校验每个元素是否还存在重叠的元素
        for( var i=0; i < lis_length; i++ ){
            G_POSITION[i] = Coords.get(G_POSITION[i].elem);
            var colliders = this.collision_api.get_closest_colliders(G_POSITION[i], G_POSITION);
            if( colliders.length > 0 ){

                var handle_obj = colliders[colliders.length-1];
                if( FD.is_helper(handle_obj.elem) && colliders.length>1 ){ //若是辅助元素，则换成实际的拖拽元素（或是拖拽元素）
                    handle_obj = colliders[colliders.length-2];
                }

                this.collision_handle( G_POSITION[i], handle_obj);
            }
        }

        this.check_all_module();

    }

    flowLayout.prototype.collision_handle = function(a , b) {

        var FD = this.FD,
            yGap = FD.options.y_interval,
            match_mode = FD.match_mode;

        //获取元素移动规则标识
        var rule = this.collision_api.detect_from_overlapping(a,b);

        var A = rule.A; //基准元素
        var B = rule.B; //操作元素
        var A_elem = FD.is_drager(A.elem) ? FD.$preview_holder : A.elem;
        var origin = FD.is_drager(A.elem) ? FD.handleOrigin : A.elem.origion;

        _toggleClass(B.elem,'react-moved-item',true);

        switch ( rule.flg ){

            case 1:
                    _css(B.elem,'top',A.y1);
                    _css(A_elem,'top', (parseInt(A.y1)+parseInt(B.height)+parseInt(yGap)) );
                break;

            case 2:
            case 101:
            case 1000:
                    _css(B.elem,'top', (parseInt(A.y2)+parseInt(yGap)) );
                break;

            case 100:
                    _css(A_elem,'top',B.y1);
                    _css(B.elem,'top', (parseInt(B.y1)+parseInt(A.height)+parseInt(yGap)) );
                break;

            case 102:
                    _css(A_elem,'top', (parseInt(B.y2)+parseInt(yGap)) );
                break;

            default:
                if( match_mode == 1 ){
                    _css(A_elem,'left', origin.x1 );
                    _css(A_elem,'top', origin.y1 );
                } else {
                    _css(A_elem,'width', origin.width );
                    _css(A_elem,'height', origin.height );
                }

        }

    }

    flowLayout.prototype.check_all_module = function() {

        var FD = this.FD;

        /**
         * Y轴方向的检查
         * 1、拖拽元素无需检查
         * 2、已经移动过的元素无需检查
         */
        FD.position();
        var y_count = G_POSITION.length;
        for( var i=0; i<y_count; i++ ){
            if( FD.is_drager(G_POSITION[i].elem) || FD.is_moved_node(G_POSITION[i].elem) ){
                continue;
            }
            this.check_Y(G_POSITION[i]);
        }

    }

    flowLayout.prototype.check_Y = function(pos) {

        var FD = this.FD,
            yGap = FD.options.y_interval,
            temp = _extend({},pos),
            matched = [],
            end_top = temp.y1;

        for(var i=yGap; i<=end_top; i++){
            temp.y1 = i;
            var crossed_flg = 0;
            for( var j=0; j<G_POSITION.length; j++ ){
                if( FD.skip_check(temp.elem, G_POSITION[j].elem) ) {
                    continue;
                }
                if( this.collision_api.is_cross(temp,G_POSITION[j]) && !FD.is_drager(G_POSITION[j].elem) ){
                    matched = [];
                    i = parseInt(G_POSITION[j].y2)+yGap-1;
                    crossed_flg = 1;
                    break;
                }
            }
            if( crossed_flg == 0 ){
                matched.push(i);
            }
        }

        if( matched.length > 0 ){
            var new_top = ( parseInt(matched[matched.length-1])-parseInt(matched[0]) ) >= yGap ? matched[0] : matched[matched.length-1];
            var changeElem = FD.match_mode==2 && FD.is_resized_node(pos.elem) ? FD.$preview_holder : pos.elem;
            _css(changeElem,'top',new_top);
        }

    }

    flowLayout.prototype.check_X = function(pos) {
        var FD = this.FD,
            xGap = FD.options.x_interval,
            temp = _extend({},pos),
            matched = 0,
            end_left = temp.x1;

        for(var i=xGap; i<=end_left; i++){
            temp.x1 = i;
            var crossed_flg = 0;
            for( var j=0; j<G_POSITION.length; j++ ){
                if( FD.skip_check(temp.elem, G_POSITION[j].elem) ) {
                    continue;
                }
                if( this.collision_api.is_cross(temp,G_POSITION[j]) ){
                    i = G_POSITION[j].x2+xGap;
                    crossed_flg = 1;
                    break;
                }
            }
            if( crossed_flg == 0 ){
                matched = i;
                break;
            }
        }

        if( matched != 0 ){
            pos.x1 = matched;
            _css(pos.elem,'left',matched);
        }
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