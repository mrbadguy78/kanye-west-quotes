<?php

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Middleware\VerifyApiToken;
use Symfony\Component\HttpFoundation\Response;

uses(Tests\TestCase::class);

describe("VerifyApiToken middleware", function () {
    it('responds with unauthorized error if API token is missing', function () {
        $request = Request::create('/api/quotes', 'GET');

        $response = (new VerifyApiToken())->handle(
            $request,
            fn() => new \Symfony\Component\HttpFoundation\Response()
        );

        expect($response)->toBeInstanceOf(JsonResponse::class);
        expect($response->getStatusCode())->toEqual(401);
        expect($response->getData())->toHaveKey('error', 'Unauthorized');
    });

    it('proceeds with request if API token is present', function () {
        $request = Request::create('/api/quotes', 'GET');
        $request->headers->set('Authorization', 'Bearer some-valid-token');

        $response = (new VerifyApiToken())->handle(
            $request,
            function ($request) use (&$called) {
                $called = true;
                return new \Symfony\Component\HttpFoundation\Response();
            }
        );

        expect($called)->toBeTrue();
        expect($response)->toBeInstanceOf(Response::class);
        expect($response->getStatusCode())->toEqual(200);
    });
});
