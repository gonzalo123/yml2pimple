<?php

namespace G\Yaml2Pimple;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Yaml\Parser as YamlParser;

class YamlFileLoader extends Loader
{
    private $locator;
    private $yamlParser;
    private $container;
    private $currentDir;
    private $builder;

    public function __construct(ContainerBuilder $builder, FileLocatorInterface $fileLocator)
    {
        $this->locator = $fileLocator;
        $this->builder = $builder;
    }

    public function load($file, $type=null)
    {
        $path = $this->locator->locate($file);

        $content = $this->loadFile($path);

        if (null === $content) {
            return;
        }

        $this->parseImports($content, $path);

        if (isset($content['parameters'])) {
            foreach ($content['parameters'] as $key => $value) {
                $this->container['parameters'][$key] = $this->resolveServices($value);
            }
        }

        $this->parseDefinitions($content, $file);

        $this->builder->buildFromArray($this->container);
    }

    public function supports($resource, $type=null)
    {
        return is_string($resource) && 'yml' === pathinfo($resource, PATHINFO_EXTENSION);
    }

    protected function loadFile($file)
    {
        if (!stream_is_local($file)) {
            throw new \InvalidArgumentException(sprintf('This is not a local file "%s".', $file));
        }

        if (!file_exists($file)) {
            throw new \InvalidArgumentException(sprintf('The service file "%s" is not valid.', $file));
        }

        if (null === $this->yamlParser) {
            $this->yamlParser = new YamlParser();
        }

        return $this->validate($this->yamlParser->parse(file_get_contents($file)), $file);
    }

    private function validate($content, $file)
    {
        if (null === $content) {
            return $content;
        }

        if (!is_array($content)) {
            throw new \InvalidArgumentException(sprintf('The service file "%s" is not valid.', $file));
        }

        foreach (array_keys($content) as $namespace) {
            if (in_array($namespace, array('ingredients', 'basePrice'))) {
                continue;
            }
        }

        return $content;
    }

    private function parseImports($content, $file)
    {
        if (!isset($content['imports'])) {
            return;
        }

        foreach ($content['imports'] as $import) {
            $this->setCurrentDir(dirname($file));
            $this->import($import['resource'], null, isset($import['ignore_errors']) ? (bool)$import['ignore_errors'] : false, $file);
        }
    }

    public function setCurrentDir($dir)
    {
        $this->currentDir = $dir;
    }


    private function parseDefinitions($content, $file)
    {
        if (!isset($content['services'])) {
            return;
        }

        foreach ($content['services'] as $id => $service) {
            $this->parseDefinition($id, $service, $file);
        }
    }

    private function parseDefinition($id, $service)
    {
        $definition = new Definition();

        if (isset($service['class'])) {
            $definition->setClass($service['class']);
        }

        if (isset($service['arguments'])) {
            $definition->setArguments($this->resolveServices($service['arguments']));
        }

        if (isset($service['factory'])) {
            $definition->setFactory($service['factory']);
        }

        $this->container['services'][$id] = $definition;
    }

    private function resolveServices($value)
    {
        return $value;
    }
}
