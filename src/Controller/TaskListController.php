<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskList;
use App\Form\TaskListType;
use App\Form\TaskType;
use App\Repository\TaskListRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class TaskListController extends AbstractController
{
    /**
     * @Route("/", name="app_task_list_index", methods={"GET", "POST"})
     */
    public function index(Request $request, TaskListRepository $taskListRepository): Response
    {
        $taskList = new TaskList();
        $form = $this->createForm(TaskListType::class, $taskList);
        $form->handleRequest($request);
        $taskList->setCreatedAt( new \DateTimeImmutable() );
        if ($form->isSubmitted() && $form->isValid()) {
            $taskListRepository->add($taskList);
            return $this->redirectToRoute('app_task_list_edit', ['name' => $taskList->getName()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task_list/index.html.twig', [
            'task_lists' => $taskListRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/list/new", name="app_task_list_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TaskListRepository $taskListRepository): Response
    {
        $taskList = new TaskList();
        $form = $this->createForm(TaskListType::class, $taskList);
        $form->handleRequest($request);
        $taskList->setCreatedAt( new \DateTimeImmutable() );
        if ($form->isSubmitted() && $form->isValid()) {
            $taskListRepository->add($taskList);
            return $this->redirectToRoute('app_task_list_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task_list/new.html.twig', [
            'task_list' => $taskList,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/list/{name}", name="app_task_list_show", methods={"GET"})
     */
    public function show(TaskList $taskList): Response
    {
        return $this->render('task_list/show.html.twig', [
            'task_list' => $taskList,
        ]);
    }

    /**
     * @Route("/list/{name}/edit", name="app_task_list_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, TaskList $taskList, TaskListRepository $taskListRepository): Response
    {
        $form = $this->createForm(TaskListType::class, $taskList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskListRepository->add($taskList);
            return $this->redirectToRoute('app_task_list_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task_list/edit.html.twig', [
            'task_list' => $taskList,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/list/{name}/edit/new_task", name="app_task_new", methods={"GET", "POST"})
     */
    public function newTask(Request $request, TaskListRepository $taskListRepository, TaskRepository $taskRepository): Response
    {
        $task = new Task();
        $taskList = $taskListRepository->findOneBy(['name' => $request->attributes->get('name')]);
        $task->setTaskList($taskList);
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        $task->setCreatedAt( new \DateTimeImmutable() );
        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->add($task);
            return $this->redirectToRoute('app_task_list_edit', ['name' => $taskList->getName()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/list/{name}", name="app_task_list_delete", methods={"POST"})
     */
    public function delete(Request $request, TaskList $taskList, TaskListRepository $taskListRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taskList->getName(), $request->request->get('_token'))) {
            $taskListRepository->remove($taskList);
        }

        return $this->redirectToRoute('app_task_list_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/list/{name}/delDone", name="app_task_list_delete_dones", methods={"GET", "POST"})
     */
    public function deleteDones(Request $request, TaskList $taskList, TaskListRepository $taskListRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taskList->getName(), $request->request->get('_token'))) {
            $taskListRepository->removeDoneTasks($taskList);
        }
        
        return $this->redirectToRoute('app_task_list_edit', ['name' => $taskList->getName()], Response::HTTP_SEE_OTHER);
    }
}
