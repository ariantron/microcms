<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class PaginationHelper
{
    /**
     * Validate and normalize pagination parameters.
     */
    public static function validatePagination(int $perPage = 12, int $page = 1): array
    {
        return [
            'per_page' => max(1, min(50, $perPage)), // Between 1 and 50
            'page' => max(1, $page)
        ];
    }

    /**
     * Extract pagination parameters from request.
     */
    public static function getPaginationFromRequest(Request $request, int $defaultPerPage = 12): array
    {
        $perPage = (int) $request->get('per_page', $defaultPerPage);
        $page = (int) $request->get('page', 1);

        return self::validatePagination($perPage, $page);
    }

    /**
     * Format pagination metadata for API response.
     */
    public static function formatPaginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'has_more_pages' => $paginator->hasMorePages(),
        ];
    }

    /**
     * Create pagination response data.
     */
    public static function createPaginationResponse(LengthAwarePaginator $paginator, string $dataKey = 'data'): array
    {
        return [
            $dataKey => $paginator->items(),
            'pagination' => self::formatPaginationMeta($paginator)
        ];
    }
}
