<?php
namespace TrackFinity;
/**
 * Class FilterResponse
 */
class FilterResponse
{
    /**
     * @var string
     */
    public $action = '';

    /**
     * @var string
     */
    public $type = '';

    /**
     * @var string
     */
    public $path = '';

    /**
     * @var string
     */
    public $message = '';

    /**
     * @var string
     */
    public $cookies = '';

    /**
     * FilterResponse constructor.
     *
     * @param $jsonString
     */
    function __construct($jsonString)
    {
        foreach (json_decode($jsonString, true) as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return string|array $cookies
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @return string $action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string $message
     */
    public function getMessage()
    {
        return $this->message;
    }
}