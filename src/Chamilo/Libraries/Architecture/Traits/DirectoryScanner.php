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
     * Scans files in a given directory, with a given regex pattern.
     *
     * @param string $directory
     * @param string $pattern
     * @param integer $depth - The maximum allowed depth to search recursively, -1 is infinite
     *
     * @tutorial the function returns an empty set when no files are found to make sure that the dataproviders
     *           to not crash
     * @return string[]
     */
    protected function scan_files_in_directory($directory, $pattern, $depth = - 1)
    {
        $directory = new RecursiveDirectoryIterator($directory);
        $iterator = new RecursiveIteratorIterator($directory);
        $iterator->setMaxDepth($depth);

        $regex = new RegexIterator($iterator, $pattern, RegexIterator::GET_MATCH);

        $files = array();

        foreach ($regex as $matches)
        {
            $files[] = array($matches[0]);
        }

        if (!count($files))
        {
            $files = array(array(''));
        }

        return $files;
    }
}