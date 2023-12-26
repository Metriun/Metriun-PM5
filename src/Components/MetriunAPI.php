<?php

declare(strict_types=1);

namespace Metriun\Components;

use pocketmine\utils\Internet;

class MetriunAPI {

  private const URL = "https://metriun.com/api";
  
  static public function getChart(String $plataform_token, String $chart_id) {
    $url = self::URL . "/charts/" . $chart_id;

    try {
      $result = Internet::simpleCurl($url, 3000, [
        "Authorization: Bearer " . $plataform_token
      ], [
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FORBID_REUSE => 1,
        CURLOPT_FRESH_CONNECT => 1,
        CURLOPT_HEADER => true,
      ]);

      
      $body = json_decode($result->getBody());
      
      if (!array_key_exists(intval($result->getCode()), [200, 201, 204, 206, 304]) === false) {
        $errormessage = "Erro desconhecido.";

        if (property_exists($body, "erros")) {
          $errormessage = $body->erros;
        }

        if (property_exists($body, "message")) {
          $errormessage = $body->message;
        }


        $error = new \stdClass();
        $error->message = "Erro ao pegar as informações do gráfico: " . $errormessage;

        return $error;
      }

      return new Chart($body->chart->token ?? null, $body->chart->plataform_id ?? null, $body->chart->name ?? null, $body->chart->type ?? null, $body->chart->titles ?? null, $body->chart->graphic_values ?? null, $body->chart->requests ?? null, $body->chart->fails ?? null, $body->chart->ms ?? null, $body->chart->id ?? null);
    } catch (\Exception $e) {
      $error = new \stdClass();
      $error->message = "Erro ao pegar as informações do gráfico: " . $e->getMessage();

      return $error;
    }
  }

  static public function sendChart(
    String $plataform_token,
    String $chart_id,
    Array $data,
    String $fragment,
    String | null $change = null
    ) {
      
      if (!is_array($data)) {
        $error = new \stdClass();
        $error->success = false;
        $error->message = "O valor de data deve ser uma array, atualmente ele é um objeto, verifique se você colocou por exemplo: ['x' => 'y'] ao inves de ['x', 'y']";

        return $error;
      }
      
      $url = self::URL . "/charts/" . $chart_id . "/send";
      
      $bodySend = [
        "time" => time() * 1000,
        "data" => $data,
        "fragment" => $fragment,
      ];
      
      if ($change) {
        $bodySend["change"] = $change;
      }
      
      try {
        $result = Internet::simpleCurl($url, 3000, [
          "Authorization: Bearer " . $plataform_token
        ], [
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_SSL_VERIFYHOST => 2,
          CURLOPT_FORBID_REUSE => 1,
          CURLOPT_FRESH_CONNECT => 1,
          CURLOPT_CUSTOMREQUEST => "PUT",
          CURLOPT_POST => 1,
          CURLOPT_HEADER => true,
          CURLOPT_POSTFIELDS => json_encode($bodySend),
        ]);

      $body = json_decode($result->getBody());

      if (!array_key_exists(intval($result->getCode()), [200, 201, 204, 206, 304]) === false) {
        $errormessage = "Erro desconhecido.";

        if (property_exists($body, "erros")) {
          $errormessage = $body->erros;
        }

        if (property_exists($body, "message")) {
          $errormessage = $body->message;
        }

        $error = new \stdClass();
        $error->success = false;
        $error->message = "Erro ao enviar as informações ao grafico: " . $errormessage;
        return $error;
      }

      return new Chart($body->chart->token ?? null, $body->chart->plataform_id ?? null, $body->chart->name ?? null, $body->chart->type ?? null, $body->chart->titles ?? null, $body->chart->graphic_values ?? null, $body->chart->requests ?? null, $body->chart->fails ?? null, $body->chart->ms ?? null, $body->chart->id ?? null);
    } catch (\Exception $e) {
      $error = new \stdClass();
      $error->success = false;
      $error->message = "Erro ao enviar as informações ao grafico: " . $e->getMessage();
      return $error;
    }
  }

  static public function createChart(
    String $plataform_id,
    String $plataform_token,
    String $name,
    String $type,
    Array $titles,
    String $token = null,
    ) {      
      if (!is_array($titles)) {
        $error = new \stdClass();
        $error->success = false;
        $error->message = 'O valor de titles passado no sendChart deve ser uma array, atualmente ele é um objeto, verifique se você colocou por exemplo: ["x" => "y"] ao inves de ["x", "y"]';
        return $error;
      }

      if (!array_key_exists($type, ["Bar", "Line", "Area", "Bubble", "Candlestick", "Column", "Combo", "Pie", "Scatter", "Gauge", "Geo", "Calendar"]) === false) {
        $error = new \stdClass();
        $error->success = false;
        $error->message = "O tipo do gráfico deve ser uma das opções: Bar, Line, Bubble, Area, Candlestick, Column, Combo, Pie, Scatter, Geo ou Calendar";
        return $error;
      }

      $url = self::URL . "/plataform/$plataform_id/addchart";
      $body = [
        "name" => $name,
        "type" => $type,
        "titles" => $titles,
      ];
      
      if ($token) {
        $body["token"] = $token;
      }

      try {
        $result = Internet::simpleCurl($url, 3000, [
          "Authorization: Bearer " . $plataform_token
        ], [
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_SSL_VERIFYHOST => 2,
          CURLOPT_FORBID_REUSE => 1,
          CURLOPT_FRESH_CONNECT => 1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POST => 1,
          CURLOPT_HEADER => true,
          CURLOPT_POSTFIELDS => json_encode($body),
        ]);
        
        $body = json_decode($result->getBody());
        
        if (!array_key_exists(intval($result->getCode()), [200, 201, 204, 206, 304]) === false) {
          $errormessage = "Erro desconhecido.";

          if (property_exists($body, "erros")) {
            $errormessage = $body->erros;
          }

          if (property_exists($body, "message")) {
            $errormessage = $body->message;
          }

          $error = new \stdClass();
          $error->success = false;
          $error->message = "Erro ao criar o gráfico: " . $errormessage;
          return $error;
        }

        return new Chart($body->chart->token ?? null, $body->chart->plataform_id ?? null, $body->chart->name ?? null, $body->chart->type ?? null, $body->chart->titles ?? null, $body->chart->graphic_values ?? null, $body->chart->requests ?? null, $body->chart->fails ?? null, $body->chart->ms ?? null, $body->chart->id ?? null);
      } catch (\Exception $e) {
        $error = new \stdClass();
        $error->success = false;
        $error->message = "Erro ao criar o gráfico: " . $e->getMessage();
        return $error;
      }
    }
}