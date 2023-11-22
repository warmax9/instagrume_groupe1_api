<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Commentaire;
use App\Service\JsonConverter;
use OpenApi\Attributes as OA;
class CommentaireController extends AbstractController {

    private $jsonConverter;
    public  function __construct(JsonConverter $jsonConverter) {
        $this->jsonConverter = $jsonConverter;
    }

    #[Route('/api/commentaire', methods: ['GET'])]
    #[OA\Tag(name: 'Commentaire')]
    public function getPosts(ManagerRegistry $doctrine){
        $entityManager = $doctrine->getManager();

        $commentaires = $entityManager->getRepository(Commentaire::class)->findAll();

        return new Response($this->jsonConverter->encodeToJson($commentaires));
    }


}