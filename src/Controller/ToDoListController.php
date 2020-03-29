<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToDoListController extends AbstractController
{
    /**
     * @Route("/", name="to_do_list")
     */
    public function index()
    {
        /*return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ToDoListController.php',
        ]);*/
        //return $this->render('index.html.twig');
        $tasks=$this->getDoctrine()->getRepository(Task::class)->findBy(['status'=>0,'is_delete'=>0],['id'=>'desc']);
        dump($tasks);
        $tasksDone=$this->getDoctrine()->getRepository(Task::class)->findBy(['status'=>1,'is_delete'=>0],['id'=>'desc']);
        return $this->render('todo.html.twig',['tasks'=>$tasks,'tasksDone'=>$tasksDone]);
    }

    /**
     * @Route("/create", name="create_task",methods={"POST"})
     */
    public function create(Request $request)
    {
        $title = trim($request->request->get('title'));
        if(empty($title)){
            //return $this->redirectToRoute('to_do_list');
            return $this->json([
                'info' => 1,
                'message' => 'Please insert name'
            ]);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $task= new Task();
        $task->setTitle($title);
        $task->setStatus(0);
        $task->setIsDelete(0);
        $entityManager->persist($task);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        //return $this->redirectToRoute('to_do_list');
        //return new Response('Saved new product with id '.$task->getId());
        return $this->json([
            'info' => 0,
            'message' => 'Insert success',
            'id'=>$task->getId()
        ]);
    }

    /**
     * @Route("/switch-status/{id}", name="switch_status")
     */
    public function switchStatus($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            throw $this->createNotFoundException(
                'No task found for id '.$id
            );
        }

        $task->setStatus(!$task->getStatus());
        $entityManager->flush();
        return $this->json([
            'info' => 0,
            'message' => 'Switch status success'
        ]);
        //return $this->redirectToRoute('to_do_list');
    }

    /**
     * @Route("/delete/{id}", name="task_delete")
     */
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->json([
                'info' => 1,
                'message' => 'No task found for id '.$id
            ]);
        }

        $task->setIsDelete(1);
        $entityManager->flush();
        return $this->json([
            'info' => 0,
            'message' => 'Delete success'
        ]);
    }
}
