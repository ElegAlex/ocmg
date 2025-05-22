<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 22/10/18
 * Time: 16:41
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Region
 *
 * @ORM\Table(name="region")
 * @ORM\Entity(repositoryClass="App\Repository\RegionRepository")
 */
class Region
{

    /**
     * @var int
     *
     * @ORM\Column(
     *     name="id",
     *      type="integer"
     * )
     * @ORM\Id
     *
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(
     *     name="libelle_region",
     *     type="string",
     *     nullable=false
    )
     */
    private $libelle_region;

    /**
     * @var boolean
     *
     * @ORM\Column(
     *     name="is_active",
     *     type="boolean",
     *     length=1,
     *
     *     options={"default":false}
     * )
     */
    private $is_active;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Departement",
     *     mappedBy="region",
     *     cascade={"persist","remove"}
     *)
     *
     */
    private $departements;

    /**
     * Region constructor.
     */
    public function __construct()
    {
        $this->departements = new ArrayCollection();

    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLibelleRegion()
    {
        return $this->libelle_region;
    }

    /**
     * @param string $libelle_region
     */
    public function setLibelleRegion(string $libelle_region)
    {
        $this->libelle_region = $libelle_region;
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
     * @return mixed
     */
    public function getDepartements()
    {
        return $this->departements;
    }

    /**
     * @param Departement ...$departements
     * @return $this
     */
    public function addDepartements(Departement ...$departements)
    {
        foreach ($departements as $departement) {
            if (!$this->departements->contains($departement)) {
                $this->departements->add($departement);
                $departement->setRegion($this);
            }

        }
        return $this;
    }

    /**
     * @param Departement $departement
     * @return $this
     */
    public function removeDepartement(Departement $departement)
    {
        $this->departements->remove($departement);
        $departement->setRegion(null);
        return $this;
    }
}