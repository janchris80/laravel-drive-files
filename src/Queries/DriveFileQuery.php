<?php

namespace Janchris80\DriveFiles\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Janchris80\DriveFiles\Models\DriveFile;
use Janchris80\DriveFiles\Traits\HandlesDriveListQuery;

class DriveFileQuery
{
    use HandlesDriveListQuery;

    public function __construct(protected Request $request)
    {
    }

    public function paginate(): LengthAwarePaginator
    {
        $q = DriveFile::query();

        $this->applySearch($q, $this->request->input('search'), ['name']);

        foreach (['mime_type', 'category', 'visibility'] as $field) {
            if ($this->request->filled($field)) {
                $q->where($field, $this->request->input($field));
            }
        }

        $this->applySort(
            $q,
            $this->request,
            ['name', 'size_bytes', 'created_at', 'mime_type'],
            'created_at'
        );

        return $this->applyPagination($q, $this->request);
    }
}
