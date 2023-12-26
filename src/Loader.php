<?php

declare(strict_types=1);

namespace Metriun;

use Metriun\Utils\MetriunConfig;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

class Loader extends PluginBase{

  private $events = [];
  private $configs = [];

  public function onEnable(): void {
    @mkdir($this->getDataFolder() . "events/");
    @mkdir($this->getDataFolder() . "exemple_events/");
    
    $exemple_events = glob($this->getResourceFolder() . "exemple_events/*.php");
    foreach ($exemple_events as $event) {
      $name = basename($event);
      $this->saveResource("/exemple_events/$name");
    }
    
    $this->saveResource("config.yml");
    $this->saveDefaultConfig();

    $this->getLogger()->info("Plugin Metriun ligado, iniciando eventos...");
    $this->loadEvents();
  }

  public function onDisable(): void {
    foreach ($this->configs as $key => $config) {
      $config->save();
    }
  }

  public function getEvents(): array {
    return $this->events;
  }

  private function getEventInfo(string $event): array | null {
    $pattern = '/(?:#|\/\/)\s?EventName:\s*([^\s]+)/im';
    $file_contents = file_get_contents($event);

    if (preg_match($pattern, $file_contents, $matches)) {
      $eventName = $matches[1];
      return [
        "event_name" => $eventName,
      ];
    }

    return null;
  }

  private function loadEvents(): void {
    $events_folder = $this->getDataFolder() . "events/*.php";
    $phpfiles = glob($events_folder);
    foreach ($phpfiles as $file) {
      $event_infos = $this->getEventInfo($file);
      if ($event_infos !== null) {
        $event_name = $event_infos["event_name"];
        $events[$event_name] = $file;
        require_once $file;
        $this->getLogger()->info("Evento $event_name carregado com suceso!");
      } else {
        $file_name = basename($file);
        $this->getLogger()->error("Falha ao carregar o evento do arquivo $file_name, não foi declarado um EventName válido.");
      }
    }
  }

  public function createConfig(string $event_name): MetriunConfig {
    $config = new MetriunConfig($this, $event_name);
    $this->configs[$event_name] = $config;
    return $config;
  }

  public static function getInstance(): Loader | null {
    $plugin = Server::getInstance()->getPluginManager()->getPlugin("Metriun");
    if ($plugin instanceof PluginBase) {
      return $plugin;
    } else {
      echo "\nPlugin Metriun não encontrado\n";
      return null;
    }
  }

  public function getGlobalConfig() : Config {
    return $this->getConfig();
  }
}
