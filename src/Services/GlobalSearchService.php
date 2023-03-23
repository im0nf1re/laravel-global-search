<?php

namespace Imonfire\GlobalSearch\Services;

use Illuminate\Http\Request;
use Imonfire\GlobalSearch\Components\SearchResultCollection;

class GlobalSearchService
{
    private $searchables = [];

    private $value;
    private $page;
    private $perPage;

    public function __construct(Request $request)
    {
        $this->value = $request['search'] ?? '';
        $this->page = $request['page'] ?? 1;
        $this->perPage = $request['per-page'] ?? 10;
    }

    public function search(): SearchResultCollection
    {      
        $searchResultCollection = new SearchResultCollection();

        foreach ($this->searchables as $searchable) {
            $searchable = new $searchable();
            $searchResultCollection->mergeResults($searchable->search($this->value));
        }

        $sortedResults = $searchResultCollection->sortByDesc('similarity')->forPage($this->page, $this->perPage);

        return $sortedResults;
    }

    public function registerSearchables(array $searchables): void 
    {
        $this->searchables = $searchables;
    }
} 