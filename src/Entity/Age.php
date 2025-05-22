<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Age
 *
 * @ORM\Table(name="age")
 * @ORM\Entity(repositoryClass="App\Repository\AgeRepository")
 *
 * @UniqueEntity(fields="codeAge", message="la tranche d'âge existe déjà.")
 */
class Age
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(
     *     name="code_age",
     *      type="string",
     *      length=50,
     *      unique=true
     *  )
     * @Assert\NotBlank()
     */
    private $codeAge;


    /**
     * @ORM\OneToMany(
     *     targetEntity="Data",
     *     mappedBy="age",
     *     cascade={"persist", "remove"}
     * )
     */
    private $datas;

    /**
     * Age constructor.
     */
    public function __construct()
    {
        $this->datas = new ArrayCollection();
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get codeAge.
     *
     * @return string
     */
    public function getCodeAge()
    {
        return $this->codeAge;
    }

    /**
     * Set codeAge.
     *
     * @param string $codeAge
     *
     * @return Age
     */
    public function setCodeAge($codeAge)
    {
        $this->codeAge = $codeAge;

        return $this;
    }

    public function __toString()
    {
        return $this->getCodeAge();
    }


    /**
     * @return ArrayCollection
     */
    public function getDatas()
    {
        return $this->datas;
    }


    /**
     * @param Data ...$datas
     * @return $this
     */
    public function addDatas(Data ...$datas)
    {
        foreach ($datas as $data) {
            if (!$this->datas->contains($data)) {

                $this->datas->add($data);
                $data->setAge($this);

            }

        }
        return $this;
    }

    /**
     * @param Data $data
     * @return $this
     */
    public function removeData(Data $data)
    {

        $this->datas->remove($data);
        $data->setAge(null);
        return $this;

    }
}
