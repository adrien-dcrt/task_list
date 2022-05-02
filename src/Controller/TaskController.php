<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/{id}", name="app_task_show", methods={"GET"})
     */
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_task_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->add($task);
            return $this->redirectToRoute('app_task_list_edit', ['name' => $task->getTaskList()->getName()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    /**
     * @Route("toggle/{id}", name="app_task_toggle", methods={"GET", "POST"})
     */
    public function toggle(Task $task, Request $request, TaskRepository $taskRepository): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        ( 1 == $task->getState() ) ? $task->setState(0) : $task->setState(1);
        $taskRepository->add($task);
        return $this->redirectToRoute('app_task_list_edit', ['name' => $task->getTaskList()->getName()]);
    }

    /**
     * @Route("/{id}", name="app_task_delete", methods={"POST"})
     */
    public function delete(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $name = $task->getTaskList()->getName();
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $taskRepository->remove($task);
        }

        return $this->redirectToRoute('app_task_list_edit', ['name' => $name], Response::HTTP_SEE_OTHER);
    }
}
