
//大争科技JS库
(function ($){

    $.dz = {
        pikaday:function()
        {
            var args = arguments;

            if (!args || !args.length) {
                args = [{ }];
            }

            return this.each(function()
            {
                var self   = $(this),
                    plugin = self.data('pikaday');

                if (!(plugin instanceof Pikaday)) {
                    if (typeof args[0] === 'object') {
                        var options = $.extend({}, args[0]);
                        options.field = self[0];
                        self.data('pikaday', new Pikaday(options));
                    }
                } else {
                    if (typeof args[0] === 'string' && typeof plugin[args[0]] === 'function') {
                        plugin[args[0]].apply(plugin, Array.prototype.slice.call(args,1));

                        if (args[0] === 'destroy') {
                            self.removeData('pikaday');
                        }
                    }
                }
            });
        },
        randomId : function() {
            return UUID.generate().replace(/-/g, '');
        },

        inArray : function (needle, stack) {
            for (var i in stack) {
                if (needle == stack[i]) {
                    return true;
                }
            }

            return false;
        },

        stringifyArray:function(array) {
            return JSON.stringify(array)
        },

        parseArray:function (array) {
            return JSON.parse(array)
        },

        getUrlParams : function() {
            var params = [];
            var urlParts = window.location.href.split('?');
            if (urlParts.length == 1) {
                urlParts.push('');
            }
            var urlParts = urlParts[1].split('&')
            for (var i in urlParts) {
                var paramStr = urlParts[i];
                params[paramStr.split('=')[0]] = paramStr.split('=')[1];
            }

            return params;
        },


        buildPostContent : function(dataObj) {
            var str = [];
            for (var key in dataObj) {
                str.push(key + '=' + encodeURIComponent(dataObj[key]));
            }
            return str.join('&');
        },

        //把当前Url中的最后一部分分离出来
        getUrlLastPath : function(url) {
            var urlInfo = url.split('?');
            urlInfo = urlInfo[0].split('/');
            return urlInfo[urlInfo.length - 1];
        },

        //数据转换
        evalData: function (data) {
            eval('var inner_data=' + data);
            return inner_data;
        },

        //判断为空
        isEmpty: function (data) {
            if (typeof(data) == 'undefined') {
                return true;
            }

            if (data == null) {
                return true;
            }

            if ($.trim(data).length == 0) {
                return true;
            }
            return false;
        },

        /**
         * 日期 转换为 Unix时间戳
         * @param <string> 2014-01-01 20:20:20  日期格式
         * @return <int>        unix时间戳(秒)
         */
        dateToUnix: function(string) {
            var f = string.split(' ', 2);
            var d = (f[0] ? f[0] : '').split('-', 3);
            var t = (f[1] ? f[1] : '').split(':', 3);
            return (new Date(
                    parseInt(d[0], 10) || null,
                    (parseInt(d[1], 10) || 1) - 1,
                    parseInt(d[2], 10) || null,
                    parseInt(t[0], 10) || null,
                    parseInt(t[1], 10) || null,
                    parseInt(t[2], 10) || null
                )).getTime() / 1000;
        },


        /**
         * 时间戳转换日期
         * @param <int> unixTime    待时间戳(秒)
         * @param <bool> isFull    返回完整时间(Y-m-d 或者 Y-m-d H:i:s)
         * @param <int>  timeZone   时区
         */
        unixToDate: function(unixTime, isFull, timeZone) {
            if (typeof (timeZone) == 'number')
            {
                unixTime = parseInt(unixTime) + parseInt(timeZone) * 60 * 60;
            }
            var time = new Date(unixTime * 1000);
            var ymdhis = "";
            ymdhis += time.getUTCFullYear() + "-";
            ymdhis += (time.getUTCMonth()+1) + "-";
            ymdhis += time.getUTCDate();
            if (isFull === true)
            {
                ymdhis += " " + time.getUTCHours() + ":";
                ymdhis += time.getUTCMinutes() + ":";
                ymdhis += time.getUTCSeconds();
            }
            return ymdhis;
        },


        //判断有效手机号码
        isValidMobile: function (mobile) {
            return /^\d{11}$/.test(mobile);
        },

        //刷新页面
        refreshPage : function() {
            var curLocation = window.location.href;
            if (curLocation.indexOf('#') > 0) {
                curLocation = curLocation.substr(0, curLocation.indexOf('#'));
            }

            window.location.href = curLocation;
        },

        //格式化货币
        formatMoney : function(amount) {
            return Math.floor(amount * 100) / 100;
        },


        //弹窗
        DG : {
            g_dia_counter : 1,
            g_dia_instances : [],
            g_dia_error : null,
            loader : '<svg width="50" height="30" viewBox="0 0 120 30" xmlns="http://www.w3.org/2000/svg" fill="#000000">    <circle cx="15" cy="15" r="15">        <animate attributeName="r" from="15" to="15"                 begin="0s" dur="0.8s"                 values="15;9;15" calcMode="linear"                 repeatCount="indefinite" />        <animate attributeName="fill-opacity" from="1" to="1"                 begin="0s" dur="0.8s"                 values="1;.5;1" calcMode="linear"                 repeatCount="indefinite" />    </circle>    <circle cx="60" cy="15" r="9" fill-opacity="0.3">        <animate attributeName="r" from="9" to="9"                 begin="0s" dur="0.8s"                 values="9;15;9" calcMode="linear"                 repeatCount="indefinite" />        <animate attributeName="fill-opacity" from="0.5" to="0.5"                 begin="0s" dur="0.8s"                 values=".5;1;.5" calcMode="linear"                 repeatCount="indefinite" />    </circle>    <circle cx="105" cy="15" r="15">        <animate attributeName="r" from="15" to="15"                 begin="0s" dur="0.8s"                 values="15;9;15" calcMode="linear"                 repeatCount="indefinite" />        <animate attributeName="fill-opacity" from="1" to="1"                 begin="0s" dur="0.8s"                 values="1;.5;1" calcMode="linear"                 repeatCount="indefinite" />    </circle></svg>',


            reset : function() {
                this.g_dia_instances = [];
            },

            confirm : function(text, yesFunc) {
                this.g_dia_instances.push(dialog({
                    title: '确认',
                    content: '<div style="padding:20px 80px;">' + text + '</div>',
                    ok: yesFunc,
                    cancel: function() {
                        $.dz.DG.close();
                    }
                }).show());
            },

            ajustPosition : function() {
                var dia = this.g_dia_instances.pop();
                dia.reset();
                this.g_dia_instances.push(dia);
            },

            waitDialogue:function(text,params) {
                this.g_dia_counter++;
                var required =  {content:'' +
                '<div style="padding: 30px 10px 10px;text-align: center;">' +
                '<img style="width:50px;height:60px;" src="/static/image/loading.gif"/>' +
                '<div>' +
                text
                + '</div></div>'};    //必要参数
                //额外配置参数
                var options = typeof(params) !== "undefined" ? $.extend({},required,params) : required ;
                this.g_dia_instances.push(dialog(options).showModal());
            },

            close:function() {
                var topDia = this.g_dia_instances.pop();
                if (topDia) {
                    topDia.closeFromStack = true;
                    topDia.close().remove();
                }
            },

            closeError : function() {
                if (this.g_dia_error) {
                    this.g_dia_error.close().remove();
                    this.g_dia_error = null;
                }
            },
            okMessage:function(text, params) {
                this.g_dia_counter++;
                var required =  {content:'' +
                '<div style="padding: 30px 50px 10px;text-align: center;">' +
                '<img src="/static/image/icon_ok.png" style="width:50px;vertical-align: top;" /><div style="line-height: 30px;margin-top: 10px;">' +
                text
                + '</div><div style="text-align: center;"><a href="#" onclick="$.dz.DG.close();return false;" style="display: block;display: block;background-color: #ff7c24;width: 100px;color: white;border-radius: 3px;padding: 10px;margin: 30px auto 0;">确定</a></div></div>'};
                //额外配置参数
                var options = typeof(params) !== "undefined" ? $.extend({},required,params) : required ;
                this.g_dia_instances.push(dialog(options).showModal());
            },
            errorMessage:function(text,params) {
                if (this.g_dia_error != null) {
                    return;
                }
                var required =  {content:'' +
                '<div style="padding: 30px 50px 10px;text-align: center;">' +
                '<img src="/static/image/icon_error.png" style="width:50px;vertical-align: top;" /><div style="line-height: 30px;margin-top: 10px;">' +
                text
                + '</div><div style="text-align: center;"><a href="#" onclick="$.dz.DG.closeError();return false;" style="display: block;display: block;background-color: #ff7c24;width: 100px;color: white;border-radius: 3px;padding: 10px;margin: 30px auto 0;">确定</a></div></div>'};
                //额外配置参数
                var options = typeof(params) !== "undefined" ? $.extend({},required,params) : required ;
                this.g_dia_error = dialog(options).showModal();
            },

            show:function (context,title) {
                this.g_dia_counter++;
                var required =  {content:context,title:title};
                var options = typeof(params) !== "undefined" ? $.extend({},required,params) : required ;
                this.g_dia_instances.push(dialog(options).showModal());
            },

            UrlMessage: function (text, params, url) {
                this.g_dia_counter++;
                var required =  {content:'' +
                '<div style="padding: 30px 50px 10px;text-align: center;">' +
                '<div style="line-height: 30px;margin-top: 10px;">' +
                text
                + '</div><div style="text-align: center;">' +
                '<a href="' + url + '"   style="display: block;display: block;background-color: #bd2029;width: 100px;color: white;border-radius: 3px;padding: 10px;margin: 30px auto 0;">确认</a>' +
                '</div></div>'};
                var options = typeof(params) !== "undefined" ? $.extend({},required,params) : required ;
                this.g_dia_instances.push(dialog(options).showModal());
            },

            loadUrl : function(url, title) {
                this.g_dia_counter++;
                var required = {
                    title : title,
                    content: '' +
                    '<div style="padding: 0px 10px 10px;text-align: center;" id="div_dia' + this.g_dia_counter + '">' +
                    this.loader +
                    '<div style="text-align: center;">正在载入...</div>' +
                    '<div>',
                    onclose: function () {
                        if (this.closeFromStack == false) {
                            $.dz.DG.g_dia_instances.pop();
                        }
                    },
                };
                var options = typeof(params) !== "undefined" ? $.extend({}, required, params) : required;
                var dia = dialog(options);
                dia.closeFromStack = false;
                this.g_dia_instances.push(dia);
                dia.showModal();
                var currentCounter = this.g_dia_counter;
                $.get(url, null, function(data) {
                    $('#div_dia' + currentCounter).html(data);
                    dia.reset();
                    $.dz.installAjaxSubmit();
                });
            }
        },


        //倒计时效果
        countDownInstance : function(btnConfig) {
            return new (function (btnConfig) {

                var btn = btnConfig.btn;                            //节点
                var defaultCss = btnConfig.defaultCss;              //默认样式
                var defaultText = btnConfig.defaultText;            //默认描述
                var countdownCss = btnConfig.countdownCss;          //触发样式
                var countdownDuration = btnConfig.duration || 60;   //倒计时长

                var timeCounter = countdownDuration;
                var vcodeTimer = null;

                var restore = function () {
                    btn.removeAttr('disabled').val(defaultText).css(defaultCss);
                    clearInterval(vcodeTimer);
                };

                var updateCounter = function () {
                    timeCounter--;
                    if (timeCounter == 0) {
                        restore();
                        clearInterval(vcodeTimer);
                        timeCounter = countdownDuration;
                        return;
                    }
                    btn.val(timeCounter + '秒后重发');
                };

                this.begin = function () {
                    btn.attr('disabled', 'true').css(countdownCss);
                    updateCounter();
                    vcodeTimer = window.setInterval(updateCounter, 1000);
                }

                this.restore = restore;
            })(btnConfig);
        },

        ajaxInPlace : function(container, linkContainer) {
            linkContainer.find('a').each(function(){
                var linkUrl = $(this).attr('href');
                $(this).attr('href', 'javascript:void(0);');
                $(this).on('click', function(event) {
                    $.get(linkUrl, null, function(data) {
                        container.replaceWith(data);
                    });
                });
            });
        },

        installAjaxSubmit : function() {
            $('form').each(function (index) {
                if ($(this).attr('dz-ajax-submit') != 'true') {
                    return;
                }

                if ($(this).attr('dz-ajax-submit-installed') == 'true') {
                    return;
                }

                $(this).attr('dz-ajax-submit-installed', 'true');

                var handler = null;
                handler = $(this).attr('dz-ajax-handler');

                $(this).on('submit', function(e){
                    e.preventDefault();
                    if ($.dzValidator && $.dzValidator.validate($(this)) == false) {
                        return false;
                    }


                    var context = $(this);
                    $.post($(this).attr('action'), $(this).serialize(), function(data) {
                        var returnInfo = $.dz.evalData(data);

                        //如果有自定义的处理函数，则用处理函数
                        if (handler) {
                            eval(handler + '(returnInfo);');
                            return;
                        }

                        //默认处理为刷新页面
                        if (returnInfo.code == 0) {
                            if (returnInfo.msg != '') {
                                window.location.href = returnInfo.msg;
                            } else {
                                $.dz.refreshPage();
                            }
                        }else if(returnInfo.content == '' || returnInfo.content == null){
                            $.dz.DG.errorMessage(returnInfo.msg);
                        }else if(returnInfo.code == 99){
                            $.dz.DG.UrlMessage(returnInfo.msg,null,returnInfo.content);
                        }

                        else {

                            $.dzValidator.showErrorMsgs(returnInfo.content, context);
                        }

                    });
                });
            });
        },

        ajaxAnimateLoading : function(container, url, method, data, callback) {
            var loading = '<div style="padding: 50px 0;text-align: center;"><img src="/static/image/loading.gif" /></div>';
            container.html(loading);
            method = method || 'get';
            $.ajax({
                url : url,
                async : true,
                data : data,
                type : method,
                success:function(data, textStatus, jqXHR){
                    container.html(data);
                    if (callback) {
                        callback(data);
                    }
                },
                error :function() {
                    var errorMsg = '<div style="padding: 50px 0;text-align: center;">网络异常，请稍后再试 <a href="javascript:void(0);" onclick="$.dz.refreshPage();">刷新</a></div>';
                    container.html(errorMsg);
                }
            });
        }
    };


})(jQuery);



