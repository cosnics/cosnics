<?php
namespace Chamilo\Core\Admin\Service;

use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Admin\Form\AdminSearchForm;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\Action;
use Chamilo\Libraries\Format\Tabs\ActionsTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\Service
 */
class ActionProvider
{
    /**
     * @var \Chamilo\Core\Admin\Service\ActionProviderInterface[]
     */
    protected array $actionProviders;

    protected ClassnameUtilities $classnameUtilities;

    protected RegistrationConsulter $registrationConsulter;

    protected Translator $translator;

    public function __construct(
        ClassnameUtilities $classnameUtilities, Translator $translator, RegistrationConsulter $registrationConsulter
    )
    {
        $this->classnameUtilities = $classnameUtilities;
        $this->translator = $translator;
        $this->registrationConsulter = $registrationConsulter;
        $this->actionProviders = [];
    }

    public function addActionProvider(ActionProviderInterface $actionProvider): void
    {
        $this->actionProviders[$actionProvider->getContext()] = $actionProvider;
    }

    public function existsForContext(string $context): bool
    {
        return array_key_exists($context, $this->actionProviders);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \QuickformException
     */
    public function getTabsCollection(string $typeContext): TabsCollection
    {
        $registrationConsulter = $this->registrationConsulter;
        $tabsCollection = new TabsCollection();
        $index = 0;

        foreach ($this->actionProviders as $actionProvider)
        {
            $registration = $registrationConsulter->getRegistrationForContext($actionProvider->getContext());

            if ($registrationConsulter->isContextRegisteredAndActive($actionProvider->getContext()) &&
                $registration[Registration::PROPERTY_TYPE] == $typeContext)
            {
                $index ++;

                $actions = $actionProvider->getActions();

                $actionsTab = new ActionsTab(
                    $this->classnameUtilities->getNamespaceId($actions->getContext()),
                    $this->translator->trans('TypeName', [], $actions->getContext()), new NamespaceIdentGlyph(
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
        }

        return $tabsCollection;
    }
}