<?php

class Task
{

  public function __construct(
    public int $id,
    public string $description,
    public string $status = "todo",
    public $createdAt = '',
    public $updatedAt = ''
  ) {
    $this->createdAt = date("d/m/Y H:i:s");
    $this->updatedAt = date("d/m/Y H:i:s");
  }
}
