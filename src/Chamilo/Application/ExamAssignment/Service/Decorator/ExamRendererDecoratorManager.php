<?php
namespace Chamilo\Application\ExamAssignment\Service\Decorator;

/**
 * Class ExamRendererDecoratorManager
 * @package Chamilo\Application\ExamAssignment\Service\Decorator
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ExamRendererDecoratorManager
{
    /**
     * @var ExamRendererDecoratorInterface[]
     */
    protected $examRendererDecorators;

    /**
     * ExamRendererDecoratorManager constructor.
     */
    public function __construct()
    {
        $this->examRendererDecorators = [];
    }

    /**
     * @param ExamRendererDecoratorInterface $examRendererDecorator
     */
    public function addExamRendererDecorator(ExamRendererDecoratorInterface $examRendererDecorator)
    {
        $this->examRendererDecorators[] = $examRendererDecorator;
    }

    /**
     * @return string
     */
    public function renderBelowExamList()
    {
        $rendition = [];

        foreach($this->examRendererDecorators as $examRendererDecorator)
        {
            $rendition[] = $examRendererDecorator->renderBelowExamList();
        }

        return implode(PHP_EOL, $rendition);
    }

}
