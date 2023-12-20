<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Commentaire;
use App\Entity\Post;
use App\Entity\User;

use App\Service\JsonConverter;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

class CommentaireController extends AbstractController
{
    public  function __construct(private JsonConverter $jsonConverter, private ManagerRegistry $doctrine)
    {
    }

    #[Route('/api/commentaire', methods: ['GET'])]
    #[OA\Parameter(
        name: 'post_id',
        in: 'query',
        description: 'L\'id qui permet d\'avoir tous les commentaires d\'un post',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'Commentaire')]
    public function getPosts(Request $request): Response
    {
        $entityManager = $this->doctrine->getManager();
        $idPost = $request->query->get('post_id');
        if (!$idPost) {
            $commentaires = $entityManager->getRepository(Commentaire::class)->findAll();
        } else {
            $commentaires = $entityManager->getRepository(Commentaire::class)->findOneBy(['post' => $idPost]);
        }
        return new Response($this->jsonConverter->encodeToJson($commentaires));
    }

    #[Route('/api/commentaire', methods: ['POST'])]
    #[OA\Post(description: 'Crée un nouveau commentaire soit sur un post soit sur un commentaire et le retourne ')]
    #[OA\Response(
        response: 200,
        description: 'Le nouveau commentaire',
        content: new OA\JsonContent(ref: new Model(type: Commentaire::class))

    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'content', type: 'string'),
                new OA\Property(property: 'user_id', type: 'integer'),
                new OA\Property(property: 'post_id', type: 'integer', default: null),
                new OA\Property(property: 'commentaire_id', type: 'integer', default: null)
            ]
        )
    )]
    #[OA\Tag(name: 'Commentaire')]
    public function insertCommentaire(Request $request): Response
    {
        $dataArray = json_decode($request->getContent(), true);
        $entityManager = $this->doctrine->getManager();

        $commentaire = new Commentaire();
        $commentaire->setContent($dataArray['content']);
        $user = $entityManager->getRepository(User::class)->find($dataArray['user_id']);
        $commentaire->setUser($user);
        if (isset($dataArray['post_id'])) {
            $post = $entityManager->getRepository(Post::class)->find($dataArray['post_id']);
            $commentaire->setPost($post);
        }
        if ($dataArray['commentaire_id'] != "") {
            $commentaireParent = $entityManager->getRepository(Commentaire::class)->find($dataArray['commentaire_id']);
            $commentaire->setCommentaireParent($commentaireParent);
            $entityManager->persist($commentaire);
            $entityManager->flush();
            return new Response($this->jsonConverter->encodeToJson($commentaireParent->getPost()));
        }
        $entityManager->persist($commentaire);
        $entityManager->flush();

        return new Response($this->jsonConverter->encodeToJson($post));
    }

    #[Route('/api/commentaire/{id}', methods: ['DELETE'])]
    #[OA\Delete(description: 'Supprime un commentaire')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        schema: new OA\Schema(type: 'integer'),
        required: true,
        description: 'L\'identifiant d\'un commentaire'
    )]
    #[OA\Tag(name: 'Commentaire')]
    public function deleteCommentaire($id)
    {
        $entityManager = $this->doctrine->getManager();
        $commentaire = $entityManager->getRepository(Commentaire::class)->find($id);
        if (!$commentaire) {
            throw $this->createNotFoundException(
                'Pas de like avec id ' . $id
            );
        }
        $entityManager->remove($commentaire);
        $entityManager->flush();
        return new Response("sucess");
    }

    #[Route('/api/commentaire', methods: ['PUT'])]
    #[OA\Put(description: 'Modifie un commentaire et retourne ses informations')]
    #[OA\Response(
        response: 200,
        description: 'Le commentaire mis à jour',
        content: new OA\JsonContent(ref: new Model(type: Commentaire::class))
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'content', type: 'string')
            ]
        )
    )]
    #[OA\Tag(name: 'Commentaire')]
    public function updateCommentaire(Request $request): Response
    {
        $entityManager = $this->doctrine->getManager();
        $data = json_decode($request->getContent(), true);
        $commentaire = $this->doctrine->getRepository(Commentaire::class)->find($data['id']);
        if (!$commentaire) {
            throw $this->createNotFoundException(
                'Pas de commentaire'
            );
        }
        $commentaire->setContent($data['content']);
        $entityManager->persist($commentaire);
        $entityManager->flush();

        return new Response($this->jsonConverter->encodeToJson($commentaire));
    }
}
