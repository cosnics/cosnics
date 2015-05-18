<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Core\MetadataOld\Value\Element\Form\ElementValueEditorFormBuilder;
use Chamilo\Core\Repository\Form\TagsFormBuilder;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Filter form used to filter on metadata and on tags
 *
 * @package application\weblcms\integration\core\reporting
 * @author Tom Goethals - Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MetadataFilterForm extends FormValidator
{

    /**
     * Constructor
     *
     * @param string $url
     */
    function __construct($url)
    {
        parent :: __construct('metadata_filter', 'post', $url);
    }

    /**
     * Adds the elements to the form for the given content objects.
     * Uses the ElementValueEditorFormBuilder and
     * the TagsFormBuilder to render the metadata and tags forms.
     *
     * @param array $objects
     */
    public function add_elements(array $objects)
    {
        $elements = \Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Type\Storage\DataManager :: get_common_metadata_elements(
            $objects);

        $this->addElement('category', Translation :: get('Filter'));

        $form_builder = new ElementValueEditorFormBuilder($this);
        $form_builder->build_form($elements, null, false);

        $tags_form_builder = new TagsFormBuilder($this);
        $tags_form_builder->build_form(
            \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object_tags_for_user(
                Session :: get_user_id()));

        $button = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Search', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive'));
        $this->addElement($button);
        $this->addElement('category');
    }
}