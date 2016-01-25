<?php

namespace G\Yaml2Pimple;

use Pimple\Container;

class ContainerBuilder
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function buildFromArray($conf)
    {
        foreach ($conf['parameters'] as $parameterName => $parameterValue) {
            $this->container[$parameterName] = $parameterValue;
        }

        foreach ($conf['services'] as $serviceName => $serviceConf) {
            $serviceCallback = function () use ($serviceConf) {
                $class  = new \ReflectionClass($serviceConf->getClass());
                $params = [];
                foreach ((array)$serviceConf->getArguments() as $argument) {
                    $params[] = $this->decodeArgument($argument);
                }

                return $class->newInstanceArgs($params);
            };

            if ($serviceConf->isFactory()) {
                $this->container[$serviceName] = $this->container->factory($serviceCallback);
            } else {
                $this->container[$serviceName] = $serviceCallback;
            }

        }
    }

    private function decodeArgument($value)
    {
        if (is_string($value)) {
            if (0 === strpos($value, '@')) {
                $value = $this->container[substr($value, 1)];
            } elseif (0 === strpos($value, '%')) {
                $value = $this->container[substr($value, 1, -1)];
            }
        }

        return $value;
    }
}
