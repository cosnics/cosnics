<?php
namespace Chamilo\Core\Admin\Architecture\Domain;

use Chamilo\Core\Admin\Architecture\Interface\ActionProviderInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\SearchFormType;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\NamespaceIdentGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Enum\IdentGlyphSizeEnum;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Action;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ActionsTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

/**
 * @package Chamilo\Core\Admin\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionProviderRegistry
{
    public function __construct(
        protected Translator $translator, protected StringUtilities $stringUtilities,
        protected readonly FormFactoryInterface $formFactory, protected readonly Environment $twigFormEnvironment,
        protected ArrayCollection $actionProviders = new ArrayCollection()
    )
    {
    }

    public function addActionProvider(ActionProviderInterface $actionProvider): void
    {
        $this->actionProviders->set($actionProvider->getContext(), $actionProvider);
    }

    public function getSearchForm(string $searchUri = '', array $data = []): FormInterface
    {
        $form = $this->formFactory->create(SearchFormType::class, $data, ['action' => $searchUri]);
        $form->remove('cancel');

        return $form;
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function getTabsCollection(): TabsCollection
    {
        $tabsCollection = new TabsCollection();

        foreach ($this->actionProviders as $actionProvider) {
            $actions = $actionProvider->getActions();

            $actionsTab = new ActionsTab(
                $this->stringUtilities->createString($actions->getContext())->md5()->toString(),
                $this->translator->trans('TypeName', [], $actions->getContext()), new NamespaceIdentGlyph(
                    $actions->getContext(), true, false, false, IdentGlyphSizeEnum::SMALL
                )
            );

            $actionsTab->setActions($actions->toArray());

            if ($actions->getSearchUrl()) {
                $form = $this->getSearchForm($actions->getSearchUrl());
                $formHtml = $this->twigFormEnvironment->render('searchForm.html.twig', [
                    'form' => $form->createView(),
                ]);

                $actionsTab->addAction(
                    new Action(
                        $formHtml, null, new FontAwesomeGlyph(
                            'search', ['fa-fw', 'fa-2x'], null, 'fas'
                        )
                    )
                );
            }

            $tabsCollection->add($actionsTab);
        }

        return $tabsCollection;
    }
}