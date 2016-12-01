<?php
namespace Chamilo\Libraries\Format\Utilities;

use Assetic\Filter\CssRewriteFilter;
use Chamilo\Libraries\File\PathBuilder;

/**
 *
 * @package Chamilo\Libraries\Format\Utilities
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CssFileAsset extends FileAsset
{

    /**
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param string $stylesheetPath
     */
    public function __construct(PathBuilder $pathBuilder, $stylesheetPath)
    {
        parent::__construct($pathBuilder, $stylesheetPath, array(new CssRewriteFilter()));
    }
}