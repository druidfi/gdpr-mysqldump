<?php

namespace druidfi\GdprDump\ColumnTransformer;

use Symfony\Component\EventDispatcher\EventDispatcher;
use druidfi\GdprDump\ColumnTransformer\Plugins\ClearColumnTransformer;
use druidfi\GdprDump\ColumnTransformer\Plugins\FakerColumnTransformer;

abstract class ColumnTransformer
{
    const COLUMN_TRANSFORM_REQUEST = "columntransform.request";

    protected static $dispatcher;

    public static function setUp()
    {
        if (!isset(self::$dispatcher)) {
            self::$dispatcher = new EventDispatcher();

            self::$dispatcher->addListener(self::COLUMN_TRANSFORM_REQUEST,
              new FakerColumnTransformer());
            self::$dispatcher->addListener(self::COLUMN_TRANSFORM_REQUEST,
              new ClearColumnTransformer());
        }
    }

    public static function replaceValue($tableName, $columnName, $expression)
    {
        self::setUp();

        $event = new ColumnTransformEvent($tableName, $columnName, $expression);

        self::$dispatcher->dispatch($event, self::COLUMN_TRANSFORM_REQUEST);

        if ($event->isReplacementSet()) {
            return $event->getReplacementValue();
        }

        return false;
    }

    public function __invoke(ColumnTransformEvent $event)
    {
        if (in_array(($event->getExpression())['formatter'], $this->getSupportedFormatters())) {
            $event->setReplacementValue($this->getValue($event->getExpression()));
        }
    }

    abstract public function getValue($expression): string;

    abstract protected function getSupportedFormatters();
}
