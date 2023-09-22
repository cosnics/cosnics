<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Table\DoublesDetailsTableRenderer;
use Chamilo\Core\Repository\Table\DoublesTableRenderer;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Repository\Component
 */
class DoublesViewerComponent extends Manager
{

    private ?ContentObject $contentObject = null;

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     */
    public function run()
    {
        $id = $this->getRequest()->query->get(self::PARAM_CONTENT_OBJECT_ID);
        $trail = $this->getBreadcrumbTrail();

        $html = [];

        $html[] = $this->render_header();

        if (isset($id))
        {
            $this->contentObject = DataManager::retrieve_by_id(ContentObject::class, $id);

            $html[] = ContentObjectRenditionImplementation::launch(
                $this->contentObject, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_FULL
            );

            $html[] = '<br />';
            $html[] = $this->renderDoublesDetailsTable();

            $params = [self::PARAM_CONTENT_OBJECT_ID => $this->contentObject->getId()];
            $trail->add(new Breadcrumb($this->get_url($params), $this->contentObject->get_title()));
        }
        else
        {
            $html[] = $this->renderDoublesTable();
        }

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS]),
                $this->getTranslator()->trans('BrowserComponent', [], Manager::CONTEXT)
            )
        );
    }

    public function getDoublesCondition(): AndCondition
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->getUser()->getId())
        );
        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_STATE),
                new StaticConditionVariable(ContentObject::STATE_RECYCLED)
            )
        );

        return new AndCondition($conditions);
    }

    public function getDoublesDetailsCondition(): AndCondition
    {
        $conditions = [];
        $conditions[] = $this->getDoublesCondition();
        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID),
                new StaticConditionVariable($this->contentObject->getId())
            )
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_CONTENT_HASH),
            new StaticConditionVariable($this->contentObject->get_content_hash())
        );

        return new AndCondition($conditions);
    }

    public function getDoublesDetailsTableRenderer(): DoublesDetailsTableRenderer
    {
        return $this->getService(DoublesDetailsTableRenderer::class);
    }

    public function getDoublesTableRenderer(): DoublesTableRenderer
    {
        return $this->getService(DoublesTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function renderDoublesDetailsTable(): string
    {
        $totalNumberOfItems = \Chamilo\Core\Repository\Storage\DataManager::count_active_content_objects(
            $this->getDoublesDetailsCondition()
        );
        $doublesTableRenderer = $this->getDoublesTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $doublesTableRenderer->getParameterNames(), $doublesTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $contentObjects = \Chamilo\Core\Repository\Storage\DataManager::retrieve_active_content_objects(
            ContentObject::class, new DataClassRetrievesParameters(
                $this->getDoublesDetailsCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(), $doublesTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $doublesTableRenderer->render($tableParameterValues, $contentObjects);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderDoublesTable(): string
    {
        $totalNumberOfItems =
            \Chamilo\Core\Repository\Storage\DataManager::count_doubles_in_repository($this->getDoublesCondition());
        $doublesTableRenderer = $this->getDoublesTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $doublesTableRenderer->getParameterNames(), $doublesTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $contentObjects = \Chamilo\Core\Repository\Storage\DataManager::retrieve_doubles_in_repository(
            $this->getDoublesCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
            $tableParameterValues->getOffset(), $doublesTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $doublesTableRenderer->render($tableParameterValues, $contentObjects);
    }
}
