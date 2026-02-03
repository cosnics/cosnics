<?php
namespace Chamilo\Libraries\UserInterface\Tab\Architecture\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 *
 * @psalm-template TKey of array-key
 * @psalm-template T
 * @template-implements Collection<TKey,T>
 * @template-implements Selectable<TKey,T>
 * @psalm-consistent-constructor
 */
class TabsCollection extends ArrayCollection
{
    public function hasMultipleTabs(): bool
    {
        return $this->count() > 1;
    }

    public function hasOnlyOneTab(): bool
    {
        return $this->count() == 1;
    }

    public function isValidIdentifier(string $tabIdentifierToValidate): bool
    {
        foreach ($this->toArray() as $tab) {
            if ($tab->getIdentifier() == $tabIdentifierToValidate) {
                return true;
            }
        }

        return false;
    }

    public function sortByLabel(): static
    {
        $tabs = $this->toArray();

        usort(
            $tabs, function (AbstractTab $tabLeft, AbstractTab $tabRight) {
            return strcasecmp($tabLeft->getLabel(), $tabRight->getLabel());
        }
        );

        $this->clear();

        foreach ($tabs as $tab) {
            $this->add($tab);
        }

        return $this;
    }
}