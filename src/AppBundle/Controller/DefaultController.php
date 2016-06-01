<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ItemType;
use AppBundle\Entity\Item;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $form = $this->createForm(ItemType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Item $item */
            $item = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($item);
            $entityManager->flush();
            return $this->redirectToRoute('app');
        }

        $items = $this->getDoctrine()->getRepository('AppBundle:Item')
            ->findAll();

        return $this->render('default/index.html.twig', [
            'items' => $items,
            'total' => count($items),
            'form' => $form->createView()
        ]);
    }

    public function activeListAction()
    {
        $items = $this->getDoctrine()->getRepository('AppBundle:Item')
            ->findAllActive();

        return $this->render('default/active.html.twig', [
            'items' => $items,
            'total' => count($items)
        ]);
    }

    public function completedListAction()
    {
        $items = $this->getDoctrine()->getRepository('AppBundle:Item')
            ->findAllCompleted();

        return $this->render('default/completed.html.twig', [
            'items' => $items,
            'total' => count($items)
        ]);
    }

    public function closeAction($id)
    {
        /** @var Item $item */
        $item = $this->getDoctrine()->getRepository('AppBundle:Item')
            ->find($id);
        $item->setStatus('close');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($item);
        $entityManager->flush();
        return $this->redirectToRoute('app');
    }
}
