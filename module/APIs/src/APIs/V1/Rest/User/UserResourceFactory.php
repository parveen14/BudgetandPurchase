<?php
namespace APIs\V1\Rest\User;

class UserResourceFactory
{
    public function __invoke($services)
    {
        return new UserResource($services->get('APIs\V1\Rest\User\UserMapper'));
    }
}
