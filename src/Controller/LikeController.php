<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Commentaire;
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

    #[Route('/api/likes', methods: ['POST'])]
    #[OA\Post(description: 'Crée un nouveau like soit sur un post soit sur un commentaire et le retourne ')]
    #[OA\Response(
		response: 200,
		description: 'Le nouveau like',
        content: new OA\JsonContent(ref: new Model(type: Like::class))
	)]
	#[OA\RequestBody(
		required: false,
		content: new OA\JsonContent(
			type: 'object',
			properties: [
                new OA\Property(property: 'value', type: 'boolean'),
                new OA\Property(property: 'user_id', type: 'integer'),
                new OA\Property(property: 'post_id', type: 'integer', default: null),
                new OA\Property(property: 'commentaire_id', type: 'integer', default: null)
			]
		)
	)]
	#[OA\Tag(name: 'Like')]
    public function insertLike(ManagerRegistry $doctrine){
        $request = Request::createFromGlobals();
        $dataArray = json_decode($request->getContent(), true);
        $entityManager = $doctrine->getManager();

        $like = new Like();
        $like->setValue($dataArray['value']);
        $user = $entityManager->getRepository(User::class)->find($dataArray['user_id']);
        $like->setUser($user);
        if(isset($dataArray['post_id'])){
            $post = $entityManager->getRepository(Post::class)->find($dataArray['post_id']);
            $like->setPost($post);
        }
        if(isset($dataArray['commentaire_id'])){
            $commentaire = $entityManager->getRepository(Commentaire::class)->find($dataArray['commentaire_id']);
            $like->setCommentaire($commentaire);
        }
        $entityManager->persist($like);
        $entityManager->flush();

        return new Response($this->jsonConverter->encodeToJson($like));
    }
}