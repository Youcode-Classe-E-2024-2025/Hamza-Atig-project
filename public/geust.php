<?php
class Database {
    private $conn;

    public function __construct() {
        include BASE_PATH . 'config/database.php';
        $this->conn = new mysqli($host, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function query($query) {
        return $this->conn->query($query);
    }

    public function close() {
        $this->conn->close();
    }
}

class Project {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getPublicProjects() {
        $query = "SELECT * FROM projects WHERE status = 1";
        $result = $this->db->query($query);
        $publicProjects = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $publicProjects[] = $row;
            }
        }

        return $publicProjects;
    }

    public function getTasksForProject($projectId) {
        $query = "SELECT * FROM tasks WHERE project_id = $projectId ORDER BY status";
        $result = $this->db->query($query);
        $tasks = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $tasks[] = $row;
            }
        }

        return $tasks;
    }
}

// Main Logic
$db = new Database();
$project = new Project($db);

$publicProjects = $project->getPublicProjects();

foreach ($publicProjects as &$projectItem) {
    $projectId = $projectItem['project_id'];
    $projectItem['tasks'] = $project->getTasksForProject($projectId);
}

$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Guest Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex justify-end px-4 py-2">
      <a href="login.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full mr-2">Login</a>
      <a href="signup.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">Signup</a>
    </div>

  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Public Projects</h1>

    <?php if (!empty($publicProjects)): ?>
      <?php foreach ($publicProjects as $project): ?>
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
          <h2 class="text-2xl font-semibold text-gray-700 mb-2"><?php echo htmlspecialchars($project['project_name']); ?></h2>
          <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($project['description']); ?></p>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Backlog Column -->
            <div class="bg-gray-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-gray-700 mb-4">Backlog</h3>
              <?php foreach ($project['tasks'] as $task): ?>
                <?php if ($task['status'] == 'backlog'): ?>
                  <div class="bg-white p-3 rounded-lg shadow-sm mb-3 hover:shadow-md transition-shadow">
                    <p class="text-gray-800"><?php echo htmlspecialchars($task['task_title']); ?></p>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>

            <!-- To Do Column -->
            <div class="bg-gray-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-gray-700 mb-4">To Do</h3>
              <?php foreach ($project['tasks'] as $task): ?>
                <?php if ($task['status'] == 'todo'): ?>
                  <div class="bg-white p-3 rounded-lg shadow-sm mb-3 hover:shadow-md transition-shadow">
                    <p class="text-gray-800"><?php echo htmlspecialchars($task['task_title']); ?></p>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>

            <!-- In Progress Column -->
            <div class="bg-gray-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-gray-700 mb-4">In Progress</h3>
              <?php foreach ($project['tasks'] as $task): ?>
                <?php if ($task['status'] == 'in_progress'): ?>
                  <div class="bg-white p-3 rounded-lg shadow-sm mb-3 hover:shadow-md transition-shadow">
                    <p class="text-gray-800"><?php echo htmlspecialchars($task['task_title']); ?></p>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>

            <!-- Completed Column -->
            <div class="bg-gray-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-gray-700 mb-4">Completed</h3>
              <?php foreach ($project['tasks'] as $task): ?>
                <?php if ($task['status'] == 'completed'): ?>
                  <div class="bg-white p-3 rounded-lg shadow-sm mb-3 hover:shadow-md transition-shadow">
                    <p class="text-gray-800"><?php echo htmlspecialchars($task['task_title']); ?></p>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center text-gray-600">No public projects available.</p>
    <?php endif; ?>
  </div>

</body>
</html>