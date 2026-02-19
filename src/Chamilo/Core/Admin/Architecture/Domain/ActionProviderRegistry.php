<?php
namespace Chamilo\Core\Admin\Architecture\Domain;

use Chamilo\Core\Admin\Architecture\Interface\ActionProviderInterface;
use Chamilo\Core\Admin\UserInterface\Form\AdminSearchForm;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\NamespaceIdentGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Enum\IdentGlyphSizeEnum;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Action;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ActionsTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionProviderRegistry extends ArrayCollection
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
        foreach ($this->getActionProviders() as $actionProvider) {
            $index ++;

            $actions = $actionProvider->getActions();

            $actionsTab = new ActionsTab(
                $this->getClassnameUtilities()->getNamespaceId($actions->getContext()),
                $this->getTranslator()->trans('TypeName', [], $actions->getContext()), new NamespaceIdentGlyph(
                    $actions->getContext(), true, false, false, IdentGlyphSizeEnum::SMALL
                )
            );

            $actionsTab->setActions($actions->toArray());

            if ($actions->getSearchUrl()) {
                $searchForm = new AdminSearchForm($actions->getSearchUrl(), (string) $index);
                $actionsTab->addAction(
                    new Action(
                        $searchForm->render(), null, new FontAwesomeGlyph(
                            'search', ['fa-fw', 'fa-2x'], null, 'fas'
                        )
                    )
                );
            }

            $tabsCollection->add($actionsTab);
        }

        return $tabsCollection;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}