<?php

declare(strict_types=1);

namespace App\Tests\Functional\Stream;

use App\Tests\Shared\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UpdateStreamStreamCommandControllerTest extends BaseWebTestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function testCreateEntity(): void
    {
        $payload = [
            ];

        $response = $this->post('/api/v1/streams/updatestream/', $payload);
        $response->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $content = $response->getData();

        $this->assertArrayHasKey('id', $content);
        $this->assertNotNull($content['id']);
    }
}
