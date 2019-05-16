<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/employee")
 */
class EmployeeController extends AbstractController
{
    /**
     *
     * @Route("/list/{page}", name="employee_index", methods={"GET"}, defaults={"page": 1} , requirements={"page"="\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @throws \Exception
     */
    public function index(
        EmployeeRepository $employeeRepository,
        Request $request,
        PaginatorInterface $paginator,
        $page = 1
    ): Response {
        $searchCriteria = $request->query->all();
        $employeeQuery = $employeeRepository->getQueryBySearchCriteria($searchCriteria);
        // Paginate the results of the query
        $employees = $paginator->paginate(
        // Doctrine Query, not results
            $employeeQuery,
            // Define the page parameter
            $page,
            // Items per page
            2
        );

        return $this->render(
            'employee/index.html.twig',
            [
                'employees' => $employees,
                'search' => $searchCriteria,
            ]
        );

    }

    /**
     * @Route("/new", name="employee_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $employee->getPhoto();

            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('upload_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochure' property to store the PDF file name
            // instead of its contents
            $employee->setPhoto($fileName);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($employee);
            $entityManager->flush();
            $this->addFlash('success', "Data has been added successfully");

            return $this->redirectToRoute('employee_index');
        }

        return $this->render(
            'employee/new.html.twig',
            [
                'employee' => $employee,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     *
     * @Route("/report", name="employee_report", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @throws \Exception
     */
    public function showReport(EmployeeRepository $employeeRepository): Response
    {
        $employees = $employeeRepository->getGenderWiseEmployee();

        return $this->render(
            'employee/report.html.twig',
            [
                'employee_list' => $employees,
            ]
        );
    }

    /**
     *
     * @Route("/thirdparty", name="api_consumer", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @throws \Exception
     */
    public function apiConsumer(): Response
    {
        $data = json_decode(
            file_get_contents("https://rxnav.nlm.nih.gov/REST/interaction/interaction.json?rxcui=341248")
        );

        return $this->render(
            'api-data/index.html.twig',
            [
                'minconceptitem' => $data->interactionTypeGroup[0]->interactionType[0]->minConceptItem,
                'interactionPair' => $data->interactionTypeGroup[0]->interactionType[0]->interactionPair[0],
            ]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @Route("/{id}", name="employee_show", methods={"GET"})
     */
    public function show(Employee $employee): Response
    {
        return $this->render(
            'employee/show.html.twig',
            [
                'employee' => $employee,
            ]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @Route("/{id}/edit", name="employee_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Employee $employee): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            // updates the 'brochure' property to store the PDF file name
            // instead of its contents

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Data has been updated successfully");

            return $this->redirectToRoute(
                'employee_index',
                [
                    'id' => $employee->getId(),
                ]
            );
        }

        return $this->render(
            'employee/edit.html.twig',
            [
                'employee' => $employee,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @Route("/{id}", name="employee_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Employee $employee): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($employee);
            $entityManager->flush();
        }
        $this->addFlash('success', "Data has been deleted successfully");

        return $this->redirectToRoute('employee_index');
    }


}
