<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Periode
 *
 * @ORM\Table(name="periode")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PeriodeRepository")
 *
 */
class Periode
{
    /**
     * @var int
     *
     * @ORM\Column(
     *     name="id",
     *     type="integer"
     * )
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(
     *     name="annee",
     *     type="integer",
     *     length=4,
     *
     * )
     * @Assert\Length(min="4" , max="4")
     * @Assert\NotBlank(message="Veuillez rensigner le champs année yyyy")
     * @Assert\Regex(
     *     pattern="/^[0-9]{4}/",
     *     message="L'année ne doit contenir que des chiffres")
     */
    private $annee;

    /**
     * @var string
     *
     * @ORM\Column(
     *     name="code",
     *     type="string",
     *     unique=true
     * )
     *
     */
    private $code;

    /**
     * @var boolean
     *
     * @ORM\Column(
     *     name="is_active",
     *     type="boolean",
     *     length=1,
     *     options={"default":true},
     *     unique=true
     * )
     */
    private $is_active;


    /**
     * @var boolean
     *
     * @ORM\Column(
     *     name="is_semestre",
     *     type="boolean",
     *     length=1,
     *     options={"default":false}
     * )
     */
    private $is_semestre;

    /**
     * @return bool
     */
    public function isSemestre()
    {
        return $this->is_semestre;
    }

    /**
     * @param bool $is_semestre
     */
    public function setIsSemestre(bool $is_semestre)
    {
        $this->is_semestre = $is_semestre;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * @param bool $is_active
     */
    public function setIsActive(bool $is_active)
    {
        $this->is_active = $is_active;
    }

    /**
     * @ORM\OneToMany(
     *     targetEntity="Data",
     *     mappedBy="periode",
     *     cascade={"persist", "remove"}
     * )
     */
    private $datas;

    /**
     * Periode constructor.
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
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * @param string $annee
     */
    public function setAnnee(string $annee)
    {
        $this->annee = $annee;
    }


    /**
     * @return string|null
     */
    public function __toString()
    {

        return $this->getCode();

    }

    /**
     * @return ArrayCollection
     */
    public function getDatas()
    {
        return $this->datas;
    }

    /**
     *
     * @param Data ...$datas
     * @return $this
     *
     */
    public function addDatas(Data ...$datas)
    {
        foreach ($datas as $data) {
            if (!$this->datas->contains($data)) {
                $this->datas->add($data);
                $data->setPeriode($this);

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
        $data->setPeriode(null);
        return $this;
    }

}
