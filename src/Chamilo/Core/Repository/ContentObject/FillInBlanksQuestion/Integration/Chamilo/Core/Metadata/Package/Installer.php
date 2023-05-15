<?php
namespace Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Integration\Chamilo\Core\Metadata\Package;

use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Integration\Chamilo\Core\Metadata\PropertyProvider\ContentObjectPropertyProvider;

/**
 * @package Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Integration\Chamilo\Core\Metadata\Package
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Action\Installer
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Integration\Chamilo\Core\Metadata';

    public function getPropertyProviderTypes()
    {
        return [ContentObjectPropertyProvider::class];
    }
}