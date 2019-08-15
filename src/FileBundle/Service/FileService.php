<?php

namespace Betreuteszocken\CsConfig\FileBundle\Service;

use Betreuteszocken\CsConfig\FileBundle\Model\File;
use Betreuteszocken\CsConfig\FileBundle\Model\FileInterface;
use DirectoryIterator;

/**
 * Class FileService
 *
 * @package Betreuteszocken\CsConfig\FileBundle
 */
class FileService
{
    /**
     * @param string $path
     * @param string $extension
     * @param FileInterface[] $files
     *
     * @return FileInterface[]
     */
    public function getFiles(string $path, string $extension, array $files = array()): array
    {
        $directoryIterator = new DirectoryIterator($path);

        foreach ($directoryIterator as $_iterator) {

            /** @var DirectoryIterator $_iterator */
            if ($_iterator->isDot()) {
                continue;
            }

            if ($_iterator->isDir()) {
                $files = array_merge($files, $this->getFiles($_iterator->getRealPath(), $extension));
            }

            if ($_iterator->getExtension() !== $extension) {
                continue;
            }

            $_map = new File($_iterator->getRealPath());

            array_push($files, $_map);
        }

        return $files;
    }
}