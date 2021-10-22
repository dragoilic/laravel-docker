<?php

namespace App\Betting\SportEvent;

use App\Betting\Settlement;
use Decimal\Decimal;

class Line
{
    private string $id;
    private string $period;
    private string $name;
    private string $type;
    private ?int $price;
    private ?Decimal $line;
    private ?Settlement $settlement;

    public function __construct(string $id, string $period, string $name, string $type, ?int $price, ?Decimal $line, ?Settlement $settlement)
    {
        $this->id = $id;
        $this->period = $period;
        $this->name = $name;
        $this->type = $type;
        $this->price = $price;
        $this->line = $line;
        $this->settlement = $settlement;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPeriod(): string
    {
        return $this->period;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function getLine(): ?Decimal
    {
        return $this->line;
    }

    public function getSettlement(): ?Settlement
    {
        return $this->settlement;
    }
}
