<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\ButtonRendererCollection;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ActionBar\Form\ButtonSearchForm;
use QuickformException;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonToolBarRenderer
{
    protected ButtonRendererCollection $buttonRendererCollection;

    protected SearchQueryConditionGenerator $searchQueryConditionGenerator;

    public function __construct(
        SearchQueryConditionGenerator $searchQueryConditionGenerator, ButtonRendererCollection $buttonRendererCollection
    )
    {
        $this->searchQueryConditionGenerator = $searchQueryConditionGenerator;
        $this->buttonRendererCollection = $buttonRendererCollection;
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function render(ButtonToolBar $buttonToolBar): string
    {
        $html = [];

        $html[] = '<div class="' . implode(' ', $this->determineClasses($buttonToolBar)) . '">';

        foreach ($buttonToolBar->getButtonCollection() as $button)
        {
            $html[] = $this->getButtonRendererCollection()->getButtonRendererForButton($button)->render($button);
        }

        if ($buttonToolBar->getSearchUrl())
        {
            $html[] = $this->getSearchForm($buttonToolBar->getSearchUrl())->render();
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    protected function determineClasses(ButtonToolBar $buttonToolBar): array
    {
        return array_merge(['btn-toolbar', 'btn-action-toolbar'], $buttonToolBar->getClasses());
    }

    public function getButtonRendererCollection(): ButtonRendererCollection
    {
        return $this->buttonRendererCollection;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable[] $properties
     */
    public function getConditions(array $properties = []): ?AndCondition
    {
        if (!is_array($properties))
        {
            $properties = [$properties];
        }

        $query = $this->getSearchQuery();

        if ($query && count($properties))
        {
            $searchQueryConditionGenerator = new SearchQueryConditionGenerator();

            return $searchQueryConditionGenerator->getSearchConditions($query, $properties);
        }

        return null;
    }

    /**
     * @throws \QuickformException
     */
    public function getSearchForm(string $searchUri = ''): ButtonSearchForm
    {
        return new ButtonSearchForm($searchUri);
    }

    public function getSearchQuery(): ?string
    {
        try
        {
            return $this->getSearchForm()->getQuery();
        }
        catch (QuickformException)
        {
            return null;
        }
    }

    public function getSearchQueryConditionGenerator(): SearchQueryConditionGenerator
    {
        return $this->searchQueryConditionGenerator;
    }
}