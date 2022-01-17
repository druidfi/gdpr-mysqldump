<?php

namespace machbarmacher\GdprDump\ColumnTransformer\Plugins;

use Faker\Factory;
use Faker\Generator;
use machbarmacher\GdprDump\ColumnTransformer\ColumnTransformer;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class FakerColumnTransformer extends ColumnTransformer
{
    private static Generator $generator;

    // These are kept for backward compatibility
    private static array $formatterTransformerMap = [
        'longText' => 'paragraph',
        'number' => 'randomNumber',
        'randomText' => 'sentence',
        'text' => 'sentence',
        'uri' => 'url',
    ];

    protected function getSupportedFormatters(): array
    {
        return array_keys(self::$formatterTransformerMap);
    }

    /**
     * @throws ReflectionException
     */
    public function __construct()
    {
        if (!isset(self::$generator)) {
            self::$generator = Factory::create();

            foreach (self::$generator->getProviders() as $provider) {
                $clazz = new ReflectionClass($provider);
                $methods = $clazz->getMethods(ReflectionMethod::IS_PUBLIC);

                foreach ($methods as $m) {
                    if (strpos($m->name, '__') === 0) continue;
                    self::$formatterTransformerMap[$m->name] = $m->name;
                }
            }
        }
    }

    public function getValue($expression): string
    {
        return self::$generator->format(self::$formatterTransformerMap[$expression['formatter']]);
    }
}
