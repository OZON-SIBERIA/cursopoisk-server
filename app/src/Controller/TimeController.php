<?php


namespace App\Controller;


use App\Entity\Time;
use App\Repository\TimeRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
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

    /**
     * @var LoggerInterface $logger
     */
    private LoggerInterface $logger;

    public function __construct(TimeRepository $timeRepository,
                                UserRepository $userRepository,
                                LoggerInterface $logger)
    {
        $this->timeRepository = $timeRepository;
        $this->userRepository =  $userRepository;
        $this->logger = $logger;
    }

    /**
     * @Route("/time/make", name="maketime", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function make(Request $request): Response
    {
        /*$token = $request->headers->get('X-AUTH-TOKEN');
        $token = str_replace('\\', '', $token);*/

        $token= $request->query->get('token');
        $day = $request->query->get('day');
        $timeStart = $request->query->get('timeStart');
        $timeEnd = $request->query->get('timeEnd');

        /*$this->logger->debug("р", $request->headers->all());*/

        if (null === $token ||  null === $day || null === $timeStart || null === $timeEnd) {
            return new JsonResponse('Data is incorrect', 500);
        }

        if (!$this->userRepository->findOneBy(['token' => $token])) {
            return new JsonResponse('This token is incorrect', 500);
        }

        $user = $this->userRepository->findOneBy(['token' => $token]);

        $timeObject = new Time();
        $timeObject->setDay($day);
        $timeObject->setTimeStart($timeStart);
        $timeObject->setTimeEnd($timeEnd);
        $timeObject->setUserId($user);
        $this->timeRepository->save($timeObject);

        return new JsonResponse('Time is created');
    }

    /**
     * @Route("/time/get", name="gettime", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function get(Request $request): Response
    {
        /*$token = $request->headers->get('X-AUTH-TOKEN');
        $token = str_replace('\\', '', $token);*/

        /*$this->logger->debug("р", $request->headers->all());*/

        $token= $request->query->get('token');

        if (null === $token) {
            return new JsonResponse('Token is incorrect', 500);
        }

        if (!$this->userRepository->findOneBy(['token' => $token])) {
            return new JsonResponse('Token is incorrect', 500);
        }

        $user = $this->userRepository->findOneBy(['token' => $token]);

        $times = $this->timeRepository->findBy(['user_id' => $user->getId()]);

        $result = array();

        foreach ($times as $time) {
            $result[] = ['day' => $time->getDay(), 'timeStart' => $time->getTimeStart(),
                'timeEnd' => $time->getTimeEnd()];
        }

        return new JsonResponse($result);
    }
}