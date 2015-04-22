<?php
namespace APIs\V1\Rest\Purchaserequest;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class PurchaserequestResource extends AbstractResourceListener
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
        try {
           $data1=(array)$data;
           if(isset($data1['i_purchase_id']) AND !empty($data1['i_purchase_id'])) {
               return $this->mapper->updatepurchaserequest($data1);
            } else {
                return $this->mapper->addpurchaserequest($data1);
            }
        } catch (\Exception $e) {
            return new ApiProblem(405, $e->getMessage());
        }
    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
		try { 
			if(empty($id)) {
				return new ApiProblem(405, 'ID is required field');
			}
            return $this->mapper->deletepurchaserequest($id);
        } catch (\Exception $e) {
            return new ApiProblem(405, $e->getMessage());
        }
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
     try {
           if(empty($id)) {
               throw new \Exception("Company id required");
           }
     
            return $this->mapper->getpurchaserequest($id);
            
            //return new ApiProblem(405, 'The POST method has not been defined');
        } catch (\Exception $e) {
            return new ApiProblem(405, $e->getMessage());
        }
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array())
    {
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
        echo $id;
        echo $data;die;
        return new ApiProblem(200, 'The PATCH method has not been defined for individual resources');
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
        echo "HERE"; 
        echo $id;
        echo $data; die;
       // return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
    }
}
