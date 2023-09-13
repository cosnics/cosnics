<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupImportForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Group\Component
 */
class ImporterComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $form = new GroupImportForm($this->get_url());

        if ($form->validate())
        {
            $success = $form->import_groups();
            $this->redirectWithMessage(
                $this->getTranslator()->trans($success ? 'GroupXMLProcessed' : 'GroupXMLNotProcessed') . '<br />' .
                $form->get_failed_elements(), !$success, [Application::PARAM_ACTION => self::ACTION_IMPORT]
            );
        }
        else
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $form->render();
            $html[] = $this->renderXmlFormat();
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
    }

    public function renderXmlFormat(): string
    {
        $translator = $this->getTranslator();

        $html = [];
        $html[] = '<p>' . $translator->trans('XMLMustLookLike', [], Manager::CONTEXT) . ' (' .
            $translator->trans('MandatoryFields', [], Manager::CONTEXT) . ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $html[] = '&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;';
        $html[] = '&lt;groups&gt;';
        $html[] = '    &lt;item&gt;';
        $html[] = '        <b>&lt;action&gt;A/U/D&lt;/action&gt;</b>';
        $html[] = '        <b>&lt;name&gt;xxx&lt;/name&gt;</b>';
        $html[] = '        <b>&lt;code&gt;xxx&lt;/code&gt;</b>';
        $html[] = '        &lt;description&gt;xxx&lt;/description&gt;';
        $html[] = '        &lt;children&gt;';
        $html[] = '            &lt;item&gt;';
        $html[] = '                <b>&lt;action&gt;A/U/D&lt;/action&gt;</b>';
        $html[] = '                <b>&lt;name&gt;xxx&lt;/name&gt;</b>';
        $html[] = '                <b>&lt;code&gt;xxx&lt;/code&gt;</b>';
        $html[] = '                &lt;description&gt;xxx&lt;/description&gt;';
        $html[] = '                &lt;children&gt;xxx&lt;/children&gt;';
        $html[] = '            &lt;/item&gt;';
        $html[] = '        &lt;/children&gt;';
        $html[] = '    &lt;/item&gt;';
        $html[] = '&lt;/groups&gt;';
        $html[] = '</pre>';
        $html[] = '</blockquote>';
        $html[] = '<p>' . $translator->trans('Details') . '</p>';
        $html[] = '<blockquote>';
        $html[] = '<u><b>' . $translator->trans('Action', [], StringUtilities::LIBRARIES) . '</u></b>';
        $html[] = '<br />A: ' . $translator->trans('Add', [], StringUtilities::LIBRARIES);
        $html[] = '<br />U: ' . $translator->trans('Update', [], StringUtilities::LIBRARIES);
        $html[] = '<br />D: ' . $translator->trans('Delete', [], StringUtilities::LIBRARIES);
        $html[] = '</blockquote>';

        return implode(PHP_EOL, $html);
    }
}
