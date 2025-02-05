<!DOCTYPE html>
<html>
    @vite('resources\css\styles.css')
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome to Dashboard, </h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
<?php
?>