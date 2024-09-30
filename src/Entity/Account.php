<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 50)]
    private ?string $Birthday = null;

    #[ORM\Column(length: 50)]
    private ?string $palceOfBirth = null;

    #[ORM\Column(length: 50)]
    private ?string $typeIdentity = null;

    #[ORM\Column(length: 255)]
    private ?string $fileOne = null;

    #[ORM\Column(length: 255)]
    private ?string $fileTwo = null;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    private ?User $users = null;

    /**
     * @var Collection<int, Wallet>
     */
    #[ORM\OneToMany(targetEntity: Wallet::class, mappedBy: 'account')]
    private Collection $wallets;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'account')]
    private Collection $transactions;

    public function __construct()
    {
        $this->wallets = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getBirthday(): ?string
    {
        return $this->Birthday;
    }

    public function setBirthday(string $Birthday): static
    {
        $this->Birthday = $Birthday;

        return $this;
    }

    public function getPalceOfBirth(): ?string
    {
        return $this->palceOfBirth;
    }

    public function setPalceOfBirth(string $palceOfBirth): static
    {
        $this->palceOfBirth = $palceOfBirth;

        return $this;
    }

    public function getTypeIdentity(): ?string
    {
        return $this->typeIdentity;
    }

    public function setTypeIdentity(string $typeIdentity): static
    {
        $this->typeIdentity = $typeIdentity;

        return $this;
    }

    public function getFileOne(): ?string
    {
        return $this->fileOne;
    }

    public function setFileOne(string $fileOne): static
    {
        $this->fileOne = $fileOne;

        return $this;
    }

    public function getFileTwo(): ?string
    {
        return $this->fileTwo;
    }

    public function setFileTwo(string $fileTwo): static
    {
        $this->fileTwo = $fileTwo;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): static
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return Collection<int, Wallet>
     */
    public function getWallets(): Collection
    {
        return $this->wallets;
    }

    public function addWallet(Wallet $wallet): static
    {
        if (!$this->wallets->contains($wallet)) {
            $this->wallets->add($wallet);
            $wallet->setAccount($this);
        }

        return $this;
    }

    public function removeWallet(Wallet $wallet): static
    {
        if ($this->wallets->removeElement($wallet)) {
            // set the owning side to null (unless already changed)
            if ($wallet->getAccount() === $this) {
                $wallet->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setAccount($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getAccount() === $this) {
                $transaction->setAccount(null);
            }
        }

        return $this;
    }
}
