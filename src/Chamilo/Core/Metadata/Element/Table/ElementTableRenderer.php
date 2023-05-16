<?php
namespace Chamilo\Core\Metadata\Element\Table;

use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
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
 * @package Chamilo\Core\Metadata\Element\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ElementTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const COLUMN_PREFIX = 'prefix';
    public const COLUMN_VALUE_FREE = 'value_free';
    public const COLUMN_VALUE_VOCABULARY_PREDEFINED = 'value_vocabulary_predefined';
    public const COLUMN_VALUE_VOCABULARY_USER = 'value_vocabulary_user';

    public const TABLE_IDENTIFIER = Manager::PARAM_ELEMENT_ID;

    protected StringUtilities $stringUtilities;

    public function __construct(
        StringUtilities $stringUtilities, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->stringUtilities = $stringUtilities;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
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

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();
        $stringUtilities = $this->getStringUtilities();

        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_PREFIX, $translator->trans(
                $stringUtilities->createString(self::COLUMN_PREFIX)->upperCamelize()->toString(), [], Manager::CONTEXT
            )
            )
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(
                Element::class, Element::PROPERTY_NAME, $translator->trans(
                $stringUtilities->createString(Element::PROPERTY_NAME)->upperCamelize()->toString(), [],
                Manager::CONTEXT
            )
            )
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(
                Element::class, Element::PROPERTY_DISPLAY_NAME, $translator->trans(
                $stringUtilities->createString(Element::PROPERTY_DISPLAY_NAME)->upperCamelize()->toString(), [],
                Manager::CONTEXT
            ), false
            )
        );

        $glyph = new FontAwesomeGlyph(
            'pen-nib', [], $translator->trans('FreeValues', [], Manager::CONTEXT), 'fas'
        );

        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_VALUE_FREE, $glyph->render()
            )
        );

        $glyph = new FontAwesomeGlyph(
            'globe', [], $translator->trans('PredefinedValues', [], Manager::CONTEXT), 'fas'
        );

        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_VALUE_VOCABULARY_PREDEFINED, $glyph->render()
            )
        );

        $glyph = new FontAwesomeGlyph(
            'users', [], $translator->trans('UserValues', [], Manager::CONTEXT), 'fas'
        );

        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_VALUE_VOCABULARY_USER, $glyph->render()
            )
        );
    }

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $element): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        switch ($column->get_name())
        {
            case self::COLUMN_PREFIX :
                return $element->get_namespace();
            case self::COLUMN_VALUE_VOCABULARY_PREDEFINED :
                if ($element->isVocabularyPredefined())
                {
                    $link = $urlGenerator->fromParameters(
                        [
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Manager::PARAM_ACTION => Manager::ACTION_VOCABULARY,
                            \Chamilo\Core\Metadata\Vocabulary\Manager::PARAM_ACTION => \Chamilo\Core\Metadata\Vocabulary\Manager::ACTION_BROWSE,
                            Manager::PARAM_ELEMENT_ID => $element->getId()
                        ]
                    );
                }
                else
                {
                    $link = null;
                }

                $extraClasses = $element->isVocabularyPredefined() ? [] : ['text-muted'];
                $glyph = new FontAwesomeGlyph(
                    'globe', $extraClasses, $translator->trans('PredefinedValues', [], Manager::CONTEXT), 'fas'
                );

                if ($link)
                {
                    return '<a href="' . $link . '">' . $glyph->render() . '</a>';
                }
                else
                {
                    return $glyph->render();
                }
            case self::COLUMN_VALUE_VOCABULARY_USER :
                if ($element->isVocabularyPredefined())
                {
                    $link = $urlGenerator->fromParameters([
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Manager::PARAM_ACTION => Manager::ACTION_VOCABULARY,
                        \Chamilo\Core\Metadata\Vocabulary\Manager::PARAM_ACTION => \Chamilo\Core\Metadata\Vocabulary\Manager::ACTION_USER,
                        Manager::PARAM_ELEMENT_ID => $element->getId()
                    ]);
                }
                else
                {
                    $link = null;
                }

                $extraClasses = $element->isVocabularyUserDefined() ? [] : ['text-muted'];
                $glyph = new FontAwesomeGlyph(
                    'users', $extraClasses, $translator->trans('UserValues', [], Manager::CONTEXT), 'fas'
                );

                if ($link)
                {
                    return '<a href="' . $link . '">' . $glyph->render() . '</a>';
                }
                else
                {
                    return $glyph->render();
                }
        }

        return parent::renderCell($column, $resultPosition, $element);
    }

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $element): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        if ($element->is_fixed())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('EditNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('pencil-alt', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('DeleteNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('times', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $urlGenerator->fromParameters(
                        [
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
                            Manager::PARAM_ELEMENT_ID => $element->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $urlGenerator->fromParameters(
                        [
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                            Manager::PARAM_ELEMENT_ID => $element->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        $limit = DataManager::get_display_order_total_for_schema($element->get_schema_id());

        // show move up button
        if ($element->get_display_order() != 1 && $limit != 1)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveUp', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-up'),
                    $urlGenerator->fromParameters(
                        [
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Manager::PARAM_ACTION => Manager::ACTION_MOVE,
                            Manager::PARAM_ELEMENT_ID => $element->getId(),
                            Manager::PARAM_MOVE => - 1
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveUpNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-up', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        // show move down button
        if ($element->get_display_order() < $limit)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveDown', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-down'),
                    $urlGenerator->fromParameters(
                        [
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Manager::PARAM_ACTION => Manager::ACTION_MOVE,
                            Manager::PARAM_MOVE => 1,
                            Manager::PARAM_ELEMENT_ID => $element->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveDownNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-down', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
