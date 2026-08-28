<script src="{{ asset(mix('vendors/js/vendors.min.js')) }}"></script>

<script src="{{asset(mix('vendors/bower/jquery.sticky/jquery.sticky.js'))}}"></script>
@yield('vendor-script')

<!-- BEGIN: Theme JS-->
<script src="{{ asset(mix('js/core/app-menu.js')) }}"></script>
<script src="{{ asset(mix('js/core/app.js')) }}"></script>

@if(Request::is('admin**'))
    @include('admin.partials.notify')
@else
    @include('partials.plugins')
    @include('admin.partials.notify')
@endif

<!-- custome scripts file for user -->
<script src="{{ asset(mix('js/core/scripts.js')) }}"></script>
<!-- custom file for FX & Stock Chart -->
<script src="https://webdev.prosp.devexperts.com:8095/widget/vendors.js"></script>
<script src="https://webdev.prosp.devexperts.com:8095/widget/chart-react.js"></script>

@if($configData['blankPage'] === false)
<script src="{{ asset(mix('js/scripts/customizer.js')) }}"></script>
@endif

<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->

@yield('page-script')
@stack('script-lib')
@stack('script')
@stack('modals')

<!-- END: Page JS-->

@livewireScripts
<script defer src="https://unpkg.com/alpinejs@3.7.0/dist/cdn.min.js"></script>
<script>
document.addEventListener('click', function (event) {
  var button = event.target.closest('[data-balance-toggle]');
  if (!button) return;
  var card = button.closest('.balance-privacy');
  if (!card) return;
  var hidden = card.classList.toggle('is-balance-hidden');
  button.setAttribute('aria-pressed', hidden ? 'true' : 'false');
  button.setAttribute('aria-label', hidden ? 'Show balance' : 'Hide balance');
  button.innerHTML = '<i class="bi bi-' + (hidden ? 'eye-slash' : 'eye') + '"></i>';
});
</script>
