<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetRenderer extends \Chamilo\Core\Repository\Publication\Service\PublicationTargetRenderer
{
    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     * @param string $publicationContext
     *
     * @throws \Exception
     */
    public function addPublicationAttributes(FormValidator $form, string $publicationContext)
    {

        $applicationContext = \Chamilo\Application\Weblcms\Manager::context();

        $splitterHtml = array();

        $splitterHtml[] = '<div class="form_splitter" >';
        $splitterHtml[] =
            '<span class="category">' . $this->getTranslator()->trans('PublicationDetails', [], $applicationContext) .
            '</span>';
        $splitterHtml[] = '<div style="clear: both;"></div>';
        $splitterHtml[] = '</div>';

        $form->addElement('html', implode(PHP_EOL, $splitterHtml));

        $form->addElement(
            'checkbox',
            Manager::WIZARD_TARGET . '[' . $publicationContext . '][' . ContentObjectPublication::PROPERTY_HIDDEN . ']',
            $this->getTranslator()->trans('Hidden', [], $applicationContext)
        );
        $form->add_forever_or_timewindow(
            'PublicationPeriod', Manager::WIZARD_TARGET . '[' . $publicationContext . ']', true
        );
        $form->addElement(
            'checkbox', Manager::WIZARD_TARGET . '[' . $publicationContext . '][' .
            ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION . ']',
            $this->getTranslator()->trans('CourseAdminCollaborate', [], $applicationContext)
        );

        $form->addElement(
            'checkbox',
            Manager::WIZARD_TARGET . '[' . $publicationContext . '][' . ContentObjectPublication::PROPERTY_EMAIL_SENT .
            ']', $this->getTranslator()->trans('SendByEMail', [], $applicationContext)
        );

        $defaults[Manager::WIZARD_TARGET][$publicationContext]['forever'] = 1;

        $defaults[Manager::WIZARD_TARGET][$publicationContext][ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION] =
            1;

        $form->setDefaults($defaults);
    }
}