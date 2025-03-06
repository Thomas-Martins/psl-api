<?php

namespace App\Helpers;

class PaginationHelper
{
    /**
     * Si le paramètre "paginate" est présent dans la requête,
     * applique la pagination. Sinon, retourne tous les résultats.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @param int $max Limite maximale pour le cas où le paramètre limit serait négatif
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public static function paginateIfAsked($query, $max = 200)
    {
        if (request()->get('paginate', false)) {
            return self::paginate($query, $max);
        }
        return $query->get();
    }

    /**
     * Applique la pagination en tenant compte des paramètres de tri et de limite.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @param int $max Limite maximale si le paramètre "limit" est négatif
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function paginate($query, $max = 200)
    {
        $query = $query->orderBy(request('orderBy', 'id'), request('orderWay', 'DESC'));

        $limit = (int) request('limit', 10);
        if ($limit < 0) {
            $limit = max($query->count(), $max);
        }

        return $query->paginate($limit);
    }
}
