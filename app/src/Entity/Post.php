<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="`post`")
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(referencedColumnName="id", name="author")
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $time;

    /**
     * @ORM\Column(type="text", length=2000)
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $subject;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $form;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $duration;
}