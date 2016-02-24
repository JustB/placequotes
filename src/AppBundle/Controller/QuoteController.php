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
        // @todo move default topics elsewhere
        $topics = [
            'life',
            'love',
            'happiness',
            'peace',
        ];

        /**
         * @var $wrapper FlickrApi
         */
        $wrapper = $this->get('app.flickr_api');

        $photo  = $wrapper->getPhotoByTag($topics[mt_rand(0, count($topics) - 1)]);

        return $this->render('quote/index.html.twig', [
            'photo' => $photo,
        ]);
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
        /**
         * @var $wrapper FlickrApi
         */
        $wrapper = $this->get('app.flickr_api');

        $photo  = $wrapper->getPhotoByTag($topic);

        return $this->render('quote/topic.html.twig', [
            'topic' => $topic,
            'photo' => $photo,
        ]);
    }
}