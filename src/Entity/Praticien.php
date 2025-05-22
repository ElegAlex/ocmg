<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Praticien
 *
 * @ORM\Table(name="praticien")
 * @ORM\Entity(repositoryClass="App\Repository\PraticienRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Praticien
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
     * @ORM\Column(name="code_praticien", unique = true, type="string", length=13)
     */
    private $codePraticien;

    /**
     * @var int
     *
     * @ORM\Column(name="cle_prat", type="smallint", nullable=true)
     */
    private $clePrat;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_prat", type="string", length=50, nullable=true)
     */
    private $nomPrat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="prenom_prat", type="string", length=50, nullable=true)
     */
    private $prenomPrat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_at", type="datetime")
     */
    private $updateAt;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Utaa",
     *     inversedBy="praticiens"
     * )
     * @ORM\JoinColumn(
     *     name="utaa_id",
     *     referencedColumnName="id",
     *     nullable=true,
     *     onDelete="CASCADE"
     * )
     */
    private $utaa;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Ville",
     *     inversedBy="praticiens"
     * )
     * @ORM\JoinColumn(
     *     name="ville_id",
     *     referencedColumnName="id",
     *     nullable=true,
     *     onDelete="CASCADE"
     *)
     */
    private $ville;


    /**
     * @ORM\OneToMany(
     *     targetEntity = "Data",
     *     mappedBy = "praticien",
     *     cascade = { "persist", "remove" }
     *
     * )
     */
    private $datas;

    /**
     * Praticien constructor.
     */
    public function __construct()
    {
        $this->datas = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getUtaa()
    {
        return $this->utaa;
    }

    /**
     * @param mixed $utaa
     */
    public function setUtaa(Utaa $utaa)
    {
        $this->utaa = $utaa;
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

    public function __toString()
    {
        return $this->codePraticien;
    }

    /**
     * Get codePraticien.
     *
     * @return string
     */
    public function getCodePraticien()
    {
        return $this->codePraticien;
    }

    /**
     * Set codePraticien.
     *
     * @param string $codePraticien
     *
     * @return Praticien
     */
    public function setCodePraticien($codePraticien)
    {
        $this->codePraticien = $codePraticien;

        return $this;
    }

    /**
     * Get clePrat.
     *
     * @return int
     */
    public function getClePrat()
    {
        return $this->clePrat;
    }

    /**
     * Set clePrat.
     *
     * @param int $clePrat
     *
     * @return Praticien
     */
    public function setClePrat($clePrat)
    {
        $this->clePrat = $clePrat;

        return $this;
    }

    /**
     * Get nomPrat.
     *
     * @return string
     */
    public function getNomPrat()
    {
        return $this->nomPrat;
    }

    /**
     * Set nomPrat.
     *
     * @param string $nomPrat
     *
     * @return Praticien
     */
    public function setNomPrat($nomPrat)
    {
        $this->nomPrat = $nomPrat;

        return $this;
    }

    /**
     * Get prenomPrat.
     *
     * @return string|null
     */
    public function getPrenomPrat()
    {
        return $this->prenomPrat;
    }

    /**
     * Set prenomPrat.
     *
     * @param string|null $prenomPrat
     *
     * @return Praticien
     */
    public function setPrenomPrat($prenomPrat = null)
    {
        $this->prenomPrat = $prenomPrat;

        return $this;
    }

    /**
     * Get updateAt.
     *
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setUpdateAt()
    {
        $this->updateAt = new \DateTime();

    }

    /**
     * @return mixed
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * @param mixed $ville
     */
    public function setVille($ville)
    {
        $this->ville = $ville;
    }

    /**
     * @return ArrayCollection
     */
    public function getDatas(): ArrayCollection
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
                $data->setPraticien($this);
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
        $data->setPraticien(null);
        return $this;
    }
}
