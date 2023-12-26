<?php

declare(strict_types=1);

namespace Metriun\Utils;

use Metriun\Loader;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use Metriun\Tasks\MetriunTask;

class Task{

  static public function running(\Closure $callback, int $time): void {
    $plugin = Loader::getInstance();
    if ($plugin instanceof PluginBase) {
      $plugin->getScheduler()->scheduleRepeatingTask(new MetriunTask($callback, $plugin), $time * 20);
    } else {
      echo "\nPlugin Metriun não encontrado\n";
    }
  }

}
