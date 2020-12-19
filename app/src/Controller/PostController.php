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
        $this->userRepository =  $userRepository;
        $this->postRepository = $postRepository;
        $this->logger = $logger;
    }

    /**
     * @Route("/post/make", name="makepost", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function make(Request $request): Response
    {
        $token = $request->headers->get('X-AUTH-TOKEN');
        $token = str_replace('\\', '', $token);

        $requestBody = json_decode($request->getContent(), true);

        $type = $requestBody['type'];
        $time = $requestBody['time'];
        $text = $requestBody['text'];
        $subject = $requestBody['subject'];
        $price = $requestBody['price'];
        $form = $requestBody['form'];
        $duration = $requestBody['duration'];

        /*$type = $request->query->get('type');
        $time = $request->query->get('time');
        $text = $request->query->get('text');
        $subject = $request->query->get('subject');
        $price = $request->query->get('price');
        $form = $request->query->get('form');
        $duration = $request->query->get('duration');*/

        $this->logger->debug("р", $request->headers->all());

        if (null === $token ||  null === $type || null === $time
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
        $post->setTime($time);
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
        $token = $request->headers->get('X-AUTH-TOKEN');
        $token = str_replace('\\', '', $token);

        $this->logger->debug("р", $request->headers->all());

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
            $result[] = ['type' => $post->getType(), 'time' => $post->getTime(),
                'text' => $post->getText(), 'subject' => $post->getSubject(),
                'price' => $post->getPrice(), 'form' => $post->getForm(),
                'duration' => $post->getDuration()];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/post/getby", name="postgetby", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function getBy(Request $request): Response
    {
        $requestBody = json_decode($request->getContent(), true);
        $token = $request->headers->get('X-AUTH-TOKEN');
        $token = str_replace('\\', '', $token);

        $criteria = $requestBody['criteria'];
        $searchValue = $requestBody['searchValue'];
        $page = $requestBody['page'];
        $limit = $requestBody['limit'];

        $this->logger->debug("р", $request->headers->all());

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
            $posts[] = ['author' => $post->getAuthor(), 'type' => $post->getType(), 'time' => $post->getTime(),
                'text' => $post->getText(), 'subject' => $post->getSubject(),
                'price' => $post->getPrice(), 'form' => $post->getForm(),
                'duration' => $post->getDuration()];
        }

        return new JsonResponse(['maxPage' => $maxPages, 'posts' => $posts]);
    }
}