<?php
session_start();

// Redirect to login if the user is not logged in or is not a member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

include '../config/database.php';

// Fetch all teams and their chefs
$query = "SELECT user_id, username, team_name FROM users WHERE role = 'Chef'";
$chefs_result = $conn->query($query);
$chefs = [];
if ($chefs_result->num_rows > 0) {
    while ($row = $chefs_result->fetch_assoc()) {
        $chefs[] = $row;
    }
}

// Fetch the logged-in member's join requests
$member_id = $_SESSION['user_id'];
$query = "SELECT jr.request_id, jr.receiver_id, jr.status, u.username, u.team_name 
          FROM join_requests jr
          JOIN users u ON jr.receiver_id = u.user_id
          WHERE jr.user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$join_requests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $join_requests[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request'])) {
    $chef_id = $_POST['chef_id'];
    $member_id = $_SESSION['user_id'];

    $query = "SELECT request_id FROM join_requests WHERE user_id = ? AND receiver_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ii", $member_id, $chef_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $query = "INSERT INTO join_requests (user_id, receiver_id, status) VALUES (?, ?, 'pending')";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ii", $member_id, $chef_id);
        if ($stmt->execute()) {
            $success_message = "Join request sent successfully!";
        } else {
            $error_message = "Failed to send join request.";
        }
    } else {
        $error_message = "You have already sent a request to this chef.";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Join Request</title>
    <link rel="stylesheet" href="../assets/css/tailwind.css">
</head>

<body class="bg-gray-100 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="bg-gray-800 text-white w-64 min-h-screen p-6">
        <h2 class="text-xl font-semibold mb-6">Member Dashboard</h2>
        <ul class="space-y-4">
            <li>
                <a href="member.php" class="block text-gray-300 hover:text-white">Dashboard</a>
            </li>
            <li>
                <a href="send_request.php" class="block text-gray-300 hover:text-white">Join Teams</a>
            </li>
            <li>
                <a href="#" class="block text-gray-300 hover:text-white">My Projects</a>
            </li>
            <li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Send Join Request</h1>

        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- List of Teams and Chefs -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Available Teams</h2>
            <?php if (empty($chefs)): ?>
                <p class="text-gray-600">No teams available.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($chefs as $chef): ?>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <?php echo htmlspecialchars($chef['team_name']); ?>
                            </h3>
                            <p class="text-gray-600 mb-4">
                                Chef: <?php echo htmlspecialchars($chef['username']); ?>
                            </p>
                            <form method="POST">
                                <input type="hidden" name="chef_id" value="<?php echo $chef['user_id']; ?>">
                                <button type="submit" name="send_request"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                    Send Join Request
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- List of Join Requests -->
        <div>
            <h2 class="text-xl font-semibold text-gray-800 mb-4">My Join Requests</h2>
            <?php if (empty($join_requests)): ?>
                <p class="text-gray-600">No join requests sent.</p>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Team
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Chef
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($join_requests as $request): ?>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($request['team_name']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <?php echo htmlspecialchars($request['username']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-sm font-semibold rounded-full 
                                            <?php
                                            switch ($request['status']) {
                                                case 'pending':
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'approved':
                                                    echo 'bg-green-100 text-green-800';
                                                    break;
                                                case 'rejected':
                                                    echo 'bg-red-100 text-red-800';
                                                    break;
                                                default:
                                                    echo 'bg-gray-100 text-gray-800';
                                            }
                                            ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>