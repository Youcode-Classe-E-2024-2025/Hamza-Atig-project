<?php
include '../config/database.php';

// Fetch public projects
$query = "SELECT * FROM projects WHERE status = 1";
$result = $conn->query($query);
$public_projects = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $public_projects[] = $row;
  }
}

// Fetch tasks for each public project
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
  <title>Guest Page</title>
  <style>
    .kanban-board {
      display: flex;
    }
    .column {
      margin-right: 20px;
    }
    .task {
      border: 1px solid #ccc;
      padding: 10px;
      margin-bottom: 5px;
    }
  </style>
</head>
<body>

<h1>Public Projects</h1>

<?php if (!empty($public_projects)): ?>
  <?php foreach ($public_projects as $project): ?>
    <h2><?php echo htmlspecialchars($project['project_name']); ?></h2>
    <p><?php echo htmlspecialchars($project['description']); ?></p>

    <div class="kanban-board">
      <div class="column">
        <h3>Backlog</h3>
        <?php foreach ($project['tasks'] as $task): ?>
          <?php if ($task['status'] == 'backlog'): ?>
            <div class="task"><?php echo htmlspecialchars($task['task_title']); ?></div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <div class="column">
        <h3>To Do</h3>
        <?php foreach ($project['tasks'] as $task): ?>
          <?php if ($task['status'] == 'todo'): ?>
            <div class="task"><?php echo htmlspecialchars($task['task_title']); ?></div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <div class="column">
        <h3>In Progress</h3>
        <?php foreach ($project['tasks'] as $task): ?>
          <?php if ($task['status'] == 'in_progress'): ?>
            <div class="task"><?php echo htmlspecialchars($task['task_title']); ?></div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <div class="column">
        <h3>Completed</h3>
        <?php foreach ($project['tasks'] as $task): ?>
          <?php if ($task['status'] == 'completed'): ?>
            <div class="task"><?php echo htmlspecialchars($task['task_title']); ?></div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <p>No public projects available.</p>
<?php endif; ?>

</body>
</html>