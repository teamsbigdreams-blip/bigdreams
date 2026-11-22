<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TransactionHourly;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TransactionHourlyService
{
    /**
     * Get paginated and filtered transactionHourlies list.
     *
     * @param array<string, mixed> $filters
     */
    public function getPaginatedList(array $filters): LengthAwarePaginator
    {
        $query = TransactionHourly::query()
            ->select('transaction_hourly.*')
            ->leftJoin('company', 'transaction_hourly.company_id', '=', 'company.id')
            ->with('company');
        // Uncomment the line below if you want to eager load the 'department' relationship
        //$query = TransactionHourly::query()->with('relasi');

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);

            $query->whereHas('company', function ($q) use ($search): void {
                $q->where('company_code', 'like', "%{$search}%");
            });
        }

        $latestDate = ! empty($filters['start_date']) || ! empty($filters['end_date'])
            ? null
            : TransactionHourly::query()->max('datetime');

        $defaultDate = $latestDate ? Carbon::parse((string) $latestDate) : Carbon::today();

        $start = ! empty($filters['start_date'])
            ? Carbon::parse((string) $filters['start_date'])->startOfDay()
            : $defaultDate->copy()->startOfDay();

        $end = ! empty($filters['end_date'])
            ? Carbon::parse((string) $filters['end_date'])->endOfDay()
            : $defaultDate->copy()->endOfDay();

        $query->whereBetween('transaction_hourly.datetime', [$start, $end]);

        // 4. Urutkan berdasarkan timestamp terbaru lalu company_code
        return $query
            ->orderBy('transaction_hourly.datetime', 'desc')
            ->orderBy('company.company_code', 'asc')
            ->paginate(20);
    }


    /**
     * Get formatted paginated list for AJAX/Axios responses.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getFormattedPaginatedList(array $filters): array
    {
        $transactionHourlies = $this->getPaginatedList($filters);

        return [
            'status' => 'success',
            'data' => $transactionHourlies->items(),
            'pagination' => [
                'current_page' => $transactionHourlies->currentPage(),
                'last_page' => $transactionHourlies->lastPage(),
                'per_page' => $transactionHourlies->perPage(),
                'total' => $transactionHourlies->total(),
                'next_page_url' => $transactionHourlies->nextPageUrl(),
                'prev_page_url' => $transactionHourlies->previousPageUrl(),
            ],
        ];
    }
    /**
     * Get department options for dropdown.
     *
     * @return array<int, string>
     */
    // public function getDepartmentDropdown(): array
    //{
    //   return Department::getDropdownOptions();
    //}
}
