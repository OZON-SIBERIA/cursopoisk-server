<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestController
{
    /**
     * @Route("/test", name="test", methods={"POST", "GET"})
     * @param Request $request
     * @return Response
     */
    public function getCategories(Request $request): Response
    {
        $requestBody = json_decode($request->getContent(), true);
        $test = $requestBody['test'];

        $results = $test . 'done';

        return new JsonResponse($results);
    }
}