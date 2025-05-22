<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Theme
 *
 * @ORM\Table(name="theme")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ThemeRepository")
 */
class Theme
{
    /**
     * @var int
     *
     * @ORM\Column(
     *     name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(
     *     name="libelle_theme",
     *      type="string",
     *      length=50,
     *      unique=true
     * )
     * @Assert\Regex(
     *     pattern="[^a-zA-Z]",
     *     message="Le libellé ne doit contenir que des lettres")
     */
    private $libelleTheme;

    /**
     * @var array
     *
     * @ORM\Column(
     *     name="colonne_theme",
     *     type="json_array"
     * )
     */
    private $colonneTheme;

    /**
     * @var array
     *
     * @ORM\Column(
     *     name="commentaire_colonne",
     *     nullable  = true,
     *     type="json_array"
     * )
     */
    private $commentaireColonne;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Data",
     *     mappedBy="theme",
     *     cascade={"persist", "remove"},
     *     orphanRemoval= true
     *
     * )
     */
    protected $datas;

    /**
     * Theme constructor.
     */
    public function __construct()
    {
        $this->datas = new ArrayCollection();
    }


    /**
     * Get id.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * Get libelleTheme.
     *
     * @return string |null
     *
     */
    public function getLibelleTheme()
    {
        return $this->libelleTheme;
    }

    /**
     * Set libelleTheme.
     *
     * @param string $libelleTheme
     *
     *
     */
    public function setLibelleTheme(string $libelleTheme)
    {
        $this->libelleTheme = $libelleTheme;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getLibelleTheme();
    }

    /**
     * @return array | null
     */
    public function getColonneTheme()
    {

        return json_decode($this->colonneTheme);
    }

    /**
     * @param array $colonneTheme
     */
    public function setColonneTheme(array $colonneTheme)
    {
        $this->colonneTheme = json_encode($colonneTheme);
    }

    /**
     * @return array|null
     */
    public function getCommentaireColonne()
    {

            return json_decode($this->commentaireColonne);

    }

    /**
     * @param array $commentaireColonne | null
     */
    public function setCommentaireColonne(array $commentaireColonne)
    {
        if (null !== $commentaireColonne) $this->commentaireColonne = json_encode($commentaireColonne);
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
                $data->setTheme($this);
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
        $data->setTheme(null);
        return $this;
    }
}
