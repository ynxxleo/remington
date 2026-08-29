<div class="nova-preloader" id="nova-preloader" role="status" aria-label="Loading page">
    <div class="nova-loader-brand">
        <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/favicon.png') }}" alt="{{ siteName() }} icon">
        <span>{{ siteName() }}</span>
    </div>
    <div class="nova-loader-track" aria-hidden="true"><i></i></div>
    <small>Loading workspace</small>
</div>
<script>
    window.addEventListener('load', function () {
        var loader = document.getElementById('nova-preloader');
        if (!loader) return;
        loader.classList.add('is-ready');
        window.setTimeout(function () { loader.remove(); }, 360);
    });
</script>
