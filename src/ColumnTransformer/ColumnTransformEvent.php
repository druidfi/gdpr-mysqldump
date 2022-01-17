<?php

namespace machbarmacher\GdprDump\ColumnTransformer;

use Symfony\Contracts\EventDispatcher\Event;

class ColumnTransformEvent extends Event
{
    protected string $table;
    protected string $column;
    protected array $expression;
    protected bool $isReplacementSet = false;
    protected string $replacementValue;

    /**
     * ColumnTransformEvent constructor.
     */
    public function __construct(string $table, string $column, array $expression)
    {
        $this->table = $table;
        $this->column = $column;
        $this->expression = $expression;
    }

    public function setReplacementValue(string $value)
    {
        $this->isReplacementSet = true;
        $this->replacementValue = $value;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getExpression(): array
    {
        return $this->expression;
    }

    public function isReplacementSet(): bool
    {
        return $this->isReplacementSet;
    }

    public function getReplacementValue(): string
    {
        return $this->replacementValue;
    }
}
