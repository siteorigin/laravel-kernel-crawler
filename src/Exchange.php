<?php

namespace SiteOrigin\KernelCrawler;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Exchange
{
    public Request $request;
    public Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}