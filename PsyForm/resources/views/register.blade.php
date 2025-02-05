<?php
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: Content-Type");
// header("Content-Type: application/json");

// $conn = new mysqli("localhost", "root", "", "PsyForm", "3307");

// if ($conn->connect_error) {
//     die(json_encode(["message" => "Database connection failed"]));
// }

// $data = json_decode(file_get_contents("php://input"), true);

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $name = $data['name'];
//     $email = $data['email'];
//     $password = password_hash($data['password'], PASSWORD_DEFAULT);

//     $checkEmail = $conn->query("SELECT * FROM users WHERE email='$email'");

//     if ($checkEmail->num_rows > 0) {
//         echo json_encode(["message" => "Email already exists"]);
//     } else {
//         $sql = "INSERT INTO users2 (name, email, password) VALUES ('$name', '$email', '$password')";
//         if ($conn->query($sql) === TRUE) {
//             echo json_encode(["message" => "Registration successful"]);
//         } else {
//             echo json_encode(["message" => "Error: " . $conn->error]);
//         }
//     }
// }

// $conn->close();

?>

<!DOCTYPE html>
<html>
@vite('resources\css\styles.css')
<head>
    <title>Register</title>
</head>
<body>
    <form method="POST" action="{{ route('login') }}" class="form-container">
        <h2>Register</h2>
        @csrf
        <input type="text" name="name" placeholder="Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required><br>
        <button type="submit">Register</button>
        <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </form>
</body>
</html>
