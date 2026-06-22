<?php

namespace Janchris80\DriveFiles\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HandlesDriveListQuery
{
    protected function applySearch(Builder $q, ?string $term, array $fields): void
    {
        if (! $term || $term === '') {
            return;
        }

        $q->where(function ($w) use ($term, $fields) {
            foreach ($fields as $i => $field) {
                $i === 0
                    ? $w->where($field, 'LIKE', "%{$term}%")
                    : $w->orWhere($field, 'LIKE', "%{$term}%");
            }
        });
    }

    protected function applySort(Builder $q, Request $r, array $allowed, string $default): void
    {
        $sort = $r->input('sort', $default);
        if (! in_array($sort, $allowed, true)) {
            $sort = $default;
        }

        $dir = strtolower((string) $r->input('direction', 'desc'));
        if (! in_array($dir, ['asc', 'desc'], true)) {
            $dir = 'desc';
        }

        $q->orderBy($sort, $dir);
    }

    protected function applyPagination(Builder $q, Request $r, int $default = 15, int $max = 100): LengthAwarePaginator
    {
        $perPage = (int) $r->input('per_page', $default);
        $perPage = max(1, min($perPage, $max));

        return $q->paginate($perPage);
    }
}
