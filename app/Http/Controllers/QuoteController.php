<?php

namespace App\Http\Controllers;

use App\Exceptions\FailedQuotesFetchException;
use App\Exceptions\FailedRefreshQuotesException;
use App\Http\Requests\Quotes\RefreshRequest;
use App\Services\QuoteService;
use Illuminate\Http\JsonResponse;

class QuoteController extends Controller
{
    public function __construct(protected QuoteService $quoteService)
    {
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $quotes = $this->quoteService->getQuotes();
        } catch (FailedQuotesFetchException $exception) {
            return response()->json(
                ['error' => $exception->getMessage()],
                $exception->getCode()
            );
        }

        return response()->json(['quotes' => $quotes]);
    }

    /**
     * @param RefreshRequest $refreshRequest
     *
     * @return JsonResponse
     */
    public function refresh(RefreshRequest $refreshRequest): JsonResponse
    {
        try {
            $quotes = $this->quoteService->refreshQuotes($refreshRequest->quotes);
        } catch (FailedRefreshQuotesException $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }

        return response()->json(['quotes' => $quotes]);
    }
}
