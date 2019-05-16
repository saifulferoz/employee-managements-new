<?php


namespace App\Controller\Rest;


use App\Entity\Employee;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EmployeeApiController
 * @package App\Controller\Rest
 * @Route("/api")
 */
class EmployeeApiController extends FOSRestController
{
    /**
     * Retrieves a collection of Employee resource
     * @Rest\Get("/employee")
     */
    public function getEmployees(): View
    {
        $employeeRespository = $this->getDoctrine()->getRepository(Employee::class);

        // query for a single Product by its primary key (usually "id")
        $employees = $employeeRespository->findall();

        // In case our GET was a success we need to return a 200 HTTP OK response with the collection of article object
        return View::create($employees, Response::HTTP_OK);
    }

}