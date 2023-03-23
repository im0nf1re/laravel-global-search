<?php

namespace Imonfire\GlobalSearch\Components;

use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class GlobalSearch 
{
    public $limit = 10;

    public $modelClass;

    protected $searchFields;

    protected $relations;

    public final function search(string $value): SearchResultCollection 
    {
        
        $query = $this->makeQuery($value);
        $collection = $this->getCollection($query);

        $searchResultCollection = new SearchResultCollection();
        $searchResultCollection->addResults([$this, 'getSearchResult'], $collection);
        
        return $searchResultCollection->sortByDesc('similarity');
    } 

    // should be changeable for different search engines
    private final function makeQuery(string $value): Builder 
    {
        $selectString = "*";
        $whereString = "1=0";
        $orderByString = "greatest(0";
        foreach($this->searchFields as $field) {
            $selectString .= ", similarity($field::text, '$value') as sim_$field";
            $whereString .= " or $field::text % '$value'";
            $orderByString .= ", similarity($field::text, '$value')";
        }
        $orderByString .= ") desc";

        $query = $this->modelClass::select(DB::raw($selectString));
        if (!empty($this->relations)) {
            $query = $query->with($this->relations);
        }
        $query->whereRaw($whereString)->orderByRaw($orderByString)->limit($this->limit);

        return $query;
    }

    // should be changeable for different search engines
    private final function getCollection($query): Collection
    {
        return $query->get();
    }

    // can be overrided
    public function getSearchResult(Model $model): SearchResult 
    {
        $maxSimilarityField = $this->getMaxSimilarityField($model);

        return new SearchResult(
            $model,
            str_replace('sim_', '', $maxSimilarityField),
            $model->$maxSimilarityField,
            $this->modelClass,
        );
    }

    private final function getMaxSimilarityField(Model $model): string
    {   
        $max = 0;
        $maxSimilarityField = '';
        foreach ($this->searchFields as $searchField) {
            $simField = "sim_$searchField";
            if (floatval($model->$simField) > $max) {
                $max = floatval($model->$simField);
                $maxSimilarityField = $simField;
            }
        }

        return $maxSimilarityField;
    }
}