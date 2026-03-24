<?php

namespace App\Controller;

use App\Entity\Kid;
use App\Repository\KidRepository;
use App\Repository\MotherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/kid')]
final class KidController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(KidRepository $kidRepository, SerializerInterface $serializer): JsonResponse
    {
        $kids = $kidRepository->findAll();

        return JsonResponse::fromJsonString(
            $serializer->serialize($kids, 'json', ['groups' => ['kid:read']])
        );
    }

    #[Route('', methods: ['POST'])]
    public function create( Request $request, EntityManagerInterface $em, SerializerInterface $serializer, MotherRepository $motherRepository): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['mother'])) { return new JsonResponse(['error' => 'Mother is required'], 400); }

        $mother = $motherRepository->find($data['mother']);
        if (!$mother) { return new JsonResponse(['error' => 'Mother not found'], 404); }

        $kid = $serializer->deserialize($request->getContent(), Kid::class, 'json', ['groups' => ['kid:write']]);

        $kid->setMother($mother);

        $em->persist($kid);
        $em->flush();

        return new JsonResponse( $serializer->serialize($kid, 'json', ['groups' => ['kid:read']]), 201, [], true);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Kid $kid, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($kid, 'json', ['groups' => ['kid:read']]),
            200,
            [],
            true
        );
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Kid $kid, EntityManagerInterface $em, SerializerInterface $serializer,MotherRepository $motherRepository): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $serializer->deserialize($request->getContent(), Kid::class, 'json', ['object_to_populate' => $kid, 'groups' => ['kid:write']]);

        if (isset($data['mother'])) {
            $mother = $motherRepository->find($data['mother']);

            if (!$mother) { return new JsonResponse(['error' => 'Mother not found'], 404); }

            $kid->setMother($mother);
        }

        $em->flush();

        return new JsonResponse($serializer->serialize($kid, 'json', ['groups' => ['kid:read']]), 200, [], true);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Kid $kid, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($kid);
        $em->flush();

        return new JsonResponse(null, 204);
    }
}