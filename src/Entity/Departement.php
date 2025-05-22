<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Departement
 *
 * @ORM\Table(name="departement")
 * @ORM\Entity(repositoryClass="App\Repository\DepartementRepository")
 */
class Departement
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
     * @var int
     *
     * @ORM\Column(
     *     name="code_dep",
     *     type="string",
     *     nullable=true
     * )
     */
    private $codeDep;

    /**
     * @var string
     *
     * @ORM\Column(
     *     name="libelle_dep",
     *     type="string",
     *     length=50,
     *     nullable=true
     * )
     */
    private $libelleDep;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Utaa",
     *     mappedBy="departement",
     *     cascade={"persist","remove"}
     *)
     *
     */
    private $utaas;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Region",
     *     inversedBy="departements"
     * )
     * @ORM\JoinColumn(
     *     name="region_id",
     *     referencedColumnName="id",
     *     nullable=true,
     *     onDelete="CASCADE"
     * )
     */
    private $region;

    /**
     * Departement constructor.
     */
    public function __construct()
    {
        $this->utaas = new ArrayCollection();
    }

    /**
     * @var boolean
     *
     * @ORM\Column(
     *     name="is_active",
     *     type="boolean",
     *     length=1,
     *     options={"default":false}
     * )
     */
    private $is_active;



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
     * Get codeDep.
     *
     * @return string
     */
    public function getCodeDep()
    {
        return $this->codeDep;
    }

    /**
     * Set codeDep.
     *
     * @param string $codeDep
     *
     *
     */
    public function setCodeDep($codeDep)
    {
        $this->codeDep = $codeDep;


    }

    /**
     * @return string
     */
    public function __toString()
    {

        return $this->getCodeDep() . '-' . $this->getLibelleDep();

    }



    /**
     * Get libelleDep.
     *
     * @return string
     */
    public function getLibelleDep()
    {
        return $this->libelleDep;
    }

    /**
     * Set libelleDep.
     *
     * @param string $libelleDep
     *
     * @return Departement
     */
    public function setLibelleDep($libelleDep)
    {
        $this->libelleDep = $libelleDep;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getUtaas()
    {
        return $this->utaas;
    }

    /**
     * @param Utaa ...$utaas
     * @return $this
     */
    public function addUtaas(Utaa ...$utaas)
    {
        foreach ($utaas as $utaa) {
            if (!$this->utaas->contains($utaa)) {
                $this->utaas->add($utaa);
                $utaa->setDepartement($this);
            }

        }
        return $this;
    }

    /**
     * @param Utaa $utaa
     * @return $this
     */
    public function removeUtaa(Utaa $utaa)
    {
        $this->utaas->remove($utaa);
        $utaa->setDepartement(null);
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
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
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }



}
