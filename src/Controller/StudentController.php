<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Base;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentController extends BaseController
{
    protected $entityManager;
    protected $passwordHasher;
    private $userLogged;
    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        parent::__construct($entityManager, $session);
        $this->userLogged = $this->getUser();
    }

    /**
     * @Route("/student/create", name="student_create")
     */
    public function create(Request $request): Response
    {
        $student = new Student();
        
        $form = $this->createForm(StudentType::class, $student);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $student->setCreatedBy($this->userLogged->getId());
            $this->entityManager->persist($student);
            $this->entityManager->flush();
            $this->addFlash('success', 'Student created!');

            return $this->redirectToRoute('home');
        }

        return $this->render('student/create.html.twig', [
            'form' => $form->createView(),
            'user' => $this->userLogged
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function list(StudentRepository $studentRepository): Response
    {
        $students = $studentRepository->findAll();

        return $this->render('student/list.html.twig', [
            'students' => $students,
            'user' => $this->userLogged
        ]);
    }

    /**
     * @Route("/student/delete/{id}", name="student_delete")
     */
    public function delete(StudentRepository $studentRepository, int $id): Response
    {
        $student = $studentRepository->find($id);

        if (!$student) {
            return $this->redirectToRoute('home');
        }

        $this->entityManager->remove($student);
        $this->entityManager->flush();

        $this->addFlash('success', 'Student deleted!');

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/student/edit/{id}", name="student_edit")
     */
    public function edit(StudentRepository $studentRepository, int $id, Request $request): Response
    {
        $student = $studentRepository->find($id);

        $form = $this->createForm(StudentType::class, $student);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $student->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($student);
            $this->entityManager->flush();

            $this->addFlash('success', 'Student edited!');

            return $this->redirectToRoute('home');
        }

        return $this->render('student/edit.html.twig', [
            'form' => $form->createView(),
            'student' => $student,
            'user' => $this->userLogged
        ]);
    }

    /**
     * @Route("/student/export", name="student_export")
     */
    public function exportToExcel(StudentRepository $studentRepository)
    {   
        if ($studentRepository->findAll() == null) {
            $this->addFlash('error', 'There are no students to export! Add students first.');
            return $this->redirectToRoute('home');
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Room');
        $sheet->setCellValue('D1', 'Guardian');
        $sheet->setCellValue('E1', 'Age');
        $sheet->setCellValue('F1', 'Gender');

        $row = 2;

        foreach ($studentRepository->findAll() as $student) {
            $sheet->setCellValue('A' . $row, $student->getId());
            $sheet->setCellValue('B' . $row, $student->getName());
            $sheet->setCellValue('C' . $row, $student->getRoom());
            $sheet->setCellValue('D' . $row, $student->getGuardian());
            $sheet->setCellValue('E' . $row, $student->getAge());
            $sheet->setCellValue('F' . $row, $student->getGender());
            $row++;
        }
        
        $writer = new Xlsx($spreadsheet);
        
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        $filename = 'students.xlsx';

        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . urlencode($filename) . '"');

        return $response;

    }
}
