<?php
namespace App\Betting\SportEvent;

class League
{
    private string $id;
    private string $name;
    private string $sportId;
    private string $provider;

    public function __construct(string $id, string $name, string $sportId, string $provider)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sportId = $sportId;
        $this->provider = $provider;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSportId(): string
    {
        return $this->sportId;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }
}
