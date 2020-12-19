<?php


namespace App\Controller;


use App\Entity\Time;
use App\Repository\TimeRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TimeController
{
    /**
     * @var UserRepository $userRepository
     */
    protected UserRepository $userRepository;

    /**
     * @var TimeRepository $timeRepository
     */
    private TimeRepository $timeRepository;

    public function __construct(TimeRepository $timeRepository,
                                UserRepository $userRepository)
    {
        $this->timeRepository = $timeRepository;
        $this->userRepository =  $userRepository;
    }

    /**
     * @Route("/time/make", name="maketime", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function make(Request $request): Response
    {
        $requestBody = json_decode($request->getContent(), true);
        $token = $request->headers->get('X-AUTH-TOKEN');
        $day = $requestBody['day'];
        $time = $requestBody['time'];

        if (null === $token ||  null === $day || null === $time) {
            return new JsonResponse('Data is incorrect', 500);
        }

        if (!$this->userRepository->findOneBy(['token' => $token])) {
            var_dump($token);
            return new JsonResponse('This token is incorrect', 500);
        }

        $user = $this->userRepository->findOneBy(['token' => $token]);

        $timeObject = new Time();
        $timeObject->setDay($day);
        $timeObject->setTime($time);
        $timeObject->setUserId($user);
        $this->timeRepository->save($timeObject);

        return new JsonResponse('Time is created');
    }
}