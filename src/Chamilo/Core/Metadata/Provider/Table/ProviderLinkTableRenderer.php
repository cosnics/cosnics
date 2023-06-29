<?php
namespace Chamilo\Core\Metadata\Provider\Table;

use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Provider\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Metadata\Provider\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ProviderLinkTableRenderer extends DataClassListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_ELEMENT = 'element';
    public const PROPERTY_NAME = 'name';
    public const PROPERTY_SCHEMA = 'schema';
    public const PROPERTY_TYPE = 'type';

    public const TABLE_IDENTIFIER = Manager::PARAM_PROVIDER_LINK_ID;

    protected ClassnameUtilities $classnameUtilities;

    protected DataClassEntityFactory $dataClassEntityFactory;

    protected StringUtilities $stringUtilities;

    public function __construct(
        StringUtilities $stringUtilities, ClassnameUtilities $classnameUtilities,
        DataClassEntityFactory $dataClassEntityFactory, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->stringUtilities = $stringUtilities;
        $this->classnameUtilities = $classnameUtilities;
        $this->dataClassEntityFactory = $dataClassEntityFactory;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    public function getDataClassEntityFactory(): DataClassEntityFactory
    {
        return $this->dataClassEntityFactory;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromParameters(
                    [Application::PARAM_CONTEXT => Manager::CONTEXT, Manager::PARAM_ACTION => Manager::ACTION_DELETE]
                ), $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }

    protected function initializeColumns(): void
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_TYPE, $translator->trans('ProviderLinkType', [], Manager::CONTEXT))
        );
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_NAME, $translator->trans('ProviderLinkName', [], Manager::CONTEXT))
        );
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_SCHEMA, $translator->trans('ProviderLinkSchema', [], Manager::CONTEXT))
        );
        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_ELEMENT, $translator->trans('ProviderLinkElement', [], Manager::CONTEXT)
            )
        );
    }

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\ProviderLink $providerLink
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $providerLink): string
    {
        $translator = $this->getTranslator();
        $stringUtilities = $this->getStringUtilities();
        $classnameUtilities = $this->getClassnameUtilities();

        switch ($column->get_name())
        {
            case self::PROPERTY_TYPE :
                $entity = $this->getDataClassEntityFactory()->getEntityFromDataClassName(
                    $providerLink->get_entity_type()
                );

                return $entity->getType();
            case self::PROPERTY_NAME :
                $propertyNamespace = $classnameUtilities->getNamespaceFromClassname(
                    $providerLink->getProviderRegistration()->get_provider_class()
                );
                $propertyName = $providerLink->getProviderRegistration()->get_property_name();
                $propertyNameTranslationVariable =
                    $stringUtilities->createString($propertyName)->upperCamelize()->toString();

                return $translator->trans($propertyNameTranslationVariable, [], $propertyNamespace);
            case self::PROPERTY_SCHEMA :
                return $providerLink->getElement()->getSchema()->getTranslationByIsocode(
                    $translator->getLocale()
                );
            case self::PROPERTY_ELEMENT :
                return $providerLink->getElement()->get_display_name();
        }

        return parent::renderCell($column, $resultPosition, $providerLink);
    }

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\ProviderLink $providerLink
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $providerLink): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_PROVIDER_LINK_ID => $providerLink->getId()
                    ]
                ), ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->render();
    }
}
