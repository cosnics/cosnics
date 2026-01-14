<?php
namespace Chamilo\Core\Admin\Architecture\Domain;

use Chamilo\Core\Admin\Architecture\Interface\ActionProviderInterface;
use Chamilo\Core\Admin\UserInterface\Form\AdminSearchForm;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\Action;
use Chamilo\Libraries\Format\Tabs\ActionsTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionProviderCollection extends ArrayCollection
{
    protected ClassnameUtilities $classnameUtilities;

    protected Translator $translator;

    public function __construct(ClassnameUtilities $classnameUtilities, Translator $translator)
    {
        parent::__construct();

        $this->classnameUtilities = $classnameUtilities;
        $this->translator = $translator;
    }

    public function addActionProvider(ActionProviderInterface $actionProvider): void
    {
        $this->set($actionProvider->getContext(), $actionProvider);
    }

    public function existsForContext(string $context): bool
    {
        return $this->containsKey($context);
    }

    /**
     * @return \Chamilo\Core\Admin\Architecture\Interface\ActionProviderInterface[]
     */
    public function getActionProviders(): array
    {
        return $this->toArray();
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    /**
     * @throws \QuickformException
     */
    public function getTabsCollection(): TabsCollection
    {
        $tabsCollection = new TabsCollection();
        $index = 0;
        foreach ($this->getActionProviders() as $actionProvider)
        {
            $index ++;

            $actions = $actionProvider->getActions();

            $actionsTab = new ActionsTab(
                $this->getClassnameUtilities()->getNamespaceId($actions->getContext()),
                $this->getTranslator()->trans('TypeName', [], $actions->getContext()), new NamespaceIdentGlyph(
                    $actions->getContext(), true, false, false, IdentGlyph::SIZE_SMALL
                )
            );

            if ($actions->getSearchUrl())
            {
                $search_form = new AdminSearchForm($actions->getSearchUrl(), (string) $index);
                $actionsTab->addAction(
                    new Action(
                        $search_form->render(), null, new FontAwesomeGlyph(
                            'search', ['fa-fw', 'fa-2x'], null, 'fas'
                        )
                    )
                );
            }

            $actionsTab->setActions($actions->toArray());

            $tabsCollection->add($actionsTab);
        }

        return $tabsCollection;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}