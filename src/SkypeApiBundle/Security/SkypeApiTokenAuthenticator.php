<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 01.03.17
 * Time: 16:38
 */
namespace SkypeApiBundle\Security;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use AppBundle\Exception\AuthenticationBotInactiveException;

class SkypeApiTokenAuthenticator extends AbstractGuardAuthenticator
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
    public function getCredentials(Request $request)
    {
        if (!$token = $request->headers->get('API-AUTH-TOKEN')) {
            // no token? Return null and no other methods will be called
            //return null;
            throw new AuthenticationCredentialsNotFoundException();
        }

        $skypeId = $request->headers->get('SKYPE-ID');


        // What you return here will be passed to getUser() as $credentials
        return array(
            'token' => $token,
            'skypeId' => $skypeId,
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $this->em
            ->getRepository("AppBundle:Publisher")
            ->findOneBy(["apiToken" => $credentials['token']]);

        if (!$user) {
            throw new AuthenticationCredentialsNotFoundException();
        }

        if (!$user->getIsSkypeBotActive()) {
            throw new AuthenticationBotInactiveException();
        }

        // first access saves skypeId
        if ( $user->getSkypeId() == NULL and $credentials["skypeId"]) {
            $user->setSkypeId($credentials["skypeId"]);

            $this->em->persist($user);
            $this->em->flush();

            return $user;
        } elseif ( $user->getSkypeId() != NULL and $user->getSkypeId() == $credentials["skypeId"] ) {
            return $user;
        } else {
            throw new AuthenticationCredentialsNotFoundException();
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // you might translate this message
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}