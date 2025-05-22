<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Utaa
 *
 * @ORM\Table(name="utaa")
 * @ORM\Entity(repositoryClass="App\Repository\UtaaRepository")
 */
class Utaa
{
    /**
     * @var int
     *
     * @ORM\Column(
     *     name="id",
     *      type="integer"
     * )
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(
     *     name="code_utaa",
     *     type="string",
     *     nullable=false
     * )
     */
    private $codeUtaa;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Departement",
     *     inversedBy="utaas"
     * )
     * @ORM\JoinColumn(
     *     name="departement_id",
     *     referencedColumnName="id",
     *     nullable=true,
     *     onDelete="CASCADE"
     * )
     */
    private $departement;


    /**
     * @ORM\OneToMany(
     *     targetEntity="Praticien",
     *     mappedBy="utaa",
     *     cascade={"persist", "remove"}
     * )
     */
    private $praticiens;

    /**
     * Utaa constructor.
     */
    public function __construct()
    {
        $this->praticiens = new ArrayCollection();
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
     * Get codeUtaa.
     * @return string
     */
    public function getCodeUtaa()
    {
        return $this->codeUtaa;
    }

    /**
     * Set codeUtaa.
     *
     * @param string $codeUtaa |null
     *
     * @return Utaa
     */
    public function setCodeUtaa($codeUtaa = null)
    {
        $this->codeUtaa = $codeUtaa;
    }


    public function __toString()
    {
        return $this->getCodeUtaa();
    }

    /**
     * @return mixed
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * @param $departement
     */
    public function setDepartement($departement)
    {
        $this->departement = $departement;
    }

    /**
     * @return ArrayCollection
     */
    public function getPraticiens()
    {
        return $this->praticiens;
    }

    /**
     * @param Praticien ...$praticiens
     * @return $this
     */
    public function addPraticiens(Praticien ...$praticiens)
    {
        foreach ($praticiens as $praticien) {
            if (!$this->praticiens->contains($praticien)) {
                $this->praticiens->add($praticien);
                $praticien->setUtaa($this);
            }
        }

        return $this;
    }

    /**
     * @param Praticien $praticien
     * @return $this
     */
    public function removePraticien(Praticien $praticien)
    {
        $this->praticiens->remove($praticien);
        $praticien->setUtaa(null);
        return $this;
    }

}
