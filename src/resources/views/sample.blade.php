<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta name='csrf-token' content="{{ csrf_token() }}">
    <title>title</title>
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet" type="text/css">
</head>
<h1>sample</h1>
<div id="app">
    <example-component></example-component>
</div>

<script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>