    <?php

    use Doctrine\DBAL\Types\BinaryType;
    use Doctrine\DBAL\Types\BlobType;
    use Doctrine\ORM\Mapping as ORM; 
    /** @ORM/Entity(repositoryName="App\Entity\VehicleRepository") */
    class Vehicle { 
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
    private $id; 
            /** @ORM\Column(type="string" , length=200)*/
    private $registrationNummer; 
            /** @ORM\Column(type="float" , length=200)*/
    private $rentalPrice; 
            /** @ORM\Column(type="float" , length=200)*/
    private $inssurancePrice; 
            /** @ORM\Column(type="int" , length=200)*/
    private $passengerNumber; 
            /** @ORM\Column(type="blob" , length=200)*/
    private $image; 
            /** @ORM\Column(type="int" )*/
    private $suitcaseNumber; 
            /** @ORM\Column(type="string" , length=200)*/
    private $state; 
            /** @ORM\Column(type="string" , length=200)*/
    private $gearbox; 
            /** @ORM\Column(type="string" , length=200)*/
    private $status; 
           /**@ORM\OneToOne(targetEntity="App\Entity\Agency")
            * @ORM\JoinColumn(name="id_Agency" , referencedColumnName="id")
            */
    private $id_Agency; 

    function __construct( int $registrationNumber, float $rentalPrice, float $inssurancePrice,
    int $passengerNumber , BlobType $image , string   $state,  string $gearbox , string $status)
    {
        $this->registrationNummer = $registrationNumber;
        $this->rentalPrice =$rentalPrice; 
        $this->inssurancePrice=$inssurancePrice; 
        $this->passengerNumber=$passengerNumber; 
        $this->image=$image; 
        $this->state=$state; 
        $this->$gearbox=$gearbox; 
        $this->$status; 

    }

        function getid():int{ 
            return $this ->id ; 
        }
        function setid( int $id ) :void { 
            $this ->id=$id; 
        }

        function getregistrationNumber():string{ 
            return $this->registrationNummer; 
        }
        function setregistrationNumber( int $registrationNumber) :void { 
            $this ->registrationNummer = $registrationNumber; 
        }

        
        function getrentalPrice():float{ 
            return $this ->rentalPrice; 
        }
        function setrentalPrice( int $rentalPrice ) :void { 
            $this ->rentalPrice=$rentalPrice; 
        }
    
        function getinssurancePrice():float{ 
            return $this ->inssurancePrice; 
        }
        function setinssurancePrice( float $inssurancePrice ) :void { 
            $this ->inssurancePrice=$inssurancePrice; 
        }

        function getpassengerNumber():float{ 
            return $this ->passengerNumber; 
        }
        function setpassengerNumber( int $passengerNumber ) :void { 
            $this ->passengerNumber=$passengerNumber; 
        }

        function getimage():BlobType{ 
            return $this ->image; 
        }
        function setimage( BinaryType $image ) :void { 
            $this ->image=$image; 
        }

        function getsuitcaseNumber():int { 
            return $this ->suitcaseNumber; 
        }
        function setsuitcaseNumber( int $suitcaseNumber ) :void { 
            $this ->suitcaseNumber=$suitcaseNumber; 
        }

        function getstate():string{ 
            return $this ->state; 
        }
        function setstate( string $state ) :void { 
            $this ->state=$state; 
        }


        function getgearbox():float{ 
            return $this ->gearbox; 
        }
        function setgearbox( float $gearbox) :void { 
            $this ->gearbox=$gearbox; 
        }

        function getstatus():string{ 
            return $this ->status; 
        }
        function setstatus( string $status) :void { 
            $this ->status=$status; 
        }
        
        function getid_Agency() :int{ 
           return $this->id_Agency; 
        }
        function setid_Agency(int $id_Agency) :void { 
               $this->id_Agency = $id_Agency;
        }
    }

    

    ?>