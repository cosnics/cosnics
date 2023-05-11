<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Table\ObjectPublicationTableRenderer;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Renderer to display a sortable table with learning object publications.
 *
 * @package application.weblcms
 * @author  Hans De Bisschop - EHB
 * @author  Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class TableContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer
{

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function as_html()
    {
        return $this->renderTable();
    }

    protected function countContentObjectPublications(): int
    {
        $tool_browser = $this->get_tool_browser();
        $type = $tool_browser->get_publication_type();

        switch ($type)
        {
            case Manager::PUBLICATION_TYPE_FROM_ME :
                return DataManager::count_my_publications(
                    $tool_browser->get_location(), $tool_browser->get_entities(), $this->get_publication_conditions(),
                    $tool_browser->get_user_id()
                );
            case Manager::PUBLICATION_TYPE_ALL :
                return DataManager::count_content_object_publications(
                    $this->get_publication_conditions()
                );
            default :
                return DataManager::count_content_object_publications_with_view_right_granted_in_category_location(
                    $tool_browser->get_location(), $tool_browser->get_entities(), $this->get_publication_conditions(),
                    $tool_browser->get_user_id()
                );
        }
    }

    public function getObjectPublicationTableRenderer(): ObjectPublicationTableRenderer
    {
        $context = $this->get_tool_browser()->get_parent()->package();
        $publicationTableRendererName = $context . '\Table\ObjectPublicationTableRenderer';

        if (class_exists($publicationTableRendererName))
        {
            return $this->getService($publicationTableRendererName);
        }
        else
        {
            return $this->getService(ObjectPublicationTableRenderer::class);
        }
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * Returns the parameters that the table needs for the url building
     *
     * @return string[]
     */
    public function get_parameters()
    {
        return $this->get_tool_browser()->get_parameters();
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->countContentObjectPublications();
        $objectPublicationTableRenderer = $this->getObjectPublicationTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $objectPublicationTableRenderer->getParameterNames(),
            $objectPublicationTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $contentObjectPublications = $this->retrieveContentObjectPublications(
            $tableParameterValues->getOffset(), $tableParameterValues->getNumberOfItemsPerPage(),
            $objectPublicationTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $objectPublicationTableRenderer->render($tableParameterValues, $contentObjectPublications);
    }

    protected function retrieveContentObjectPublications(int $offset, int $count, OrderBy $orderBy): ArrayCollection
    {
        $tool_browser = $this->get_tool_browser();

        if ($orderBy->count() == 0)
        {
            $orderBy = $tool_browser->getDefaultOrderBy();
        }

        $type = $tool_browser->get_publication_type();

        switch ($type)
        {
            case Manager::PUBLICATION_TYPE_FROM_ME :
                return DataManager::retrieve_my_publications(
                    $tool_browser->get_location(), $tool_browser->get_entities(), $this->get_publication_conditions(),
                    $orderBy, $offset, $count, $tool_browser->get_user_id()
                );
            case Manager::PUBLICATION_TYPE_ALL :
                return DataManager::retrieve_content_object_publications(
                    $this->get_publication_conditions(), $orderBy, $offset, $count
                );
            default :
                return DataManager::retrieve_content_object_publications_with_view_right_granted_in_category_location(
                    $tool_browser->get_location(), $tool_browser->get_entities(), $this->get_publication_conditions(),
                    $orderBy, $offset, $count, $tool_browser->get_user_id()
                );
        }
    }
}
