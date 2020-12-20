<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\TimeRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController
{
    protected UserRepository $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;
    private TimeRepository $timeRepository;

    public function __construct(UserRepository $userRepository,
                                UserPasswordEncoderInterface $passwordEncoder,
                                TimeRepository $timeRepository)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->timeRepository = $timeRepository;
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
     * @Route("/user/register", name="register", methods={"POST", "GET"})
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

        if (null === $name || null === $lastName || null === $email || null === $password) {
            return new JsonResponse('Data is incorrect', 500);
        }

        if ($this->userRepository->findOneBy(['email' => $email])) {
            return new JsonResponse('This e-mail is already used !', 500);
        }

        $this->userRepository->createUser($name, $lastName, $email, $password);

        $results = array();
        $results += ['result' => 'New user created'];

        return new JsonResponse($results);
    }

    /**
     * @Route("/user/login", name="login", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function login(Request $request): Response
    {
        $email = $request->query->get('email');
        $password = $request->query->get('password');

        if (null === $email || null === $password) {
            return new JsonResponse('Data is incorrect', 500);
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (null === $user || !$this->passwordEncoder->isPasswordValid($user, $password)) {
            return new JsonResponse('Incorrect password or email', 500);
        }

        /*$userToken = base64_encode(random_bytes(50));
        * doesn`t work for android app
        */

        $userToken = 'wdadadjbj1rawdwadaw21=';
        //$this->userRepository->saveUserToken($user, $userToken);

        $user = $this->userRepository->findOneBy(['email' => $email]);

        $times = $this->timeRepository->findBy(['user_id' => $user->getId()]);

        $result = array();

        foreach ($times as $time) {
            $result[] = ['day' => $time->getDay(),
                'timeStart' => str_replace('-', '', $time->getTimeStart()),
                'timeEnd' => str_replace('-', '', $time->getTimeEnd())];
        }

        $userInfo[] = ['name' => $user->getUserName(), 'lastName' => $user->getLastName(),
            'email' => $user->getEmail()];

        return new JsonResponse(['token' => $userToken, 'time' => $result, 'userInfo' => $userInfo]);
    }
}