<?php

namespace App\Entity;

use App\Repository\FilmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Count;

/**
 * @ORM\Entity(repositoryClass=FilmRepository::class)
 * @UniqueEntity(
 *     fields={"titre", "realisateur"},
 *     errorPath = "titre",
 *     message="Ce titre de film est déjà enregistré pour ce réalisateur !"
 * )
 */
class Film
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le titre du film doit être renseigné !")
     * @Assert\Length(
     *      min = 4,
     *      max = 100,
     *      minMessage = "Le titre doit faire au minimum {{ limit }} caractères !",
     *      maxMessage = "Le titre doit faire au maximum {{ limit }} caractères !"
     * )
     */
    private $titre;

    /**
     * @ORM\ManyToOne(targetEntity=Realisateur::class, inversedBy="films")
     * @Assert\NotNull(message="Un nom de réalisateur doit être choisi")
     */
    private $realisateur;

    /**
     * @ORM\ManyToMany(targetEntity=Acteur::class, inversedBy="films")
     * @ORM\OrderBy({"nom" = "ASC"})
     * @Count(
     *     min = 1,
     *     minMessage = "Au moins un acteur doit être selectionné"
     * )
     */
    private $acteurs;

    /**
     * @ORM\OneToMany(targetEntity=Hashtag::class, mappedBy="film", cascade={"persist", "remove"})
     * @ORM\OrderBy({"terme" = "ASC"})
     * @Assert\Valid()
     */
    private $hashtags;

    public function __construct()
    {
        $this->acteurs = new ArrayCollection();
        $this->hashtags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getRealisateur(): ?Realisateur
    {
        return $this->realisateur;
    }

    public function setRealisateur(?Realisateur $realisateur): self
    {
        $this->realisateur = $realisateur;

        return $this;
    }

    /**
     * @return Collection|Acteur[]
     */
    public function getActeurs(): Collection
    {
        return $this->acteurs;
    }

    public function addActeur(Acteur $acteur): self
    {
        if (!$this->acteurs->contains($acteur)) {
            $this->acteurs[] = $acteur;
        }

        return $this;
    }

    public function removeActeur(Acteur $acteur): self
    {
        if ($this->acteurs->contains($acteur)) {
            $this->acteurs->removeElement($acteur);
        }

        return $this;
    }

    /**
     * @return Collection|Hashtag[]
     */
    public function getHashtags(): Collection
    {
        return $this->hashtags;
    }

    public function addHashtag(Hashtag $hashtag): self
    {
        if (!$this->hashtags->contains($hashtag)) {
            $this->hashtags[] = $hashtag;
            $hashtag->setFilm($this);
        }

        return $this;
    }

    public function removeHashtag(Hashtag $hashtag): self
    {
        if ($this->hashtags->contains($hashtag)) {
            $this->hashtags->removeElement($hashtag);
            // set the owning side to null (unless already changed)
            if ($hashtag->getFilm() === $this) {
                $hashtag->setFilm(null);
            }
        }

        return $this;
    }
}
