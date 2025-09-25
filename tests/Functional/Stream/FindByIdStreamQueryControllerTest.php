<?php

declare(strict_types=1);

namespace App\Tests\Functional\Stream;

use App\Core\Infrastructure\Factory\StreamFactory;
use App\Tests\Shared\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class FindByIdStreamQueryControllerTest extends BaseWebTestCase
{
    use Factories;

    public function testListEntitiesWithPagination(): void
    {
        $entity = StreamFactory::createOne([
            ])->_disableAutoRefresh();

        StreamFactory::createMany(9);

        $valueId = $entity->getId();

        $response = $this->get('/api/v1/streams/findbyid?id='.$valueId);

        $response->assertStatusCode(Response::HTTP_OK);

        $content = $response->getData();

        $this->assertIsArray($content);
        $this->assertArrayHasKey('items', $content);

        $this->assertCount(1, $content['items']);

        $firstItem = $content['items'][0] ?? null;
        $this->assertNotNull($firstItem);

        $this->assertArrayHasKey('id', $firstItem);
        $this->assertEquals($entity->getId(), $firstItem['id']);
    }
}
