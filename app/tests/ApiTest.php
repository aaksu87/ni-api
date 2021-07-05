<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;

class ApiTest extends WebTestCase
{
    private AbstractBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    public function testProductListSuccessResponse(): void
    {
        $this->client->request(
            'GET',
            '/products'
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseData = json_decode($response->getContent(), true);
        $this->assertCount(18, $responseData);
    }

    public function testUnauthorizedLinkReturnsError(): void
    {
        $this->client->request(
            'GET',
            '/user/products'
        );

        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals("Not authorized!", $responseData["error"]);
    }

    public function testMissingParameterOnAuthReturnsError(): void
    {
        $this->client->request(
            'POST',
            '/auth',
            [],[],['CONTENT_TYPE' => 'application/json'],
            '{"email":"abc@sd.re"}'
        );

        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals("[password] This field is missing.", $responseData["error"]);
    }

    public function testInvalidEmailOnAuthReturnsError(): void
    {
        $this->client->request(
            'POST',
            '/auth',
            [],[],['CONTENT_TYPE' => 'application/json'],
            '{"email":"abc","password":"secret"}'
        );

        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals("[email] This value is not a valid email address.", $responseData["error"]);
    }

    public function testSuccessUserDataAfterAuth(): void
    {
        $this->client->request(
            'POST',
            '/auth',
            [],[],['CONTENT_TYPE' => 'application/json'],
            '{"email":"mac94@moen.com","password":"secret"}'
        );
        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $this->client->request(
            'GET',
            '/user'
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseData = json_decode($response->getContent(), true);
        $this->assertSame("Weston Ratke", $responseData["name"]);
    }

    public function testAddPurchasedInvalidSkuReturnsError(): void
    {
        $this->client->request(
            'POST',
            '/auth',
            [],[],['CONTENT_TYPE' => 'application/json'],
            '{"email":"mac94@moen.com","password":"secret"}'
        );

        $this->client->request(
            'POST',
            '/user/products',
            [],[],['CONTENT_TYPE' => 'application/json'],
            '{"sku":"abc"}'
        );
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals("User or product could not found", $responseData["error"]);
    }

    public function testAddPurchasedSuccess(): void
    {
        $this->client->request(
            'POST',
            '/auth',
            [],[],['CONTENT_TYPE' => 'application/json'],
            '{"email":"mac94@moen.com","password":"secret"}'
        );

        //get current count
        $this->client->request(
            'GET',
            '/user/products'
        );
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $countUserProducts = count($responseData);


        //add a product
        $this->client->request(
            'POST',
            '/user/products',
            [],[],['CONTENT_TYPE' => 'application/json'],
            '{"sku":"lone-forest"}'
        );

        //get new count
        $this->client->request(
            'GET',
            '/user/products'
        );
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $newCountUserProducts = count($responseData);

        $this->assertEquals($newCountUserProducts,$countUserProducts+1);
    }

    public function testRemovePurchasedSuccess(): void
    {
        $this->client->request(
            'POST',
            '/auth',
            [],[],['CONTENT_TYPE' => 'application/json'],
            '{"email":"mac94@moen.com","password":"secret"}'
        );

        //get current count
        $this->client->request(
            'GET',
            '/user/products'
        );
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $countUserProducts = count($responseData);
        $sku = $responseData[0]["sku"];


        //remove a product
        $this->client->request(
            'DELETE',
            '/user/products/'.$sku
        );

        //get new count
        $this->client->request(
            'GET',
            '/user/products'
        );
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $newCountUserProducts = count($responseData);

        $this->assertEquals($newCountUserProducts,$countUserProducts-1);
    }


}
