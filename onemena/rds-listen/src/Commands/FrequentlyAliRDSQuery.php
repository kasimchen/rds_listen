<?php

namespace Onemena\RdsListen\Commands;

use aliyun\rds\request\DescribeDBInstancePerformanceRequest;
use aliyun\rds\request\DescribeSQLLogReportListRequest;
use Illuminate\Console\Command;
use aliyun\core\DefaultAcsClient;
use aliyun\core\exception\ClientException;
use aliyun\core\exception\ServerException;
use aliyun\core\profile\DefaultProfile;
use Illuminate\Support\Facades\Storage;
use Onemena\RdsListen\RdsListen;

class FrequentlyAliRDSQuery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'frequently:rds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发送rds高峰sql日志';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {



        $server_config = config('rds-listen.server_list');

        foreach ($server_config as $config){

            # 创建DefaultAcsClient实例并初始化
            $clientProfile = DefaultProfile::getProfile(
                $config['RigionId'],                   # 您的 Region ID
                $config['AccessKeyId'],               # 您的 AccessKey ID
                $config['AccessKeySecret']           # 您的 AccessKey Secret
            );
            $client = new DefaultAcsClient($clientProfile);
            # 创建API请求并设置参数

            $status = $this->getStatus($client,$config);

            if($status>$config['RDS_MySQL_QPSTPS_MAX']){
                $this->send($client,$config);
            }else{
                exit('QPS '.$status);
            }
        }
    }


    public function getStatus(DefaultAcsClient $client,$config){

        $request = new DescribeDBInstancePerformanceRequest();
        $request->setDBInstanceId($config['DBInstanceId']);
        $date = date("Y-m-d",time()).'T';
        $start_time = $date.date("H:i",time()-3600*3).'Z';
        $end_time = $date.date("H:i",time()).'Z';

        $request->setKey("MySQL_QPSTPS");
        $request->setStartTime($start_time);
        $request->setEndTime($end_time);
        # 发起请求并处理返回


        try {

            $response = $client->getAcsResponse($request);
            if(empty($response->PerformanceKeys->PerformanceKey[0]->Values->PerformanceValue)){
                exit('没有内容');
            }

            $qps = array();
            foreach ($response->PerformanceKeys->PerformanceKey[0]->Values->PerformanceValue as $item){
                $qps = $item;
            }

            $value = explode('&',$qps->Value);
            $value = (float)$value[0];
            return $value;

        } catch(ServerException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
        } catch(ClientException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
        }


    }

    public function send(DefaultAcsClient $client,$config){


        $request = new DescribeSQLLogReportListRequest();
        $request->setDBInstanceId($config['DBInstanceId']);
        $date = date("Y-m-d",time()).'T';
        $start_time = $date.date("H:i:s",time()-$config['ReportDuringTime']).'Z';
        $end_time = $date.date("H:i:s",time()).'Z';

        $request->setStartTime($start_time);
        $request->setEndTime($end_time);
        $request->setPageSize(10);



        # 发起请求并处理返回
        try {
            $response = $client->getAcsResponse($request);

            $data = array();


            foreach ($response->Items as $item){
                foreach ($item as $i){

                    $qpstop = $latency = array();
                    foreach ($i->QPSTopNItems->QPSTopNItem as $q){
                        $qpstop[] = array('SQLExecuteTimes'=>$q->SQLExecuteTimes,'sql'=>$q->SQLText);
                    }

                    if(empty($qpstop)) continue;

                    $ReportTime = $i->ReportTime;

                    $data[] = ['qpstop'=>$qpstop,'ReportTime'=>$ReportTime];
                }
            }

            if(empty($data)) exit('没有内容');

            $time = date('Y-m-d H:i:s',time());

            $html = view('vendor.rds-listen.alirds_frenquently_log',compact('data'))->render();

            if(!Storage::exists('public/alirds_frenquently_log')){
                Storage::makeDirectory('public/alirds_frenquently_log');
            }

            $file_path = storage_path('app/public/alirds_frenquently_log/').date('Y-m-d',time()-3600*24).'.html';
            if(\File::exists($file_path)){
                \File::delete($file_path);
            }

            \File::append($file_path,$html);

            $this->sendEmail("RDS高峰查询日志-".$config['Name']."-".$time,$html);

        } catch(ServerException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
        } catch(ClientException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
        }



    }

    public function sendEmail($subject,$html){

        $mailHelper = new RdsListen();
        $result = $mailHelper->send($subject,$html);
        if($result){
            \Log::info('邮件发送成功-'.$subject);
            exit('发送成功');

        }else{
            \Log::info('邮件发送失败'.$subject);
            exit('发送失败');
        }

    }

}
