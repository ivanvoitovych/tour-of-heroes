<?php

namespace BackendApp;

use Components\Models\HeroModel;
use ReflectionException;
use Viewi\Common\JsonMapper;

class Repository
{
    /**
     * 
     * @var null|HasId[]
     */
    private ?array $data = null;
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
        return $this->data;
    }

    public function GetById(int $id)
    {
        $this->Prepare();
        $searchResult = array_values(array_filter(
            $this->data,
            function ($x) use ($id) {
                /** @var HasId $x */
                return $x->Id == $id;
            }
        ));
        return $searchResult ? $searchResult[0] : null;
    }

    /**
     * 
     * @param int $id 
     * @param HasId $object 
     * @return bool 
     * @throws ReflectionException 
     */
    public function Update($object)
    {
        $this->Prepare();
        foreach ($this->data as $index => $item) {
            if ($item->Id === $object->Id) {
                $this->data[$index] = $object;
                $this->Flush();
                return true;
            }
        }
        return false;
    }

    /**
     * 
     * @param HasId $object 
     * @return bool 
     * @throws ReflectionException 
     */
    public function Create($object)
    {
        $this->Prepare();
        if (count($this->data)) {
            $object->Id = $this->data[count($this->data) - 1]->Id + 1;
        } else {
            $object->Id = 1;
        }
        $this->data[] = $object;
        $this->Flush();
        return $object;
    }

    public function Delete(int $id)
    {
        $this->Prepare();
        foreach ($this->data as $index => $item) {
            if ($item->Id === $id) {
                unset($this->data[$index]);
                $this->data = array_values($this->data);
                $this->Flush();
                return true;
            }
        }
        return false;
    }

    private function Flush()
    {
        file_put_contents($this->fileName, json_encode($this->data));
    }

    private function Prepare()
    {
        if (!$this->ready) {
            $this->data = [];
            if (file_exists($this->fileName)) {
                $json = file_get_contents($this->fileName);
                $objects = json_decode($json, false);
                foreach ($objects as $object) {
                    $this->data[] = JsonMapper::Instantiate($this->className, $object);
                }
            } else {
                $this->data = [];
                file_put_contents($this->fileName, json_encode($this->data));
            }
        }
    }
}

abstract class HasId
{
    public int $Id;
}
