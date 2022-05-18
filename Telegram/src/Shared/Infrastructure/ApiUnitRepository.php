<?php

namespace Im\Shared\Infrastructure;

class ApiUnitRepository implements UnitRepository
{
    private string $basePath;
    private ?array $aliases = null;
    private ?array $units   = null;
    private string $lang;

    public function __construct(string $basePath, string $lang)
    {
        $this->basePath = $basePath;
        $this->lang     = $lang;
    }

    public function existAlias(string $alias): bool
    {
        return $this->hasAlias($alias) || $this->hasUnit($alias);
    }

    public function unitByAlias(string $alias): array
    {
        $unitId = $this->byAlias($alias) ?? $this->toUnitId($alias);
        $unit   = $this->byUnitId($unitId);

        return [
            'baseId' => $unit['baseId'],
            'name'   => $unit[$this->lang],
        ];
    }

    private function aliases()
    {
        return $this->aliases = $this->aliases ?: $this->buildAlias();
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
            $units[strtoupper($unit['baseId'])] = $unit;
        }

        return $units;
    }

    private function hasAlias(string $alias): bool
    {
        return array_key_exists(strtolower($alias), $this->aliases());
    }

    private function hasUnit(string $alias): bool
    {
        return array_key_exists(
            strtoupper($alias),
            $this->units()
        );
    }

    private function byAlias(string $alias)
    {
        return $this->aliases()[strtolower($alias)] ?? null;
    }

    private function toUnitId(string $alias): string
    {
        return strtoupper($alias);
    }

    private function byUnitId($unitId)
    {
        return $this->units()[strtoupper($unitId)] ?? null;
    }
}
