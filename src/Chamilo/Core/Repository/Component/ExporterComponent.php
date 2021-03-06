<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportController;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportImplementation;
use Chamilo\Core\Repository\Common\Export\ExportParameters;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Table\Export\ExportTable;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;

/**
 *
 * @package repository.lib.repository_manager.component
 */
class ExporterComponent extends Manager
{

    private $export_types;

    private $export_types_cache;

    private $is_exportable;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! RightsService::getInstance()->canCopyContentObjects($this->get_user(), $this->getWorkspace()))
        {
            throw new NotAllowedException();
        }

        $content_object_ids = $this->getRequest()->get(self::PARAM_CONTENT_OBJECT_ID);
        $this->set_parameter(self::PARAM_CONTENT_OBJECT_ID, $content_object_ids);

        $category_ids = Request::get(FilterData::FILTER_CATEGORY);

        if (! is_array($content_object_ids) && ! is_null($content_object_ids))
        {
            $content_object_ids = array($content_object_ids);
        }

        foreach ($content_object_ids as $content_object_id)
        {
            $content_object = DataManager::retrieve_by_id(ContentObject::class_name(), $content_object_id);
            if (!$content_object instanceof ContentObject)
            {
                throw new \InvalidArgumentException('No valid content object selected ' . $content_object_id);
            }

            if (!RightsService::getInstance()->canCopyContentObject(
                $this->get_user(),
                $content_object,
                $this->getWorkspace()))
            {
                throw new NotAllowedException();
            }
        }

        if (! is_array($category_ids) && ! is_null($category_ids))
        {
            $category_ids = array($category_ids);
        }

        if (count($content_object_ids) == 0 && count($category_ids) == 0)
        {
            $category_ids[] = 0;
        }

        // If content objects are selected then do not use the category.
        if (count($content_object_ids) > 0)
        {
            $category_ids = array();
        }

        if (count($content_object_ids) > 0 || count($category_ids) > 0)
        {
            $type = Request::get(self::PARAM_EXPORT_TYPE);
            $export_parameters = new ExportParameters(
                $this->getWorkspace(),
                $this->get_user_id(),
                $type,
                $content_object_ids,
                $category_ids);

            if (! $type)
            {
                $table_data = $this->export_table($export_parameters->get_content_object_ids());

                $table_row = array(' ', ' ', count($export_parameters->get_content_object_ids()));

                foreach (ContentObjectExport::get_types() as $export_type)
                {
                    if ($this->is_exportable[$export_type])
                    {
                        if (count($content_object_ids))
                        {
                            $table_row[] = '<a href="' . $this->get_content_objects_exporting_url(
                                self::PARAM_CONTENT_OBJECT_ID,
                                $this->get_export_types_cache($export_type),
                                $export_type) . '">' . Theme::getInstance()->getCommonImage('Action/Export') . '</a>';
                        }
                        else
                        {
                            $table_row[] = '<a href="' . $this->get_content_objects_exporting_url(
                                FilterData::FILTER_CATEGORY,
                                $category_ids,
                                $export_type) . '">' . Theme::getInstance()->getCommonImage('Action/Export') . '</a>';
                        }
                    }
                    else
                    {
                        $table_row[] = Theme::getInstance()->getCommonImage(
                            'Action/ExportNa',
                            'png',
                            Translation::get('ExportNotAvailable'),
                            null,
                            ToolbarItem::DISPLAY_ICON);
                    }
                }

                $table_data[] = $table_row;

                $headers = array();
                $headers[] = new StaticTableColumn('');
                $headers[] = new StaticTableColumn(Translation::get('Type'));
                $headers[] = new StaticTableColumn(Translation::get('ShortCount'));

                foreach (ContentObjectExport::get_types() as $key => $export_type)
                {
                    $headers[] = new StaticTableColumn(
                        Translation::get(
                            'ImportType' . StringUtilities::getInstance()->createString($export_type)->upperCamelize()));
                }

                $export_table = new ExportTable($table_data, $headers);

                $html = array();

                $html[] = $this->render_header();
                $html[] = $export_table->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
            else
            {
                $exporter = ContentObjectExportController::factory($export_parameters);
                $exporter->download();
            }
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->display_error_message(
                Translation::get(
                    'NoObjectsSelected',
                    array('OBJECT' => Translation::get('ContentObject')),
                    Utilities::COMMON_LIBRARIES));
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    private function export_table($content_object_ids)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID),
            $content_object_ids);
        $parameters = new DataClassDistinctParameters(
            $condition,
            new DataClassProperties(
                array(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE))));

        $types = DataManager::distinct(ContentObject::class_name(), $parameters);

        $this->is_exportable = array();

        $table_data = array();

        foreach ($types as $type)
        {
            $type_namespace = ClassnameUtilities::getInstance()->getNamespaceParent($type, 3);

            $table_row = array();
            $table_row[] = Theme::getInstance()->getImage(
                'Logo/16',
                'png',
                Translation::get('TypeName', null, $type_namespace),
                null,
                ToolbarItem::DISPLAY_ICON,
                false,
                $type_namespace);
            $table_row[] = Translation::get('TypeName', null, $type_namespace);

            $conditions = array();
            $conditions[] = new InCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID),
                $content_object_ids);
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TYPE),
                new StaticConditionVariable($type));
            $condition = new AndCondition($conditions);

            $parameters = new DataClassDistinctParameters(
                $condition,
                new DataClassProperties(
                    array(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID))));
            $ids = DataManager::distinct(ContentObject::class_name(), $parameters);
            $table_row[] = count($ids);

            foreach (ContentObjectExport::get_types() as $export_type)
            {
                $export_types = ContentObjectExportImplementation::get_types_for_object($type_namespace);
                if (in_array($export_type, $export_types))
                {
                    $this->set_export_types_cache($export_type, $ids);
                    $this->is_exportable[$export_type] = true;
                    $table_row[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Status/ConfirmMini') . '"/>';
                }
                else
                {
                    $table_row[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Status/ErrorMini') . '"/>';
                }
            }

            $table_data[] = $table_row;
        }

        return $table_data;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repository_exporter');
    }

    public function get_export_types_cache($type)
    {
        return $this->export_types_cache[$type];
    }

    public function set_export_types_cache($type, $values)
    {
        $this->export_types_cache[$type] = array_merge((array) $this->export_types_cache[$type], $values);
    }
}
