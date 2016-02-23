<?php
/**
 * This Controller is responsible for all routes regarding quotes
 */

namespace AppBundle\Controller;

use AppBundle\Wrapper\Curl;
use AppBundle\Wrapper\FlickrApi;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class QuoteController extends Controller
{
    /**
     *
     * @Route("/quote/", name="random_quote")
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('quote/index.html.twig');
    }

    /**
     * @Route("/quote/{topic}/", name="topic_quote")
     *
     * @param $topic string Topic the quote is about
     *
     * @return Response
     */
    public function topicAction($topic)
    {
        $wrapper = new FlickrApi(
            new Curl(),
            'https://api.flickr.com/services/rest/', '76662479@N00', '5498865d0f4cdc6c0b9e89e13d98c04c'
        );
//        $recent  = $wrapper->getPhotoByTag($topic);

        return $this->render('quote/topic.html.twig', [
            'topic' => $topic,
        ]);
    }
}