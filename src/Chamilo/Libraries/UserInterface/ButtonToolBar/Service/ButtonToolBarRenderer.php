<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Service;

use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonRendererRegistry;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererClassesTrait;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\SearchFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonToolBarRenderer extends AbstractButtonCollectionButtonRenderer implements ButtonRendererInterface
{
    use ButtonRendererClassesTrait;

    public function __construct(
        ButtonRendererRegistry $buttonRendererRegistry,
        protected SearchQueryConditionGenerator $searchQueryConditionGenerator,
        protected readonly FormFactoryInterface $formFactory, protected readonly Environment $twigFormEnvironment,
        protected ChamiloRequest $request
    )
    {
        parent::__construct($buttonRendererRegistry);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function render(ButtonToolBar $buttonToolBar): string
    {
        $html = [];

        $html[] = '<div';
        $html[] =
            'class="' . $this->renderClasses($buttonToolBar, ['btn-toolbar', 'mb-3'], ['justify-content-between']) .
            '" role="toolbar">';

        foreach ($buttonToolBar->getButtons() as $button) {
            $html[] = $this->buttonRendererRegistry->getButtonRenderer($button->getButtonRendererClassName())->render(
                $button
            );
        }

        if ($buttonToolBar->getSearchUrl()) {
            $data = [];

            if (!empty($this->getSearchQuery())) {
                $data[SearchFormType::PARAM_SIMPLE_SEARCH_QUERY] = $this->getSearchQuery();
            }

            $form = $this->getSearchForm($buttonToolBar->getSearchUrl(), $data);

            if (empty($this->getSearchQuery())) {
                $form->remove('cancel');
            }

            try {
                $html[] = $this->twigFormEnvironment->render('searchForm.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
            catch (LoaderError|RuntimeError|SyntaxError) {
                // In case the search form template is not found or has an error, we will ignore the search form and continue rendering the button toolbar.
            }
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getButtonClassName(): string
    {
        return ButtonToolBar::class;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable[] $properties
     */
    public function getConditions(array $properties = []): ?AndCondition
    {
        return $this->searchQueryConditionGenerator->getSearchConditions($this->getSearchQuery(), $properties);
    }

    public function getSearchForm(string $searchUri = '', array $data = []): FormInterface
    {
        return $this->formFactory->create(SearchFormType::class, $data, ['action' => $searchUri]);
    }

    public function getSearchQuery(): ?string
    {
        $form = $this->getSearchForm();
        $form->handleRequest($this->request);
        $cancelButton = $form->get('cancel');

        if ($form->isSubmitted() && $form->isValid() && $cancelButton instanceof SubmitButton &&
            !$cancelButton->isClicked()) {
            $this->request->query->set(
                SearchFormType::PARAM_SIMPLE_SEARCH_QUERY, $form->getData()[SearchFormType::PARAM_SIMPLE_SEARCH_QUERY]
            );
        }

        return $this->request->query->get(SearchFormType::PARAM_SIMPLE_SEARCH_QUERY);
    }
}