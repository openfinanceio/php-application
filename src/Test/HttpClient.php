<?php
namespace CFX\Test;

class HttpClient extends \GuzzleHttp\Client {
    protected $nextResponse = [];
    protected $requestTrace = [];

    public function setNextResponse($r) {
        if (
            !($r instanceof \Psr\Http\Message\ResponseInterface) &&
            !($r instanceof \RuntimeException)
        ) {
            throw new \TypeError("First argument must be a \Psr\Http\Message\ResponseInterface or some kind of exception");
        }
        $this->nextResponse[] = $r;
        return $this;
    }

    public function getRequestTrace()
    {
        return $this->requestTrace;
    }

    public function clearRequestTrace()
    {
        $this->requestTrace = [];
        return $this;
    }

    public function request($method, $uri = '', array $options = [])
    {
        return $this->transferStub(new \GuzzleHttp\Psr7\Request($method, $uri), $options);
    }

    public function send(\Psr\Http\Message\RequestInterface $request, array $options = [])
    {
        return $this->transferStub($request, $options);
    }

    protected function transferStub(\Psr\Http\Message\RequestInterface $request, array $options = []) {
        if (count($this->nextResponse) == 0) throw new \RuntimeException("This is a test HTTP Client that does not make real HTTP calls. You must set the response for the request you're about to execute by using the `setNextResponse(\GuzzleHttp\Message\ResponseInterface \$r)` method.\n\nRequest details: {$request->getMethod()} {$request->getUri()}");

        $request = $this->applyOptionsStub($request, $options);

        $this->requestTrace[] = $request;
        $res = array_shift($this->nextResponse);

        if ($res instanceof \Exception) {
            throw $res;
        }

        return $res;
    }
















    /**
     * Applies the array of request options to a request.
     *
     * **NOTE:** Had to copy/paste this from the actual Guzzle client because our friends
     * from Guzzle made it private :(.
     *
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return RequestInterface
     */
    protected function applyOptionsStub(\Psr\Http\Message\RequestInterface $request, array &$options)
    {
        $modify = [
            'set_headers' => [],
        ];

        if (isset($options['headers'])) {
            $modify['set_headers'] = $options['headers'];
            unset($options['headers']);
        }

        if (isset($options['form_params'])) {
            if (isset($options['multipart'])) {
                throw new \InvalidArgumentException('You cannot use '
                    . 'form_params and multipart at the same time. Use the '
                    . 'form_params option if you want to send application/'
                    . 'x-www-form-urlencoded requests, and the multipart '
                    . 'option to send multipart/form-data requests.');
            }
            $options['body'] = http_build_query($options['form_params'], '', '&');
            unset($options['form_params']);
            // Ensure that we don't have the header in different case and set the new value.
            $options['_conditional'] = \GuzzleHttp\Psr7\_caseless_remove(['Content-Type'], $options['_conditional']);
            $options['_conditional']['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        if (isset($options['multipart'])) {
            $options['body'] = new \GuzzleHttp\Psr7\MultipartStream($options['multipart']);
            unset($options['multipart']);
        }

        if (isset($options['json'])) {
            $options['body'] = \GuzzleHttp\json_encode($options['json']);
            unset($options['json']);
            // Ensure that we don't have the header in different case and set the new value.
            $options['_conditional'] = \GuzzleHttp\Psr7\_caseless_remove(['Content-Type'], $options['_conditional']);
            $options['_conditional']['Content-Type'] = 'application/json';
        }

        if (!empty($options['decode_content'])
            && $options['decode_content'] !== true
        ) {
            // Ensure that we don't have the header in different case and set the new value.
            $options['_conditional'] = \GuzzleHttp\Psr7\_caseless_remove(['Accept-Encoding'], $options['_conditional']);
            $modify['set_headers']['Accept-Encoding'] = $options['decode_content'];
        }

        if (isset($options['body'])) {
            if (is_array($options['body'])) {
                $this->invalidBodyStub();
            }
            $modify['body'] = \GuzzleHttp\Psr7\stream_for($options['body']);
            unset($options['body']);
        }

        if (!empty($options['auth']) && is_array($options['auth'])) {
            $value = $options['auth'];
            $type = isset($value[2]) ? strtolower($value[2]) : 'basic';
            switch ($type) {
                case 'basic':
                    // Ensure that we don't have the header in different case and set the new value.
                    $modify['set_headers'] = \GuzzleHttp\Psr7\_caseless_remove(['Authorization'], $modify['set_headers']);
                    $modify['set_headers']['Authorization'] = 'Basic '
                        . base64_encode("$value[0]:$value[1]");
                    break;
                case 'digest':
                    // @todo: Do not rely on curl
                    $options['curl'][CURLOPT_HTTPAUTH] = CURLAUTH_DIGEST;
                    $options['curl'][CURLOPT_USERPWD] = "$value[0]:$value[1]";
                    break;
                case 'ntlm':
                    $options['curl'][CURLOPT_HTTPAUTH] = CURLAUTH_NTLM;
                    $options['curl'][CURLOPT_USERPWD] = "$value[0]:$value[1]";
                    break;
            }
        }

        if (isset($options['query'])) {
            $value = $options['query'];
            if (is_array($value)) {
                $value = http_build_query($value, null, '&', PHP_QUERY_RFC3986);
            }
            if (!is_string($value)) {
                throw new \InvalidArgumentException('query must be a string or array');
            }
            $modify['query'] = $value;
            unset($options['query']);
        }

        // Ensure that sink is not an invalid value.
        if (isset($options['sink'])) {
            // TODO: Add more sink validation?
            if (is_bool($options['sink'])) {
                throw new \InvalidArgumentException('sink must not be a boolean');
            }
        }

        $request = \GuzzleHttp\Psr7\modify_request($request, $modify);
        if ($request->getBody() instanceof \GuzzleHttp\Psr7\MultipartStream) {
            // Use a multipart/form-data POST if a Content-Type is not set.
            // Ensure that we don't have the header in different case and set the new value.
            $options['_conditional'] = \GuzzleHttp\Psr7\_caseless_remove(['Content-Type'], $options['_conditional']);
            $options['_conditional']['Content-Type'] = 'multipart/form-data; boundary='
                . $request->getBody()->getBoundary();
        }

        // Merge in conditional headers if they are not present.
        if (isset($options['_conditional'])) {
            // Build up the changes so it's in a single clone of the message.
            $modify = [];
            foreach ($options['_conditional'] as $k => $v) {
                if (!$request->hasHeader($k)) {
                    $modify['set_headers'][$k] = $v;
                }
            }
            $request = \GuzzleHttp\Psr7\modify_request($request, $modify);
            // Don't pass this internal value along to middleware/handlers.
            unset($options['_conditional']);
        }

        return $request;
    }


    /**
     * Copied from guzzle client
     */
    protected function invalidBodyStub()
    {
        throw new \InvalidArgumentException('Passing in the "body" request '
            . 'option as an array to send a POST request has been deprecated. '
            . 'Please use the "form_params" request option to send a '
            . 'application/x-www-form-urlencoded request, or the "multipart" '
            . 'request option to send a multipart/form-data request.');
    }

}

