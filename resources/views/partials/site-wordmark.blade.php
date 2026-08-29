<span class="nova-site-wordmark {{ $class ?? '' }}" style="display:inline-flex;align-items:center;gap:11px;min-width:0">
    <span class="nova-site-wordmark-icon" aria-hidden="true" style="display:grid;place-items:center;width:38px;height:38px;flex:0 0 38px;border-radius:11px;background:#19ed8a;overflow:hidden">
        <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/favicon.png') }}" alt="" style="display:block;width:100%;height:100%;padding:4px;object-fit:contain;border-radius:inherit">
    </span>
    <span class="nova-site-wordmark-text" style="max-width:190px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#f8fafc;font-family:Manrope,'DM Sans',sans-serif;font-size:18px;font-weight:800;letter-spacing:-.035em;line-height:1.1">{{ siteName() }}</span>
</span>
