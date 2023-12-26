<?php

declare(strict_types=1);

namespace Metriun\Tasks;

use Metriun\Loader;
use pocketmine\scheduler\Task;

class MetriunTask extends Task{
  
  private \Closure $callback;
  private Loader $plugin;
      
  public function __construct(\Closure $callback, Loader $plugin) {
    $this->callback = $callback;
    $this->plugin = $plugin;
  }
      
  public function onRun(): void {
    ($this->callback)($this->plugin, $this->plugin->getServer());
  }  

}
