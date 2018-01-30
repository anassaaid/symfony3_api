<?php

namespace AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Book;
use AppBundle\Form\BookType;
use Symfony\Component\HttpKernel\Exception\HttpException;


/**
 * @Rest\RouteResource("Book")
 */
Class BookController extends AbstractController{

	private $serializer;

	/**
     * FOSRestBundle don't support Type-hint injection until now
     * This is a workaround until FOSUserBundle solves the problem
     * https://github.com/FriendsOfSymfony/FOSRestBundle/pull/1733
     *
     * @param SerializerInterface $serializer
     */
	public function __construct(SerializerInterface $serializer){
		$this->serializer = $serializer;
	}

	/**
     * Load books 
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @ApiDoc(
     *  description="Get list of books",
     *  section="Books"
     * )
     */
	public function cgetAction(){

		$em = $this->getDoctrine()->getManager();

		$books = $em->getRepository(Book::class)->findAll();

		$response = $this->serializer->serialize(['books' => $books],'json');
		
		return new Response($response); 

	}

     /**
     * Load book by id 
     * 
     * @param Book $book
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @ApiDoc(
     *  description="Get book by id",
     *  section="Books",
     *  requirements={
     *      {"name"="book", "dataType"="integer", "requirement"="\d+", "description"="Book id" }
     *  },
     * )
     */
     public function getAction(Book $book){

          $response = $this->serializer->serialize(['book' => $book],'json');
          
          return new Response($response); 

     }  

     /**
     * Add new book 
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @ApiDoc(
     *  description="Get book by id",
     *  section="Books",
     *  input="AppBundle\Form\BookType",
     *  output="AppBundle\Entity\Book"
     * )
     */
     public function postAction(Request $request){
          $form = $this->createForm(BookType::class);
          $form->handleRequest($request);

          if($form->isSubmitted() && $form->isValid()){
               $book = $form->getData();

               $em = $this->getDoctrine()->getManager();
               $em->persist($book);
               $em->flush();

               $response = $this->serializer->serialize(['book' => $book],'json');
          
               return new Response($response); 
          }

          throw new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid Data');
     }   

}