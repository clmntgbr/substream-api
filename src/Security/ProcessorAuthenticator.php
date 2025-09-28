<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ProcessorAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private string $processorToken,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return '/processor/get-video-processor-success' === $request->getPathInfo()
               || '/processor/get-video-processor-failure' === $request->getPathInfo();
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get('Authorization');

        if (null === $token) {
            throw new TokenNotFoundException('No Authorization header provided');
        }

        if ($token !== $this->processorToken) {
            throw new TokenNotFoundException('Invalid processor token');
        }

        return new SelfValidatingPassport(
            new UserBadge('processor', function () {
                return new User();
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => 'Authentication failed',
            'message' => $exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }
}
