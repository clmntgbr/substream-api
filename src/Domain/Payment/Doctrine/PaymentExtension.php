<?php

declare(strict_types=1);

namespace App\Domain\Payment\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Domain\Payment\Entity\Payment;
use App\Domain\User\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final readonly class PaymentExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    /**
     * @throws \Exception
     */
    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    /**
     * @throws \Exception
     */
    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Payment::class !== $resourceClass) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new UnauthorizedHttpException('You have to be authenticated.');
        }

        $subscriptions = $user->getSubscriptions();

        if ($subscriptions->isEmpty()) {
            $queryBuilder->andWhere('1 = 0');

            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.subscription IN (:subscriptions)', $rootAlias));
        $queryBuilder->setParameter('subscriptions', $subscriptions);
    }
}
