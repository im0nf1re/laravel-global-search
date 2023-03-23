<?php

namespace Imonfire\GlobalSearch\Components;

use Illuminate\Database\Eloquent\Model;

class SearchResult
{
    public Model $model;
    public string $type;
    public string $matchField;
    public string $similarity;

    public function __construct(Model $model, string $matchField, string $similarity, string $type) 
    {
        $this->model = $model;
        $this->matchField = $matchField;
        $this->similarity = $similarity;
        $this->type = $type;
    }
}