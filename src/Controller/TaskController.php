<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    #[Route('/task', name: 'app_task')]
    public function index(): Response {
        try {
            $tasks = $this->taskService->getAllTasks();
            $this->addFlash('success', 'Liste des tâches récupérée avec succès');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erreur lors de la récupération des tâches');
        }

        return $this->render('index.html.twig', [
            'tasks' => $tasks ?? [],
        ]);
    }

    #[Route('/task/new', name: 'app_task_add')]
    public function add(Request $request): Response {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->taskService->addTask($task);
                $this->addFlash('success', 'La tâche a été ajoutée avec succès');
                return $this->redirectToRoute('app_task');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Erreur lors de l\'ajout de la tâche');
            }
        }

        return $this->render('new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
