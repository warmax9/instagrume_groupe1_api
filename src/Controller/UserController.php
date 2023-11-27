<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

use App\Entity\User;
use App\Service\JsonConverter;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;


class UserController extends AbstractController {
        
        private $passwordHasher;
        private $jsonConverter;

        public  function __construct(UserPasswordHasherInterface $passwordHasher, JsonConverter $jsonConverter) {
            $this->passwordHasher = $passwordHasher;
            $this->jsonConverter = $jsonConverter;
        }
        #[Route('/api/login', methods: ['POST'])]
        #[OA\Response(
            response: 200,
            description: 'Génère un token de connexion',
            content: new OA\JsonContent(type: 'object',
            properties: [
                new OA\Property(property: 'token', type: 'string')
            ])
        )]
        #[OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'username', type: 'string', default: "admin"),
                    new OA\Property(property: 'password', type: 'string', default: "password")
                ]
            )
        )]
        #[OA\Tag(name: 'User')]
        public function getToken(ManagerRegistry $doctrine, JWTTokenManagerInterface $JWTManager) {
            $request = Request::createFromGlobals();
            $data = json_decode($request->getContent(), true);

            if (!is_array($data) || $data == null || empty($data['username']) || empty($data['password'])) {
                return new Response('Identifiants invalides', 401);
            }

            $entityManager = $doctrine->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $data['username']]);

            if (!$user) {
                throw $this->createNotFoundException();
            }
            if (!$this->passwordHasher->isPasswordValid($user, $data['password'])) {
                return new Response('Identifiants invalides', 401);
            }

            $token = $JWTManager->create($user);
            return new JsonResponse(['token' => $token]);
    }

    #[Route('/api/myself', methods: ['GET'])]
    #[OA\Tag(name: 'User')]
    public function getUserByToken(Request $request, JWTEncoderInterface $JWTManager, ManagerRegistry $doctrine){
         $headers = $request->headers->all();
         $authorization = $request->headers->get('Authorization');
         $token = substr($authorization, 7);
         $data = $JWTManager->decode($token);

         $entityManager = $doctrine->getManager();
         $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $data['username']]);
         $data = $this->jsonConverter->encodeToJson($user);
         return new Response($data);
    }

    #[Route('/api/user/{id}', methods: ['GET'])]
    #[OA\Tag(name: 'User')]
    public function getUserById(Request $request, JWTEncoderInterface $JWTManager, ManagerRegistry $doctrine, $id){

         $entityManager = $doctrine->getManager();
         $user = $entityManager->getRepository(User::class)->find($id);
         return new Response($this->jsonConverter->encodeToJson($user));
    }
    
    #[Route('/api/register', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Créer un nouvelle utilisateur et le retourne',
        content: new OA\JsonContent(ref: new Model(type: User::class))
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'username', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
                new OA\Property(property: 'photo', type: 'string'),
            ]
        )
    )]
    #[OA\Tag(name: 'User')]
    public function register(Request $request, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $dataArray = json_decode($request->getContent(), true);

        $user = new User();
        $user->setUsername($dataArray['username']);
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->passwordHasher->hashPassword($user, $dataArray['password']));
        $user->setPhoto($dataArray['photo']);
        $user->setModo(false);
        $entityManager->persist($user);
        $data = $this->jsonConverter->encodeToJson($user);
        $entityManager->flush();
        return new Response($data);
    }

    #[Route('/api/banned/{id}', methods: ['PUT'])]
    #[OA\Put(description: 'Bannir un utilisateur via son id')]
    #[OA\Response(
		response: 200,
		description: 'L\'utilisateur',
        content: new OA\JsonContent(ref: new Model(type: User::class))
	)]
    #[OA\Tag(name: 'User')]
	public function banneUser(ManagerRegistry $doctrine, $id) {
        $entityManager = $doctrine->getManager();
        $user = $doctrine->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'Pas d\'utilisateur'
            );
        }
        $user->setIsBanned(true);
        $entityManager->persist($user);
        $entityManager->flush();
        return new Response($this->jsonConverter->encodeToJson($user));
    }

    #[Route('/api/unbanned/{id}', methods: ['PUT'])]
    #[OA\Put(description: 'Bannir un utilisateur via son id')]
    #[OA\Response(
		response: 200,
		description: 'L\'utilisateur',
        content: new OA\JsonContent(ref: new Model(type: User::class))
	)]
    #[OA\Tag(name: 'User')]
	public function unbanneUser(ManagerRegistry $doctrine, $id) {
        $entityManager = $doctrine->getManager();
        $user = $doctrine->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'Pas d\'utilisateur'
            );
        }
        $user->setIsBanned(false);
        $entityManager->persist($user);
        $entityManager->flush();
        return new Response($this->jsonConverter->encodeToJson($user));
    }
}
