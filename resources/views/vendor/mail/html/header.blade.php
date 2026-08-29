<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
@include('partials.site-wordmark')
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
