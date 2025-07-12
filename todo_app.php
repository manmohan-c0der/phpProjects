<?php
// Save and load tasks to a JSON file
$file = 'todo.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $data['action'];

    if ($action === 'add') {
        $task = $data['task'];
        $tasks = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
        $tasks[] = ['text' => $task, 'done' => false];
        file_put_contents($file, json_encode($tasks));
        echo json_encode(['status' => 'success']);
    }

    if ($action === 'delete') {
        $index = $data['index'];
        $tasks = json_decode(file_get_contents($file), true);
        array_splice($tasks, $index, 1);
        file_put_contents($file, json_encode($tasks));
        echo json_encode(['status' => 'deleted']);
    }

    if ($action === 'toggle') {
        $index = $data['index'];
        $tasks = json_decode(file_get_contents($file), true);
        $tasks[$index]['done'] = !$tasks[$index]['done'];
        file_put_contents($file, json_encode($tasks));
        echo json_encode(['status' => 'toggled']);
    }

    exit;
}

// Load tasks on page load
$tasks = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Todo App</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f4f4;
      display: flex;
      justify-content: center;
      padding-top: 60px;
    }
    .todo-container {
      background: #fff;
      padding: 20px 30px;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      width: 400px;
    }
    h2 {
      text-align: center;
      color: #333;
    }
    #taskInput {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ddd;
      border-radius: 5px;
      margin-bottom: 15px;
    }
    ul {
      list-style: none;
      padding: 0;
    }
    li {
      padding: 10px 15px;
      background: #f9f9f9;
      margin-bottom: 8px;
      border-radius: 5px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    li.done {
      text-decoration: line-through;
      color: #888;
    }
    .btn-delete {
      background: #ff5252;
      border: none;
      color: white;
      border-radius: 3px;
      padding: 5px 10px;
      cursor: pointer;
    }
    .btn-toggle {
      background: #4caf50;
      border: none;
      color: white;
      border-radius: 3px;
      padding: 5px 10px;
      margin-right: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="todo-container">
    <h2>My Todo List</h2>
    <input type="text" id="taskInput" placeholder="Enter a task and press Enter">
    <ul id="taskList">
      <?php foreach ($tasks as $index => $task): ?>
        <li class="<?= $task['done'] ? 'done' : '' ?>" data-index="<?= $index ?>">
          <?= htmlspecialchars($task['text']) ?>
          <span>
            <button class="btn-toggle">✓</button>
            <button class="btn-delete">🗑</button>
          </span>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(function() {
      $('#taskInput').on('keypress', function(e) {
        if (e.which === 13 && this.value.trim() !== '') {
          const task = this.value.trim();
          $.ajax({
            url: '',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ action: 'add', task }),
            success: function() {
              location.reload();
            }
          });
        }
      });

      $('.btn-delete').click(function() {
        const index = $(this).closest('li').data('index');
        $.ajax({
          url: '',
          method: 'POST',
          contentType: 'application/json',
          data: JSON.stringify({ action: 'delete', index }),
          success: function() {
            location.reload();
          }
        });
      });

      $('.btn-toggle').click(function() {
        const index = $(this).closest('li').data('index');
        $.ajax({
          url: '',
          method: 'POST',
          contentType: 'application/json',
          data: JSON.stringify({ action: 'toggle', index }),
          success: function() {
            location.reload();
          }
        });
      });
    });
  </script>
</body>
</html>
