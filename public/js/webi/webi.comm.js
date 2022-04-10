/*
 * 通用的JS处理函数
 * webi.comm.JS
 * version 1.0
 * author: zoey
 */
 
(function(){

	var _w = window,
        _s = self,
        _d = document,
        _n = navigator;
		
	var q = void 0,
		r = !0,
		t = null,
		u = !1;
	
	var WeBI = {};
	
	//生成guid
	WeBI.guid = function () {
        return 'xxxxxxxxxxxx4xxxyxxxxxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
        });
    };

    /**
     * 深度拷贝
     * @param obj
     * @returns {*}
     */
	WeBI.deepClone = function(obj){
        let objClone = Array.isArray(obj)?[]:{};
        if(obj && typeof obj==="object"){
            for(key in obj){
                if(obj.hasOwnProperty(key)){
                    //判断ojb子元素是否为对象，如果是，递归复制
                    if(obj[key]&&typeof obj[key] ==="object"){
                        objClone[key] = deepClone(obj[key]);
                    }else{
                        //如果不是，简单复制
                        objClone[key] = obj[key];
                    }
                }
            }
        }
        return objClone;
    };

    /**
     * 对象的合成
     * @param dst 原始对象
     * @param src 参数对象
     * @returns {*}
     * @private
     */
    WeBI.extend = function(dst, src) {
        if (dst && src) {
            for (var key in src) {
                if (src.hasOwnProperty(key)) {
                    dst[key] = src[key];
                }
            }
        }
        return dst;
    };

    /**
     * 给元素添加/移除class
     * @param el
     * @param name
     * @param state
     * @private
     */
    WeBI.toggleClass = function (el, name, state) {
        if (el && name) {
            if (el.classList) {
                el.classList[state ? 'add' : 'remove'](name);
            }
            else {
                var className = (' ' + el.className + ' ').replace(R_SPACE, ' ').replace(' ' + name + ' ', ' ');
                el.className = (className + (state ? ' ' + name : '')).replace(R_SPACE, ' ');
            }
        }
    };

    /**
     * 处理元素css样式
     * @param el
     * @param prop
     * @param val
     * @returns {*}
     * @private
     */
    WeBI.css = function(el, prop, val) {
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
    };

    /**
	 * 判断是否为空对象
     * @param obj
     * @returns {boolean}
     */
	WeBI.isEmptyObject = function(obj){
        switch ( typeof obj){
            case 'undefined':
                	return true;
                break;
            case 'object':
					var fy = JSON.stringify(obj);
					if( fy=='{}' || fy=='[]' || fy=='null' ){
						return true;
					}
				break;
            default:
                return true;
        }
        return false;
	};
	
	//浏览器相关操作
	WeBI.browser = {};
	WeBI.browser.findDimensions = function(){
		
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
		
	};
	WeBI.browser.versions = function(){
		var u = navigator.userAgent, app = navigator.appVersion;
		return {
			trident: u.indexOf('Trident') > -1, //IE内核
			presto: u.indexOf('Presto') > -1, //opera内核
			webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
			gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1,//火狐内核
			mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
			ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
			android: u.indexOf('Android') > -1 || u.indexOf('Adr') > -1, //android终端
			iPhone: u.indexOf('iPhone') > -1 , //是否为iPhone或者QQHD浏览器
			iPad: u.indexOf('iPad') > -1, //是否iPad
			webApp: u.indexOf('Safari') == -1, //是否web应该程序，没有头部与底部
			weixin: u.indexOf('MicroMessenger') > -1, //是否微信
			qq: u.match(/\sQQ/i) == " qq" //是否QQ
		};
	};
	WeBI.browser.isMobile = function(){
        var versions = BI.browser.versions();
        if( versions.mobile || versions.ios || versions.iPhone || versions.weixin ){
            return 1;
        } else {
            return 0;
		}
	};

	/*
	 * 事件处理类
	 */
	WeBI.event = {};
	WeBI.event.bind = function(obj, action, d){
		obj.attachEvent ? obj.attachEvent("on" + action,
		function(action) {
			d.call(obj, action)
		}) : obj.addEventListener && obj.addEventListener(action, d , u)
	};
    WeBI.event._off = function(el, event, fn) {
        el.removeEventListener(event, fn, captureMode);
    };
	WeBI.event.preventDefault = function(obj) {
        obj.preventDefault ? obj.preventDefault() : obj.returnValue = u
    };
	
	/*
	 * 位置信息处理类
	 */
	WeBI.position = {};
	WeBI.position.getAbsolute = function (reference, target) {
        var result = {
             left: -target.clientLeft,
            top: -target.clientTop
        }
        var node = target;
         while(node != reference && node != document){
            result.left = result.left + node.offsetLeft + node.clientLeft;
            result.top = result.top + node.offsetTop + node.clientTop;
            node = node.parentNode;
        }
        if(isNaN(reference.scrollLeft)){
            result.right = document.documentElement.scrollWidth - result.left;
            result.bottom = document.documentElement.scrollHeight - result.top;
        }else {
            result.right = reference.scrollWidth - result.left;
            result.bottom = reference.scrollHeight - result.top;
        }
        return result;
    }
	WeBI.position.getViewport = function (target) {
         var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
         var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft;
         var windowHeight = window.innerHeight || document.documentElement.offsetHeight;
         var windowWidth = window.innerWidth || document.documentElement.offsetWidth;
         var absolutePosi = WeBI.position.getAbsolute(document, target);
         return {
            left: absolutePosi.left - scrollLeft,
            top: absolutePosi.top - scrollTop,
            right: windowWidth - (absolutePosi.left - scrollLeft),
            bottom: windowHeight - (absolutePosi.top - scrollTop)
         }
    };
	
	/*
	 * cookie操作
	 */
	WeBI.cookie = {};
    WeBI.cookie.set = function(k, v, t) {
        var domain = _d.domain.replace('www','');
        var cookieTime = t || 0;
        var exp = new Date();
        exp.setTime(exp.getTime() + cookieTime * 1000);

        if (cookieTime == 0)
            document.cookie = k + "=" + encodeURI(v) + ";path=/;domain=" + domain + ";";
        else
            document.cookie = k + "=" + encodeURI(v) + ";expires=" + exp.toGMTString() + ";path=/;domain=" + domain + ";";
    };
    WeBI.cookie.get = function(k) {
        var strCookie = _d.cookie;
        var arrCookie = strCookie.split("; ");
        var arrCookieCount = arrCookie.length;
        var arr,identifyContent = null;
        for(var i = 0; i < arrCookieCount ; i++){
            arr = arrCookie[i].split("=");
            if(k == arr[0]){
                identifyContent = decodeURIComponent(decodeURIComponent(arr[1]));
                break;
            }
        }
        if (identifyContent == null) return null; else return identifyContent;
    };
	
	/*
	 * JS处理ajax请求
	 */
	WeBI.ajax = {};
	WeBI.ajax.get = function(url, fn){
		var xhr = new XMLHttpRequest();
		xhr.open('GET', url, true);
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && xhr.status == 200 || xhr.status == 304) {
				fn.call(this, xhr.responseText);
			}
		};
		xhr.send();
	};
	WeBI.ajax.post = function(url, data, fn){
		var params = '';
		if( typeof data == 'object' ){
			for( k in data ){
				params += k+'='+data[k]+'&';
			}
			params = params.slice(0,-1);
		} else {
			params = data;
		}
		var xhr = new XMLHttpRequest();
		xhr.open("POST", url, true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 304)) {
				fn.call(this, xhr.responseText);
			}
		};
		xhr.send(params);
	};

	/*
	 * JS排序
	 */
	 WeBI.key_sort = function(resource){
		 var sdic = Object.keys(resource).sort();
		 var new_obj = {};
		 for(ki in sdic){
			new_obj[sdic[ki]] = resource[sdic[ki]];
		 }
		 return new_obj;
	 };
	 
	 WeBI.val_sort = function(resource,val_key,type){
		 var type = type || 1;
		 if( type == 1 ){ //按值升序
			var sdic = Object.keys(resource).sort(function(a,b){return resource[a][val_key] - resource[b][val_key]});
		 } else { //按值降序
			var sdic = Object.keys(resource).sort(function(a,b){return resource[a][val_key] < resource[b][val_key]});
		 }
		 var new_obj = {};
		 for(ki in sdic){
			new_obj[sdic[ki]] = resource[sdic[ki]];
		 }
		 return new_obj;
	 };
	
	//发起jsonp请求
    WeBI.ls = function (url, callback) {
        var script = document.createElement("script");
        script.type = "text/javascript";
        if (script.readyState){ //IE
            script.onreadystatechange = function(){
                if (script.readyState == "loaded" ||
                    script.readyState == "complete"){
                    script.onreadystatechange = null;
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            };
        } else { //Others: Firefox, Safari, Chrome, and Opera
            script.onload = function(){
                if (typeof callback === 'function') {
                    callback();
                }
            };
        }
        script.src = url;
        document.body.appendChild(script);

    };
	
	//获取相邻元素节点
	WeBI.siblingElem = function(elem){
        var _nodes = [],
            _elem = elem;
        while ((elem = elem.previousSibling)){
            if(elem.nodeType === 1){
                _nodes.push(elem);
                break;
            }
        }

        elem = _elem;
        while ((elem = elem.nextSibling)){
            if(elem.nodeType === 1){
                _nodes.push(elem);
                break;
            }
        }

        return _nodes;
    };

	//设置select下拉框指定文本内容选项为选中状态
    WeBI.selectChoose = function(id_obj,target_val) {
    	if( typeof id_obj == 'string' ){
            var select = document.getElementById(id_obj);
		} else {
            var select = id_obj;
		}
        var options_length = select.options.length;
        for(var i=0; i<options_length; i++){
            if(select.options[i].innerHTML == target_val){
                select.options[i].selected = true;
                break;
            }
        }
	};

	window.BI = WeBI;

})();