//DOM对象回车事件
(function($) {
    $.fn.enterAction = function (callback) {
        $(this).keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                callback(event);
            }
        });
    };
})(jQuery);


//ajax自动提交
$(function() {
    $.dz.installAjaxSubmit();
});


//项目相关
(function ($){
    $.ll =  {
        //选择图片
        seleteImageCallback : null,
        showSelectImage : function(callback) {
            this.seleteImageCallback = callback;
            $.dz.DG.loadUrl('/user/seller/album/ajax-select-images?callback=' + encodeURI('$.ll.seleteImageCallback'), '插入图片');
        }
    }
})(jQuery);

/*!
 * JavaScript Cookie v2.1.3
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
;(function (factory) {
    var registeredInModuleLoader = false;
    if (typeof define === 'function' && define.amd) {
        define(factory);
        registeredInModuleLoader = true;
    }
    if (typeof exports === 'object') {
        module.exports = factory();
        registeredInModuleLoader = true;
    }
    if (!registeredInModuleLoader) {
        var OldCookies = window.Cookies;
        var api = window.Cookies = factory();
        api.noConflict = function () {
            window.Cookies = OldCookies;
            return api;
        };
    }
}(function () {
    function extend () {
        var i = 0;
        var result = {};
        for (; i < arguments.length; i++) {
            var attributes = arguments[ i ];
            for (var key in attributes) {
                result[key] = attributes[key];
            }
        }
        return result;
    }

    function init (converter) {
        function api (key, value, attributes) {
            var result;
            if (typeof document === 'undefined') {
                return;
            }

            // Write

            if (arguments.length > 1) {
                attributes = extend({
                    path: '/'
                }, api.defaults, attributes);

                if (typeof attributes.expires === 'number') {
                    var expires = new Date();
                    expires.setMilliseconds(expires.getMilliseconds() + attributes.expires * 864e+5);
                    attributes.expires = expires;
                }

                try {
                    result = JSON.stringify(value);
                    if (/^[\{\[]/.test(result)) {
                        value = result;
                    }
                } catch (e) {}

                if (!converter.write) {
                    value = encodeURIComponent(String(value))
                        .replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);
                } else {
                    value = converter.write(value, key);
                }

                key = encodeURIComponent(String(key));
                key = key.replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent);
                key = key.replace(/[\(\)]/g, escape);

                return (document.cookie = [
                    key, '=', value,
                    attributes.expires ? '; expires=' + attributes.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                    attributes.path ? '; path=' + attributes.path : '',
                    attributes.domain ? '; domain=' + attributes.domain : '',
                    attributes.secure ? '; secure' : ''
                ].join(''));
            }

            // Read

            if (!key) {
                result = {};
            }

            // To prevent the for loop in the first place assign an empty array
            // in case there are no cookies at all. Also prevents odd result when
            // calling "get()"
            var cookies = document.cookie ? document.cookie.split('; ') : [];
            var rdecode = /(%[0-9A-Z]{2})+/g;
            var i = 0;

            for (; i < cookies.length; i++) {
                var parts = cookies[i].split('=');
                var cookie = parts.slice(1).join('=');

                if (cookie.charAt(0) === '"') {
                    cookie = cookie.slice(1, -1);
                }

                try {
                    var name = parts[0].replace(rdecode, decodeURIComponent);
                    cookie = converter.read ?
                        converter.read(cookie, name) : converter(cookie, name) ||
                        cookie.replace(rdecode, decodeURIComponent);

                    if (this.json) {
                        try {
                            cookie = JSON.parse(cookie);
                        } catch (e) {}
                    }

                    if (key === name) {
                        result = cookie;
                        break;
                    }

                    if (!key) {
                        result[name] = cookie;
                    }
                } catch (e) {}
            }

            return result;
        }

        api.set = api;
        api.get = function (key) {
            return api.call(api, key);
        };
        api.getJSON = function () {
            return api.apply({
                json: true
            }, [].slice.call(arguments));
        };
        api.defaults = {};

        api.remove = function (key, attributes) {
            api(key, '', extend(attributes, {
                expires: -1
            }));
        };

        api.withConverter = init;

        return api;
    }

    return init(function () {});
}));

/* HTML5 Placeholder jQuery Plugin - v2.3.1
 * Copyright (c)2015 Mathias Bynens
 * 2015-12-16
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a("object"==typeof module&&module.exports?require("jquery"):jQuery)}(function(a){function b(b){var c={},d=/^jQuery\d+$/;return a.each(b.attributes,function(a,b){b.specified&&!d.test(b.name)&&(c[b.name]=b.value)}),c}function c(b,c){var d=this,f=a(this);if(d.value===f.attr(h?"placeholder-x":"placeholder")&&f.hasClass(n.customClass))if(d.value="",f.removeClass(n.customClass),f.data("placeholder-password")){if(f=f.hide().nextAll('input[type="password"]:first').show().attr("id",f.removeAttr("id").data("placeholder-id")),b===!0)return f[0].value=c,c;f.focus()}else d==e()&&d.select()}function d(d){var e,f=this,g=a(this),i=f.id;if(!d||"blur"!==d.type||!g.hasClass(n.customClass))if(""===f.value){if("password"===f.type){if(!g.data("placeholder-textinput")){try{e=g.clone().prop({type:"text"})}catch(j){e=a("<input>").attr(a.extend(b(this),{type:"text"}))}e.removeAttr("name").data({"placeholder-enabled":!0,"placeholder-password":g,"placeholder-id":i}).bind("focus.placeholder",c),g.data({"placeholder-textinput":e,"placeholder-id":i}).before(e)}f.value="",g=g.removeAttr("id").hide().prevAll('input[type="text"]:first').attr("id",g.data("placeholder-id")).show()}else{var k=g.data("placeholder-password");k&&(k[0].value="",g.attr("id",g.data("placeholder-id")).show().nextAll('input[type="password"]:last').hide().removeAttr("id"))}g.addClass(n.customClass),g[0].value=g.attr(h?"placeholder-x":"placeholder")}else g.removeClass(n.customClass)}function e(){try{return document.activeElement}catch(a){}}var f,g,h=!1,i="[object OperaMini]"===Object.prototype.toString.call(window.operamini),j="placeholder"in document.createElement("input")&&!i&&!h,k="placeholder"in document.createElement("textarea")&&!i&&!h,l=a.valHooks,m=a.propHooks,n={};j&&k?(g=a.fn.placeholder=function(){return this},g.input=!0,g.textarea=!0):(g=a.fn.placeholder=function(b){var e={customClass:"placeholder"};return n=a.extend({},e,b),this.filter((j?"textarea":":input")+"["+(h?"placeholder-x":"placeholder")+"]").not("."+n.customClass).not(":radio, :checkbox, [type=hidden]").bind({"focus.placeholder":c,"blur.placeholder":d}).data("placeholder-enabled",!0).trigger("blur.placeholder")},g.input=j,g.textarea=k,f={get:function(b){var c=a(b),d=c.data("placeholder-password");return d?d[0].value:c.data("placeholder-enabled")&&c.hasClass(n.customClass)?"":b.value},set:function(b,f){var g,h,i=a(b);return""!==f&&(g=i.data("placeholder-textinput"),h=i.data("placeholder-password"),g?(c.call(g[0],!0,f)||(b.value=f),g[0].value=f):h&&(c.call(b,!0,f)||(h[0].value=f),b.value=f)),i.data("placeholder-enabled")?(""===f?(b.value=f,b!=e()&&d.call(b)):(i.hasClass(n.customClass)&&c.call(b),b.value=f),i):(b.value=f,i)}},j||(l.input=f,m.value=f),k||(l.textarea=f,m.value=f),a(function(){a(document).delegate("form","submit.placeholder",function(){var b=a("."+n.customClass,this).each(function(){c.call(this,!0,"")});setTimeout(function(){b.each(d)},10)})}),a(window).bind("beforeunload.placeholder",function(){var b=!0;try{"javascript:void(0)"===document.activeElement.toString()&&(b=!1)}catch(c){}b&&a("."+n.customClass).each(function(){this.value=""})}))});

$(function(){
    $('input, textarea').placeholder();
});


/*
 Version: v3.3.0
 The MIT License: Copyright (c) 2010-2016 LiosK.
 */
var UUID;UUID=function(g){"use strict";function f(){}function b(c){return 0>c?NaN:30>=c?0|Math.random()*(1<<c):53>=c?(0|1073741824*Math.random())+1073741824*(0|Math.random()*(1<<c-30)):NaN}function a(c,b){for(var a=c.toString(16),d=b-a.length,e="0";0<d;d>>>=1,e+=e)d&1&&(a=e+a);return a}f.generate=function(){return a(b(32),8)+"-"+a(b(16),4)+"-"+a(16384|b(12),4)+"-"+a(32768|b(14),4)+"-"+a(b(48),12)};f.overwrittenUUID=g;return f}(UUID);






