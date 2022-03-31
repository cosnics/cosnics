<?php

namespace Chamilo\Libraries\Console\Command\Vendor\PHPStan;

/**
 * Class PHPStanPackage
 *
 * @package Chamilo\Libraries\Console\Command\Vendor\PHPStan
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class PHPStanPackage
{
    /**
     * @var int
     */
    protected $level;

    /**
     * @var string
     */
    protected $packageName;

    /**
     * @var string[]
     */
    protected $paths;

    /**
     * PHPStanPackage constructor.
     *
     * @param string $packageName
     * @param int $level
     * @param string[] $paths
     */
    public function __construct(string $packageName, int $level = 0, array $paths = [])
    {
        $this->packageName = $packageName;
        $this->level = $level;

        foreach ($paths as $index => $path)
        {
            $this->paths[] = 'src' . DIRECTORY_SEPARATOR . $path;
        }
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @return string
     */
    public function getPackageName(): string
    {
        return $this->packageName;
    }

    /**
     * @return string[]
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

}