<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ScreenerRequest;
use App\Models\Screener;
use App\Services\ScreenerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScreenerController extends Controller
{
    public function __construct(
        protected ScreenerService $screenerService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date'],
        ]);

        $items = $this->screenerService->getList($filters);

        if ($request->wantsJson() || $request->ajax()) {
            $count = $items->count();

            return response()->json([
                'status' => 'success',
                'data' => $items->values(),
                'total' => $count,
                'per_page' => $count,
                'current_page' => 1,
                'last_page' => 1,
                'from' => $count > 0 ? 1 : 0,
                'to' => $count,
                'prev_page_url' => null,
                'next_page_url' => null,
            ]);
        }

        return view('screeners.index');
    }
}
