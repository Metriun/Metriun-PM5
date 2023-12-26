<?php

declare(strict_types=1);

namespace Metriun\Utils;

use Metriun\Loader;
use pocketmine\utils\Config;

class MetriunConfig extends Config {

  public function __construct(Loader $plugin, String $event) {
    $config_file = $plugin->getDataFolder() . "database/";
    @mkdir($config_file);
    parent::__construct($config_file . "$event.yml", Config::YAML);
  }
}
