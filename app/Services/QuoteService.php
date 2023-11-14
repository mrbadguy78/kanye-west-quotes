<?php

namespace App\Services;

use App\Exceptions\FailedQuotesFetchException;
use App\Exceptions\FailedRefreshQuotesException;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class QuoteService
{
    public function __construct() {}

    /**
     * @return \Illuminate\Support\Collection
     * @throws FailedQuotesFetchException
     */
    public function getQuotes(): \Illuminate\Support\Collection
    {
        try {
            $quotes = collect();
            for ($i = 0; $i < config('kayne.api.random_quotes'); $i++) {
                $response = HTTP::get(config('kayne.api.uri'));
                $quotes->push($response->json()['quote']);
            }
        } catch (Exception $exception) {
            throw new FailedQuotesFetchException($exception->getMessage(), 409);
        }


        return $quotes;
    }

    /**
     * @param array $quotes
     *
     * @return Collection
     * @throws FailedRefreshQuotesException
     */
    public function refreshQuotes(array $quotes): \Illuminate\Support\Collection
    {
        try {
            $existingQuotes = collect($quotes);
            $newQuotes = collect();
            while ($newQuotes->count() < config('kayne.api.random_quotes')) {
                $response = HTTP::post(config('kayne.api.uri'));
                $quote = $response->json()['quote'];
                if (!$existingQuotes->contains($quote)) {
                    $newQuotes->push($quote);
                }
            }
            $mergedQuotes = $existingQuotes->merge($newQuotes);
        } catch (Exception $exception) {
            throw new FailedRefreshQuotesException($exception->getMessage(), 409);
        }

        return $mergedQuotes;
    }
}
