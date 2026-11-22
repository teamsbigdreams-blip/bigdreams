<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Screener;
use App\Models\TransactionDaily;
use App\Models\TransactionHourly;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class ScreenerService
{
    /**
     * Get filtered screeners list without pagination.
     *
     * @param array<string, mixed> $filters
     * @return EloquentCollection<int, Screener>
     */
    public function getList(array $filters = []): EloquentCollection
    {
        $query = Screener::query()
            ->select('screeners.*')
            ->leftJoin('company', 'screeners.company_id', '=', 'company.id')
            ->with('company');

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);

            $query->whereHas('company', function ($q) use ($search): void {
                $q->where('company_code', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $latestDate = ! empty($filters['start_date']) || ! empty($filters['end_date'])
            ? null
            : Screener::query()->max('date');

        $defaultDate = $latestDate ? Carbon::parse((string) $latestDate) : Carbon::today();

        $start = ! empty($filters['start_date'])
            ? Carbon::parse((string) $filters['start_date'])->startOfDay()
            : $defaultDate->copy()->startOfDay();

        $end = ! empty($filters['end_date'])
            ? Carbon::parse((string) $filters['end_date'])->endOfDay()
            : $defaultDate->copy()->endOfDay();

        $query->whereBetween('screeners.date', [$start, $end]);

        /** @var EloquentCollection<int, Screener> $screeners */
        $screeners = $query
            ->orderBy('screeners.date', 'desc')
            ->orderBy('company.company_code', 'asc')
            ->get();

        return $this->attachStockbitMetrics($screeners);
    }

    /**
     * Attach current_price, prev_close, and Stockbit-standard change_pct to each item.
     *
     * @param EloquentCollection<int, Screener> $screeners
     * @return EloquentCollection<int, Screener>
     */
    private function attachStockbitMetrics(EloquentCollection $screeners): EloquentCollection
    {
        $companyIds = $screeners
            ->pluck('company_id')
            ->filter()
            ->map(fn($id): int => (int) $id)
            ->unique()
            ->values();

        if ($companyIds->isEmpty()) {
            return $screeners;
        }

        $dailiesByCompany = TransactionDaily::query()
            ->whereIn('company_id', $companyIds)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get()
            ->groupBy('company_id');

        $latestHourlyByCompany = TransactionHourly::query()
            ->whereIn('company_id', $companyIds)
            ->orderByDesc('datetime')
            ->orderByDesc('id')
            ->get()
            ->groupBy('company_id')
            ->map(fn(Collection $rows): ?TransactionHourly => $rows->first());

        $screeners->transform(function (Screener $screener) use ($dailiesByCompany, $latestHourlyByCompany): Screener {
            $companyId = (int) $screener->company_id;

            /** @var Collection<int, TransactionDaily> $dailyRows */
            $dailyRows = $dailiesByCompany->get($companyId, collect());

            /** @var ?TransactionHourly $latestHourly */
            $latestHourly = $latestHourlyByCompany->get($companyId);

            /** @var ?TransactionDaily $latestDaily */
            $latestDaily = $dailyRows->first();

            $currentPrice = $latestHourly?->close
                ?? $latestDaily?->close
                ?? $screener->close;

            $currentPriceFloat = $currentPrice !== null ? (float) $currentPrice : null;
            $prevCloseFloat = $this->resolvePreviousClose($dailyRows, $latestHourly, $latestDaily, $screener);

            $changePct = null;
            if ($currentPriceFloat !== null && $prevCloseFloat !== null && $prevCloseFloat > 0) {
                $changePct = round((($currentPriceFloat - $prevCloseFloat) / $prevCloseFloat) * 100, 2);
            }

            $screener->setAttribute('current_price', $currentPriceFloat);
            $screener->setAttribute('prev_close', $prevCloseFloat);
            $screener->setAttribute('change_pct', $changePct);

            return $screener;
        });

        return $screeners;
    }

    /**
     * @param Collection<int, TransactionDaily> $dailyRows
     */
    private function resolvePreviousClose(
        Collection $dailyRows,
        ?TransactionHourly $latestHourly,
        ?TransactionDaily $latestDaily,
        Screener $screener
    ): ?float {
        $latestHourlyDate = $latestHourly?->datetime
            ? Carbon::parse((string) $latestHourly->datetime)->toDateString()
            : null;

        if ($latestHourlyDate !== null) {
            $prevDaily = $dailyRows->first(function (TransactionDaily $row) use ($latestHourlyDate): bool {
                $dailyDate = $row->date ?? $row->datetime ?? null;
                if (! $dailyDate) {
                    return false;
                }

                return Carbon::parse((string) $dailyDate)->toDateString() < $latestHourlyDate;
            });

            if ($prevDaily && $prevDaily->close !== null) {
                return (float) $prevDaily->close;
            }
        }

        if ($dailyRows->count() >= 2 && $dailyRows->get(1)?->close !== null) {
            return (float) $dailyRows->get(1)->close;
        }

        if ($latestDaily && $latestDaily->close !== null) {
            return (float) $latestDaily->close;
        }

        return $screener->close !== null ? (float) $screener->close : null;
    }
}
