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

        $data = [
            'InvId' => $this->getTransactionId(),
            'MerchantLogin' => $this->getPurse(),
            'OutSum' => $this->getAmount(),
            'Description' => $this->getDescription(),
            'IncCurrLabel' => $this->getCurrency(),
            'OutSumCurrency' => $this->getCurrency(),
            'SignatureValue' => $this->generateSignature(),
            'IsTest' => (int)$this->getTestMode(),
            'Receipt' => $this->getReceipt(),
            'Email' => $this->getEmail(),
            'Culture' => $this->getLanguage(),
        ] + $this->getCustomFields();

        info($data);
        
        return $data;
    }

    public function generateSignature()
    {
        $params = [
            $this->getPurse(),
            $this->getAmount(),
            $this->getInvId()
        ];
        
        if ($this->getCurrency()) {
            $params[] = $this->getCurrency();
        }
        if ($this->getReceipt()) {
            $params[] = $this->getReceipt();
        }
        $params[] = $this->getSecretKey();

        foreach ($this->getCustomFields() as $field => $value) {
            $params[] = "$field=$value";
        }

        $hash = md5(implode(':', $params));

        info($params);
        info($hash);

        return $hash;
    }

    public function getCustomFields()
    {
        $fields = array_filter([
            'Shp_TransactionId' => $this->getTransactionId(),
            'Shp_Client' => $this->getClient()
        ]);

        ksort($fields);

        return $fields;
    }

    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
