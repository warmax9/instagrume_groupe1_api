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

class PostController extends AbstractController
{

    public function __construct(private JsonConverter $jsonConverter, private ManagerRegistry $doctrine)
    {
    }

    #[Route('/api/posts', methods: ['GET'])]
    #[OA\Parameter(
        name: 'user_id',
        in: 'query',
        description: 'L\'id qui permet d\'avoir tous les pots d\'un user',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Récupère tous les posts',
        content: new OA\JsonContent(ref: new Model(type: Post::class))
    )]
    #[OA\Tag(name: 'Post')]
    public function getPosts(Request $request): Response
    {
        $entityManager = $this->doctrine->getManager();
        $idUSer = $request->query->get('user_id');
        if (!$idUSer) {
            $posts = $entityManager->getRepository(Post::class)->findAll();
        } else {
            $user = $entityManager->getRepository(User::class)->find($idUSer);
            $posts = $entityManager->getRepository(Post::class)->findPostsByUser($user);
        }
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
    public function insertPost(Request $request): Response
    {
        $uniqueId = uniqid();
        $dataArray = json_decode($request->getContent(), true);
        $entityManager = $this->doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($dataArray['user_id']);
        $post = new Post();
        $post->setUser($user);
        $binaryImageData = base64_decode($dataArray['image']);
        if (isset($dataArray['description'])) {
            $post->setDescription($dataArray['description']);
        }
        $post->setIsOpen(true);
        //récupère l'extension de l'image
        $imageType = exif_imagetype('data://image/jpeg;base64,' . base64_encode($binaryImageData));
        $extension = image_type_to_extension($imageType);

        $filePath = __DIR__ . '/../../public/images/post/' . $uniqueId . $extension;
        file_put_contents($filePath, $binaryImageData);

        $post->setImage($uniqueId . $extension);
        $entityManager->persist($post);
        $entityManager->flush();

        return new Response($this->jsonConverter->encodeToJson($post));
    }

    #[Route('/api/posts/{id}', methods: ['DELETE'])]
    #[OA\Delete(description: 'Supprime un posts')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        schema: new OA\Schema(type: 'integer'),
        required: true,
        description: 'L\'identifiant d\'un post'
    )]
    #[OA\Tag(name: 'Post')]
    public function deletePost($id): Response
    {
        $entityManager = $this->doctrine->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        if (!$post) {
            throw $this->createNotFoundException(
                'Pas de post avec id ' . $id
            );
        }
        $entityManager->remove($post);
        $entityManager->flush();
        return new Response("sucess");
    }

    #[Route('/api/posts', methods: ['PUT'])]
    #[OA\Put(description: 'Modifie un post et retourne ses informations')]
    #[OA\Response(
        response: 200,
        description: 'Le post mis à jour',
        content: new OA\JsonContent(ref: new Model(type: Post::class))
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'image', type: 'string'),
                new OA\Property(property: 'is_open', type: 'bool', default: true),
                new OA\Property(property: 'description', type: 'string')
            ]
        )
    )]
    #[OA\Tag(name: 'Post')]
    public function updatePost(Request $request): Response
    {
        $entityManager = $this->doctrine->getManager();
        $data = json_decode($request->getContent(), true);
        $post = $this->doctrine->getRepository(Post::class)->find($data['id']);
        if (!$post) {
            throw $this->createNotFoundException(
                'Pas de post'
            );
        }
        if (isset($data['image'])) $post->setImage($data['image']);
        if (isset($data['is_open'])) $post->setIsOpen($data['is_open']);
        if (isset($data['description'])) $post->setDescription($data['description']);
        $entityManager->persist($post);
        $entityManager->flush();
        return new Response($this->jsonConverter->encodeToJson($post));
    }
}
