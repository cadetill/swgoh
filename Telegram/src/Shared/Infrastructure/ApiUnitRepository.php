<?php

namespace Im\Shared\Infrastructure;

class ApiUnitRepository implements UnitRepository
{
    private string $basePath;
    private ?array $alias = null;
    private ?array $units = null;
    private string $lang;

    public function __construct(string $basePath, string $lang)
    {
        $this->basePath = $basePath;
        $this->lang     = $lang;
    }

    public function existAlias(string $alias): bool
    {
        return array_key_exists($alias, $this->alias()) || array_key_exists(strtoupper($alias), $this->units());
    }

    public function unitByAlias(string $alias): array
    {
        $unitId = $this->alias()[$alias] ?? strtoupper($alias);

        $unit = $this->units()[$unitId];

        return [
            'baseId' => $unit['baseId'],
            'name'   => $unit[$this->lang],
        ];
    }

    private function alias()
    {
        return $this->alias = $this->alias ?: $this->buildAlias();
    }

    private function buildAlias()
    {
        $filePath = $this->basePath . '/json/alias.json';
        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);
            $content     = json_decode($fileContent, true);
        } else {
            $content = [];
        }

        $alias = [];
        foreach ($content as $unitId => $aliases) {
            foreach ($aliases as $unitAlias) {
                $alias[strtolower($unitAlias)] = $unitId;
            }
        }

        return $alias;
    }

    private function units()
    {
        return $this->units = $this->units ?: $this->buildUnits();
    }

    private function buildUnits()
    {
        $filePath = $this->basePath . '/json/units.json';
        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);
            $content     = json_decode($fileContent, true);
        } else {
            $content = [];
        }

        $units = [];
        foreach ($content as $unit) {
            $units[$unit['baseId']] = $unit;
        }

        return $units;
    }
}
