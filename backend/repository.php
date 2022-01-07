<?php

namespace BackendApp;

use Components\Models\HeroModel;
use Viewi\Common\JsonMapper;

class Repository
{
    private ?array $heroes = null;
    private bool $ready = false;
    private string $className;
    private string $fileName;

    public function __construct(string $className)
    {
        $this->className = $className;
        $baseName = substr(strrchr($className, "\\"), 1);
        $this->fileName = __DIR__ . "/$baseName.json";
    }

    public function Get(): array
    {
        $this->Prepare();
        return $this->heroes;
    }

    public function GetById(int $id)
    {
        $this->Prepare();
        $searchResult = array_values(array_filter(
            $this->heroes,
            function (HeroModel $x) use ($id) {
                return $x->Id == $id;
            }
        ));
        return $searchResult ? $searchResult[0] : null;
    }

    private function Prepare()
    {
        if (!$this->ready) {
            $this->heroes = [];
            if (file_exists($this->fileName)) {
                $json = file_get_contents($this->fileName);
                $objects = json_decode($json, false);
                foreach ($objects as $object) {
                    $this->heroes[] = JsonMapper::Instantiate($this->className, $object);
                }
            } else {
                $this->heroes = [];
                file_put_contents($this->fileName, json_encode($this->heroes));
            }
        }
    }
}
