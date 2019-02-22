<?php

namespace modmore\Commerce\Modules\Pure360;

use modmore\Commerce\Modules\BaseModule;
use Symfony\Component\EventDispatcher\EventDispatcher;
use modmore\Commerce\Frontend\Steps\Cart;
use modmore\Commerce\Events\Checkout;
use modmore\Commerce\Frontend\Steps\Payment;
use modmore\Commerce\Frontend\Steps\ThankYou;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Admin\Widgets\Form\TextareaField;
use modmore\Commerce\Admin\Widgets\Form\CheckboxField;
class Signup extends BaseModule
{
    public function getName()
    {
        return 'Pure360 Marketing Email';
    }

    public function getAuthor()
    {
        return 'Mark Calimosa,Nathanael McMillan';
    }

    public function getDescription()
    {
        return 'Module for Pure360 API';
    }

    public function initialize(EventDispatcher $dispatcher)
    {
        $dispatcher->addListener(\Commerce::EVENT_CHECKOUT_BEFORE_STEP, array($this, 'checkAcceptedMarketing'));
        $dispatcher->addListener(\Commerce::EVENT_CHECKOUT_AFTER_STEP, array($this, 'sendMarketing'));
    }

    public function checkAcceptedMarketing(Checkout $event){
        $step = $event->getStep();
        if (($step instanceof Cart)) {
            $step->setPlaceholder('module_pure360_marketing_enabled', true);
            $order = $event->getOrder();
            $accepted = (bool)$event->getDataKey('accept_marketing', false);
            $order->setProperty('accepted_marketing', $accepted);
            $order->save();
            return;
        }

    }

    public function sendMarketing(Checkout $event){
        $step = $event->getStep();
        if (($step instanceof ThankYou)) {
            $order = $event->getOrder();
            $is_marketing_accepted = $order->getProperty('accepted_marketing');
            if($is_marketing_accepted){
                $address = $order->getShippingAddress()->toArray();
                $email = $address['email'];
                $products = [];
                $items = $order->getItems();
                foreach($items as $item){
                    $products[] = $item->get('name');
                }
                $implode_products = implode('||',$products);
                $address['products'] = $implode_products;

                // Data fields
                $list_fields = $this->getConfig('custom_fields');
                $add_list_fields = array();
                $list_columns = explode(",", $list_fields);
                foreach ($list_columns as $column) {
                    $split = explode(":", $column);
                    $add_list_fields[$split[0]] = isset($address[$split[1]]) ? $address[$split[1]] : '';
                }
                if(!$load_module = $this->adapter->loadClass($this->getConfig('class_name'),$this->getConfig('class_path'),true,true)){
                    $this->adapter->log(modX::LOG_LEVEL_ERROR,'Failed to load module');
                    return;
                };
                $double_optin = $this->getConfig('double_optin') ? 'TRUE' : 'FALSE';
                $pure360 = new \pure360();
                $signUp = $pure360->signUp($this->getConfig('account_name'),$this->getConfig('list_name'),$email,$add_list_fields,$double_optin);
                if($signUp == 'OK'){
                    return;
                }
                else{
                    $this->adapter->log(modX::LOG_LEVEL_ERROR,$signUp);
                }
            }

            return;
        }
    }

    public function getModuleConfiguration(\comModule $module)
    {
        $fields = [];

        $fields[] = new TextField($this->commerce, [
            'name' => 'properties[account_name]',
            'label' => $this->adapter->lexicon('commerce.module.pure360.account_name'),
            'description' => $this->adapter->lexicon('commerce.module.pure360.account_name.description'),
            'value' => $module->getProperty('account_name'),
            'validation' => [
                new \modmore\Commerce\Admin\Widgets\Form\Validation\Required(),
            ]
        ]);

        $fields[] = new TextField($this->commerce, [
            'name' => 'properties[list_name]',
            'label' => $this->adapter->lexicon('commerce.module.pure360.list_name'),
            'description' => $this->adapter->lexicon('commerce.module.pure360.list_name.description'),
            'value' => $module->getProperty('list_name'),
            'validation' => [
                new \modmore\Commerce\Admin\Widgets\Form\Validation\Required(),
            ]
        ]);

        $fields[] = new TextareaField($this->commerce, [
            'name' => 'properties[custom_fields]',
            'label' => $this->adapter->lexicon('commerce.module.pure360.custom_fields'),
            'description' => $this->adapter->lexicon('commerce.module.pure360.custom_fields.description'),
            'value' => $module->getProperty('custom_fields'),
            'validation' => [
                new \modmore\Commerce\Admin\Widgets\Form\Validation\Required(),
            ]
        ]);

        $fields[] = new CheckboxField($this->commerce, [
            'name' => 'properties[double_optin]',
            'label' => $this->adapter->lexicon('commerce.module.pure360.double_optin'),
            'description' => $this->adapter->lexicon('commerce.module.pure360.double_optin.description'),
            'value' => $module->getProperty('double_optin')
        ]);

        $fields[] = new TextField($this->commerce, [
            'name' => 'properties[class_name]',
            'label' => $this->adapter->lexicon('commerce.module.pure360.class_name'),
            'description' => $this->adapter->lexicon('commerce.module.pure360.class_name.description'),
            'value' => $module->getProperty('class_name'),
            'validation' => [
                new \modmore\Commerce\Admin\Widgets\Form\Validation\Required(),
            ]
        ]);

        $fields[] = new TextField($this->commerce, [
            'name' => 'properties[class_path]',
            'label' => $this->adapter->lexicon('commerce.module.pure360.class_path'),
            'description' => $this->adapter->lexicon('commerce.module.pure360.class_path.description'),
            'value' => $module->getProperty('class_path'),
            'validation' => [
                new \modmore\Commerce\Admin\Widgets\Form\Validation\Required(),
            ]
        ]);



        return $fields;
    }

}