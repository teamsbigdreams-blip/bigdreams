<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TransactionDaily;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TransactionDailyService
{
    /**
     * Get paginated and filtered transactionDailies list.
     *
     * @param array<string, mixed> $filters
     */
    public function getPaginatedList(array $filters): LengthAwarePaginator
    {
        $query = TransactionDaily::query()
            ->select('transaction_daily.*')
            ->leftJoin('company', 'transaction_daily.company_id', '=', 'company.id')
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
            : TransactionDaily::query()->max('date');

        $defaultDate = $latestDate ? Carbon::parse((string) $latestDate) : Carbon::today();

        $start = ! empty($filters['start_date'])
            ? Carbon::parse((string) $filters['start_date'])->startOfDay()
            : $defaultDate->copy()->startOfDay();

        $end = ! empty($filters['end_date'])
            ? Carbon::parse((string) $filters['end_date'])->endOfDay()
            : $defaultDate->copy()->endOfDay();

        $query->whereBetween('transaction_daily.date', [$start, $end]);

        // 4. Urutkan berdasarkan timestamp terbaru lalu company_code
        return $query
            ->orderBy('transaction_daily.date', 'desc')
            ->orderBy('company.company_code', 'asc')
            ->paginate(20);
    }
}
