<?php
namespace Chamilo\Core\Repository\Common\Renderer;

use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

abstract class ContentObjectRenderer
{
    public const TYPE_GALLERY = 'GalleryTable';
    public const TYPE_SLIDESHOW = 'Slideshow';
    public const TYPE_TABLE = 'Table';

    abstract public function render(Condition $condition): string;

    /**
     * @return string[]
     */
    public static function getAvailableRendererTypes(): array
    {
        return [
            self::TYPE_TABLE,
            self::TYPE_GALLERY,
            self::TYPE_SLIDESHOW
        ];
    }

    public function get_parameters($include_search = false)
    {
        $parameters = $this->get_repository_browser()->get_parameters();

        $selected_types = TypeSelector::get_selection();

        if (is_array($selected_types) && count($selected_types))
        {
            $parameters[TypeSelector::PARAM_SELECTION] = $selected_types;
        }

        $parameters[ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY] =
            $this->get_repository_browser()->getButtonToolbarRenderer()->getSearchForm()->getQuery();

        return $parameters;
    }
}
