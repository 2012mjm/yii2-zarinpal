<?php

namespace mjm\zarinpal;

use SoapClient;
use yii\base\Model;

class Zarinpal extends Model
{
    public $merchant_id;
    public $callback_url;
    public $active_sandbox = false;
    private $_status;
    private $_authority;
    private $_ref_id;

    public function request($amount, $description, $email = null, $mobile = null)
    {
        $client = new SoapClient($this->generateSoapUrl(), ['encoding' => 'UTF-8']);
        $result = $client->PaymentRequest(
            [
                'MerchantID'  => $this->merchant_id,
                'Amount'      => $amount,
                'Description' => $description,
                'Email'       => $email,
                'Mobile'      => $mobile,
                'CallbackURL' => $this->callback_url,
            ]
        );

        $this->_status = $result->Status;
        $this->_authority = $result->Authority;

        return $this;
    }

    public function verify($authority, $amount)
    {
        $this->_authority = $authority;
        $client = new SoapClient($this->generateSoapUrl(), ['encoding' => 'UTF-8']);
        $result = $client->PaymentVerification(
            [
                'MerchantID' => $this->merchant_id,
                'Authority'  => $this->_authority,
                'Amount'     => $amount,
            ]
        );

        $this->_status = $result->Status;
        $this->_ref_id = $result->RefID;

        return $this;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function getRefID()
    {
        return $this->_ref_id;
    }

    public function getRedirectUrl($zaringate = true)
    {
        if($this->active_sandbox) {
            $url = 'https://sandbox.';
        }
        else {
            $url = 'https://www.';
        }

        $url .= 'zarinpal.com/pg/StartPay/'.$this->_authority;
        $url .=  ($zaringate) ? '/ZarinGate' : '';

        return $url;
    }

    public function getAuthority()
    {
        return $this->_authority;
    }

    private function generateSoapUrl()
    {
        if($this->active_sandbox) {
            return 'https://sandbox.zarinpal.com/pg/services/WebGate/wsdl';
        }

        return 'https://www.zarinpal.com/pg/services/WebGate/wsdl';
    }
}
