<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Libraries\File\PathBuilder;

/**
 *
 * @package Chamilo\Libraries\Format\Utilities
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FileAsset extends \Assetic\Asset\FileAsset
{

    /**
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param string $stylesheetPath
     * @param \Assetic\Filter\FilterInterface[] $filters
     */
    public function __construct(PathBuilder $pathBuilder, $stylesheetPath, $filters = array())
    {
        $stylesheetPath = strtr($stylesheetPath, DIRECTORY_SEPARATOR, '/');
        $basePath = strtr(realpath($pathBuilder->getBasePath()), DIRECTORY_SEPARATOR, '/');
        parent::__construct($stylesheetPath, $filters, $basePath);
    }
}