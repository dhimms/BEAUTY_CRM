@component('mail::message')
# Halo!

{{ $content }}

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
