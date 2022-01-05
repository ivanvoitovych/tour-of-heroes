<?php

namespace Components\Mocks;

use Components\Models\HeroModel;

class HerosMocks
{
    private ?array $heroes = null;

    public function GetHeroes(): array
    {
        if ($this->heroes == null) {
            $this->heroes = [
                $this->CreateHero(1, 'Metal Man'),
                $this->CreateHero(2, 'Firefly'),
                $this->CreateHero(3, 'Mastermind'),
                $this->CreateHero(4, 'Bulletproof'),
                $this->CreateHero(5, 'Fireball'),
                $this->CreateHero(6, 'Apex'),
                $this->CreateHero(7, 'Turbine'),
                $this->CreateHero(8, 'Tarantula'),
                $this->CreateHero(9, 'Shockwave'),
                $this->CreateHero(10, 'Steamroller')
            ];
        }
        return $this->heroes;
    }

    public function CreateHero(int $id, string $name): HeroModel
    {
        $hero = new HeroModel();
        $hero->Id = $id;
        $hero->Name = $name;
        return $hero;
    }
}
