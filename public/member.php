<?php
session_start();

class Database {
    private $conn;

    public function __construct() {
        include BASE_PATH . 'config/database.php';
        $this->conn = new mysqli($host, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function prepare($query) {
        return $this->conn->prepare($query);
    }

    public function close() {
        $this->conn->close();
    }
}

class Member {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getTeamName($memberId) {
        $query = "SELECT team_name FROM users WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }

        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $result = $stmt->get_result();
        $teamName = '';
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $teamName = $row['team_name'];
        }
        return $teamName;
    }

    public function sendJoinRequest($memberId, $chefId) {
        $query = "SELECT request_id FROM join_requests WHERE user_id = ? AND receiver_id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("ii", $memberId, $chefId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $query = "INSERT INTO join_requests (user_id, receiver_id, status) VALUES (?, ?, 'pending')";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                die("Prepare failed: " . $this->db->error);
            }
            $stmt->bind_param("ii", $memberId, $chefId);
            return $stmt->execute();
        } else {
            return false; // Request already sent
        }
    }

    public function getProjects($memberId) {
        $query = "SELECT project_id, project_name, description, created_at 
                  FROM projects 
                  WHERE assigned_to = ? 
                  ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }

        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $result = $stmt->get_result();
        $projects = [];
        while ($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }
        return $projects;
    }

    public function getTasks($memberId) {
        $query = "SELECT task_id, task_title, task_description, due_date, status 
                  FROM tasks 
                  WHERE assigned_to = ? 
                  ORDER BY due_date ASC";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }

        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
        return $tasks;
    }
}

// Main Logic
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

$db = new Database();
$member = new Member($db);

$memberId = $_SESSION['user_id'];
$teamName = $member->getTeamName($memberId);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request'])) {
    $chefId = $_POST['chef_id'];

    if ($member->sendJoinRequest($memberId, $chefId)) {
        $successMessage = "Join request sent successfully!";
    } else {
        $errorMessage = "You have already sent a request to this chef.";
    }
}

$projects = $member->getProjects($memberId);
$tasks = $member->getTasks($memberId);

$db->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard</title>
    <link rel="stylesheet" href="../assets/css/tailwind.css">
    <script src="https://cdn.tailwindcss.com"></script>
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