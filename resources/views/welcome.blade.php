<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <!-- Responsive: Added missing viewport meta tag to enable mobile rendering -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div id="app"></div>


</body>

</html>
