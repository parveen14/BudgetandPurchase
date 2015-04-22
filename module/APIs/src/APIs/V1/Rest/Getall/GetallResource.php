<?php
namespace APIs\V1\Rest\Getall;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class GetallResource extends AbstractResourceListener
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
        return new ApiProblem(405, 'The POST method has not been defined');
    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
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
		$type = $this->getEvent()->getRouteMatch()->getParam('type');
		$companyid = $this->getEvent()->getRouteMatch()->getParam('param2');
        if(isset($type) AND !empty($type)) {
           switch ($type) {
               case 'countries':
                   return $this->mapper->getcountries();
                   break;
               case 'wbs':
                   return $this->mapper->getwbs($companyid);
                   break;
               default:
               case 'suppliers':
                   return $this->mapper->getsuppliers($companyid);
                   break;
		       case 'costcenter':
                   return $this->mapper->getcostcenter($companyid);
                   break;
               default:
                   return new ApiProblem(422, 'Method not defined','Server side validation error','Method not found');
           }
           
       } else {
           return new ApiProblem(422, 'Method not defined','Server side validation error','Method not found');
       }
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array())
    { echo "sdfds"; 
	print_r($params); die;
	
        return new ApiProblem(405, 'The GET method has not been defined for collections');
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
        return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
    }
}
