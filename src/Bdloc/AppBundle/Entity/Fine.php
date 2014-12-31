<?php

namespace Bdloc\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fine
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bdloc\AppBundle\Entity\FineRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Fine
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=50)
     */
    private $reason;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=50)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModified", type="datetime")
     */
    private $dateModified;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePaid", type="datetime")
     */
    private $datePaid;

    /**
     * @var string
     *
     * @ORM\Column(name="montant", type="decimal")
     */
    private $montant;
    
    /**
    *
    *@ORM\ManyToOne(targetEntity="Cart", inversedBy="fines")
    */
    private $cart;

     /**
     *
     *@ORM\ManytoOne(targetEntity="User", inversedBy="fines")
     */
    private $user;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set reason
     *
     * @param string $reason
     * @return Fine
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string 
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Fine
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Fine
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @return Fine
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime 
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set datePaid
     *
     * @param \DateTime $datePaid
     * @return Fine
     */
    public function setDatePaid($datePaid)
    {
        $this->datePaid = $datePaid;

        return $this;
    }

    /**
     * Get datePaid
     *
     * @return \DateTime 
     */
    public function getDatePaid()
    {
        return $this->datePaid;
    }

    /**
     * Set montant
     *
     * @param string $montant
     * @return Fine
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return string 
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set cart
     *
     * @param \Bdloc\AppBundle\Entity\Cart $cart
     * @return Fine
     */
    public function setCart(\Bdloc\AppBundle\Entity\Cart $cart = null)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Get cart
     *
     * @return \Bdloc\AppBundle\Entity\Cart 
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Set user
     *
     * @param \Bdloc\AppBundle\Entity\User $user
     * @return Fine
     */
    public function setUser(\Bdloc\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Bdloc\AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
