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
    public function prepend(Breadcrumb $breadcrumb): void
    {
        $breadcrumbs = $this->toArray();
        array_unshift($breadcrumbs, $breadcrumb);
        $this->clear();

        foreach ($breadcrumbs as $breadcrumb) {
            $this->add($breadcrumb);
        }
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb[] $breadcrumbs
     */
    public function prependMultiple(array $breadcrumbs, bool $reverseOrder = true): void
    {
        if($reverseOrder)
        {
            $breadcrumbs = array_reverse($breadcrumbs);
        }

        foreach ($breadcrumbs as $breadcrumb) {
            $this->prepend($breadcrumb);
        }
    }
}