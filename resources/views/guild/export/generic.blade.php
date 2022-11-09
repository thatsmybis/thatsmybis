<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <title>{{ $name }} - {{ env('APP_NAME') }}</title>
</head>
<body style="white-space: pre-line;white-space: pre-wrap;">
    <textarea rows="50" width="100%">{{ $data }}</textarea>
</body>
</html>


