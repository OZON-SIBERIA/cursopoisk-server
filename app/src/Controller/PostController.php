<?php


namespace App\Controller;


use App\Entity\Post;
use App\Repository\PostRepository;
use App\Repository\TimeRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController
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
     * @var PostRepository $postRepository
     */
    private PostRepository $postRepository;

    /**
     * @var LoggerInterface $logger
     */
    private LoggerInterface $logger;

    public function __construct(TimeRepository $timeRepository,
                                UserRepository $userRepository,
                                PostRepository $postRepository,
                                LoggerInterface $logger)
    {
        $this->timeRepository = $timeRepository;
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
        $this->logger = $logger;
    }

    /**
     * @Route("/post/make", name="makepost", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function make(Request $request): Response
    {
        /*$token = $request->headers->get('X-AUTH-TOKEN');
        $token = str_replace('\\', '', $token);

        $requestBody = json_decode($request->getContent(), true);*/

        /*$type = $requestBody['type'];
        $time = $requestBody['time'];
        $text = $requestBody['text'];
        $subject = $requestBody['subject'];
        $price = $requestBody['price'];
        $form = $requestBody['form'];
        $duration = $requestBody['duration'];*/

        $token = $request->query->get('token');
        $type = $request->query->get('type');
        $timeStart = $request->query->get('timeStart');
        $timeEnd = $request->query->get('timeEnd');
        $text = $request->query->get('text');
        $subject = $request->query->get('subject');
        $price = $request->query->get('price');
        $form = $request->query->get('form');
        $duration = $request->query->get('duration');

        $this->logger->debug("р", $request->headers->all());

        if (null === $token || null === $type || null === $timeStart || null === $timeEnd
            || null === $text || null === $subject || null === $price
            || null === $form || null === $duration) {
            return new JsonResponse('Data is incorrect', 500);
        }

        if (!$this->userRepository->findOneBy(['token' => $token])) {
            return new JsonResponse('This token is incorrect', 500);
        }

        $user = $this->userRepository->findOneBy(['token' => $token]);

        $post = new Post();
        $post->setAuthor($user);
        $post->setType($type);
        $post->setTimeStart($timeStart);
        $post->setText($text);
        $post->setSubject($subject);
        $post->setPrice($price);
        $post->setForm($form);
        $post->setDuration($duration);
        $this->postRepository->save($post);

        return new JsonResponse('Post created');
    }

    /**
     * @Route("/post/get", name="getposts", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function get(Request $request): Response
    {
        /*$token = $request->headers->get('X-AUTH-TOKEN');
        $token = str_replace('\\', '', $token);

        $this->logger->debug("р", $request->headers->all());*/

        $token = $request->query->get('token');
        $token = str_replace('\\', '', $token);

        if (null === $token) {
            return new JsonResponse('Token is incorrect', 500);
        }

        if (!$this->userRepository->findOneBy(['token' => $token])) {
            return new JsonResponse('Token is incorrect', 500);
        }

        $user = $this->userRepository->findOneBy(['token' => $token]);

        $posts = $this->postRepository->findBy(['author' => $user->getId()]);

        $result = array();

        foreach ($posts as $post) {
            $result[] = ['type' => $post->getType(), 'timeStart' => $post->getTimeStart(),
                'timeEnd' => $post->getTimeEnd(), 'text' => $post->getText(),
                'subject' => $post->getSubject(), 'price' => $post->getPrice(),
                'form' => $post->getForm(), 'duration' => $post->getDuration()];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/post/search", name="postsearch", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        /*$token = $request->headers->get('X-AUTH-TOKEN');
        $token = str_replace('\\', '', $token);*/

        $token = $request->query->get('token');
        $token = str_replace('\\', '', $token);
        $criteria = $request->query->get('criteria');
        $searchValue = $request->query->get('searchvalue');
        $page = $request->query->get('page');
        $limit = $request->query->get('limit');

        /*$this->logger->debug("р", $request->headers->all());*/

        if (null === $token || null === $criteria || null === $searchValue || null === $page || null === $limit) {
            return new JsonResponse('Data is incorrect', 500);
        }

        if (!$this->userRepository->findOneBy(['token' => $token])) {
            return new JsonResponse('Token is incorrect', 500);
        }

        $postsInResult = $this->postRepository->findPaginate([$criteria => $searchValue], $page, $limit);
        $maxPages = $this->postRepository->getMaxPages([$criteria => $searchValue], $limit);

        $posts = array();

        foreach ($postsInResult as $post) {
            $user = $this->userRepository->findOneBy(['id' => $post->getAuthor()]);
            $author = $user->getUserName() . ' ' . $user->getLastName();
            $posts[] = ['id' => $post->getId(), 'author' => $author, 'type' => $post->getType(),
                'timeStart' => $post->getTimeStart(), 'timeEnd' => $post->getTimeEnd(),
                'text' => $post->getText(), 'subject' => $post->getSubject(),
                'price' => $post->getPrice(), 'form' => $post->getForm(),
                'duration' => $post->getDuration()];
        }

        return new JsonResponse(['maxPage' => $maxPages, 'posts' => $posts]);
    }

    /**
     * @Route("/post/cross", name="postcross", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function cross(Request $request): Response
    {
        /*$token = $request->headers->get('X-AUTH-TOKEN');
        $token = str_replace('\\', '', $token);*/
        $token = $request->query->get('token');
        $token = str_replace('\\', '', $token);
        $id = $request->query->get('author');

        /*$this->logger->debug("р", $request->headers->all());*/

        if (null === $token || null === $id) {
            return new JsonResponse('Data is incorrect', 500);
        }

        if (!$this->userRepository->findOneBy(['token' => $token])) {
            return new JsonResponse('Token is incorrect', 500);
        }

        $userFrom = $this->userRepository->findOneBy(['token' => $token]);
        $userTo = $this->userRepository->findOneBy(['id' => $id]);

        //$post = $this->postRepository->findOneBy(['id' => $id]);

        $timesFrom = $this->timeRepository->findBy(['id' => $userFrom]);
        $timesTo = $this->timeRepository->findBy(['id' => $userFrom]);

        foreach ($timesFrom as $timeFrom) {
            preg_match('/(.*?):/', $timeFrom->getTimeStart(), $result);
            $timeFromStartTimeH = $result[1];
            preg_match('/:(.*?)-/', $timeFrom->getTimeStart(), $result);
            $timeFromStartTimeM = $result[1];
            preg_match('/(.*?):/', $timeFrom->getTimeEnd(), $result);
            $timeFromEndTimeH = $result[1];
            preg_match('/:(.*?)-/', $timeFrom->getTimeEnd(), $result);
            $timeFromEndTimeM = $result[1];
            $dayFrom = $userFrom->getDay();
            foreach ($timesTo as $timeTo) {
                preg_match('/(.*?):/', $timeTo->getTimeStart(), $result);
                $timeToStartTimeH = $result[1];
                preg_match('/:(.*?)-/', $timeTo->getTimeStart(), $result);
                $timeToStartTimeM = $result[1];
                preg_match('/(.*?):/', $timeTo->getTimeEnd(), $result);
                $timeToEndTimeH = $result[1];
                preg_match('/:(.*?)-/', $timeTo->getTimeEnd(), $result);
                $timeToEndTimeM = $result[1];
                $dayTo = $userTo->getDay();
                if ((($timeFromStartTimeH <= $timeToStartTimeH) && ($timeFromStartTimeM <= $timeToStartTimeM)) &&
                    (($timeFromEndTimeH >= $timeToEndTimeH) && ($timeFromEndTimeM >= $timeToEndTimeM)) && $dayFrom === $dayTo) {
                    $results[] = ['user' => ['day' => $timeFrom->getDay(), 'timeStart' => $timeFrom->getTimeStart(),
                        'timeEnd' => $timeFrom->getTimeEnd()],
                        'author' => ['day' => $timeTo->getDay(), 'timeStart' => $timeTo->getTimeStart(),
                            'timeEnd' => $timeTo->getTimeEnd()]];
                }
            }
        }

        return new JsonResponse(['crossings' => $results]);
    }
}