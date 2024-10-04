<?php

require 'Task.php';

class TaskController
{
  private $file = __DIR__ . "/task.json";
  private array $tasks = [];

  public function __construct()
  {
    if (file_exists($this->file)) {
      $tasksFileContent = file_get_contents($this->file);
      $this->tasks = json_decode($tasksFileContent, true);
      $this->tasks = array_map(function ($task) {
        return new Task(...$task);
      }, $this->tasks);
    }
  }

  private function nextId(): int
  {
    $lastTask = end($this->tasks);

    return $lastTask ? $lastTask->id + 1 : 1;
  }

  public function addTask(string $description)
  {

    $task = new Task($this->nextId(), $description, 'todo');
    $this->tasks[] = $task;
    $this->saveTasks();

    echo "Task added successfully (ID: $task->id) ";
  }

  private function saveTasks()
  {
    $data = array_map(function ($task) {
      return [
        'id' => $task->id,
        'description' => $task->description,
        'status' => $task->status,
        'createdAt' => $task->createdAt,
        'updatedAt' => $task->updatedAt,
      ];
    }, $this->tasks);

    $json = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($this->file, $json);
  }

  public function updateTask(int $id, string $description)
  {
    if (empty($this->tasks)) {
      echo "No tasks found \n";
      return;
    }
    foreach ($this->tasks as $key => $task) {
      if ($task->id == $id) {
        $this->tasks[$key]->description = $description;
        $this->tasks[$key]->updatedAt = date('Y-m-d H:i:s');
        $this->saveTasks();
        break;
      }
    }
    echo "Task updated successfully";
  }

  public function deleteTask(int $id)
  {
    foreach ($this->tasks as $key => $task) {
      if ($task->id == $id) {
        unset($this->tasks[$key]);
        $this->saveTasks();
        echo "Task $id deleted successfully";
        return true;
      }
    }
  }

  public function listTasks(array $params)
  {
    if (empty($this->tasks)) {
      echo "No tasks found\n";
      return;
    }

    if (isset($params[2])) {
      foreach ($this->tasks as $task) {
        if ($task->status === $params[2]) {
          echo $task->id . ". " . $task->description . " - " . $task->status . "\n";
        }
      }
    } else {
      foreach ($this->tasks as $task) {
        echo $task->id . ". " . $task->description . " - " . $task->status . "\n";
      }
    }
  }

  public function markInProgress(int $id)
  {
    if (empty($this->tasks)) {
      echo "No tasks found\n";
      return;
    }

    foreach ($this->tasks as $key => $task) {
      if ($task->id === $id) {
        $this->tasks[$key]->status = 'in-progress';
        $this->tasks[$key]->updatedAt = date('d/m/Y H:i:s');
        $this->saveTasks();
        break;
      }
    }

    echo "Task $id marked as in progress";
  }

  public function markDone(int $id)
  {
    if (empty($this->tasks)) {
      echo "No tasks found\n";
      return;
    }

    foreach ($this->tasks as $key => $task) {
      if ($task->id === $id) {
        if ($task->status === "done") {
          echo "Task ID:$id is already done";
          return;
        }

        $this->tasks[$key]->status = 'done';
        $this->tasks[$key]->updatedAt = date('d/m/Y H:i:s');
        $this->saveTasks();
        break;
      }
    }

    echo "Task ID:$id marked as done";
  }

  public  static function showHelp()
  {
    
  }
}
