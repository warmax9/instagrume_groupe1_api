<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?Post $post = null;

    #[ORM\OneToMany(mappedBy: 'commentaire', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'commentairesChildren')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?self $commentaireParent = null;

    #[ORM\OneToMany(mappedBy: 'commentaireParent', targetEntity: self::class)]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private Collection $commentairesChildren;


    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->commentairesChildren = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }


    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setCommentaire($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getCommentaire() === $this) {
                $like->setCommentaire(null);
            }
        }

        return $this;
    }


    public function setCommentaireParent(?self $commentaireParent): static
    {
        $this->commentaireParent = $commentaireParent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCommentairesChildren(): Collection
    {
        return $this->commentairesChildren;
    }

    public function getCommentaireParent(): ?self
    {
        return $this->commentaireParent;
    }

    public function addCommentairesChild(Commentaire $commentairesChild): static
    {
        if (!$this->commentairesChildren->contains($commentairesChild)) {
            $this->commentairesChildren->add($commentairesChild);
            $commentairesChild->setCommentaireParent($this);
        }

        return $this;
    }

    public function removeCommentairesChild(Commentaire $commentairesChild): static
    {
        if ($this->commentairesChildren->removeElement($commentairesChild)) {
            // set the owning side to null (unless already changed)
            if ($commentairesChild->getCommentaireParent() === $this) {
                $commentairesChild->setCommentaireParent(null);
            }
        }

        return $this;
    }



}
