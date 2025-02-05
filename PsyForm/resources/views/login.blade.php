<!DOCTYPE html>
<html>
@vite('resources\css\styles.css')
<head>
    <title>Login</title>
</head>
<body>
    <form method="POST" action="{{ route('home') }}" class="form-container">
        <h2>Login</h2>
        @csrf
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
    </form>
</body>
</html>
