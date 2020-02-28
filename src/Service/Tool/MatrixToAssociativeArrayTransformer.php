<?php

declare(strict_types=1);

namespace App\Service\Tool;

class MatrixToAssociativeArrayTransformer
{
    /**
     * @param array $someCasualMatrix
     * @return array
     */
    public function transformArrayToAssociative(array $someCasualMatrix): array
    {
        $headers = $someCasualMatrix[0];
        unset($someCasualMatrix[0]);
        $associativeArray = [];

        foreach ($someCasualMatrix as $row) {
            $newRow = [];

            foreach ($headers as $key => $value) {
                $newRow[$value] = $row[$key];
            }
            $associativeArray[] = $newRow;
        }

        return $associativeArray;
    }
}
