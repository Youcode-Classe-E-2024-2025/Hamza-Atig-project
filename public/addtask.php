<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../config/database.php';

$created_by = $_SESSION['user_id'];
$query = "SELECT project_id, project_name FROM projects WHERE created_by = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $created_by);
$stmt->execute();
$result = $stmt->get_result();
$projects = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}

$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : null;
$tasks = [];
if ($project_id) {
    $query = "SELECT t.task_id, t.task_title, t.task_description, t.due_date, t.status, t.assigned_to, u.username 
              FROM tasks t
              LEFT JOIN users u ON t.assigned_to = u.user_id
              WHERE t.project_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
    }
}

$members = [];
if ($project_id) {
    $query = "SELECT u.user_id, u.username 
              FROM users u
              JOIN join_requests jr ON u.user_id = jr.user_id
              WHERE jr.receiver_id = ? AND jr.status = 'approved'";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $members[] = $row;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $task_title = trim($_POST['task_title']);
    $task_description = trim($_POST['task_description']);
    $due_date = $_POST['due_date'];
    $assigned_to = isset($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;

    if (empty($task_title) || empty($task_description) || empty($due_date)) {
        die("Please fill in all required fields.");
    }

    $query = "INSERT INTO tasks (task_title, task_description, due_date, status, project_id, assigned_to) 
              VALUES (?, ?, ?, 'backlog', ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssii", $task_title, $task_description, $due_date, $project_id, $assigned_to);
    if ($stmt->execute()) {
        header("Location: addtask.php?project_id=$project_id");
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
    <title>Kanban Board</title>
    <link rel="stylesheet" href="../assets/css/tailwind.css">
</head>

<body class="bg-gray-900 min-h-screen p-6">
    <!-- Sidebar -->
    <aside id="default-sidebar"
        class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0"
        aria-label="Sidebar">
        <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800">
            <ul class="space-y-2 font-medium">
                <li>
                    <a href="./index.php"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 22 21">
                            <path
                                d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                            <path
                                d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                        </svg>
                        <span class="ms-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="./addtask.php"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 18 18">
                            <path
                                d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Kanban</span>
                        <span
                            class="inline-flex items-center justify-center px-2 ms-3 text-sm font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">Pro</span>
                    </a>
                </li>
                <li>
                    <a href="inbox.php"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path
                                d="m17.418 3.623-.018-.008a6.713 6.713 0 0 0-2.4-.569V2h1a1 1 0 1 0 0-2h-2a1 1 0 0 0-1 1v2H9.89A6.977 6.977 0 0 1 12 8v5h-2V8A5 5 0 1 0 0 8v6a1 1 0 0 0 1 1h8v4a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-4h6a1 1 0 0 0 1-1V8a5 5 0 0 0-2.582-4.377ZM6 12H4a1 1 0 0 1 0-2h2a1 1 0 0 1 0 2Z" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Inbox</span>
                        <span
                            class="inline-flex items-center justify-center w-3 h-3 p-3 ms-3 text-sm font-medium text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">3</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 20 18">
                            <path
                                d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Users</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 18 20">
                            <path
                                d="M17 5.923A1 1 0 0 0 16 5h-3V4a4 4 0 1 0-8 0v1H2a1 1 0 0 0-1 .923L.086 17.846A2 2 0 0 0 2.08 20h13.84a2 2 0 0 0 1.994-2.153L17 5.923ZM7 9a1 1 0 0 1-2 0V7h2v2Zm0-5a2 2 0 1 1 4 0v1H7V4Zm6 5a1 1 0 1 1-2 0V7h2v2Z" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Products</span>
                    </a>
                </li>
                <li>
                    <a href="logout.php"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 8h11m0 0L8 4m4 4-4 4m4-11h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <div class="w-[80%] left-[18%] absolute">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-100">Kanban Board</h1>
            <button onclick="openAddTaskModal()"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">Add Task</button>
        </div>

        <!-- Project Selection -->
        <div class="mb-6">
            <label for="projectSelect" class="block text-sm font-medium text-gray-300 mb-2">Select Project</label>
            <select id="projectSelect" onchange="loadProjectTasks()"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                <option value="">Choose a Project</option>
                <?php foreach ($projects as $project): ?>
                    <option value="<?php echo $project['project_id']; ?>"
                        <?php echo ($project_id == $project['project_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($project['project_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Kanban Board -->
        <div class="flex flex-row gap-6 overflow-x-auto">
            <!-- Backlog -->
            <div class="bg-white rounded-lg shadow-lg p-4 w-[350px]" ondrop="drop(event)" ondragover="allowDrop(event)"
                id="backlog">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Backlog</h2>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded"
                        id="backlog-count">0</span>
                </div>
                <div class="space-y-3" id="backlog-tasks">
                    <?php foreach ($tasks as $task): ?>
                        <?php if ($task['status'] === 'backlog'): ?>
                            <div class="bg-gray-50 p-4 rounded-lg hover:shadow-md transition-all cursor-pointer"
                                draggable="true" ondragstart="drag(event)" id="task<?php echo $task['task_id']; ?>">
                                <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($task['task_title']); ?></h3>
                                <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($task['task_description']); ?></p>
                                <div class="flex justify-between items-center mt-3">
                                    <span class="text-xs text-gray-500">Due: <?php echo $task['due_date']; ?></span>
                                    <div class="space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800"
                                            onclick="editTask(<?php echo $task['task_id']; ?>)">✎</button>
                                        <button class="text-red-600 hover:text-red-800"
                                            onclick="deleteTask(<?php echo $task['task_id']; ?>)">×</button>
                                    </div>
                                </div>
                                <?php if ($task['username']): ?>
                                    <div class="mt-2">
                                        <span class="text-xs text-gray-500">Assigned to: <?php echo htmlspecialchars($task['username']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ToDo -->
            <div class="bg-white rounded-lg shadow-lg p-4 w-[350px]" ondrop="drop(event)" ondragover="allowDrop(event)"
                id="todo">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">ToDo</h2>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded"
                        id="todo-count">0</span>
                </div>
                <div class="space-y-3" id="todo-tasks">
                    <?php foreach ($tasks as $task): ?>
                        <?php if ($task['status'] === 'todo'): ?>
                            <div class="bg-yellow-50 p-4 rounded-lg hover:shadow-md transition-all cursor-pointer"
                                draggable="true" ondragstart="drag(event)" id="task<?php echo $task['task_id']; ?>">
                                <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($task['task_title']); ?></h3>
                                <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($task['task_description']); ?></p>
                                <div class="flex justify-between items-center mt-3">
                                    <span class="text-xs text-gray-500">Due: <?php echo $task['due_date']; ?></span>
                                    <div class="space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800"
                                            onclick="editTask(<?php echo $task['task_id']; ?>)">✎</button>
                                        <button class="text-red-600 hover:text-red-800"
                                            onclick="deleteTask(<?php echo $task['task_id']; ?>)">×</button>
                                    </div>
                                </div>
                                <?php if ($task['username']): ?>
                                    <div class="mt-2">
                                        <span class="text-xs text-gray-500">Assigned to: <?php echo htmlspecialchars($task['username']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- In Progress -->
            <div class="bg-white rounded-lg shadow-lg p-4 w-[350px]" ondrop="drop(event)" ondragover="allowDrop(event)"
                id="in-progress">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">In Progress</h2>
                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded"
                        id="in-progress-count">0</span>
                </div>
                <div class="space-y-3" id="in-progress-tasks">
                    <?php foreach ($tasks as $task): ?>
                        <?php if ($task['status'] === 'in_progress'): ?>
                            <div class="bg-purple-50 p-4 rounded-lg hover:shadow-md transition-all cursor-pointer"
                                draggable="true" ondragstart="drag(event)" id="task<?php echo $task['task_id']; ?>">
                                <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($task['task_title']); ?></h3>
                                <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($task['task_description']); ?></p>
                                <div class="flex justify-between items-center mt-3">
                                    <span class="text-xs text-gray-500">Due: <?php echo $task['due_date']; ?></span>
                                    <div class="space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800"
                                            onclick="editTask(<?php echo $task['task_id']; ?>)">✎</button>
                                        <button class="text-red-600 hover:text-red-800"
                                            onclick="deleteTask(<?php echo $task['task_id']; ?>)">×</button>
                                    </div>
                                </div>
                                <?php if ($task['username']): ?>
                                    <div class="mt-2">
                                        <span class="text-xs text-gray-500">Assigned to: <?php echo htmlspecialchars($task['username']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Completed -->
            <div class="bg-white rounded-lg shadow-lg p-4 w-[350px]" ondrop="drop(event)" ondragover="allowDrop(event)"
                id="completed">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Completed</h2>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded"
                        id="completed-count">0</span>
                </div>
                <div class="space-y-3" id="completed-tasks">
                    <?php foreach ($tasks as $task): ?>
                        <?php if ($task['status'] === 'completed'): ?>
                            <div class="bg-green-50 p-4 rounded-lg hover:shadow-md transition-all cursor-pointer"
                                draggable="true" ondragstart="drag(event)" id="task<?php echo $task['task_id']; ?>">
                                <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($task['task_title']); ?></h3>
                                <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($task['task_description']); ?></p>
                                <div class="flex justify-between items-center mt-3">
                                    <span class="text-xs text-gray-500">Completed</span>
                                    <div class="space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800"
                                            onclick="editTask(<?php echo $task['task_id']; ?>)">✎</button>
                                        <button class="text-red-600 hover:text-red-800"
                                            onclick="deleteTask(<?php echo $task['task_id']; ?>)">×</button>
                                    </div>
                                </div>
                                <?php if ($task['username']): ?>
                                    <div class="mt-2">
                                        <span class="text-xs text-gray-500">Assigned to: <?php echo htmlspecialchars($task['username']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div id="addTaskModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-6 border w-96 shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Add New Task</h3>
                <p class="text-sm text-gray-500">Fill in the details to create a new task.</p>
            </div>
            <!-- Modal Form -->
            <form method="POST" class="space-y-4">
                <!-- Task Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="task_title"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        placeholder="Enter task title" required>
                </div>
                <!-- Task Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="task_description"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        rows="3" placeholder="Enter task description" required></textarea>
                </div>
                <!-- Task Due Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" name="due_date"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        required>
                </div>
                <!-- Assign Task to Member -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assign to</label>
                    <select name="assigned_to"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        <option value="">Unassigned</option>
                        <?php foreach ($members as $member): ?>
                            <option value="<?php echo $member['user_id']; ?>">
                                <?php echo htmlspecialchars($member['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Modal Actions -->
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddTaskModal()"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-200">Cancel</button>
                    <button type="submit" name="add_task"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">Add
                        Task</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function allowDrop(ev) {
            ev.preventDefault();
        }

        function drag(ev) {
            ev.dataTransfer.setData("text", ev.target.id);
        }

        function drop(ev) {
            ev.preventDefault();
            const data = ev.dataTransfer.getData("text");
            const draggedElement = document.getElementById(data);
            const dropZone = ev.target.closest('[ondrop]');
            if (dropZone && draggedElement) {
                const tasksContainer = dropZone.querySelector('div[class="space-y-3"]');
                tasksContainer.appendChild(draggedElement);
                updateCounts();
            }
        }

        function updateCounts() {
            document.getElementById("backlog-count").textContent = document.getElementById("backlog-tasks").children.length;
            document.getElementById("todo-count").textContent = document.getElementById("todo-tasks").children.length;
            document.getElementById("in-progress-count").textContent = document.getElementById("in-progress-tasks").children.length;
            document.getElementById("completed-count").textContent = document.getElementById("completed-tasks").children.length;
        }

        function openAddTaskModal() {
            document.getElementById("addTaskModal").classList.remove("hidden");
        }

        function closeAddTaskModal() {
            document.getElementById("addTaskModal").classList.add("hidden");
        }

        function loadProjectTasks() {
            const projectId = document.getElementById("projectSelect").value;
            if (projectId) {
                window.location.href = `addtask.php?project_id=${projectId}`;
            }
        }
    </script>
</body>

</html>