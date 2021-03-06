<?php
namespace App\security;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
 use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
 use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use \Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException; 
use Exception;
use App\Entity\Client;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    
    private $jwtEncoder;
    private $em;
    public function __construct(JWTEncoderInterface $jwtEncoder, EntityManagerInterface $em)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->em = $em;

    } 
 
    public function supports(Request $request)
    {
          return true; 
    }
    
    public function getCredentials(Request $request)
    {   
     
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer', 
            'Authorization'
        );
        $token = $extractor->extract($request);      
         if (!$token) {
            return  false;
        }
         return $token;

    }
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            
            if (!$credentials ) {  return null;}     
                 
             $data = $this->jwtEncoder->decode( str_replace('Bearer ', '' , $credentials));
              $user= $this->em->getRepository(Client::class)
                ->findOneBy(['email' => $data['username']]);
             return $user;
        } catch ( Exception $e) {
           dd($e->getMessage()); 
            throw new CustomUserMessageAuthenticationException($e->getMessage());
        }
    }

    public function checkCredentials($credentials, UserInterface $client)
    {
        return true;

    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
      //  return new Response(json_encode(['message' => 'login success from Api']), Response::HTTP_OK);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {  
       $data = [
            'message' => [$exception->getMessageKey(), $exception->getMessageData()]
        ];
        return new Response(json_encode($data), Response::HTTP_UNAUTHORIZED);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
         $data = [
            'message' => ' Authentication required',
        ];
        return new Response(json_encode($data), Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }

 
 
   
}  
