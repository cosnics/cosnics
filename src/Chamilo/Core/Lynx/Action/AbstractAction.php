<?php
namespace Chamilo\Core\Lynx\Action;

use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Libraries\Format\MessageLogger;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * Abstract class that describes an action for a package.
 * Makes use of the message logger to log messages.
 *
 * @author Sven Vanpoucke
 */
abstract class AbstractAction extends MessageLogger
{

    private Package $package;

    /**
     * @var string[] $result
     */
    private array $result;

    public function __construct(string $context)
    {
        parent::__construct();
        $this->package = Package::get($context);
    }

    public function addResult(string $result)
    {
        $this->result[] = $result;
    }

    public function getContext(): string
    {
        return $this->package->get_context();
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    /**
     * @return string|string[]
     */
    public function getResult($asString = false)
    {
        if ($asString)
        {
            return implode(PHP_EOL, $this->result);
        }
        else
        {
            return $this->result;
        }
    }

    public function hasFailed(string $title, ?InlineGlyph $image = null, ?string $errorMessage = null): bool
    {
        $this->addResult($this->processResult($title, $image, $errorMessage, self::TYPE_ERROR));

        return false;
    }

    public function processResult(
        string $title, ?InlineGlyph $image = null, ?string $finalMessage = null,
        int $finalMessageType = self::TYPE_CONFIRM
    ): string
    {
        if ($finalMessage)
        {
            $this->add_message($finalMessage, $finalMessageType);
        }

        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';

        if ($image instanceof InlineGlyph)
        {
            $html[] = $image->render();
        }

        $html[] = ' ' . $title . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $this->render();
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function wasSuccessful(string $title, ?InlineGlyph $image, ?string $message = null): bool
    {
        $this->addResult($this->processResult($title, $image, $message));

        return true;
    }
}
