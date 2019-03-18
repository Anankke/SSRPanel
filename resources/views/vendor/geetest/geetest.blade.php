<script src="https://cdn.bootcss.com/jquery/2.1.0/jquery.min.js"></script>
<script src="https://static.geetest.com/static/tools/gt.js"></script>
<div id="{{ $captchaid }}"></div>
<p id="wait-{{ $captchaid }}" class="show">正在加载验证码...</p>
@define use Illuminate\Support\Facades\Config
<script>
    var geetest = function(url) {
        var handlerEmbed = function(captchaObj) {
            $("#{{ $captchaid }}").closest('form').submit(function(e) {
                // captchaObj.verify();
                var validate = captchaObj.getValidate();
                if (!validate) {
                    Msg(false, '{{ Config::get('geetest.client_fail_alert')}}', 'error');
                    e.preventDefault();
                }
            });
            captchaObj.appendTo("#{{ $captchaid }}");
            captchaObj.onReady(function() {
                $("#wait-{{ $captchaid }}")[0].className = "hide";
            })
            if ('{{ $product }}' == 'popup') {
                captchaObj.bindOn($('#{{ $captchaid }}').closest('form').find(':submit'));
                captchaObj.appendTo("#{{ $captchaid }}");
            }
        };
        
        // 前端第一次验证
        $.ajax({
            url: url + "?t=" + (new Date()).getTime(),
            type: "get",
            dataType: "json",
            success: function(data) {
                initGeetest({
                    gt: data.gt,
                    challenge: data.challenge,
                    product: "{{ $product?$product:Config::get('geetest.product', 'float') }}",
                    offline: !data.success, // 表示用户后台检测极验服务器是否宕机
                    new_captcha: data.new_captcha,  // 用于宕机时表示是新验证码的宕机
                    lang: '{{ Config::get('geetest.lang', 'zh-cn') }}',
                    http: '{{ Config::get('geetest.protocol', 'http') }}' + '://',
                    width: '100%'
                }, handlerEmbed);
            }
        });
    };

    function Msg(clear, msg, type) {
        if ( !clear ) $('.login-form .alert').remove();
        
        var typeClass = 'alert-danger',
            clear = clear ? clear : false,
            $elem = $('.login-form');
        type === 'error' ? typeClass = 'alert-danger' : typeClass = 'alert-success';

        var tpl = '<div class="alert ' + typeClass + '">' +
                '<button class="close" data-close="alert"></button>' +
                '<span> ' + msg + ' </span></div>';
        
        if ( !clear ) {
            $elem.prepend(tpl);
        } else {
            $('.login-form .alert').remove();
        }
    }

    (function() {
        geetest('{{ $url?$url:Config::get('geetest.url', 'geetest') }}');
    })();
</script>
<style>
    .hide {
        display: none;
    }
</style>
