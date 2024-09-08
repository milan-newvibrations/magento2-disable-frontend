<?php
namespace Abelbm\DisableFrontend\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Backend\Helper\Data;
use Abelbm\DisableFrontend\Helper\Data as DisableFrontendHelper;

class DisableFrontend implements ObserverInterface{

    protected   $_actionFlag;
    protected   $redirect;
    private     $helperBackend;
    private     $logger;
    private     $disableFrontendHelper;
    private     $responseFactory;
    private     $storeManager;
    
    /**
     * DisableFrontend constructor.
     *
     * @author Abel Bolanos Martinez <abelbmartinez@gmail.com>
     * @param ActionFlag $actionFlag
     * @param RedirectInterface $redirect
     * @param Data $helperBackend
     * @param DisableFrontendHelper $disableFrontendHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ActionFlag $actionFlag,
        RedirectInterface $redirect,
        Data $helperBackend,
        DisableFrontendHelper $disableFrontendHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
        
    ) {
        $this->_actionFlag = $actionFlag;
        $this->redirect = $redirect;
        $this->helperBackend = $helperBackend;
        $this->logger = $logger;
        $this->disableFrontendHelper = $disableFrontendHelper;
        $this->responseFactory = $responseFactory;
        $this->storeManager = $storeManager;
    }
    
    /**
     * Show an empty page(default) or redirect to Admin site.
     * Depend in the config in
     * Stores > Configuration > Advanced > Admin > Disable Frontend
     *
     * @author Abel Bolanos Martinez <abelbmartinez@gmail.com>
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer){
        $task = $this->disableFrontendHelper->getConfigValue();
         //$this->logger->info('TEST');
        
        if ($task){
             $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);            
            if ($task == 2){
                $root = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
                $this->responseFactory->create()->setRedirect($root .'/index.html')->sendResponse();
                die();
            }elseif ($task == 3){//redirect to Admin
                $controller = $observer->getControllerAction();
                $this->redirect->redirect($controller->getResponse(),$this->helperBackend->getHomePageUrl());
            }
        }else{   
            return $this;
        }
    }
}
