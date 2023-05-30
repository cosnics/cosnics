<?php
namespace Chamilo\Core\Repository\Selector\Renderer;

use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Selector\TypeSelectorRenderer;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Render content object type selection tabs based on their category
 *
 * @author Hans De Bisschop
 */
class FormTypeSelectorRenderer extends TypeSelectorRenderer
{

    /**
     * @var \libraries\format\FormValidator
     */
    private $form;

    /**
     * @var string
     */
    private $postback_url;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $parent
     * @param string[] $content_object_types
     * @param string[][] $additional_links
     * @param bool $use_general_statistics
     * @param string $postback_url
     */
    public function __construct(Application $parent, TypeSelector $type_selector, $postback_url = null)
    {
        parent::__construct($parent, $type_selector);

        $this->postback_url = $postback_url ?: $parent->get_url();
        $this->form = new FormValidator(
            ClassnameUtilities::getInstance()->getClassNameFromNamespace(__CLASS__, true),
            FormValidator::FORM_METHOD_POST, $this->postback_url
        );
    }

    /**
     * Render the content object type selection form
     *
     * @return string
     */
    public function render()
    {
        $form = $this->get_form();

        $select = $form->addElement(
            'select', TypeSelector::PARAM_SELECTION, Translation::get('CreateANew'), [], ['class' => 'postback']
        );

        foreach ($this->get_type_selector()->as_tree() as $key => $type)
        {
            $attributes = !is_integer($key) ? ['disabled'] : [];
            $select->addOption($type, $key, $attributes);
        }

        $form->addElement(
            'style_button', 'submit', Translation::get('Select'), null, null, new FontAwesomeGlyph('hand-up')
        );

        $html = [];

        $renderer = clone $form->defaultRenderer();
        $renderer->setElementTemplate('{label}&nbsp;&nbsp;{element}&nbsp;');
        $form->accept($renderer);

        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        /**
         * @var \Chamilo\Libraries\Format\Utilities\ResourceManager $resourceManager
         */
        $resourceManager = $container->get(ResourceManager::class);

        /**
         * @var \Chamilo\Libraries\File\WebPathBuilder $webPathBuilder
         */
        $webPathBuilder = $container->get(WebPathBuilder::class);

        $html = [];
        $html[] = '<div style="margin-bottom: 20px;">';
        $html[] = $renderer->toHtml();
        $html[] = $resourceManager->getResourceHtml(
            $webPathBuilder->getJavascriptPath(StringUtilities::LIBRARIES) . 'Postback.js'
        );
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \libraries\format\FormValidator
     */
    public function get_form()
    {
        return $this->form;
    }

    /**
     * @return string
     */
    public function get_postback_url()
    {
        return $this->postback_url;
    }
}
