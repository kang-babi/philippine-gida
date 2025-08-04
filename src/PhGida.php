<?php

declare(strict_types=1);

namespace KangBabi\PhGida;

class PhGida
{
    /**
     * Summary of data
     * @var array<int, array{
     *  region: string,
     *  provinces: array<int, array{
     *      province: string,
     *      municipalities: array<int, array{
     *          municipality: string,
     *          barangays: string[],
     *      }>
     *  }>
     * }>
     */
    protected array $data = [];

    public function __construct()
    {
        $dataDir = __DIR__ . '/data';
        
        foreach (glob("{$dataDir}/*.php") as $file) {
            $this->data[] = include $file;
        }
    }

    /**
     * Get GIDA data.
     */
    public function get(string $region = ''): array
    {
        if(!empty($region)) {
            return array_filter($this->data, fn(array $region): bool => $region['region'] === $region);
        }

        return $this->data;
    }

    /**
     * Get Normalized GIDA data.
     */
    public function getNormalized(string $region = ''): array
    {
        $normalized = [];

        foreach ($this->data as $regionData) {
            if ($region !== '' && $regionData['region'] !== $region) {
                continue;
            }

            foreach ($regionData['provinces'] as $provinceData) {
                foreach ($provinceData['municipalities'] as $municipalityData) {
                    foreach ($municipalityData['barangays'] as $barangay) {
                        $normalized[] = [
                            'region' => $regionData['region'],
                            'province' => $provinceData['province'],
                            'municipality' => $municipalityData['municipality'],
                            'barangay' => $barangay,
                        ];
                    }
                }
            }
        }

        return $normalized;
    }
}