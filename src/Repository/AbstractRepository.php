<?php

namespace Dochne\Shopping\Repository;

use ReflectionClass;

class AbstractRepository
{
    private static $hydrationCache = [];

    protected function hydrateMany(array $data)
    {
        return array_map(function($row) {
            return $this->hydrate($row);
        }, $data);
    }

    protected function hydrate(array $row)
    {
        if (!isset(self::$hydrationCache[static::class])) {
            $class = static::class;
            $exploded = explode("\\", $class);
            $name = str_replace("Repository", "", array_pop($exploded));
            $className = "Dochne\\Shopping\\Entity\\" . $name;
            $reflectionClass = new ReflectionClass($className);
            self::$hydrationCache[static::class] = [
                "class" => $reflectionClass,
                "properties" => []
            ];
            foreach ($reflectionClass->getProperties() as $property) {
                $property->setAccessible(true);
                self::$hydrationCache[static::class]["properties"][$property->getName()] = $property;
            }
        }
        /**
         * @var ReflectionClass $reflection
         */
        $reflection = self::$hydrationCache[static::class]["class"];
        $instance = $reflection->newInstanceWithoutConstructor();
        /**
         * @var \ReflectionProperty $property
         */
        foreach (self::$hydrationCache[static::class]["properties"] as $name => $property) {
            $property->setValue($instance, $row[$name]);
        }
        return $instance;
    }
}