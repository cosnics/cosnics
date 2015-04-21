<?php
namespace Chamilo\Core\Install\Exception;

/**
 * Exception when the installation failes
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
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
     * Constructor
     * 
     * @param string $package
     * @param string $message
     */
    public function __construct($package, $message)
    {
        $this->set_package($package);
        parent :: __construct($message);
    }

    /**
     * Returns the package name
     * 
     * @return string
     */
    public function get_package()
    {
        return $this->package;
    }

    /**
     * Sets the package name
     * 
     * @param $package
     */
    public function set_package($package)
    {
        $this->package = $package;
    }
}
