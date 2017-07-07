<?php
namespace TrackFinity;
/**
 * Class TrackFinity
 */
class TrackFinity
{
    /**
     * @var string
     */
    public $version = '1.0';

    /**
     * @var null
     */
    public $request = null;

    /**
     * @var null
     */
    public $campaignkey = null;

    /**
     * @var null
     */
    public $requestid = null;

    /**
     * @var null
     */
    public $orderid = null;

    /**
     * @var float
     */
    public $amount = 0.00;

    /**
     * @var string
     */
    public $baseurl = 'http://traffic.trackthings.online/api/';

    /**
     * @var null
     */
    public $requestUri = null;

    /**
     * @var null
     */
    public $queryString = null;

    /**
     * @param null $campaignkey
     * @param null $baseurl
     * @param bool $advancedMode
     */
    public static function run($campaignkey = null, $baseurl = null, $advancedMode = false, $debug = false)
    {
        try {
            @session_start();

            $instance = new static;

            if (!empty($baseurl)) {
                $instance->baseurl = $baseurl;
            }

            $instance->campaignkey = $campaignkey;
            $instance->campaignkey = $campaignkey;

            $instance->buildFilterRequestObject();
            $instance->doRequestValidation();
            $instance->process($debug);

        } catch (\Exception $e) {
            // TODO: handel exception
        }
    }

    /**
     * @param null  $orderid
     * @param float $amount
     * @param null  $requestid
     */
    public static function conversion($orderid = null, $amount = 0.00, $requestid = null)
    {
        try {
            @session_start();
            $instance          = new static;
            $instance->orderid = $orderid;
            $instance->amount  = $amount;


            $instance->buildFilterRequestObject();
            $instance->doRequestValidation();
            $instance->requestIdentifier($requestid);
            $instance->processConversion();
        } catch (\Exception $e) {
            // TODO: handel exception
        }
    }

    /**
     *
     */
    protected function buildFilterRequestObject()
    {
        $this->request = new FilterRequest;
    }

    /**
     *
     */
    protected function doRequestValidation()
    {
        // TODO: complete this method
    }

    /**
     * @param null $requestid
     *
     * @return $this
     */
    protected function requestIdentifier($requestid = null)
    {
        if (!empty($requestid)) {
            $this->requestid = $requestid;

            return $this;
        }

        if (!empty($_SESSION["TRAFFIC_FILTER_REQUEST_ID"])) {
            $this->requestid = $_SESSION["TRAFFIC_FILTER_REQUEST_ID"];

            return $this;
        }
        //check cookie
    }

    /**
     *
     */
    protected function process($debug=false)
    {
        $data = $this->sendRequest('request');
	if ($debug) {
		var_dump($data); 
		exit(1);
	}
        $this->processResponseObject(new FilterResponse($data));
    }

    /**
     *
     */
    protected function processConversion()
    {
        $data = $this->sendRequest('conversion');
        die();
    }

    /**
     * @param $endpoint
     *
     * @return mixed
     */
    protected function sendRequest($endpoint)
    {
        //curl a FilterRequest json with api to remote url
        $data = [];
        if (!empty($this->campaignkey)) {
            $data['campaign_id'] = $this->campaignkey;
        }
        if (!empty($this->requestid)) {
            $data['requestid'] = $this->requestid;
        }
        if (!empty($this->orderid)) {
            $data['orderid'] = $this->orderid;
        }
        if (!empty($this->amount)) {
            $data['amount'] = $this->amount;
        }
        if (!empty($this->requestUri)) {
            $data['requestUri'] = $this->requestUri;
        }
        if (!empty($this->queryString)) {
            $data['queryString'] = $this->queryString;
        }

        $data['request'] = $this->request->getDataAsArray();
        $data            = json_encode($data);

        $ch = curl_init($this->baseurl . $endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)]
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);

        return $result;
    }

    /**
     * @param FilterResponse $filter
     */
    protected function processCookies(FilterResponse $filter)
    {
        if (!empty($filter->getCookies()) && is_array($filter->getCookies())) {
            foreach ($filter->getCookies() as $cookie) {
                extract($cookie);
                // TODO: Complete this method
                setcookie($name, $value, time() + 3600);
            }
        }
    }

    /**
     * @param FilterResponse $filter
     */
    protected function processRequestIdentifer(FilterResponse $filter)
    {
        if (!empty($filter->id)) {
            // Might cause a warning
            $_SESSION["TRAFFIC_FILTER_REQUEST_ID"] = $filter->id;

        }
    }

    /**
     * @param FilterResponse $filter
     */
    protected function processResponseObject(FilterResponse $filter)
    {
        $this->processRequestIdentifer($filter);
        $this->processCookies($filter);

        $this->processCookies($filter);
        switch ($filter->getAction()) {
            case 'redirect':
                header("Location: " . $filter->getpath());
                exit();
                break;
            case 'include':
                include_once($filter->getpath());
		if($filter->mode == 'money') { 
			exit(); 
		}
                break;
        }
    }
}
