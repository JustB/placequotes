<?php
/**
 * This Controller is responsible for all routes regarding quotes
 */

namespace AppBundle\Controller;

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
        return new Response('<html><body>This is a random quote</body></html>');
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
        return new Response(sprintf('<html><body>This is a quote about %s</body></html>', $topic));
    }
}