<?php
namespace Chamilo\Core\Repository\Common;

use Chamilo\Core\Repository\Common\Difference\InlineDifferenceRenderer;
use Chamilo\Core\Repository\Common\Difference\SideBySide;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Common
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectDifferenceRenderer
{
    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * ContentObjectDifferenceRenderer constructor.
     *
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Core\Repository\Common\ContentObjectDifference $contentObjectDifference
     *
     * @return string
     */
    public function render(ContentObjectDifference $contentObjectDifference)
    {
        $renderer = new InlineDifferenceRenderer();
        $translator = $this->getTranslator();

        $html = [];

        $display = ContentObjectRenditionImplementation::factory(
            $contentObjectDifference->getContentObject(), ContentObjectRendition::FORMAT_HTML,
            ContentObjectRendition::VIEW_FULL, $this
        );

        $html[] = $display->render();

        foreach ($contentObjectDifference->compare() as $propertyName => $difference)
        {
            $renderedDifference = $difference->Render($renderer);

            if ($renderedDifference)
            {
                $propertyVariable =
                    StringUtilities::getInstance()->createString($propertyName)->upperCamelize()->__toString();
                $propertyLabel = $translator->trans(
                    $propertyVariable, [], $contentObjectDifference->getContentObject()->package()
                );

                $html[] = '<div class="panel panel-default">';
                $html[] = '<div class="panel-heading">' . $translator->trans(
                        'DifferenceProperty', array('{PROPERTY}' => $propertyLabel), 'Chamilo\Core\Repository'
                    ) . '</div>';
                $html[] = $renderedDifference;
                $html[] = '</div>';
            }
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    public function get_content_object_display_attachment_url($attachment)
    {
        return '';
    }
}
