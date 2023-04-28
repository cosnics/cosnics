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
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Table\ArrayCollectionTableRenderer;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package repository.lib.repository_manager.component
 */
class ExporterComponent extends Manager
{

    /**
     * @var string[][]
     */
    protected array $exportTypesCache = [];

    /**
     * @var bool[]
     */
    protected array $is_exportable;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->getWorkspaceRightsService()->canCopyContentObjects($this->getUser(), $this->getWorkspace()))
        {
            throw new NotAllowedException();
        }

        $contentObjectIdentifiers = $this->getRequest()->getFromPostOrUrl(self::PARAM_CONTENT_OBJECT_ID, []);
        $this->set_parameter(self::PARAM_CONTENT_OBJECT_ID, $contentObjectIdentifiers);

        $categoryIdentifiers = $this->getRequest()->query->get(FilterData::FILTER_CATEGORY, []);

        if (!is_array($contentObjectIdentifiers))
        {
            $contentObjectIdentifiers = [$contentObjectIdentifiers];
        }

        if (!is_array($categoryIdentifiers))
        {
            $categoryIdentifiers = [$categoryIdentifiers];
        }

        if (count($contentObjectIdentifiers) == 0 && count($categoryIdentifiers) == 0)
        {
            $categoryIdentifiers[] = 0;
        }

        // If content objects are selected then do not use the category.
        if (count($contentObjectIdentifiers) > 0)
        {
            $categoryIdentifiers = [];
        }

        if (count($contentObjectIdentifiers) > 0 || count($categoryIdentifiers) > 0)
        {
            $type = $this->getRequest()->query->get(self::PARAM_EXPORT_TYPE);
            $exportParameters = $this->getExportParameters($contentObjectIdentifiers, $categoryIdentifiers);

            if (!$type)
            {
                $html = [];

                $html[] = $this->render_header();
                $html[] = $this->renderTable($exportParameters, $contentObjectIdentifiers, $categoryIdentifiers);
                $html[] = $this->renderFooter();

                return implode(PHP_EOL, $html);
            }
            else
            {
                $exporter = ContentObjectExportController::factory($exportParameters);
                $exporter->download();
            }
        }
        else
        {
            $translator = $this->getTranslator();

            $html = [];

            $html[] = $this->render_header();
            $html[] = $this->display_error_message(
                $translator->trans(
                    'NoObjectsSelected', ['OBJECT' => $translator->trans('ContentObject', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                )
            );
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
    }

    protected function getArrayCollectionTableRenderer(): ArrayCollectionTableRenderer
    {
        return $this->getService(ArrayCollectionTableRenderer::class);
    }

    /**
     * @param string[] $contentObjectIdentifiers
     * @param string[] $categoryIdentifiers
     *
     * @throws \Exception
     */
    protected function getExportParameters(array $contentObjectIdentifiers, array $categoryIdentifiers
    ): ExportParameters
    {
        return new ExportParameters(
            $this->getWorkspace(), $this->getUser()->getId(), $this->getRequest()->query->get(self::PARAM_EXPORT_TYPE),
            $contentObjectIdentifiers, $categoryIdentifiers
        );
    }

    /**
     * @return string[]
     */
    public function getExportTypesCache(string $type): array
    {
        return $this->exportTypesCache[$type];
    }

    /**
     * @param \Chamilo\Core\Repository\Common\Export\ExportParameters $exportParameters
     * @param string[] $contentObjectIdentifiers
     * @param string[] $categoryIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @throws \Exception
     */
    protected function getTableData(
        ExportParameters $exportParameters, array $contentObjectIdentifiers, array $categoryIdentifiers
    ): ArrayCollection
    {
        $translator = $this->getTranslator();
        $tableData = $this->prepareTableData($exportParameters->get_content_object_ids());

        $tableRow = [
            ' ',
            $translator->trans('Total', [], Manager::CONTEXT),
            count($exportParameters->get_content_object_ids())
        ];

        foreach (ContentObjectExport::get_types() as $export_type)
        {
            if ($this->is_exportable[$export_type])
            {
                $glyph = new FontAwesomeGlyph('download');

                if (count($contentObjectIdentifiers))
                {
                    $tableRow[] = '<a href="' . $this->get_content_objects_exporting_url(
                            self::PARAM_CONTENT_OBJECT_ID, $this->getExportTypesCache($export_type), $export_type
                        ) . '">' . $glyph->render() . '</a>';
                }
                else
                {
                    $tableRow[] = '<a href="' . $this->get_content_objects_exporting_url(
                            FilterData::FILTER_CATEGORY, $categoryIdentifiers, $export_type
                        ) . '">' . $glyph->render() . '</a>';
                }
            }
            else
            {
                $glyph = new FontAwesomeGlyph(
                    'download', ['text-muted'], $translator->trans('ExportNotAvailable', [], Manager::CONTEXT)
                );

                $tableRow[] = $glyph->render();
            }
        }

        $tableData[] = $tableRow;

        return new ArrayCollection($tableData);
    }

    /**
     * @param string[] $contentObjectIdentifiers
     *
     * @throws \Exception
     */
    protected function prepareTableData(array $contentObjectIdentifiers): array
    {
        $translator = $this->getTranslator();

        $condition = new InCondition(
            new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID), $contentObjectIdentifiers
        );
        $parameters = new DataClassDistinctParameters(
            $condition, new RetrieveProperties(
                [new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE)]
            )
        );

        $types = DataManager::distinct(ContentObject::class, $parameters);

        $this->is_exportable = [];

        $table_data = [];

        foreach ($types as $type)
        {
            $type_namespace = ClassnameUtilities::getInstance()->getNamespaceParent($type, 3);

            $table_row = [];

            $glyph = new NamespaceIdentGlyph(
                $type_namespace, true, false, false, IdentGlyph::SIZE_MINI, ['fa-fw'],
                $translator->trans('TypeName', [], $type_namespace)
            );

            $table_row[] = $glyph->render();
            $table_row[] = $translator->trans('TypeName', [], $type_namespace);

            $conditions = [];
            $conditions[] = new InCondition(
                new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID), $contentObjectIdentifiers
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
                new StaticConditionVariable($type)
            );
            $condition = new AndCondition($conditions);

            $parameters = new DataClassDistinctParameters(
                $condition, new RetrieveProperties(
                    [new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID)]
                )
            );
            $ids = DataManager::distinct(ContentObject::class, $parameters);
            $table_row[] = count($ids);

            foreach (ContentObjectExport::get_types() as $export_type)
            {
                $export_types = ContentObjectExportImplementation::get_types_for_object($type_namespace);
                if (in_array($export_type, $export_types))
                {
                    $this->setExportTypesCache($export_type, $ids);
                    $this->is_exportable[$export_type] = true;
                    $glyph = new FontAwesomeGlyph('check-circle', ['text-success'], null, 'fas');
                }
                else
                {
                    $glyph = new FontAwesomeGlyph('minus-circle', ['text-danger'], null, 'fas');
                }

                $table_row[] = $glyph->render();
            }

            $table_data[] = $table_row;
        }

        return $table_data;
    }

    /**
     * @param \Chamilo\Core\Repository\Common\Export\ExportParameters $exportParameters
     * @param string[] $contentObjectIdentifiers
     * @param string[] $categoryIdentifiers
     *
     * @return string
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Exception
     */
    protected function renderTable(
        ExportParameters $exportParameters, array $contentObjectIdentifiers, array $categoryIdentifiers
    ): string
    {
        $translator = $this->getTranslator();
        $tableData = $this->getTableData($exportParameters, $contentObjectIdentifiers, $categoryIdentifiers);

        $headers = [];

        $headers[] = new StaticTableColumn('Icon', '');
        $headers[] = new StaticTableColumn('Type', $translator->trans('Type', [], Manager::CONTEXT));
        $headers[] = new StaticTableColumn('ShortCount', $translator->trans('ShortCount', [], Manager::CONTEXT));

        foreach (ContentObjectExport::get_types() as $export_type)
        {
            $headerIdentifier =
                'ImportType' . StringUtilities::getInstance()->createString($export_type)->upperCamelize();

            $headers[] = new StaticTableColumn(
                $headerIdentifier, $translator->trans(
                $headerIdentifier, [], Manager::CONTEXT
            )
            );
        }

        return $this->getArrayCollectionTableRenderer()->render($headers, $tableData, 0, SORT_ASC, 20, 'exporTable');
    }

    /**
     * @param string $type
     * @param string[] $values
     */
    public function setExportTypesCache(string $type, array $values)
    {
        if (!array_key_exists($type, $this->exportTypesCache))
        {
            $this->exportTypesCache[$type] = [];
        }

        $this->exportTypesCache[$type] = array_merge($this->exportTypesCache[$type], $values);
    }
}
