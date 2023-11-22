<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

use App\Entity\Post;
use App\Entity\User;
use App\Service\JsonConverter;


class PostController extends AbstractController {

    private $jsonConverter;
    public  function __construct(JsonConverter $jsonConverter) {
        $this->jsonConverter = $jsonConverter;
    }

    #[Route('/api/posts', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Récupère tous les posts',
        content: new OA\JsonContent(ref: new Model(type: Post::class))
    )]
    #[OA\Tag(name: 'Post')]
    public function getPosts(ManagerRegistry $doctrine){
        $entityManager = $doctrine->getManager();

        $posts = $entityManager->getRepository(Post::class)->findAll();

        return new Response($this->jsonConverter->encodeToJson($posts));
    }

    #[Route('/api/posts', methods: ['POST'])]
    #[OA\Post(description: 'Crée un nouveau post et retourne ses informations')]
    #[OA\Response(
		response: 200,
		description: 'Le nouveau post',
        content: new OA\JsonContent(ref: new Model(type: Post::class))
	)]
	#[OA\RequestBody(
		required: true,
		content: new OA\JsonContent(
			type: 'object',
			properties: [
                new OA\Property(property: 'image', type: 'string'),
                new OA\Property(property: 'user_id', type: 'integer'),
                new OA\Property(property: 'description', type: 'string'),
			]
		)
	)]
	#[OA\Tag(name: 'Post')]
    public function insertPost(ManagerRegistry $doctrine){
        $request = Request::createFromGlobals();
        $dataArray = json_decode($request->getContent(), true);
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($dataArray['user_id']);
        $post = new Post();
        $post->setUser($user);
        $post->setImage($dataArray['image']);
        if(isset($dataArray['description'])){
            $post->setDescription($dataArray['description']);
        }
        $entityManager->persist($post);
        $entityManager->flush();
        return new Response($this->jsonConverter->encodeToJson($post));
    }
}