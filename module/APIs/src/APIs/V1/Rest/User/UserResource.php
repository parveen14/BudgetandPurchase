<?php
namespace APIs\V1\Rest\User;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class UserResource extends AbstractResourceListener
{
	protected $mapper;
    
    public function __construct($mapper)
    {
        $this->mapper = $mapper;
    }
	
    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {

       if(isset($data->method)) {
           switch ($data->method) {
               case 'login':
                   return $this->mapper->authenticateUser($data);
                   break;
               case 'updateprofile':
                   return $this->mapper->updateUser($data);
                   break;
               default:
               case 'forgotpassword':
                   return $this->mapper->forgotPassword($data);
                   break;
               default:
                   return new ApiProblem(422, 'Method not defined','Server side validation error','Method not found');
           }
           
       } else {
           return new ApiProblem(422, 'Method not defined','Server side validation error','Method not found');
       }
        
       // return new ApiProblem(405, 'The POST method has not been defined');
    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        echo "This is delete method"; die;
        return new ApiProblem(405, 'The DELETE method has not been defined for individual resources');
    }

    /**
     * Delete a collection, or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function deleteList($data)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for collections');
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function fetch($id)
    {
        return $this->mapper->fetchAll($id);
        return new ApiProblem(405, 'The GET method has not been defined for individual resources');
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array())
    {
		return $this->mapper->fetchAll($params);
        //return new ApiProblem(405, 'The GET method has not been defined for collections');
    }

    /**
     * Patch (partial in-place update) a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patch($id, $data)
    {
        echo "This is patch method"; die;
        return new ApiProblem(405, 'The PATCH method has not been defined for individual resources');
    }

    /**
     * Replace a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function replaceList($data)
    {
        echo "This is PUT method"; die;
        return new ApiProblem(405, 'The PUT method has not been defined for collections');
    }

    /**
     * Update a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($id, $data)
    {
        echo "This is PUT method"; die;
        return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
    }
}
