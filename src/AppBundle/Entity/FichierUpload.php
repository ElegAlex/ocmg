<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 17/07/18
 * Time: 14:35
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FichierUploadRepository")
 * @ORM\Table(name="fichierUpload")
 * @ORM\HasLifecycleCallbacks
 *
 */
class FichierUpload
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
     */
    private $updatedAt;



    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
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
     * @return FichierUpload
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


    /**
     * @var string
     *
     * @ORM\Column(
     *     name="filename",
     *     type="string",
     *     length=255,
     *     nullable=true,
     *     unique=false
     * )
     *
     * @Assert\NotBlank(message="Ce fichier doit être unique")
     */
    private $filename;

    /**
     * @Assert\File(
     *     maxSize="8Mi",
     *     maxSizeMessage="fichier trop volumieux",
     *     mimeTypes={ "text/plain" },
     *     mimeTypesMessage="L'extension du fichier n 'est pas valide",
     *     disallowEmptyMessage="un fichier vide n est pas autorisé",
     *     uploadFormSizeErrorMessage="le fichier téléchargé est trop important par rapport par rapport à la taille de fichier maximum de téléchargement"
     * )
     *
     * @var UploadedFile
     */
    private $file;


    /**
     * @var integer
     *
     * @ORM\Column(
     *
     *     name="filesize",
     *     type="integer",
     *     nullable=true
     *
     * )
     */
    private $fileSize;

    /**
     *
     * chemin du fichier tmp
     *
     * @var string
     *
     * @ORM\Column(
     *
     *     name="path",
     *     type="string",
     *     nullable=true
     *
     * )
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(
     *
     *     name="originalname",
     *     type="string",
     *     nullable=true
     *
     * )
     *
     */
    private $original_name;

    /**
     * @var
     *
     * @ORM\Column(
     *
     *     name="error_code",
     *     type="string",
     *     nullable=true
     *
     * )
     */
    private $errorCode;

    private $tempFile;

    private $oldFile;


    /**
     * @var
     *
     * @ORM\Column(
     *
     *     name="nb_col_upload_file",
     *     type="integer"
     *
     * )
     */
    private $nbColUploadFile;

    /**
     * @var
     *
     * @ORM\Column(
     *     name="nb_row_upload_file",
     *     type="integer"
     *
     * )
     */
    private $nbRowUploadFile;
    /**
     * @var
     *
     * @ORM\Column(
     *
     *     name="nb_row_uploaded",
     *     type="integer"
     *
     * )
     *
     */
    private $nbRowUploaded;

    


    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * Get filename
     *
     * @return string | null
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return FichierUpload
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get file
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set File
     *
     * @param UploadedFile $file
     *
     * @return FichierUpload
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * get file_size
     *
     * @return integer | null
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * set fileSize
     *
     * @param int $fileSize
     *
     * @return FichierUpload
     */
    public function setFileSize(int $fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * get Path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * set Path
     *
     * @param string $path
     *
     * @return FichierUpload
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * get Original_name
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->original_name;
    }

    /**
     * set Original_name
     *
     * @param string $original_name
     *
     * @return FichierUpload
     */
    public function setOriginalName(string $original_name)
    {
        $this->original_name = $original_name;

        return $this;
    }


    /**
     * get Error_code
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * set Error_code
     *
     * @param string $errorCode
     *
     * @return FichierUpload
     */
    public function setErrorCode(string $errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }


    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {

        $this->tempFile = $this->getAbsolutePath();
        $this->oldFile = $this->getPath();
        $this->updatedAt = new \DateTime();


        if (null !== $this->file) $this->path = md5(uniqid('', true)) . '.' . $this->file->guessClientExtension();

    }

    public function getAbsolutePath()
    {

        return null === $this->path
            ? null
            : $this->getUploadRootDir() . $this->path;

    }

    public function getUploadRootDir(): string
    {
        return __DIR__ . 'tmp';

    }

    /**
     * @ORM\PostUpdate()
     */
    public function upload()
    {

        if (null !== $this->file) {

            $this->file->move($this->getUploadRootDir(), $this->path);
            unset($this->file);

            if ($this->tempFile !== null) unlink($this->tempFile);

        }

    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {

        $this->tempFile = $this->getAbsolutePath();

    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if (file_exists($this->file)) unlink($file);

    }


    /**
     * Set nbColUploadFile.
     *
     * @param int $nbColUploadFile
     *
     * @return FichierUpload
     */
    public function setNbColUploadFile($nbColUploadFile)
    {
        $this->nbColUploadFile = $nbColUploadFile;

        return $this;
    }

    /**
     * Get nbColUploadFile.
     *
     * @return int
     */
    public function getNbColUploadFile()
    {
        return $this->nbColUploadFile;
    }

    /**
     * Set nbRowUploadFile.
     *
     * @param int $nbRowUploadFile
     *
     * @return FichierUpload
     */
    public function setNbRowUploadFile($nbRowUploadFile)
    {
        $this->nbRowUploadFile = $nbRowUploadFile;

        return $this;
    }

    /**
     * Get nbRowUploadFile.
     *
     * @return int
     */
    public function getNbRowUploadFile()
    {
        return $this->nbRowUploadFile;
    }

    /**
     * Set nbRowUploaded.
     *
     * @param int $nbRowUploaded
     *
     * @return FichierUpload
     */
    public function setNbRowUploaded($nbRowUploaded)
    {
        $this->nbRowUploaded = $nbRowUploaded;

        return $this;
    }

    /**
     * Get nbRowUploaded.
     *
     * @return int
     */
    public function getNbRowUploaded()
    {
        return $this->nbRowUploaded;
    }
}
