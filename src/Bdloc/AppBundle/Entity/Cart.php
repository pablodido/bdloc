<?php

namespace Bdloc\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bdloc\AppBundle\Entity\CartRepository")
 */
class Cart
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=50)
     */
    protected $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    protected $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModified", type="datetime")
     */
    protected $dateModified;

     /**
    * @ORM\ManyToOne(targetEntity="User", inversedBy="carts")
    */
    protected $users;

     /**
    * @ORM\OneToMany(targetEntity="CartItem", mappedBy="cart")
    */
    protected $cartitems;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDelivery", type="datetime",nullable=true)
     */
    protected $dateDelivery;
    
     /**
    *
    *@ORM\OneToMany(targetEntity="Fine", mappedBy="cart")
    */
    protected $fines;

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
     * Set status
     *
     * @param string $status
     * @return Cart
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
     * @return Cart
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
     * @return Cart
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
     * Constructor
     */
    public function __construct()
    {
        $this->cartitems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set user
     *
     * @param \Bdloc\AppBundle\Entity\User $user
     * @return Cart
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

    /**
     * Add cartitems
     *
     * @param \Bdloc\AppBundle\Entity\CartItem $cartitems
     * @return Cart
     */
    public function addCartitem(\Bdloc\AppBundle\Entity\CartItem $cartitems)
    {
        $this->cartitems[] = $cartitems;

        return $this;
    }

    /**
     * Remove cartitems
     *
     * @param \Bdloc\AppBundle\Entity\CartItem $cartitems
     */
    public function removeCartitem(\Bdloc\AppBundle\Entity\CartItem $cartitems)
    {
        $this->cartitems->removeElement($cartitems);
    }

    /**
     * Get cartitems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCartitems()
    {
        return $this->cartitems;
    }

    /**
     * Set dateDelivery
     *
     * @param \DateTime $dateDelivery
     * @return Cart
     */
    public function setDateDelivery($dateDelivery)
    {
        $this->dateDelivery = $dateDelivery;

        return $this;
    }

    /**
     * Get dateDelivery
     *
     * @return \DateTime 
     */
    public function getDateDelivery()
    {
        return $this->dateDelivery;
    }

    /**
     * Add fines
     *
     * @param \Bdloc\AppBundle\Entity\Fine $fines
     * @return Cart
     */
    public function addFine(\Bdloc\AppBundle\Entity\Fine $fines)
    {
        $this->fines[] = $fines;

        return $this;
    }

    /**
     * Remove fines
     *
     * @param \Bdloc\AppBundle\Entity\Fine $fines
     */
    public function removeFine(\Bdloc\AppBundle\Entity\Fine $fines)
    {
        $this->fines->removeElement($fines);
    }

    /**
     * Get fines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFines()
    {
        return $this->fines;
    }

    /**
     * Set users
     *
     * @param \Bdloc\AppBundle\Entity\User $users
     * @return Cart
     */
    public function setUsers(\Bdloc\AppBundle\Entity\User $users = null)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return \Bdloc\AppBundle\Entity\User 
     */
    public function getUsers()
    {
        return $this->users;
    }
}
