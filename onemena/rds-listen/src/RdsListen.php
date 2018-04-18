<?php
/**
 * Created by PhpStorm.
 * User: chenhuan
 * Date: 2018/4/17
 * Time: 下午1:42
 */

namespace Onemena\RdsListen;

use aliyun\core\DefaultAcsClient;
use aliyun\core\exception\ClientException;
use aliyun\core\exception\ServerException;
use aliyun\core\profile\DefaultProfile;
use aliyun\dm\SingleSendMailRequest;

class RdsListen
{

    private $client;
    private $config;

    public function __construct()
    {


        $config = config('rds-listen.mail');
        # 创建DefaultAcsClient实例并初始化
        $clientProfile = DefaultProfile::getProfile(
            $config['RigionId'],                   # 您的 Region ID
            $config['AccessKeyId'],               # 您的 AccessKey ID
            $config['AccessKeySecret']           # 您的 AccessKey Secret
        );
        $client = new DefaultAcsClient($clientProfile);
        # 创建API请求并设置参数
        $this->client = $client;
        $this->config = $config;

    }

    public function send($subject,$body){


        $request = new SingleSendMailRequest();
        $request->setAccountName($this->config['MAIL_ALI_ACCOUNT_ALIAS']);
        $request->setFromAlias($this->config['MAIL_ALI_ACCOUNT_NAME']);
        $request->setAddressType(1);
        $request->setReplyToAddress("true");
        $request->setToAddress($this->config['MAIL_ALI_TO_MAIL']);
        $request->setSubject($subject);
        $request->setHtmlBody($body);


        # 发起请求并处理返回
        try {
            $response = $this->client->getAcsResponse($request);
            return $response;

        } catch(ServerException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
            return false;

        } catch(ClientException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
            return false;

        }

    }



}