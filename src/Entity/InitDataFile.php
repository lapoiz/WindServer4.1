<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\InitDataFileRepository")
 */
class InitDataFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please, upload the XLSX file.")
     * @Assert\File(mimeTypes={ "application/octet-stream", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" })
     */
    private $dataFile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataFile()
    {
        return $this->dataFile;
    }

    public function setDataFile($dataFile)
    {
        $this->dataFile = $dataFile;

        return $this;
    }
}
