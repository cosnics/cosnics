<?php
namespace Chamilo\Libraries\UserInterface\Tab\Architecture\Domain;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabInterface;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabRendererInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TabRendererRegistry extends ArrayCollection
{
    public function addTabRenderer(TabRendererInterface $tabRenderer): void
    {
        $this->set(get_class($tabRenderer), $tabRenderer);
    }

    /**
     * @template tGetTabRenderer
     * @param class-string<tGetTabRenderer> $tabRendererClassName
     *
     * @return tGetTabRenderer|TabRendererInterface
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getTabRenderer(string $tabRendererClassName): TabRendererInterface
    {
        if (!$this->hasTabRenderer($tabRendererClassName)) {
            throw new NoSuchClassException($tabRendererClassName, TabRendererInterface::class);
        }

        return $this->get($tabRendererClassName);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getTabRendererForTab(TabInterface $tab): TabRendererInterface
    {
        return $this->getTabRenderer($tab->getTabRendererClassName());
    }

    public function hasTabRenderer(string $tabRendererClass): bool
    {
        return $this->containsKey($tabRendererClass);
    }
}
