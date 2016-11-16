<?php
namespace Chamilo\Libraries\Format\Utilities;

use Assetic\Filter\CssRewriteFilter;
use Chamilo\Libraries\File\Path;

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
     * @param Path $pathUtilities
     * @param string $stylesheetPath
     */
    public function __construct(Path $pathUtilities, $stylesheetPath)
    {
        parent::__construct($pathUtilities, $stylesheetPath, array(new CssRewriteFilter()));
    }
}