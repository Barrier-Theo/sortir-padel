<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Sortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     * min = 1,
     * max = 50,
     * minMessage = "{{ limit }} caractères minimum",
     * maxMessage = "{{ limit }} caractères maximum",
     * allowEmptyString = false
     * )
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Range(
     *      min = 1,
     *      max = 1000,
     *      notInRangeMessage = "Votre sortie doit durer au minimum {{ min }} heure",
     * )
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     */
    private $dateLimiteInscription;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Range(
     *      min = 1,
     *      max = 500,
     *      notInRangeMessage = "Votre sortie doit pouvoir accueillir au minimum {{ min }} personne",
     * )
     */
    private $nbInscriptionMax;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Length(
     * min = 0,
     * max = 255,
     * minMessage = "{{ limit }} caractères minimum",
     * maxMessage = "{{ limit }} caractères maximum",
     * allowEmptyString = false
     * )
     */
    private $infosSortie;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Lieu;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="sorties")
     */
    private $Campus;

    /**
     * @ORM\OneToMany(targetEntity=Inscription::class, mappedBy="Sortie")
     */
    private $inscriptions;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisateur;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
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

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionMax(): ?int
    {
        return $this->nbInscriptionMax;
    }

    public function setNbInscriptionMax(int $nbInscriptionMax): self
    {
        $this->nbInscriptionMax = $nbInscriptionMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(string $infosSortie): self
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->Lieu;
    }

    public function setLieu(?Lieu $Lieu): self
    {
        $this->Lieu = $Lieu;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->Campus;
    }

    public function setCampus(?Campus $Campus): self
    {
        $this->Campus = $Campus;

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
            $inscription->setSortie($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getSortie() === $this) {
                $inscription->setSortie(null);
            }
        }

        return $this;
    }

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participant $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    // Check if sortie is full 
    public function isSortieFull(): bool
    {
        if (count($this->getInscriptions()) == $this->getNbInscriptionMax()) {
            return true;
        } else {
            return false;
        }
    }

    public function isSortieCloturee(): bool
    {
        $dateNow = new DateTime('now');
        if ($dateNow > $this->getDateLimiteInscription()) {
            return true;
        } else {
            return false;
        }
    }
}
