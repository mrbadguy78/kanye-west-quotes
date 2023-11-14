<?php

use App\Http\Controllers\QuoteController;
use App\Services\QuoteService;
use Illuminate\Http\JsonResponse;
use App\Exceptions\FailedQuotesFetchException;
use App\Exceptions\FailedRefreshQuotesException;
use App\Http\Requests\Quotes\RefreshRequest;
use Tests\TestCase;

uses(TestCase::class);

describe("QuoteController class", function () {
    beforeEach(function () {
        // Mock the QuoteService dependency
        $this->quoteService = $this->mock(QuoteService::class);
        $this->controller = new QuoteController($this->quoteService);
    });

    it('returns quotes successfully', function () {
        $fakeQuotes = [
            'quote1',
            'quote2',
            'quote3',
            'quote4',
            'quote5'
        ];

        $this->quoteService->shouldReceive('getQuotes')
            ->once()
            ->andReturn(collect($fakeQuotes));

        $response = $this->controller->index();

        expect($response)->toBeInstanceOf(JsonResponse::class);
        expect($response->getStatusCode())->toEqual(200);
        expect($response->getData())->toMatchArray(['quotes' => $fakeQuotes]);
    });

    it('handles quotes fetch exception', function () {
        $this->quoteService->shouldReceive('getQuotes')
            ->once()
            ->andThrow(new FailedQuotesFetchException('Error fetching quotes', 409));

        $response = $this->controller->index();

        expect($response)->toBeInstanceOf(JsonResponse::class);
        expect($response->getStatusCode())->toEqual(409);
        expect($response->getData())->toHaveKey('error');
    });

    it('refreshes quotes successfully', function () {
        $fakeQuotes = [
            'quote1',
            'quote2',
            'quote3',
            'quote4',
            'quote5'
        ];
        $refreshRequest = new RefreshRequest();
        $refreshRequest->replace(['quotes' => $fakeQuotes]);

        $this->quoteService->shouldReceive('refreshQuotes')
            ->with($fakeQuotes)
            ->once()
            ->andReturn(collect($fakeQuotes));

        $response = $this->controller->refresh($refreshRequest);

        expect($response)->toBeInstanceOf(JsonResponse::class);
        expect($response->getStatusCode())->toEqual(200);
        expect($response->getData())->toMatchArray(['quotes' => $fakeQuotes]);
    });

    it('handles refresh quotes fetch exception', function () {
        $fakeQuotes = [
            'quote1',
            'quote2',
            'quote3',
            'quote4',
            'quote5'
        ];
        $refreshRequest = new RefreshRequest();
        $refreshRequest->replace(['quotes' => $fakeQuotes]);

        $this->quoteService->shouldReceive('refreshQuotes')
            ->once()
            ->andThrow(new FailedRefreshQuotesException('Error refreshing quotes', 409));

        $response = $this->controller->refresh($refreshRequest);

        expect($response)->toBeInstanceOf(JsonResponse::class);
        expect($response->getStatusCode())->toEqual(409);
        expect($response->getData())->toHaveKey('error');
    });
});
