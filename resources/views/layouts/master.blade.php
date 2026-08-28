@isset($pageConfigs)
{!! updatePageConfig($pageConfigs) !!}
@endisset

<!DOCTYPE html>
@php
$configData = applClasses();
@endphp

<html class="loading
@if(Request::is('admin**')) {{ ($configData['theme'] === 'light') ? '' : $configData['layoutTheme']}}"
    @if($configData['theme'] === 'dark') data-layout="dark-layout" @endif
@else {{ ($configData['themeuser'] === 'light') ? '' : $configData['layoutThemeUser']}}"
    @if($configData['themeuser'] === 'light') data-layout="light" @endif @endif
lang="@if(session()->has('locale')){{session()->get('locale')}}@else{{$configData['defaultLanguage']}}@endif"
data-textdirection="{{ env('MIX_CONTENT_DIRECTION') === 'rtl' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>document.documentElement.dataset.tradingTheme='dark';localStorage.removeItem('trading-theme');</script>
    <title>{{ $general->sitename($page_title ?? '') }}</title>
    @if(Request::is('user**'))
        @include('partials.seo')
    @endif
    <link rel="shortcut icon" type="image/png" href="{{getImage(imagePath()['logoIcon']['path'] .'/favicon.png')}}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap">
    @include('panels/styles')
<!-- Smartsupp Live Chat script -->
<script type="text/javascript">
var _smartsupp = _smartsupp || {};
_smartsupp.key = 'fee651b5024e88af853f9df7afdda41ef31399bf';
window.smartsupp||(function(d) {
  var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
  s=d.getElementsByTagName('script')[0];c=d.createElement('script');
  c.type='text/javascript';c.charset='utf-8';c.async=true;
  c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
})(document);
</script>
<noscript>Powered by <a href="https://www.smartsupp.com" target="_blank">Smartsupp</a></noscript>

</head>
<body>
    @include('partials.nova-preloader')
    @yield('app')
    {{-- <script>
        if ('serviceWorker' in navigator) {
          window.addEventListener('load', () => {
            navigator.serviceWorker.register('/service-worker.js');
          });
        }
    </script> --}}
</body>
</html>
