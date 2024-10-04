<?php
require "src/TaskController.php";
require "vendor/autoload.php";


if (!isset($argv[1])) {
  echo "Provide some especification";
}

$controller = new TaskController();


$response =  match (strtolower($argv[1])) {
  "add" => $controller->addTask($argv[2]) ,
  "update" => $controller->updateTask($argv[2], $argv[3]),
  "delete" => $controller->deleteTask($argv[2]),
  "mark-in-progress" => $controller->markInProgress($argv[2]),
  "mark-done" => $controller->markDone($argv[2]),
  "list" => $controller->listTasks($argv),
  "help", "-h", "--help" => $controller::showHelp(),
  default => "Unknow command: " . '"' . $argv[1] . '"'
};
