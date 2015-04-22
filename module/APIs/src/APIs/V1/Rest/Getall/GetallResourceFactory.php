<?php
namespace APIs\V1\Rest\Getall;

class GetallResourceFactory
{
    public function __invoke($services)
    {
        return new GetallResource($services->get('APIs\V1\Rest\Getall\GetallMapper'));
    }
}
