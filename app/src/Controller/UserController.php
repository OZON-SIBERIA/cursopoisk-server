<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/user/test", name="test", methods={"POST", "GET"})
     * @param Request $request
     * @return Response
     */
    public function test(Request $request): Response
    {
        $requestBody = json_decode($request->getContent(), true);
        $test = $requestBody['test'];

        $results = array();
        $results += ['testA' => $test . 'done'];
        $results += ['testB' => 'я ответ'];

        return new JsonResponse($results);
    }

    /**
     * @Route("/user/register", name="test", methods={"POST", "GET"})
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {
        $requestBody = json_decode($request->getContent(), true);
        $name = $requestBody['name'];
        $lastName = $requestBody['lastname'];
        $email = $requestBody['email'];
        $password = $requestBody['password'];

        if (null === $name ||  null === $lastName || null === $email || null === $password) {
            return new JsonResponse('Data is incorrect');
        }

        if ($this->userRepository->findOneBy(['email' => $email])) {
            return new Response('This e-mail is already used !');
        }

        $this->userRepository->createUser($name, $lastName, $email, $password);

        $results = array();
        $results += ['result' => 'New user created'];

        return new JsonResponse($results);
    }
}