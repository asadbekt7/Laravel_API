<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    public function __construct(public readonly Request $request) {}
    public function apply(Builder $builder): Builder
    {
        foreach ($this->filters() as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($builder, $value);
            }
        }

        return $builder;
    }
    protected function filters(): array
    {
        return array_filter(
            $this->request->all(),
            fn($value) => filled($value)
        );
    }
}
