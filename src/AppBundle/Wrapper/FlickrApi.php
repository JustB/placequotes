<?php
/**
 * Created by PhpStorm.
 * User: justb
 * Date: 11/02/16
 * Time: 22.27
 */

namespace AppBundle\Wrapper;


class FlickrApi
{
    protected $curl;
    protected $url;
    protected $user_id;
    protected $api_key;

    /**
     * @see http://www.flickr.com/services/api/
     *
     * @param Curl $curl
     * @param string $url
     * @param string $user_id
     * @param string $api_key
     */
    public function __construct(Curl $curl, $url, $user_id, $api_key)
    {
        if (empty( $url ) || empty( $user_id ) || empty( $api_key )) {
            throw new \InvalidArgumentException('Url, user_id and api_key are mandatory for using flickr api');
        }

        $this->curl    = $curl;
        $this->url     = $url;
        $this->user_id = $user_id;
        $this->api_key = $api_key;
    }

    /**
     * Builds the basic url for any method of the flickr api
     *
     * @param string $method
     * @param array $extra_parameters
     *
     * @return string
     */
    protected function buildBaseUrl($method, array $extra_parameters = [])
    {


        $parameters = http_build_query(
            [
                'method'         => $method,
                'api_key'        => $this->api_key,
                'format'         => 'json',
                'nojsoncallback' => '?',
            ] + $extra_parameters
        );


        return sprintf('%s?%s', $this->url, $parameters, $extra_parameters);
    }

    /**
     * Returns the url of a single flickr picture
     * format: http://farm{farm-id}.static.flickr.com/{server-id}/{id}_{secret}_[mstzb].jpg
     *
     * @param mixed $attributes
     * @param string $size
     *
     * @return string
     */
    protected function buildPhotoUrl($attributes, $size = 'm')
    {
        return 'http://farm'.$attributes['farm'].'.static.flickr.com/'.$attributes['server'].'/'.$attributes['id'].'_'.$attributes['secret'].'_'.$size.'.jpg';
    }


    /**
     * Checks whether the given xml has the "rsp" element with the "stat" attribute set to "ok"
     *
     * @param \DOMDocument $doc
     *
     * @return boolean
     */
    protected function isValidResponse($doc)
    {
        return 'ok' === $doc->stat;
    }


    public function getPhotoByTag($tag)
    {
        $url = $this->buildBaseUrl(
            'flickr.photos.search',
            [
                'page'     => 1,
                'per_page' => 100,
                'sort'     => 'interestingness-desc',
                'extras'   => 'url_m,tags',
                'tags'     => $tag,
            ]
        );

        $results = json_decode($this->curl->get($url));

        if ($this->isValidResponse($results)) {
            $photos = $results->photos->photo;
            return $photos[mt_rand(0, count($photos)-1)];
        }

        throw new \ErrorException('Failed fetching images');
    }

}