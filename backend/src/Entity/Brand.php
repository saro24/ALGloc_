<?php
    namespace App\Entity; 
    use Doctrine\Common\Collections\Collection;
    use  Doctrine\ORM\Mapping as ORM ;
    use Hateoas\Configuration\Annotation as Hateoas;
    use JMS\Serializer\Annotation as Serializer;

   /**
    *   @ORM\Entity(repositoryClass="App\Repository\BrandRepository")
    *   @Serializer\XmlRoot("Brand")
    *   @Hateoas\Relation("self" , href=
    *   @Hateoas\Route( "get_brand" , parameters={"id" = "expr(object.getid())"})
    * )
   */
 
   class Brand { 
            /** 
             * @ORM\Id; 
             * @ORM\GeneratedValue
             * @ORM\Column(type="integer")
             * @Serializer\XmlAttribute 
             */

            private  $id; 
            /**
             * @ORM\Column(type="string" , length=200)
            */
            private   $name_; // model name_
            /**
              * @ORM\OneToMany(targetEntity="App\Entity\Model" , mappedBy="brand")
             */
             /** @Serializer\Exclude */

            private $models; 
            function __construct()
            { 
                
            }
            function getid() :int 
            {  
            return $this ->id ; 
            }
            function setid(int $id) :void { 
                $this ->id =$id ; 
            }

            function getname() :string
            {  
            return $this ->name_ ; 
            }
            function setname(string $name_) :void { 
                $this ->name_=$name_ ; 
            }

            public function  getmodels():Collection{ 
                return $this ->models; 
              }
              public function  setmodels( Collection $models):void{ 
                $this ->models= $models; 
              }
        

   }
 
?> 

