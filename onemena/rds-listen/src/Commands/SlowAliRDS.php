<?php

namespace Onemena\RdsListen\Commands;

use aliyun\core\DefaultAcsClient;
use aliyun\core\exception\ClientException;
use aliyun\core\exception\ServerException;
use aliyun\core\profile\DefaultProfile;
use aliyun\rds\request\DescribeSlowLogsRequest;
use Illuminate\Console\Command;
use Onemena\RdsListen\RdsListen;

class SlowAliRDS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ali:rds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '阿里云慢查询分析';

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

            $html = $this->getLog($client,$config);
            $this->sendEmail("RDS慢查询--".$config['Name'],$html);
        }


    }


    private function getLog(DefaultAcsClient $client,$config){

        $request = new DescribeSlowLogsRequest();
        $request->setDBInstanceId($config['DBInstanceId']);
        $time = date('Y-m-d',time()-3600*24)."Z";
        $request->setStartTime($time);
        $request->setEndTime($time);
        $request->setPageSize(50);

        # 发起请求并处理返回
        try {
            $response = $client->getAcsResponse($request);
            if(!empty($response->Items->SQLSlowLog)){

                $logs = array();
                foreach ($response->Items->SQLSlowLog as $item){
                    unset($item->SQLId,$item->CreateTime,$item->SlowLogId,$item->ParseMaxRowCount);
                    $logs[]= (array)$item;
                }

                $logs = array_sort($logs,function($log){
                    return $log['MySQLTotalExecutionTimes'];
                });

                $collection = collect($logs);
                $collection->sortBy('MaxExecutionTime');
                $data = $collection->values()->all();
                $html = view('vendor.rds-listen..alirds_log',compact('data','time'));


                if(!\Storage::exists('public/alirds_log')){
                    \Storage::makeDirectory('public/alirds_log');
                }

                $file_path = storage_path('app/public/alirds_log/').date('Y-m-d',time()-3600*24).'.html';


                if(\File::exists($file_path)){
                    \File::delete($file_path);
                }

                \File::append($file_path,$html);

                return $html;

            }



        } catch(ServerException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
            return false;
        } catch(ClientException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
            return false;

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
