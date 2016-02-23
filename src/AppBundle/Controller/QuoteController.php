<?php
/**
 * This Controller is responsible for all routes regarding quotes
 */

namespace AppBundle\Controller;

use AppBundle\Wrapper\Curl;
use AppBundle\Wrapper\FlickrApi;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

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
     * @Cache(expires="+2 days", public=true)
     *
     * @param $topic string Topic the quote is about
     *
     * @return Response
     */
    public function topicAction($topic)
    {
        $wrapper = $this->get('app.flickr_api');

        $recent  = $wrapper->getPhotoByTag($topic);

        return $this->render('quote/topic.html.twig', [
            'topic' => $topic,
            'recent' => $recent,
        ]);
    }
}