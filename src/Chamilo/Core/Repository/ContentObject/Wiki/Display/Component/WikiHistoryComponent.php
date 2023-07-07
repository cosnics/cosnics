<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\Common\ContentObjectDifferenceRenderer;
use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Table\VersionTableRenderer;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\Wiki\Display\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WikiHistoryComponent extends Manager
{

    private $complex_wiki_page_id;

    public function run()
    {
        if (!$this->is_allowed(VIEW_RIGHT))
        {
            throw new NotAllowedException();
        }

        $this->complex_wiki_page_id = Request::get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        if ($this->complex_wiki_page_id)
        {
            $complex_wiki_page = DataManager::retrieve_by_id(
                ComplexContentObjectItem::class, $this->complex_wiki_page_id
            );

            $compareObjectIdentifiers =
                $this->getRequest()->getFromRequestOrQuery(\Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID);

            $html = [];

            $html[] = $this->render_header($complex_wiki_page);

            $this->wiki_page = $complex_wiki_page->get_ref_object();

            if ($compareObjectIdentifiers)
            {
                if (count($compareObjectIdentifiers) < 2)
                {
                    $this->redirectWithMessage(Translation::get('TooFewItems'), true);
                }

                $compareVersionIdentifier = $compareObjectIdentifiers[0];
                $compareObjectIdentifier = $compareObjectIdentifiers[1];

                $compareObject = DataManager::retrieve_by_id(
                    ContentObject::class, $compareObjectIdentifier
                );

                $html[] = '<h3 id="page-title">' . Translation::get('ComparerComponent') . ': ' .
                    $this->wiki_page->get_title() . '</h3>';

                $html[] = $this->getContentObjectDifferenceRenderer()->render(
                    $compareObject->get_difference($compareVersionIdentifier)
                );
            }
            else
            {
                $totalNumberOfItems = DataManager::count_content_objects(
                    ContentObject::class, new DataClassCountParameters($this->getVersionTableCondition())
                );

                $versionTableRenderer = $this->getVersionTableRenderer();

                $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
                    $versionTableRenderer->getParameterNames(), $versionTableRenderer->getDefaultParameterValues(),
                    $totalNumberOfItems
                );

                $contentObjects = DataManager::retrieve_content_objects(
                    ContentObject::class, new DataClassRetrievesParameters(
                        $this->getVersionTableCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
                        $tableParameterValues->getOffset(),
                        $versionTableRenderer->determineOrderBy($tableParameterValues)
                    )
                );

                $html[] = '<h3 id="page-title">' . Translation::get('RevisionHistory') . ': ' .
                    $this->wiki_page->get_title() . '</h3>';

                $html[] = $versionTableRenderer->render($tableParameterValues, $contentObjects);
                $html[] = ResourceManager::getInstance()->getResourceHtml(
                    $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Repository') . 'VersionTable.js'
                );
            }

            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            $this->redirectWithMessage(null, false, [self::PARAM_ACTION => self::ACTION_VIEW_WIKI]);
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail = $this->get_breadcrumbtrail();
    }

    public function count_content_object_versions_resultset($condition = null)
    {
        return DataManager::count_content_objects(
            ContentObject::class, $condition
        );
    }

    /**
     * @return \Chamilo\Core\Repository\Common\ContentObjectDifferenceRenderer
     */
    protected function getContentObjectDifferenceRenderer()
    {
        return $this->getService(ContentObjectDifferenceRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getVersionTableCondition(): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OBJECT_NUMBER),
            new StaticConditionVariable($this->wiki_page->get_object_number())
        );
    }

    public function getVersionTableRenderer(): VersionTableRenderer
    {
        return $this->getService(VersionTableRenderer::class);
    }

    public function get_content_object_deletion_url($content_object, $type = null)
    {
        $delete_allowed = DataManager::content_object_deletion_allowed(
            $content_object, $type
        );

        if (!$delete_allowed)
        {
            return null;
        }

        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_VERSION_DELETE,
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_wiki_page_id,
                self::PARAM_WIKI_VERSION_ID => $content_object->get_id()
            ]
        );
    }

    public function get_content_object_revert_url($content_object)
    {
        $revert_allowed = DataManager::content_object_revert_allowed($content_object);

        if (!$revert_allowed)
        {
            return null;
        }

        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_VERSION_REVERT,
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_wiki_page_id,
                self::PARAM_WIKI_VERSION_ID => $content_object->get_id()
            ]
        );
    }

    public function get_content_object_viewing_url($content_object)
    {
        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_VIEW_WIKI_PAGE,
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_wiki_page_id,
                self::PARAM_WIKI_VERSION_ID => $content_object->get_id()
            ]
        );
    }

    public function retrieve_content_object_versions_resultset(
        $condition = null, $order_by = null, $offset = 0, $max_objects = - 1
    )
    {
        return DataManager::retrieve_content_objects(
            ContentObject::class, $condition
        );
    }
}
