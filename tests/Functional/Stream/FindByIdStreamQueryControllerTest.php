<?php

declare(strict_types=1);

namespace App\Tests\Functional\Stream;

use App\Core\Infrastructure\Factory\StreamFactory;
use App\Tests\Shared\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

use function Zenstruck\Foundry\faker;

class FindByIdStreamQueryControllerTest extends BaseWebTestCase
{
    use Factories;

    public function testListEntitiesWithPagination(): void
    {
        $entity = StreamFactory::createOne([
                'fileName' => $valueFileName = faker()->sentence(),
                'originalFileName' => $valueOriginalFileName = faker()->sentence(),
                'url' => $valueUrl = faker()->sentence(),
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

        $this->assertArrayHasKey('fileName', $firstItem);
        $this->assertEquals($entity->getFileName(), $firstItem['fileName']);

        $this->assertArrayHasKey('originalFileName', $firstItem);
        $this->assertEquals($entity->getOriginalFileName(), $firstItem['originalFileName']);

        $this->assertArrayHasKey('url', $firstItem);
        $this->assertEquals($entity->getUrl(), $firstItem['url']);

        $this->assertArrayHasKey('id', $firstItem);
        $this->assertEquals($entity->getId(), $firstItem['id']);
    }
}
