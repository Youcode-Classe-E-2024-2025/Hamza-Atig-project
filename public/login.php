<?php
include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim(htmlspecialchars($_POST['username']));
    $password = trim(htmlspecialchars($_POST['password']));

    if (empty($username) || empty($password)) {
        die("<div class='bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded relative mb-4 animate-fade-in' role='alert'>Please fill all required fields.</div>");
    }

    $sql = "SELECT user_id, username, password_hash, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = htmlspecialchars($user['username']);
            $_SESSION['role'] = htmlspecialchars($user['role']);

            if ($user['role'] === 'Chef') {
                header("Location: index.php");
            } else {
                header("Location: member.php");
            }
            exit();
        } else {
            echo "<div class='bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded relative mb-4 animate-fade-in' role='alert'>Invalid username or password.</div>";
        }
    } else {
        echo "<div class='bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded relative mb-4 animate-fade-in' role='alert'>Invalid username or password.</div>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-full max-w-md transform transition-all duration-300 hover:scale-105">
        <h1 class="text-2xl font-bold mb-6 text-center text-blue-400 animate-bounce">Login</h1>
        <form method="POST" action="login.php" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-300">Username</label>
                <input type="text" id="username" name="username" required
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-100 placeholder-gray-400">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                <input type="password" id="password" name="password" required
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-100 placeholder-gray-400">
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 transform hover:scale-105">
                    Login
                </button>
            </div>
            <a href="signup.php" class="block text-center text-sm font-medium text-gray-300 hover:text-blue-500 transition-all duration-300">signup</a>
        </form>
    </div>
</body>
</html>