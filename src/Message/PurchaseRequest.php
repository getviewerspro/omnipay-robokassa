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
            'InvId' => $this->getTransactionId(),
            'MrchLogin' => $this->getPurse(),
            'OutSum' => $this->getAmount(),
            'InvDesc' => $this->getDescription(),
            'IncCurrLabel' => $this->getCurrency(),
            'SignatureValue' => $this->generateSignature(),
            'Culture' => $this->getLanguage(),
            'IsTest' => (int)$this->getTestMode(),
            'Email' => $this->getEmail(),
        ];
    }

    public function generateSignature()
    {
        $params = [
            $this->getPurse(),
            $this->getAmount(),
            $this->getTransactionId(),
            $this->getSecretKey()
        ];

        return md5(implode(':', $params));
    }

    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
