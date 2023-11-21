<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Attributes as OA;
use App\Entity\Post;
use App\Service\JsonConverter;


class PostController extends AbstractController {

    private $jsonConverter;
    public  function __construct(JsonConverter $jsonConverter) {
        $this->jsonConverter = $jsonConverter;
    }

    #[Route('/api/posts', methods: ['GET'])]
    #[OA\Tag(name: 'User')]
    public function getPosts(ManagerRegistry $doctrine){
        $entityManager = $doctrine->getManager();

        $posts = $entityManager->getRepository(Post::class)->findAll();

        return new Response($this->jsonConverter->encodeToJson($posts));
    }

    #[Route('/api/posts', methods: ['POST'])]
    public function insertPost(ManagerRegistry $doctrine){
        $request = Request::createFromGlobals();
        $dataArray = json_decode($request->getContent(), true);
        $entityManager = $doctrine->getManager();
        $ruche = $entityManager->getRepository(User::class)->find($dataArray['user_id']);
    }
}