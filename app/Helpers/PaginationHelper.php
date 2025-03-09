<?php

namespace App\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PaginationHelper
{
    /**
     * Si le paramètre "paginate" est présent dans la requête,
     * applique la pagination. Sinon, retourne tous les résultats.
     *
     * @param Builder|\Illuminate\Database\Query\Builder $query
     * @param int $max Limite maximale pour le cas où le paramètre limit serait négatif
     * @return LengthAwarePaginator|Collection
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
     * @param Builder|\Illuminate\Database\Query\Builder $query
     * @param int $max Limite maximale si le paramètre "limit" est négatif
     * @return LengthAwarePaginator
     */
    public static function paginate($query, $max = 200)
    {
        $order_by = request()->has('orderBy')
            ? request('orderBy')
            : request('order_by', 'id');

        $order_way = request()->has('orderWay')
            ? strtoupper(request('orderWay'))
            : strtoupper(request('order_way', 'DESC'));

        if (!in_array($order_way, ['ASC', 'DESC'])) {
            $order_way = 'DESC';
        }

        if ($order_by === 'identity') {
            $query = $query->orderBy('firstname', $order_way)
                ->orderBy('lastname', $order_way);
        } else {
            $query = $query->orderBy($order_by, $order_way);
        }

        $limit = (int) request('limit', 10);
        if ($limit < 0) {
            $limit = max($query->count(), $max);
        }

        return $query->paginate($limit);
    }

}
