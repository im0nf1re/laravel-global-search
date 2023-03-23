<?php

namespace Imonfire\GlobalSearch\Components;

use Closure;
use Illuminate\Support\Collection;

class SearchResultCollection extends Collection
{
    public function addResults(callable $getSearchResult, Collection $results)
    {
        $results->each(function ($result) use ($getSearchResult) {
            $this->items[] = $getSearchResult($result);
        });

        return $this;
    }

    public function mergeResults(SearchResultCollection $items)
    {
        $items->each(function ($item) {
            $this->items[] = $item;
        });

        return $this;
    }
}