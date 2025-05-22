<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Ville
 *
 * @ORM\Table(name="ville")
 * @ORM\Entity(repositoryClass="App\Repository\VilleRepository")
 */
class Ville
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
     * @var string
     *
     * @ORM\Column(
     *     name="libelle_ville",
     *      type="string",
     *      length=50,
     *     nullable=true
     * )
     */
    private $libelleVille;


    /**
     * @ORM\OneToMany(
     *     targetEntity="Praticien",
     *     mappedBy="ville",
     *     cascade={"persist", "remove"}
     * )
     */
    private $praticiens;

    /**
     * Ville constructor.
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
     * Get libelleVille.
     *
     * @return string
     */
    public function getLibelleVille()
    {
        return $this->libelleVille;
    }

    /**
     * Set libelleVille.
     *
     * @param string $libelleVille
     *
     * @return Ville
     */
    public function setLibelleVille($libelleVille)
    {
        $this->libelleVille = $libelleVille;

        return $this;
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
                $this->setVille($this);
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
        $this->setVille(null);
        return $this;
    }

}
