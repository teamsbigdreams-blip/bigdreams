<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TransactionHourlyRequest;
use App\Models\TransactionHourly;
use App\Services\TransactionHourlyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionHourlyController extends Controller
{
    public function __construct(
        protected TransactionHourlyService $transactionHourlyService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:100']
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($this->transactionHourlyService->getPaginatedList($filters));
        }

        return view('transaction-hourlies.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionHourlyRequest $request): JsonResponse
    {
        $transactionHourly = $this->transactionHourlyService->store($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Data successfully created.',
            'data' => $transactionHourly,
        ], 201);
    }

    /**
     * Show the specified resource for editing (JSON format).
     */
    public function edit(TransactionHourly $transactionHourly): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $transactionHourly,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransactionHourlyRequest $request, TransactionHourly $transactionHourly): JsonResponse
    {
        $updatedTransactionHourly = $this->transactionHourlyService->update($transactionHourly, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Data successfully updated.',
            'data' => $updatedTransactionHourly,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionHourly $transactionHourly): JsonResponse
    {
        try {
            $this->transactionHourlyService->delete($transactionHourly);

            return response()->json([
                'status' => 'success',
                'message' => 'Data successfully deleted.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
