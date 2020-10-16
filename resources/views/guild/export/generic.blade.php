<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <title>Wishlist Export - {{ env('APP_NAME') }}</title>
</head>
<body>
    <span style="white-space: pre-line;white-space: pre-wrap;">{!! $csv !!}</span>
</body>
</html>


