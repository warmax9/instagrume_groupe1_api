<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Attributes as OA;
use App\Entity\Like;
use App\Service\JsonConverter;


class LikeController extends AbstractController {

    private $jsonConverter;
    public  function __construct(JsonConverter $jsonConverter) {
        $this->jsonConverter = $jsonConverter;
    }

    #[Route('/api/likes', methods: ['GET'])]
    #[OA\Tag(name: 'Like')]
    public function getPosts(ManagerRegistry $doctrine){
        $entityManager = $doctrine->getManager();
        $like = $entityManager->getRepository(Like::class)->findAll();

        return new Response($this->jsonConverter->encodeToJson($like));
    }
}