<?php
namespace Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;

/**
 * @package Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 *
 * @psalm-template TKey of array-key
 * @template-implements Collection<TKey,\Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb>
 * @template-implements Selectable<TKey,\Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb>
 * @psalm-consistent-constructor
 */
class BreadcrumbTrail extends ArrayCollection
{
}