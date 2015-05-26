<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class IndexController extends AbstractActionController implements ServiceLocatorAwareInterface
{
    protected $_objectManager;
    protected $sm;
    
    public function indexAction()
    {     
    	$response = $this->getAPI('thejournal');
    	
    	if ($response->getStatusCode() != 200) {
    		$error = $response->getReasonPhrase();
    	}
    	
    	//print_r($response->getBody());
    	
        return new ViewModel(array(
        	'error' => isset($error) ? $error : null,
        	'content' => json_decode($response->getBody()),
        ));
    }
    
    public function googleAction()
    {
    	$response = $this->getAPI('tag/google');
    	
    	return new ViewModel(array(
        	'error' => isset($error) ? $error : null,
        	'content' => json_decode($response->getBody()),
    	));
    }
    
    private function getAPI($tag)
    {
    	$config = $this->getServiceLocator()->get('config');
    	 
    	$url = $config['api']['url'].'/'.$tag;
    	 
    	$curlConfig = array(
    			'adapter'   => 'Zend\Http\Client\Adapter\Curl',
    			'curloptions' => array(
    					CURLOPT_USERPWD => $config['api']['username'].':'.$config['api']['password'],
    					CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
    					CURLOPT_HEADER => false,
    					CURLOPT_RETURNTRANSFER => true,
    					CURLOPT_TIMEOUT => 1000,
    			),
    	);
    	 
    	$client = new \Zend\Http\Client($url, $curlConfig);
    	$response = $client->send();
    	$httpCode = $response->getStatusCode();
    	$curlError = $response->getReasonPhrase();
    	 
    	return $response;
    }
}