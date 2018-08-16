<?php

namespace App\Controller;

use App\Entity\Produto;
use App\Form\ProdutoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProdutoController extends AbstractController
{
    /**
     * @Route("/produto", name="listar_produto")
     * @Template("produto/index.html.twig")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $produtos = $em->getRepository(Produto::class)->findAll();

        return [
          "produtos" => $produtos
        ];
    }

    /**
     * @param Request $request
     * @Route("/produto/cadastrar", name="cadastrar_produto")
     * @Template("produto/create.html.twig")
     */
    public function create(Request $request)
    {
      $produto = new Produto();

      $form = $this->createForm(ProdutoType::class, $produto);

      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()){
        $em = $this->getDoctrine()->getManager();
        $em->persist($produto);
        $em->flush(); //persiste no bd e limpar a sujeira que ficou.

        //$this->get('session')->getFlashBag()->set('sucess', 'Produto foi salvo com sucesso!');
        $this->addFlash('success', "Produto cadastrado!");
        return $this->redirectToRoute('listar_produto');
      }

      // return $this->render("produto/create.html.twig", [
      //   "form" => $form->createView()
      // ]);
      return [
        "form" => $form->createView()
      ];
    }

     /**
     * @param Request $request
     * @Template("produto/update.html.twig")
     * @Route("produto/editar/{id}", name="editar_produto")
     */

    public function update(Request $request, $id)
    {
      $em = $this->getDoctrine()->getManager();
      $produto = $em->getRepository(Produto::class)->find($id);

      $form = $this->createForm(ProdutoType::class, $produto);
      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()){
        $em->persist($produto);
        $em->flush(); //persiste no bd e limpar a sujeira que ficou.

        $this->addFlash('success', "O produto ". $produto->getNome()." foi atualizado com sucesso!");
        return $this->redirectToRoute('listar_produto');
      }

      return [
        "produto" => $produto,
        "form" => $form->createView()
      ];
    }


    /**
    * @param Request $request
    * @param $id
    *
    * @return array
    * @Template("produto/view.html.twig")
    * @Route("produto/visualizar/{id}", name="visualizar_produto")
    */
    public function view(Request $request, $id)
    {
      $em = $this->getDoctrine()->getManager();
      $produto = $em->getRepository(Produto::class)->find($id);

      return [
        "produto" => $produto
      ];
    }

    /**
    * @param Request $request
    * @param $id
    *
    * @Route("produto/apagar/{id}", name="apagar_produto")
    */
    public function delete(Request $request, $id)
    {
      $em = $this->getDoctrine()->getManager();
      $produto = $em->getRepository(Produto::class)->find($id);

      if(!$produto){
        $mensagem = "Produto não foi encontrado";
        $tipo = "warning";
      }else{
        $em->remove($produto);
        $em->flush();
        $mensagem = "Produto foi excluido com Sucesso!";
        $tipo = "success";
      }
      $this->get('session')->getFlashBag()->set($tipo, $mensagem);
      return $this->redirectToRoute("listar_produto");
    }

}
