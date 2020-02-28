<?php

declare(strict_types=1);

namespace App\Service\Tool;

use App\Exception\InvalidExtensionException;
use Exception;

class FileExtensionFinder
{
    /**
     * @var string
     */
    private const EXTENSION = 'extension';

    /**
     * @var string
     */
    private $fileExtension;

    /**
     * @param $pathToFile
     * @return string
     * @throws Exception
     */
    public function findFileExtensionFromPath(string $pathToFile): string
    {
        try {
            $fileNameParts = pathinfo($pathToFile);
            $this->fileExtension = $fileNameParts[self::EXTENSION];
        } catch (Exception $exception) {
            throw new InvalidExtensionException('Invalid extension');
        }

        return $this->fileExtension;
    }
}
