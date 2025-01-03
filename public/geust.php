<?php
include BASE_PATH . 'config/database.php';

$query = "SELECT * FROM projects WHERE status = 1";
$result = $conn->query($query);
$public_projects = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $public_projects[] = $row;
  }
}

foreach ($public_projects as &$project) {
  $project_id = $project['project_id'];
  $task_query = "SELECT * FROM tasks WHERE project_id = $project_id ORDER BY status";
  $task_result = $conn->query($task_query);
  $project['tasks'] = [];

  if ($task_result->num_rows > 0) {
    while ($task_row = $task_result->fetch_assoc()) {
      $project['tasks'][] = $task_row;
    }
  }
}

$conn->close();
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

    <?php if (!empty($public_projects)): ?>
      <?php foreach ($public_projects as $project): ?>
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