# rds_listen
1、config中的app.php 中的providers中添加 Onemena\RdsListen\RdsListenServiceProvider::class

2、根目录执行 
php artisan vendor:publish --provider="Onemena\RdsListen\RdsListenServiceProvider"

3、配置 config中的rds-listen.php参数

4、执行php artisan 可以看到新增的两项脚本命令
  frequently:rds                发送rds高峰sql日志
   ali:rds                       阿里云慢查询分析
