<?php

namespace machbarmacher\GdprDump\ColumnTransformer;

use Symfony\Component\EventDispatcher\Event;

class ColumnTransformEvent extends Event
{
    protected $table;
    protected $column;
    protected $expression;
    protected $isReplacementSet = false;
    protected $replacementValue;

    /**
     * ColumnTransformEvent constructor.
     */
    public function __construct($table, $column, $expression)
    {
        $this->table = $table;
        $this->column = $column;
        $this->expression = $expression;
    }

    public function setReplacementValue($value)
    {
        $this->isReplacementSet = true;
        $this->replacementValue = $value;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function getExpression()
    {
        return $this->expression;
    }

    public function isReplacementSet()
    {
        return $this->isReplacementSet;
    }

    public function getReplacementValue()
    {
        return $this->replacementValue;
    }
}
