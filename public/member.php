<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

include '../config/database.php';

$member_id = $_SESSION['user_id'];
$query = "SELECT team_name FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$team_name = '';
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $team_name = $row['team_name'];
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

$query = "SELECT project_id, project_name, description, created_at 
          FROM projects 
          WHERE assigned_to = ? 
          ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$projects = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}

$query = "SELECT task_id, task_title, task_description, due_date, status 
          FROM tasks 
          WHERE assigned_to = ? 
          ORDER BY due_date ASC";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$tasks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
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
    <title>Member Dashboard</title>
    <link rel="stylesheet" href="../assets/css/tailwind.css">
</head>

<body class="bg-gray-100 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="bg-gray-800 text-white w-64 min-h-screen p-6">
        <h2 class="text-xl font-semibold mb-6">Member Dashboard</h2>
        <ul class="space-y-4">
            <li>
                <a href="#" class="block text-gray-300 hover:text-white">Dashboard</a>
            </li>
            <li>
                <a href="send_request.php" class="block text-gray-300 hover:text-white">Join Teams</a>
            </li>
            <li>
                <a href="work.php" class="block text-gray-300 hover:text-white">start working</a>
            </li>
            <li>
                <a href="logout.php" class="block text-gray-300 hover:text-white">Logout</a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Welcome,
            <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <!-- Assigned Projects Section -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Assigned Projects</h2>
            <?php if (empty($projects)): ?>
                <p class="text-gray-600">No projects assigned to you.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($projects as $project): ?>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <?php echo htmlspecialchars($project['project_name']); ?>
                            </h3>
                            <p class="text-gray-600 mb-4">
                                <?php echo htmlspecialchars($project['description']); ?>
                            </p>
                            <p class="text-gray-500 text-sm">
                                Created on: <?php echo date('M d, Y', strtotime($project['created_at'])); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Assigned Tasks Section -->
        <div>
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Assigned Tasks</h2>
            <?php if (empty($tasks)): ?>
                <p class="text-gray-600">No tasks assigned to you.</p>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Task
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Due Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($tasks as $task): ?>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($task['task_title']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo htmlspecialchars($task['task_description']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php echo date('M d, Y', strtotime($task['due_date'])); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-sm font-semibold rounded-full 
                                            <?php
                                            switch ($task['status']) {
                                                case 'todo':
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'in_progress':
                                                    echo 'bg-blue-100 text-blue-800';
                                                    break;
                                                case 'completed':
                                                    echo 'bg-green-100 text-green-800';
                                                    break;
                                                default:
                                                    echo 'bg-gray-100 text-gray-800';
                                            }
                                            ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>