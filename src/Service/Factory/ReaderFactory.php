<?php

declare(strict_types=1);

namespace App\Service\Factory;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xml;

class ReaderFactory
{
    /**
     * @var object
     */
    private $reader;

    /**
     * @param $fileExtension
     *
     * @return object|null
     */
    public function getFileReader($fileExtension): ?object
    {
        switch ($fileExtension) {
            case 'csv':
                $this->reader = new Csv();
                break;
            case 'xlsx':
                $this->reader = new Xlsx();
                break;
            case 'xml':
                $this->reader = new Xml();
                break;
            // just add new case if you need to read other extensions
            default:
                return null;
        }

        return $this->reader;
    }
}
