<?php

namespace Chamilo\Libraries\Protocol\REST\Generator;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RestModelGenerator
{
    protected Environment $twig;
    protected RestModelPropertiesGenerator $propertiesGenerator;

    public function __construct(Environment $twig, RestModelPropertiesGenerator $propertiesGenerator)
    {
        $this->twig = $twig;
        $this->propertiesGenerator = $propertiesGenerator;
    }

    /**
     * @param string $modelName
     * @param string $namespace
     * @param array $record
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generateRestModelsFromRecord(string $modelName, string $namespace, array $record)
    {
        $restModels = $this->getRestModels($modelName, $namespace, $record);
        foreach($restModels as $restModel)
        {
            echo $restModel;
        }
    }

    /**
     * @param string $modelName
     * @param string $namespace
     * @param array $record
     * @param string $directory
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \Exception
     */
    public function createRestModelClasses(
        string $modelName, string $namespace, array $record, string $directory
    )
    {
        if(empty($directory))
        {
            throw new \Exception('The directory can not be empty');
        }

        $restModels = $this->getRestModels($modelName, $namespace, $record);

        foreach($restModels as $modelName => $model)
        {
            $path = realpath($directory) . DIRECTORY_SEPARATOR . $modelName . '.php';
            file_put_contents($path, $model);
        }
    }

    /**
     * @param string $modelName
     * @param string $namespace
     * @param array $record
     * @return string[]
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function getRestModels(string $modelName, string $namespace, array $record): array
    {
        $properties = $this->propertiesGenerator->generatePropertiesFromArray($record);
        return $this->generateRestModelFromProperties($modelName, $namespace, $properties);
    }

    /**
     * @param string $modelName
     * @param string $namespace
     * @param RestModelProperty[] $properties
     *
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function generateRestModelFromProperties(string $modelName, string $namespace, array $properties)
    {
        $restModels = [];
        foreach($properties as $property)
        {
            if($property->getType() == 'object')
            {
                $subModelName = ucfirst($property->getName());
                $subModels = $this->generateRestModelFromProperties($subModelName, $namespace, $property->getValue());

                $restModels = array_merge($restModels, $subModels);
            }
        }

        $restModels[$modelName] = $this->twig->render(
            'Chamilo\Libraries\Protocol\REST:Model.php.twig',
            ['NAMESPACE' => $namespace, 'PROPERTIES' => $properties, 'MODEL_NAME' => $modelName]
        );

        return $restModels;
    }
}