<?php

describe('index method', function () {
    it('retrieves a list of quotes successfully', function () {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer your-valid-token',
        ])->getJson('/api/quotes');

        expect($response->assertOk());
        $response->assertJsonStructure(['quotes']);
        $responseData = $response->json();
        expect($responseData['quotes'])->toBeArray()->toHaveCount(5);
    });
});

describe('refresh method', function () {
    it('refreshes quotes successfully', function () {
        $data = [
            'quotes' => [
                "I'll say things that are serious and put them in a joke form so people can enjoy them. We laugh to keep from crying.",
                "We will be recognized",
                "I am running for President of the United States",
                "We have to evolve",
                "Pulling up in the may bike"
            ]
        ];

        $response = $response = $this->withHeaders([
            'Authorization' => 'Bearer your-valid-token',
        ])->postJson('/api/quotes/refresh', $data);

        $response->assertOk();
        $response->assertJsonStructure(['quotes']);
        $responseData = $response->json();
        expect($responseData['quotes'])->toBeArray()->toHaveCount(10);
    });
});


it('returns validation errors if the refresh data is invalid', function () {
    $response = $response = $response = $this->withHeaders([
        'Authorization' => 'Bearer your-valid-token',
    ])->postJson('/api/quotes/refresh', []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['quotes']);
});

it('responds with unauthorized error if API token is missing', function () {
    $response = $this->getJson('/api/quotes');

    $response->assertUnauthorized();
    $response->assertJson(['error' => 'Unauthorized']);
});
