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

    public  function __construct(private JsonConverter $jsonConverter, private ManagerRegistry $doctrine) {
    }

    #[Route('/api/likes', methods: ['GET'])]
    #[OA\Tag(name: 'Like')]
    public function getPosts(): Response
    {
        $entityManager = $this->doctrine->getManager();
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
    public function insertLike(Request $request): Response
    {
        $dataArray = json_decode($request->getContent(), true);
        $entityManager = $this->doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($dataArray['user_id']);
        $like = new Like();
        $like->setValue($dataArray['value']);
        $like->setUser($user);
        if(isset($dataArray['post_id'])){
            $post = $entityManager->getRepository(Post::class)->find($dataArray['post_id']);
            $like->setPost($post);
        }
        if(isset($dataArray['commentaire_id'])){
            $commentaire = $entityManager->getRepository(Commentaire::class)->find($dataArray['commentaire_id']);
            $like->setCommentaire($commentaire);
        }
        $existingLike = $entityManager->getRepository(Like::class)->findOneBy(['value' => $dataArray['value']]);
        $entityManager->persist($like);
        $entityManager->flush();

        return new Response($this->jsonConverter->encodeToJson($like));
    }

    #[Route('/api/likes', methods: ['PUT'])]
    #[OA\Put(description: 'Modifie un like et retourne ses informations')]
    #[OA\Response(
		response: 200,
		description: 'Le like mis à jour',
        content: new OA\JsonContent(ref: new Model(type: Like::class))
	)]
	#[OA\RequestBody(
		required: true,
		content: new OA\JsonContent(
			type: 'object',
			properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'value', type: 'boolean'),
			]
		)
	)]
    #[OA\Tag(name: 'Like')]
	public function updateLike(Request $request): Response
    {
		$entityManager = $this->doctrine->getManager();
        $data = json_decode($request->getContent(), true);
        $like = $this->doctrine->getRepository(Like::class)->find($data['id']);
        if (!$like) {
            throw $this->createNotFoundException(
                'Pas de like'
            );
        }
        $like->setValue($data['value']);
        $entityManager->persist($like);
        $entityManager->flush();

        return new Response($this->jsonConverter->encodeToJson($like));
    }

    #[Route('/api/likes/{id}', methods: ['DELETE'])]
    #[OA\Delete(description: 'Supprime un like')]
	#[OA\Parameter(
		name: 'id',
		in: 'path',
		schema: new OA\Schema(type: 'integer'),
		required: true,
		description: 'L\'identifiant d\'un like'
	)]
	#[OA\Tag(name: 'Like')]
	public function deleteLike($id): Response
    {
		$entityManager = $this->doctrine->getManager();
        $like = $entityManager->getRepository(Like::class)->find($id);
        if (!$like) {
            throw $this->createNotFoundException(
                'Pas de like avec id '.$id
            );
        }
        $entityManager->remove($like);
        $entityManager->flush();
        return new Response("sucess");
    }
}