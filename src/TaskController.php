d<?php

  require 'Task.php';

  use function Termwind\{render};

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


      render('<div class="mt-1 mx-2 text-blue-400">' . "Task added successfully (ID: {$task->id})" . '</div>');
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
      render('<div class="mt-1 mx-2 text-yellow-600 p-1">' . "Task updated successfully" . '</div>');
    }

    public function deleteTask(int $id)
    {
      foreach ($this->tasks as $key => $task) {
        if ($task->id == $id) {
          unset($this->tasks[$key]);
          $this->saveTasks();
          render('<div class="mt-1 mx-2 text-red-600 font-bold">' . "Task (ID: {$id}) deleted successfully " . '</div>');
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

        render(<<<'HTML'
        <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Task</th>
                <th>Status</th>
            </tr>
        </thead>
      HTML);
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
      render(<<<"HTML"
    <div  class="mt-1">
        <div class="px-1">Task (ID:{$id}) marked as </div>
        <em class="ml-1 text-green-700 font-bold uppercase">
          in progress
          </em>
    </div>
    HTML);
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
      render(<<<"HTML"
    <div class="mt-1">
        <div class="px-1">Task (ID:{$id}) marked as </div>
        <em class="ml-1 text-red-700 font-bold uppercase">
          done
          </em>
    </div>
    HTML);
    }

    public  static function showHelp()
    {
      render(<<<'HTML'
    <table>
        <thead >
            <tr>
                <th class="text-blue-700 font-bold">command</th>
                <th class="text-blue-700 font-bold">Description</th>
            </tr>
        </thead>
        <tr>
            <td><span class="text-yellow-500">add [description]</span> </td>
            <td>Add tasks to your list </td>
        </tr>
        <tr>
            <td><span class="text-green-500">update [id][description]</span> </td>
            <td>Edit the description of an existing task.</td>
        </tr>


        <tr>
            <td><span class="text-yellow-500">list</span> </td>
            <td>View all tasks with their details. </td>
        </tr>
        <tr>
            <td><span class="text-yellow-500">list [done | in-progress | todo]</span> </td>
            <td>View tasks with selected status</td>
        </tr>
        <tr>
            <td><span class="text-yellow-500">mark-done [id]</span> </td>
            <td>Set a task's status to completed.</td>
        </tr>
        <tr>
            <td><span class="text-yellow-500">mark-in-progress [id]</span> </td>
            <td>Set a task's status to in progress.</td>
        </tr>

        <tr>
            <td><span class="text-red-500">delete [id]</span> </td>
            <td>Remove a task from the list.</td>
        </tr>
        <tr >
            <td class="text-white"><span > help | -h | --help</span> </td>
            <td class="text-white">Show all commands</td>
        </tr>
    </table>
HTML);
    }
  }
