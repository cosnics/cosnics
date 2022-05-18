<?php
namespace Chamilo\Libraries\Architecture\Traits;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 *
 * @package Chamilo\Libraries\Architecture\Traits
 */
trait DirectoryScanner
{

    /**
     * @return string[]
     */
    protected function scanFilesInDirectory(string $directory, string $pattern, int $depth = - 1): array
    {
        $directory = new RecursiveDirectoryIterator($directory);
        $iterator = new RecursiveIteratorIterator($directory);
        $iterator->setMaxDepth($depth);

        $regex = new RegexIterator($iterator, $pattern, RegexIterator::GET_MATCH);

        $files = [];

        foreach ($regex as $matches)
        {
            $files[] = array($matches[0]);
        }

        if (!count($files))
        {
            $files = [['']];
        }

        return $files;
    }
}