<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="`request`")
 */
class Request
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="userfrom", referencedColumnName="id")
     */
    private $userFrom;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="userto", referencedColumnName="id")
     */
    private $userTo;

    public function __construct($userFrom, $userTo)
    {
        $this->userFrom = $userFrom;
        $this->userTo = $userTo;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUserFrom()
    {
        return $this->userFrom;
    }

    /**
     * @param mixed $userFrom
     */
    public function setUserFrom($userFrom): void
    {
        $this->userFrom = $userFrom;
    }

    /**
     * @return mixed
     */
    public function getUserTo()
    {
        return $this->userTo;
    }

    /**
     * @param mixed $userTo
     */
    public function setUserTo($userTo): void
    {
        $this->userTo = $userTo;
    }
}