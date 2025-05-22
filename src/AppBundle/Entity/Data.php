<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Data
 *
 * @ORM\Table(name="data")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DataRepository")
 */
class Data
{
    /**
     * @var int
     *
     * @ORM\Column(
     *     name="id",
     *     type="integer"
     * )
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var array
     *
     * @ORM\Column(
     *     name="datas",
     *     type="json_array"
     * )
     */
    private $datas;


    /**
     * @var \DateTime
     *
     * @ORM\Column(
     *     name="update_at",
     *     type="datetime"
     * )
     *
     * @Assert\DateTime
     *
     */
    private $updateAt;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Praticien",
     *     inversedBy="datas"
     * )
     * @ORM\JoinColumn(
     *     name="praticien_id",
     *     referencedColumnName="id",
     *     nullable=true,
     *     onDelete="CASCADE"
     * )
     */
    private $praticien;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Periode",
     *     inversedBy="datas")
     * @ORM\JoinColumn(
     *     name="periode_id",
     *     referencedColumnName="id",
     *     nullable=true,
     *     onDelete="CASCADE"
     * )
     */
    private $periode;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Theme",
     *     inversedBy="datas"
     * )
     * @ORM\JoinColumn(
     *     name="theme_id",
     *     referencedColumnName="id",
     *     nullable=true,
     *     onDelete="CASCADE"
     * )
     */
    protected $theme;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Age",
     *     inversedBy="datas"
     * )
     * @ORM\JoinColumn(
     *     name="age_id",
     *     referencedColumnName="id",
     *     nullable=true,
     *     onDelete="CASCADE"
     * )
     */
    private $age;

    /**
     * Data constructor.
     */
    public function __construct()
    {
        $this->updateAt = new \DateTime();
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): Int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getDatas(): array
    {
        return array_values(json_decode($this->datas));
    }

    /**
     * @param array $datas
     *
     */
    public function setDatas(array $datas)
    {
        $this->datas = json_encode($datas);


    }

    /**
     * @return \DateTime
     */
    public function getupdateAt(): \DateTime
    {
        return $this->updateAt;
    }

    /**
     * @param $updateAt
     */
    public function setupdateAt(\DateTime $updateAt): void
    {
        $this->updateAt = $updateAt;

    }

    /**
     * @return mixed
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * @param mixed $periode
     */
    public function setPeriode($periode)
    {
        $this->periode = $periode;
    }

    /**
     * @return mixed
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param mixed $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return mixed
     */
    public function getPraticien()
    {
        return $this->praticien;
    }

    /**
     * @param mixed $praticien
     */
    public function setPraticien($praticien)
    {
        $this->praticien = $praticien;
    }
}
