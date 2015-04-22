<?php
namespace APIs\V1\Rest\Purchaserequest;

class PurchaserequestResourceFactory
{
    public function __invoke($services)
    {
        return new PurchaserequestResource($services->get('APIs\V1\Rest\Purchaserequest\PurchaserequestMapper'));
    }
}
