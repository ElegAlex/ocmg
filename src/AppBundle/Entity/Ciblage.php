<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 18/09/18
 * Time: 14:51
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CiblageRepository")
 * @ORM\Table(name="ciblage")
 * @UniqueEntity(fields="ciblage",message="Ce ciblage existe dejà.")
 */
class Ciblage
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
     *     name="ciblageName",
     *     type="string",
     *     length=255,
     *     nullable=true,
     *     unique=true
     * )
     *
     * @Assert\NotBlank(message="Le thème ciblé doit être renseigné")
     * @Assert\Type(
     *     type="string",
     *     message="La valeur {{ value }} n'est pas de type texte valide {{ type }}.")
     *
     * @Assert\Regex(
     *     pattern="/^\w+/",
     *     message="Le libellé ne doit contenir que des lettres")
     */
    private $ciblage;

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
     * @return null|string
     */
    public function __toString()
    {
        return $this->getCiblage();

    }

    /**
     * @return string | null
     */
    public function getCiblage()
    {
        return $this->ciblage;
    }

    /**
     * @param string $ciblage
     */
    public function setCiblage(string $ciblage)
    {
        $this->ciblage = $ciblage;
    }

}