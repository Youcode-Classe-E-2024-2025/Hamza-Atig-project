<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

include '../config/database.php';

$member_id = $_SESSION['user_id'];
$query = "SELECT p.project_id, p.project_name 
          FROM projects p
          JOIN join_requests jr ON p.project_id = jr.receiver_id
          WHERE jr.user_id = ? AND jr.status = 'approved'";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$projects_from_requests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects_from_requests[] = $row;
    }
}

$query = "SELECT project_id, project_name 
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
$assigned_projects = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $assigned_projects[] = $row;
    }
}

$all_projects = array_merge($projects_from_requests, $assigned_projects);

$unique_projects = array_unique($all_projects, SORT_REGULAR);

$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : null;
$tasks = [];
if ($project_id) {
    $query = "SELECT task_id, task_title, task_description, due_date, status 
              FROM tasks 
              WHERE project_id = ? AND assigned_to = ? 
              ORDER BY due_date ASC";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $project_id, $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $task_id = (int)$_POST['task_id'];
    $status = $_POST['status'];

    $query = "UPDATE tasks SET status = ? WHERE task_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("si", $status, $task_id);
    if ($stmt->execute()) {
        header("Location: work.php?project_id=$project_id");
        exit();
    } else {
        die("Error: " . $stmt->error);
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
    <title>Work Dashboard</title>
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
                <a href="work.php" class="block text-gray-300 hover:text-white">Start Working</a>
            </li>
            <li>
                <a href="logout.php" class="block text-gray-300 hover:text-white">Logout</a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 overflow-y-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Your Tasks</h1>

        <!-- Project Selection -->
        <div class="mb-6">
            <label for="projectSelect" class="block text-sm font-medium text-gray-700 mb-2">Select Project</label>
            <select id="projectSelect" onchange="loadProjectTasks()"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                <option value="">Choose a Project</option>
                <?php foreach ($unique_projects as $project): ?>
                    <option value="<?php echo $project['project_id']; ?>"
                        <?php echo ($project_id == $project['project_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($project['project_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Kanban Board -->
        <?php if ($project_id): ?>
            <div class="flex flex-row gap-6 overflow-x-auto overflow-y-auto">
                <!-- Backlog -->
                <div class="bg-white rounded-lg shadow-lg p-4 w-[350px]">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">Backlog</h2>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded"
                            id="backlog-count">0</span>
                    </div>
                    <div class="space-y-3" id="backlog-tasks">
                        <?php foreach ($tasks as $task): ?>
                            <?php if ($task['status'] === 'backlog'): ?>
                                <div class="bg-gray-50 p-4 rounded-lg hover:shadow-md transition-all">
                                    <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($task['task_title']); ?></h3>
                                    <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($task['task_description']); ?></p>
                                    <div class="flex justify-between items-center mt-3">
                                        <span class="text-xs text-gray-500">Due: <?php echo $task['due_date']; ?></span>
                                        <form method="POST" class="flex space-x-2">
                                            <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                            <select name="status"
                                                class="px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                                <option value="backlog" <?php echo ($task['status'] === 'backlog') ? 'selected' : ''; ?>>Backlog</option>
                                                <option value="todo" <?php echo ($task['status'] === 'todo') ? 'selected' : ''; ?>>ToDo</option>
                                                <option value="in_progress" <?php echo ($task['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="completed" <?php echo ($task['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                            </select>
                                            <button type="submit" name="update_status"
                                                class="px-2 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">Update</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ToDo -->
                <div class="bg-white rounded-lg shadow-lg p-4 w-[350px]">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">ToDo</h2>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded"
                            id="todo-count">0</span>
                    </div>
                    <div class="space-y-3" id="todo-tasks">
                        <?php foreach ($tasks as $task): ?>
                            <?php if ($task['status'] === 'todo'): ?>
                                <div class="bg-yellow-50 p-4 rounded-lg hover:shadow-md transition-all">
                                    <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($task['task_title']); ?></h3>
                                    <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($task['task_description']); ?></p>
                                    <div class="flex justify-between items-center mt-3">
                                        <span class="text-xs text-gray-500">Due: <?php echo $task['due_date']; ?></span>
                                        <form method="POST" class="flex space-x-2">
                                            <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                            <select name="status"
                                                class="px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                                <option value="backlog" <?php echo ($task['status'] === 'backlog') ? 'selected' : ''; ?>>Backlog</option>
                                                <option value="todo" <?php echo ($task['status'] === 'todo') ? 'selected' : ''; ?>>ToDo</option>
                                                <option value="in_progress" <?php echo ($task['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="completed" <?php echo ($task['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                            </select>
                                            <button type="submit" name="update_status"
                                                class="px-2 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">Update</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- In Progress -->
                <div class="bg-white rounded-lg shadow-lg p-4 w-[350px]">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">In Progress</h2>
                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded"
                            id="in-progress-count">0</span>
                    </div>
                    <div class="space-y-3" id="in-progress-tasks">
                        <?php foreach ($tasks as $task): ?>
                            <?php if ($task['status'] === 'in_progress'): ?>
                                <div class="bg-purple-50 p-4 rounded-lg hover:shadow-md transition-all">
                                    <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($task['task_title']); ?></h3>
                                    <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($task['task_description']); ?></p>
                                    <div class="flex justify-between items-center mt-3">
                                        <span class="text-xs text-gray-500">Due: <?php echo $task['due_date']; ?></span>
                                        <form method="POST" class="flex space-x-2">
                                            <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                            <select name="status"
                                                class="px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                                <option value="backlog" <?php echo ($task['status'] === 'backlog') ? 'selected' : ''; ?>>Backlog</option>
                                                <option value="todo" <?php echo ($task['status'] === 'todo') ? 'selected' : ''; ?>>ToDo</option>
                                                <option value="in_progress" <?php echo ($task['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="completed" <?php echo ($task['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                            </select>
                                            <button type="submit" name="update_status"
                                                class="px-2 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">Update</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Completed -->
                <div class="bg-white rounded-lg shadow-lg p-4 w-[350px]">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">Completed</h2>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded"
                            id="completed-count">0</span>
                    </div>
                    <div class="space-y-3" id="completed-tasks">
                        <?php foreach ($tasks as $task): ?>
                            <?php if ($task['status'] === 'completed'): ?>
                                <div class="bg-green-50 p-4 rounded-lg hover:shadow-md transition-all">
                                    <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($task['task_title']); ?></h3>
                                    <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($task['task_description']); ?></p>
                                    <div class="flex justify-between items-center mt-3">
                                        <span class="text-xs text-gray-500">Completed</span>
                                        <form method="POST" class="flex space-x-2">
                                            <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                            <select name="status"
                                                class="px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                                <option value="backlog" <?php echo ($task['status'] === 'backlog') ? 'selected' : ''; ?>>Backlog</option>
                                                <option value="todo" <?php echo ($task['status'] === 'todo') ? 'selected' : ''; ?>>ToDo</option>
                                                <option value="in_progress" <?php echo ($task['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="completed" <?php echo ($task['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                            </select>
                                            <button type="submit" name="update_status"
                                                class="px-2 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">Update</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p>Please select a project to view tasks.</p>
        <?php endif; ?>
    </main>

    <script>
        function loadProjectTasks() {
            const projectId = document.getElementById("projectSelect").value;
            if (projectId) {
                window.location.href = `work.php?project_id=${projectId}`;
            }
        }

        function updateCounts() {
            document.getElementById("backlog-count").textContent = document.getElementById("backlog-tasks").children.length;
            document.getElementById("todo-count").textContent = document.getElementById("todo-tasks").children.length;
            document.getElementById("in-progress-count").textContent = document.getElementById("in-progress-tasks").children.length;
            document.getElementById("completed-count").textContent = document.getElementById("completed-tasks").children.length;
        }

        // Update counts on page load
        document.addEventListener('DOMContentLoaded', updateCounts);
    </script>
</body>

</html>