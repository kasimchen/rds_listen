<?php
/**
 * Created by PhpStorm.
 * User: chenhuan
 * Date: 2018/4/17
 * Time: 下午1:41
 */

return [

    'server_list'=>[
            "server_1"=>[
                'RigionId'=>'cn-hongkong',//区域
                'AccessKeyId'=>'',//ak
                'AccessKeySecret'=>'',//sk
                'DBInstanceId'=>'',//数据库实例
                'RDS_MySQL_QPSTPS_MAX'=>env('RDS_MySQL_QPSTPS_MAX',10),//超过此值发送邮件
                'Name'=>'服务器1',
		'ReportDuringTime'=>env('REPORT_DURING_TIME',3600)//多少秒内的频繁sql语句查询

            ]
        ],
    'mail'=>[

        'RigionId'=>'cn-beijing',//区域
        'AccessKeyId'=>env('MAIL_ALI_APP_KEY'),//ak
        'AccessKeySecret'=>env('MAIL_ALI_SECRET'),//sk
        'MAIL_ALI_ACCOUNT_ALIAS'=>env('MAIL_ALI_ACCOUNT_ALIAS','发送人账号'),
        'MAIL_ALI_ACCOUNT_NAME'=>env('MAIL_ALI_ACCOUNT_NAME','发送人名称'),
        'MAIL_ALI_TO_MAIL'=>env('MAIL_ALI_TO_MAIL','收件人邮箱')

    ]
    ];
