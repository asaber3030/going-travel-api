<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

trait PaginateResources
{
  /**
   * Get paginated data for the model with optional relationships and query modifications
   *
   * @param Request $request
   * @param array $relationships
   * @param int $defaultPerPage
   * @param bool $onlyTrashed
   * @param callable|null $queryModifier
   * @return \Illuminate\Pagination\LengthAwarePaginator
   */
  protected function paginateResources(
    Request $request,
    array $relationships = [],
    int $defaultPerPage = 15,
    bool $onlyTrashed = false,
    ?callable $queryModifier = null
  ) {
    $perPage = $request->query('per_page', $defaultPerPage);
    $query = $this->model::query();

    foreach ($relationships as $queryParam => $relationship) {
      if ($request->query($queryParam)) {
        $query->with($relationship);
      }
    }

    if ($onlyTrashed) {
      $query->onlyTrashed();
    }

    if ($queryModifier) {
      $queryModifier($query, $request);
    }

    return $query->paginate($perPage);
  }
}
