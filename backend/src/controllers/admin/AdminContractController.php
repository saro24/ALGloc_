<?php
    namespace App\controllers\admin;

        use App\Entity\Client;
        use App\Entity\Contract_;
        use App\Entity\Invoice;
        use App\Entity\Vehicle;
        use App\Repository\Contract_Repository;
        use App\service\RouteSettings;
        use  App\service\ContractService;
        use DateTime;
        use Doctrine\ORM\EntityManager;
        use Exception;
        use Hateoas\HateoasBuilder;
        use Hateoas\UrlGenerator\CallableUrlGenerator;
        use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
        use Symfony\Component\HttpFoundation\JsonResponse;
        use Symfony\Component\HttpFoundation\Request;
        use Symfony\Component\HttpFoundation\Response;
        use Symfony\Component\Routing\Annotation\Route;
        use Symfony\Component\Routing\RouterInterface;

        class AdminContractController extends AbstractController
        {

            private $hateoas;
            public function __construct(RouterInterface $router)
            {

                $this->hateoas = HateoasBuilder::create()
                    ->setUrlGenerator(
                        null,
                        new CallableUrlGenerator(
                            function ($route, $parameter, $absolute) use ($router) {
                                return $router->generate($route, $parameter, RouterInterface::ABSOLUTE_URL);
                            })
                    )->build();

            }
            private function checkdate($departure1, $arrival1, array $contracts)
            {
                // this function used to check whether the the vehicle is already linked to a contract
                foreach ($contracts as $c) {
                    if (($arrival1 > $c->getdeparture() && $arrival1 < $c->getarrival()) ||
                        ($departure1 > $c->getdeparture() && $departure1 < $c->getarrival())) {
                        return true;
                    }
                }
                return false;

            }

            /** 
             * @Route("/admin/contract"  , name="post_contract" , methods ={"POST"} ) 
             * 
             */
            public function postContract(Request $request , ContractService $contractService)
            {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $body = json_decode($request->getContent(), true);
                    $contract =  $contractService->JsonToContractObjectAdmin($body, $em);
                    if ($contract->getDeparture() > $contract->getArrival()) {
                        return new JsonResponse(['message' => "departure can not be after the arrival"], Response::HTTP_BAD_REQUEST, ["Content-type" => "application\json"]);

                    }
                    $contracts = $em->getRepository(Contract_::class)->findBy(
                        ['vehicle' => $contract->getVehicle()->getid()]);

                    $dataTaken = $this->checkdate($contract->getDeparture(), $contract->getArrival(), $contracts);
                    if ($dataTaken) {
                        return new JsonResponse(['message' => "Vehicle Already Allocated at this period"], Response::HTTP_BAD_REQUEST, ["Content-type" => "application\json"]);

                    }
                    $em->persist($contract);
                    $em->flush();
                    // the invoice is automatically generated with the contracted
                    $invoice = $contractService->generateInvoice( $contract->getDeparture() , $contract->getArrival()
                         ,$contract );
                    $em->persist($invoice);
                    $em->flush();
                    $contractJson = $this->hateoas->serialize($contract, 'json');
                    return new Response($contractJson, Response::HTTP_CREATED, ["Content-type" => "application\json"]);

                } catch (Exception $e) {
                     return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST, ["Content-type" => "application\json"]);

                }

            }

            /** @Route( "/admin/contracts"  , name="get_contracts" , methods ={"GET"} ) */
            public function getContracts(RouteSettings $setting)
            {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $constracts = $em->getRepository(Contract_::class)->findAll();
                    $contractsJson = $this->hateoas->serialize(
                        $setting->pagination($constracts, "get_contracts")
                        , 'json'
                    );
                    return new Response($contractsJson, Response::HTTP_OK, ["Content-type" => "application\json"]);

                } catch (Exception $e) {
                    return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST, ["Content-type" => "application\json"]);

                }

            }

            /** @Route( "/admin/contract/{id}"  , name="get_contract" , methods ={"GET"} ) */
            public function getContractById(int $id)
            {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $contract = $em->getRepository(Contract_::class)->findOneBy(['id' => $id]);
                    $contractJson = $this->hateoas->serialize($contract, 'json');
                    return new Response($contractJson, Response::HTTP_OK, ["Content-type" => "application\json"]);

                } catch (Exception $e) {
                    return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST, ["Content-type" => "application\json"]);

                }

            }

            /** @Route( "/admin/contract/{id}"  , name="patch_contract" , methods ={"PATCH"} ) */
            public function patchContractById(int $id, Request $request
             , Contract_Repository $contractRepo , ContractService $contractService)
            {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $contract = $em->getRepository(Contract_::class)->findOneBy(['id' => $id]);
                    $body = json_decode($request->getContent(), true);
                    // in order to patch the arrival and departure  it shoulld'nt interfer  with another contract's departures and arrivals
                    if (isset($body["arrival"])) {
                        // checking whether the arrival
                        if ( ( new DateTime( $body["arrival"] ) ) < $contract->getdeparture() ) {
                            return new JsonResponse(['message' => "departure can not be after the arrival"], Response::HTTP_BAD_REQUEST, ["Content-type" => "application\json"]);
                        }
                        $contracts = $contractRepo->selectExcept(  $id , $contract->getVehicle()->getid());
                        if ($this->checkdate($contract->getdeparture(), $body["arrival"], $contracts)) {
                            return new JsonResponse(['message' => "can not extend the period"], Response::HTTP_BAD_REQUEST, ["Content-type" => "application\json"]);
                        }
                       // case both of the conditions are false then a new invoice is generated
                       // the invoice is generated according to the number of days added 
                       // the previous arrival and the newly updated arrival 
                       // diff= Arrival2 - Arrival1
                       $invoice=   $contractService->generateInvoice($contract->getArrival() , 
                          new DateTime($body["arrival"] ), $contract  );   
                        $em->persist($invoice); 
                        $em->flush(); 
                  
                    } 

                     // generating the contract json object which will also include the whole invoices  in case the period is extended
                     $em->flush(); 
                     $contractJson = $this->hateoas->serialize($contract, 'json');
                     return new Response($contractJson, Response::HTTP_OK, ["Content-type" => "application\json"]);

                } catch (Exception $e) {
                     return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST, ["Content-type" => "application\json"]);

                }

            }

            /** @Route( "/admin/contract/{id}"  , name="delete_contract" , methods ={"DELETE"} ) */
            public function deleteContractById(int $id   , Contract_Repository  $contractRepo)
            {  
                try{  
                      $contractRepo->delete($id); 
                     return new JsonResponse(['message' => "deleted successfully"], Response::HTTP_OK, ["Content-type" => "application\json"]);

                }catch(Exception $e) { 
                    return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST, ["Content-type" => "application\json"]);

                }


            }

        }
