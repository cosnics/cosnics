<?php
namespace Chamilo\Core\Install\Exception;

/**
 * Exception when the installation failes
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class InstallFailedException extends \Exception
{

    /**
     * The package of which the installation fails
     *
     * @var string
     */
    private $package;

    /**
     *
     * @var string
     */
    private $previousResults;

    /**
     * Constructor
     *
     * @param string $package
     * @param string $message
     */
    public function __construct($package, $previousResults, $message)
    {
        $this->package = $package;
        $this->previousResults = $previousResults;

        parent :: __construct($message);
    }

    /**
     * Returns the package name
     *
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Sets the package name
     *
     * @param $package
     */
    public function setPackage($package)
    {
        $this->package = $package;
    }

    /**
     *
     * @return string
     */
    public function getPreviousResults()
    {
        return $this->previousResults;
    }

    /**
     *
     * @param string $previousResults
     */
    public function setPreviousResults($previousResults)
    {
        $this->previousResults = $previousResults;
    }
}
