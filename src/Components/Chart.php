<?php

declare(strict_types=1);

namespace Metriun\Components;

class Chart {
  public bool $success = true;
  private string | int | null $id;
  private string | null $token;
  private string | null $plataform_id;
  private string | null $name;
  private string | null $type;
  private array | null $titles;
  private object | null $graphic_values;
  private int | null $requests;
  private int | null $fails;
  private int | null $ms;

  public function __construct(
    string | null $token,
    string | null $plataform_id,
    string | null $name,
    string | null $type,
    array | null $titles,
    object | null $graphic_values,
    int | null $requests,
    int | null $fails,
    int | null $ms,
    string | int | null $id = null
  ) {
    $this->id = $id;
    $this->token = $token;
    $this->plataform_id = $plataform_id;
    $this->name = $name;
    $this->type = $type;
    $this->titles = $titles;
    $this->graphic_values = $graphic_values;
    $this->requests = $requests;
    $this->fails = $fails;
    $this->ms = $ms;
  }

  public function getId(): string | null {
    return $this->token;
  }

  public function getToken(): string | null {
    return $this->token;
  }

  public function getPlataformId(): string | null {
    return $this->plataform_id;
  }

  public function getName(): string | null {
    return $this->name;
  }

  public function getType(): string | null {
    return $this->type;
  }

  public function getTitles(): array | null {
    return $this->titles;
  }

  public function getGraphicValues(): object | null {
    return $this->graphic_values;
  }

  public function getRequests(): int | null {
    return $this->requests;
  }

  public function getFails(): int | null {
    return $this->fails;
  }

  public function getMs(): int | null {
    return $this->ms;
  }

}
