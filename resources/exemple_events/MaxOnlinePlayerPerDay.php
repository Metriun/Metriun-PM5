<?php

// Esse comentário é para sabermos o nome do plugin, e deve ser exatamente assim.
// EventName: MaxOnlinePlayerPerDay

use Metriun\Components\MetriunAPI;
use Metriun\Loader;
use Metriun\Utils\Task;
use pocketmine\Server;

// Criando uma instancia do plugin
$plugin = Loader::getInstance();
// Criando uma configuração para o evento
$config = $plugin->createConfig("maxOnlinePlayerPerDay");

// ID da plataforma
$plataform_id = Loader::getInstance()->getGlobalConfig()->get("plataform_id", null);
// Token da plataforma
$plataform_token = Loader::getInstance()->getGlobalConfig()->get("plataform_token", null);

// Verificando se o plugin foi configurada corretamente.
if ($plataform_id === null || $plataform_token === null) {
  $plugin->getLogger()->error("Você não configurou corretamente o seu config.yml, evento MaxOnlinePlayerPerDay não pode ser executado");
  return true;
}

// Tempo em que a task será executada em segundos.
$time = 60 * 30;

// Pegando a ID do gráfico atual que está na config
$chart_id = $config->get("chart", null);

// Verifica se a ID do gráfico é null, se for vai executar o código abaixo
if (is_null($chart_id)) {
  // Nome do gráfico
  $chart_name = "Máximo de jogadores online por dia";
  // Criando um gráfico
  $chart = MetriunAPI::createChart($plataform_id, $plataform_token, $chart_name, "Line", ["Dia", "Players"]);

  // Verifica se o gráfico foi criado com sucesso, se não foi irá definir o $chart_id como null.
  if ($chart->success) {
    // Armazenando o gráfico criado no config
    $config->set("chart", $chart->getId());
    // Forçando o salvamento do config
    $config->save();
    // pegando a id do gráfico e armazenando na variável
    $chart_id = $chart->getId();
  } else {
    // Se o gráfico não foi criado com sucesso, irá definir o $chart_id como null.
    $chart_id = null;
  }

  // Verifica se o $chart_id é null, se for vai pausar o script e exibir uma mensagem de erro.
  if (is_null($chart_id)) {
    $plugin->getLogger()->error("Falha ao criar o gráfico do evento MaxOnlinePlayerPerDay!");
    return true;
  }
} else {
  // Caso já tenha um gráfico na config, vai apenas dar esse alerta.
  $plugin->getLogger()->info("Foi usado o gráfico configurado no maxOnlinePlayerPerDay.yml, caso esse gráfico não exista mais, desligue o servidor e apague o arquivo maxOnlinePlayerPerDay.yml do plugin_data/Metriun/database");
}

// Irá iniciar a task que ficará sendo executada com forme descrito na variável $time
Task::running(function (Loader $plugin, Server $server) use ($config, $chart_id, $plataform_token): void {

  // Pegando o dia atual
  $day = date("Y-m-d");
  
  // Pegando a maior quantidade de players online que já entrou no servidor hoje.
  $playersConfig = $config->getNested("data.$day", 0);

  // Pegando a quantidade de players online
  $onlinePlayers = count($server->getOnlinePlayers());
  
  // verifica se a quantidade atual é maior que a anterior
  if ($onlinePlayers > $playersConfig) {
    // Se for vai armazenar na config
    $config->set("data", [$day => $onlinePlayers]);

    // Cria o fragmento
    $fragment = date("Y-m");

    // Cria o tpitul o1 do gráfico
    $chart_day = "Dia " . date("d");

    // Envia as informações ao gráfico.
    MetriunAPI::sendChart($plataform_token, $chart_id, [$chart_day, strval($onlinePlayers)], $fragment, $chart_day);

    // Salva a config para evitar perda de informações em caso de crash
    $config->save();
  }
}, $time);