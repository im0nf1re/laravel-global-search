# Package for global search

This package uses similarity() from pg_trgm to get most relevant results. You will get SearchResultCollection that consist of SearchResult elements. Default SearchResult element contains **Eloquent Model** to build url from parent relations, **max similarity field name**, **value of this field** and **Model class**.

## Install
***
```bash
composer require imonfire/laravel-global-search
```

## Usage
***
To start searching you should create GlobalSearchService instance, register your GlobalSearch classes and call **search** method.


### Controller example

```php
use Imonfire\GlobalSearch\Services\GlobalSearchService;

$globalSearchService = new GlobalSearchService($request);
$globalSearchService->registerSearchables(SearchableRegister::$searchibles);
$searchResultCollection = $globalSearchService->search();
```

### SearchableRegister class example

```php
class SearchableRegister
{
    public static $searchibles = [
        PostGlobalSearch::class,
        ArticleGlobalSearch::class,
    ];
}
```
### GlobalSearch class example

Your GlobalSearch class should extend Imonfire\GlobalSearch\Components\GlobalSearch.php  
You should define properties:  
- $modelClass - your model class
- $searchFields - fields to be searched
- $relations - relations that will be loaded for search results

```php
use Imonfire\GlobalSearch\Components\GlobalSearch;

final class PostGlobalSearch extends GlobalSearch
{
    public $modelClass = Post::class;
    
    protected $searchFields = [
        'title',
        'description',
    ];

    protected $relations = [
        'user'
    ];
}
```

You can override GlobalSearch::getSearchResult method to make your structure to SearchResult

```php
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
```

