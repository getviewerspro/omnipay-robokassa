<?php
/**
 * RoboKassa driver for Omnipay PHP payment library.
 *
 * @link      https://github.com/hiqdev/omnipay-robokassa
 * @package   omnipay-robokassa
 * @license   MIT
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace Omnipay\RoboKassa\Message;

class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate(
            'purse', 'amount', 'currency', 'description'
        );

        return [
            'InvId' => $this->getInvId(),
            'MrchLogin' => $this->getPurse(),
            'OutSum' => $this->getAmount(),
            'InvDesc' => $this->getDescription(),
            'IncCurrLabel' => $this->getCurrency(),
            'SignatureValue' => $this->generateSignature(),
            'Culture' => $this->getLanguage(),
            'IsTest' => (int)$this->getTestMode(),
            'Email' => $this->getEmail(),
        ] + $this->getCustomFields();
    }

    public function generateSignature()
    {
        $params = [
            $this->getPurse(),
            $this->getAmount(),
            $this->getInvId(),
            $this->getSecretKey()
        ];

        foreach ($this->getCustomFields() as $field => $value) {
            $params[] = "$field=$value";
        }

        return md5(implode(':', $params));
    }

    public function getCustomFields()
    {
        $fields = array_filter([
            'Shp_TransactionId' => $this->getTransactionId(),
            'Shp_Currency' => $this->getCurrency()
        ]);

        ksort($fields);

        return $fields;
    }
    
    public function getLanguage()
    {
        return $this->getParameter('language');
    }
    
    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }

    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
