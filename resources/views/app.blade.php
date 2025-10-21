<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Vue Calendar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div id="app">
    <login-form v-if="!isAuthenticated" @login="onLogin"></login-form>
    <dashboard v-else @logout="onLogout"></dashboard>
</div>
</body>
</html>
