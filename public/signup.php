<?php
include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $team_name = trim($_POST['team_name']);
    $professional_domain = trim($_POST['professional_domain']);

    if ($professional_domain === 'Other') {
        $professional_domain = trim($_POST['custom_professional_domain']);
    }

    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        die("Please fill all required fields.");
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password_hash, role, team_name, professional_domain) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $email, $password_hash, $role, $team_name, $professional_domain);

    if ($stmt->execute()) {
        echo "<div class='bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded relative mb-4 animate-fade-in' role='alert'>Signup successful!</div>";
    } else {
        echo "<div class='bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded relative mb-4 animate-fade-in' role='alert'>Error: " . $stmt->error . "</div>";
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
    <title>Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleFields() {
            const role = document.getElementById('role').value;
            const teamNameDiv = document.getElementById('team_name_div');
            const professionalDomainDiv = document.getElementById('professional_domain_div');

            if (role === 'Chef') {
                teamNameDiv.classList.remove('hidden');
                teamNameDiv.classList.add('animate-fade-in');
                professionalDomainDiv.classList.add('hidden');
            } else if (role === 'member') {
                professionalDomainDiv.classList.remove('hidden');
                professionalDomainDiv.classList.add('animate-fade-in');
                teamNameDiv.classList.add('hidden');
            } else {
                teamNameDiv.classList.add('hidden');
                professionalDomainDiv.classList.add('hidden');
            }
        }

        function toggleCustomDomain() {
            const professionalDomain = document.getElementById('professional_domain').value;
            const customDomainDiv = document.getElementById('custom_domain_div');

            if (professionalDomain === 'Other') {
                customDomainDiv.classList.remove('hidden');
                customDomainDiv.classList.add('animate-fade-in');
            } else {
                customDomainDiv.classList.add('hidden');
            }
        }
    </script>
</head>
<body class="bg-gray-900 text-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-full max-w-md transform transition-all duration-300 hover:scale-105">
        <h1 class="text-2xl font-bold mb-6 text-center text-blue-400 animate-bounce">Signup</h1>
        <form method="POST" action="signup.php" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-300">Username</label>
                <input type="text" id="username" name="username" required
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-100 placeholder-gray-400">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                <input type="email" id="email" name="email" required
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-100 placeholder-gray-400">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                <input type="password" id="password" name="password" required
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-100 placeholder-gray-400">
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-300">Role</label>
                <select id="role" name="role" required onchange="toggleFields()"
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-100">
                    <option value="">Select Role</option>
                    <option value="Chef">Chef</option>
                    <option value="member">Member</option>
                </select>
            </div>

            <div id="team_name_div" class="hidden">
                <label for="team_name" class="block text-sm font-medium text-gray-300">Team Name</label>
                <input type="text" id="team_name" name="team_name"
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-100 placeholder-gray-400">
            </div>

            <div id="professional_domain_div" class="hidden">
                <label for="professional_domain" class="block text-sm font-medium text-gray-300">Professional Domain</label>
                <select id="professional_domain" name="professional_domain" onchange="toggleCustomDomain()"
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-100">
                    <option value="Web Design">Web Design</option>
                    <option value="Development">Development</option>
                    <option value="Full Stack">Full Stack</option>
                    <option value="Data Science">Data Science</option>
                    <option value="DevOps">DevOps</option>
                    <option value="UI/UX">UI/UX</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div id="custom_domain_div" class="hidden">
                <label for="custom_professional_domain" class="block text-sm font-medium text-gray-300">Specify Your Domain</label>
                <input type="text" id="custom_professional_domain" name="custom_professional_domain"
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-100 placeholder-gray-400">
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 transform hover:scale-105">
                    Signup
                </button>
            </div>
        </form>
    </div>
</body>
</html>