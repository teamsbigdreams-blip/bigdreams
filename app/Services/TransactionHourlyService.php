<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TransactionHourly;
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
        $query = TransactionHourly::query();
        // Uncomment the line below if you want to eager load the 'department' relationship
        //$query = TransactionHourly::query()->with('relasi');

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%");
                // Tambahkan relasi/kolom pencarian lain jika diperlukan, contoh:
                // ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('is_active', (bool) $filters['status']);
        }

        return $query->latest()->orderBy('id', 'desc')->paginate(10);
    }

    /**
     * Store a newly created transactionHourly in storage.
     *
     * @param array<string, mixed> $data
     */
    public function store(array $data): TransactionHourly
    {
        return DB::transaction(function () use ($data): TransactionHourly {
            return TransactionHourly::create($data);
        });
    }

    /**
     * Update the specified transactionHourly in storage.
     *
     * @param array<string, mixed> $data
     */
    public function update(TransactionHourly $transactionHourly, array $data): TransactionHourly
    {
        return DB::transaction(function () use ($transactionHourly, $data): TransactionHourly {
            $transactionHourly->update($data);

            return $transactionHourly->fresh();
        });
    }

    /**
     * Remove the specified transactionHourly from storage.
     *
     * @throws Exception
     */
    public function delete(TransactionHourly $transactionHourly): bool
    {
        // Contoh validasi relasi sebelum dihapus (sesuaikan dengan relasi model Anda jika ada)
        // if ($transactionHourly->someRelation()->exists()) {
        //     throw new Exception('Data cannot be deleted because it is still related to other data.');
        // }

        return DB::transaction(function () use ($transactionHourly): bool {
            return (bool) $transactionHourly->delete();
        });
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