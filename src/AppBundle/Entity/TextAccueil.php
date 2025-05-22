<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 17/09/18
 * Time: 11:11
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TextAccueil
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TextAccueilRepository")
 * @ORM\Table(name="textAccueil")
 * @ORM\HasLifecycleCallbacks
 */
class TextAccueil
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
     * @var \DateTime
     *
     * @ORM\Column(
     *     name="updated_at",
     *     type="datetime",
     *     nullable=true
     * )
     * @Assert\DateTime
     */
    private $updatedAt;


    /**
     * @var string
     *
     * @ORM\Column(
     *
     *     name="text_accueil",
     *     type="text"
     * )
     *
     * @Assert\NotBlank(message="Veuillez renseigner le titre ")
     */
    private $textAccueil;

    /**
     * @var string
     *
     * @ORM\Column(
     *
     *     name="titre_accueil",
     *     type="text"
     *
     * )
     * @Assert\NotBlank(message="Veuillez renseigner le texte d'accueil ")
     */
    private $titreAccueil;

    /**
     * @var boolean
     *
     * @ORM\Column(
     *
     *     name="is_obsolete",
     *     type="boolean"
     *)
     */
    private $is_obsolete = true;


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
     * @ORM\PostLoad()
     */
    public function postLoad()
    {

        $this->updatedAt = new \DateTime();

    }


    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preload()
    {
        $this->updatedAt = new \DateTime();


    }


    /**
     * get Updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    /**
     * set Updated_at
     *
     * @param \DateTime $updatedAt
     *
     * @return TextAccueil
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * get textAccueil
     *
     * @return string | null
     */
    public function getTextAccueil()
    {
        return $this->textAccueil;
    }

    /**
     * @param string $textAccueil
     *
     * @return TextAccueil
     */
    public function setTextAccueil(string $textAccueil)
    {
        $this->textAccueil = $textAccueil;
        return $this;
    }

    /**
     * get titreAccueil
     *
     * @return string | null
     */
    public function getTitreAccueil()
    {
        return $this->titreAccueil;
    }

    /**
     * @param string $titreAccueil
     *
     * @return TextAccueil
     */
    public function setTitreAccueil(string $titreAccueil)
    {
        $this->titreAccueil = $titreAccueil;

        return $this;
    }


    /**
     * @return bool
     */
    public function isObsolete(): bool
    {
        return $this->is_obsolete;
    }

    /**
     * @param bool $is_obsolete
     */
    public function setIsObsolete(bool $is_obsolete)
    {
        $this->is_obsolete = $is_obsolete;
    }

}