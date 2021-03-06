<?php

namespace Bdloc\AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// DON'T forget this use statement!!!
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @UniqueEntity("username", message="Ce pseudo est déjà utilisé !", groups={"registration"})
 * @UniqueEntity("email", message="Vous avez déjà un compte ici !", groups={"registration"})
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="user")
 * 
 * @ORM\Entity(repositoryClass="Bdloc\AppBundle\Entity\UserRepository")
 */
class User implements UserInterface
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
     * @Assert\NotBlank(message="Veuillez fournir un pseudo.", groups={"registration"})
     * @Assert\Length(
     *      min = "2",
     *      max = "255",
     *      minMessage = "Votre pseudo doit faire au moins {{ limit }} caractères",
     *      maxMessage = "Votre pseudo ne peut pas être plus long que {{ limit }} caractères"
     *)
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    protected $username;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Veuillez fournir un email.")
     * @Assert\Email(message="Format invalide")
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    protected $email;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez fournir un mot de passe.", groups={"registration"})
     * @Assert\Length(
     *      min = "6",
     *      minMessage = "Votre mot de passe doit faire au moins {{ limit }} caractères"
     * )
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    protected $token;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    protected $salt;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array", nullable=true)
     */
    protected $roles;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez fournir un prénom.", groups={"registration"})
     * @Assert\Length(
     *      min = "2",
     *      max = "255",
     *      minMessage = "Votre prénom doit faire au moins {{ limit }} caractères",
     *      maxMessage = "Votre prénom ne peut pas être plus long que {{ limit }} caractères"
     * )
     * @ORM\Column(name="firstName", type="string", length=255)
     */
    protected $firstName;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez fournir un nom.", groups={"registration"})
     * @Assert\Length(
     *      min = "2",
     *      max = "255",
     *      minMessage = "Votre nom doit faire au moins {{ limit }} caractères",
     *      maxMessage = "Votre nom ne peut pas être plus long que {{ limit }} caractères"
     * )
     * @ORM\Column(name="lastName", type="string", length=255)
     */
    protected $lastName;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez fournir un code postal.", groups={"registration"})
     * @Assert\Regex(pattern= "#^[0-9]{5,5}$#", message="Code postal de 5 chiffres")
     * @ORM\Column(name="zip", type="string", length=5, nullable=true)
     */
    protected $zip;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez fournir une adresse.", groups={"registration"})
     * @Assert\Length(
     *      min = "2",
     *      max = "255",
     *      minMessage = "Votre adresse doit faire au moins {{ limit }} caractères",
     *      maxMessage = "Votre adresse ne peut pas être plus long que {{ limit }} caractères"
     *)
     * @ORM\Column( name="address", type="string", length=20, nullable=true)
     */
    protected $address;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez fournir un numéro de téléphone.", groups={"registration"})
     * @Assert\Regex(pattern= "#^0[1-8]([-.\s]?[0-9]{2}){4}$|^\+[1-9]{2}(\(0\))?[1-8]([-.\s]?[0-9]{2}){4}$|^00[1-9]{2}[1-8]([-.\s]?[0-9]{2}){4}$#", message="Format invalide")
     * @ORM\Column( name="phone", type="string", length=20, nullable=true)
     */
    protected $phone;

    /**
     * @var boolean
     *
     * @ORM\Column( name="isEnabled", type="boolean", nullable=true)
     */
    protected $isEnabled;

    /**
     * @var \DateTime
     *
     * @ORM\Column( name="dateCreated", type="datetime", nullable=true)
     */
    protected $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column( name="dateModified", type="datetime", nullable=true)
     */
    protected $dateModified;

     /**
    * @ORM\ManyToOne(targetEntity="DropSpot", inversedBy="users")
    */
    protected $dropspot;

     /**
    * @ORM\OneToMany(targetEntity="Cart", mappedBy="users")
    */
    protected $carts;

    /**
     * @var string
     *
     * @ORM\Column(name="subscriptionType", type="string", length=1)
     */
    protected $subscriptionType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="subscriptionRenewal", type="date")
     */
    protected $subscriptionRenewal;

    /**
     *
     *@ORM\OnetoMany(targetEntity="Paiement", mappedBy="user")
     */
    protected $paiements;

    /**
     *
     *@ORM\OnetoMany(targetEntity="Fine", mappedBy="user")
     */
    protected $fines;

     /**
     *
     *@ORM\OnetoMany(targetEntity="CreditCard", mappedBy="user")
     */
    protected $creditCards;

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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array 
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set zip
     *
     * @param string $zip
     * @return User
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string 
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set isEnabled
     *
     * @param boolean $isEnabled
     * @return User
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return boolean 
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return User
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
     * @return User
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    //requis par le UserInterface
    public function eraseCredentials(){
        $this->password = null;
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
     * @ORM\PrePersist
    */
    public function beforeInsert(){
        $this->setDateCreated( new \DateTime() );
        $this->setDateModified( new \DateTime() );
    }

    /**
     * @ORM\PreUpdate
    */
    public function beforeEdit(){
        $this->setDateModified( new \DateTime() );
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->carts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add carts
     *
     * @param \Bdloc\AppBundle\Entity\Cart $carts
     * @return User
     */
    public function addCart(\Bdloc\AppBundle\Entity\Cart $carts)
    {
        $this->carts[] = $carts;

        return $this;
    }

    /**
     * Remove carts
     *
     * @param \Bdloc\AppBundle\Entity\Cart $carts
     */
    public function removeCart(\Bdloc\AppBundle\Entity\Cart $carts)
    {
        $this->carts->removeElement($carts);
    }

    /**
     * Get carts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCarts()
    {
        return $this->carts;
    }

    /**
     * Set dropspot
     *
     * @param \Bdloc\AppBundle\Entity\DropSpot $dropspot
     * @return User
     */
    public function setDropspot(\Bdloc\AppBundle\Entity\DropSpot $dropspot = null)
    {
        $this->dropspot = $dropspot;

        return $this;
    }

    /**
     * Get dropspot
     *
     * @return \Bdloc\AppBundle\Entity\DropSpot 
     */
    public function getDropspot()
    {
        return $this->dropspot;
    }

    /**
     * Set subscriptionType
     *
     * @param string $subscriptionType
     * @return User
     */
    public function setSubscriptionType($subscriptionType)
    {
        $this->subscriptionType = $subscriptionType;

        return $this;
    }

    /**
     * Get subscriptionType
     *
     * @return string 
     */
    public function getSubscriptionType()
    {
        return $this->subscriptionType;
    }

    /**
     * Set subscriptionRenewal
     *
     * @param \DateTime $subscriptionRenewal
     * @return User
     */
    public function setSubscriptionRenewal($subscriptionRenewal)
    {
        $this->subscriptionRenewal = $subscriptionRenewal;

        return $this;
    }

    /**
     * Get subscriptionRenewal
     *
     * @return \DateTime 
     */
    public function getSubscriptionRenewal()
    {
        return $this->subscriptionRenewal;
    }

    /**
     * Add paiements
     *
     * @param \Bdloc\AppBundle\Entity\Paiement $paiements
     * @return User
     */
    public function addPaiement(\Bdloc\AppBundle\Entity\Paiement $paiements)
    {
        $this->paiements[] = $paiements;

        return $this;
    }

    /**
     * Remove paiements
     *
     * @param \Bdloc\AppBundle\Entity\Paiement $paiements
     */
    public function removePaiement(\Bdloc\AppBundle\Entity\Paiement $paiements)
    {
        $this->paiements->removeElement($paiements);
    }

    /**
     * Get paiements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPaiements()
    {
        return $this->paiements;
    }

    /**
     * Add fines
     *
     * @param \Bdloc\AppBundle\Entity\Fine $fines
     * @return User
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
     * Add creditCards
     *
     * @param \Bdloc\AppBundle\Entity\CreditCard $creditCards
     * @return User
     */
    public function addCreditCard(\Bdloc\AppBundle\Entity\CreditCard $creditCards)
    {
        $this->creditCards[] = $creditCards;

        return $this;
    }

    /**
     * Remove creditCards
     *
     * @param \Bdloc\AppBundle\Entity\CreditCard $creditCards
     */
    public function removeCreditCard(\Bdloc\AppBundle\Entity\CreditCard $creditCards)
    {
        $this->creditCards->removeElement($creditCards);
    }

    /**
     * Get creditCards
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCreditCards()
    {
        return $this->creditCards;
    }
}
