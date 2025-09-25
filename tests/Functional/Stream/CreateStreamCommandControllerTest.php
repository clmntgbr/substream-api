<?php

declare(strict_types=1);

namespace App\Tests\Functional\Stream;

use App\Tests\Shared\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

use function Zenstruck\Foundry\faker;

class CreateStreamCommandControllerTest extends BaseWebTestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function testCreateEntity(): void
    {
        $payload = [
                'fileName' => faker()->sentence(),
                'originalFileName' => faker()->sentence(),
                'url' => faker()->sentence(),
            ];

        $response = $this->post('/api/v1/streams/create/', $payload);
        $response->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $content = $response->getData();

        $this->assertArrayHasKey('id', $content);
        $this->assertNotNull($content['id']);

        $this->assertEquals($payload['fileName'], $content['fileName']);

        $this->assertEquals($payload['originalFileName'], $content['originalFileName']);

        $this->assertEquals($payload['url'], $content['url']);
    }
}
