<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 * @UniqueEntity(fields={"mail"}, message="Il y a déjà un compte avec cet e-mail")
 * @UniqueEntity(fields={"pseudo"}, message="Il y a déjà un compte avec ce pseudo")

 * @Vich\Uploadable()
 */
class Participant implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var File|null
     * @Assert\Image(
     *     mimeTypes= { "image/jpeg", "image/png" }
     * )
     * @Vich\UploadableField(mapping="participant_image", fileNameProperty="filename")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
          * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "{{ limit }} caractères minimum",
     *      maxMessage = "{{ limit }} caractères maximum",
     *      allowEmptyString = false
     * )
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
          * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "{{ limit }} caractères minimum",
     *      maxMessage = "{{ limit }} caractères maximum",
     *      allowEmptyString = false
     * )
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
          * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      minMessage = "{{ limit }} caractères minimum",
     *      maxMessage = "{{ limit }} caractères maximum",
     *      allowEmptyString = false
     * )
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255)
          * @Assert\Length(
     *      min = 4,
     *      max = 50,
     *      minMessage = "{{ limit }} caractères minimum",
     *      maxMessage = "{{ limit }} caractères maximum",
     *      allowEmptyString = false
     * )
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
          * @Assert\Length(
     *      min = 5,
     *      max = 250,
     *      minMessage = "{{ limit }} caractères minimum",
     *      maxMessage = "{{ limit }} caractères maximum",
     *      allowEmptyString = false
     * )
     */
    private $motDePasse;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="participant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus;

    /**
     * @ORM\OneToMany(targetEntity=Inscription::class, mappedBy="participant")
     */
    private $inscriptions;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organisateur")
     */
    private $sorties;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pseudo;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
        $this->sorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection|Inscription[]
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
            $inscription->setParticipant($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getParticipant() === $this) {
                $inscription->setParticipant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): self
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties[] = $sorty;
            $sorty->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        if ($this->sorties->removeElement($sorty)) {
            // set the owning side to null (unless already changed)
            if ($sorty->getOrganisateur() === $this) {
                $sorty->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        $role = null;
        $this->administrateur ? $role =  ["ROLE_ADMIN", "ROLE_USER"] : $role = ["ROLE_USER"];
        return $role;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->motDePasse;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->mail;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     * @return Participant
     */
    public function setFilename(?string $filename): Participant
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     * @return Participant
     */
    public function setImageFile(?File $imageFile): Participant
    {
        $this->imageFile = $imageFile;
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->mail,
            $this->motDePasse,
        ));
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->mail,
            $this->motDePasse,
            ) = unserialize($serialized);
    }
}
