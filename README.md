# slowquery
Slowquery图形化显示MySQL慢日志工具

视频演示：https://www.douyin.com/video/7278552026181586216

背景：由于天兔Lepus慢查询工具是运行在PHP CI框架里，而不是作为一个独立的web页面接口，所以想直接接入自动化运维平台里，移植代码比较困难，固考虑重构。

参考了开源工具Anemometer图形展示思路，并且把小米Soar工具集成进去，开发在页面上点击慢SQL，就会自动反馈优化建议，从而降低DBA人肉成本，同时也支持自动发送邮件报警功能。

agent客户端慢日志采集分析是结合Percona pt-query-digest工具来实现。

需要安装的步骤如下：

    1、percona-toolkit工具的安装
    
    2、php web mysql环境的搭建
    
    # yum install httpd mysql php php-mysqlnd -y
    
    3、安装Slowquery并配置
    
    4、导入慢查询日志
    
    5、访问界面，查看慢查询
    
    6、配置邮件报警
    
![image](https://dbaplus.cn/uploadfile/2019/0320/20190320101709165.jpg)
![image](https://dbaplus.cn/uploadfile/2019/0320/20190320101724428.jpg)

点击+号，点下面的SQL，会调用Soar反馈优化建议：

![image](https://dbaplus.cn/uploadfile/2019/0320/20190320101739345.png)
![image](https://dbaplus.cn/uploadfile/2019/0320/20190320101808544.jpg)

# 工具搭建配置

1、移动到web目录

    mv  slowquery  /var/www/html/

2、进入到slowquery/slowquery_table_schema目录下

导入dbinfo_table_schema.sql和slowquery_table_schema.sql表结构文件到你的运维管理机MySQL里。

（注：dbinfo表是保存生产MySQL主库的配置信息。）

例：

    mysql -uroot -p123456 sql_db < ./dbinfo_table_schema.sql

    mysql -uroot -p123456 sql_db < ./slowquery_table_schema.sql 

录入你要监控的MySQL主库配置信息

例：

    mysql> INSERT INTO sql_db.dbinfo VALUES (1,'192.168.148.101','test','admin','123456',3306);

3、修改配置文件config.php，将里面的配置改成你的运维管理机MySQL的地址（用户权限最好是管理员）

4、修改配置文件soar_con.php，将里面的配置改成你的运维管理机MySQL的地址（用户权限最好是管理员）

5、进入到slowquery/client_agent_script目录下，把slowquery_analysis.sh脚本拷贝到生产MySQL主库上做慢日志分析推送，并修改里面的配置信息

定时任务（10分钟一次）

    */10 * * * * /bin/bash /usr/local/bin/slowquery_analysis.sh > /dev/null 2>&1

6、别的就没啥配置的了，直接打开浏览器访问slowquery.php就OK了。

http://yourIP/slowquery/slowquery.php

加一个超链接，可方便地接入你们的自动化运维平台里。

7、慢查询邮件推送报警配置。进入到slowquery/alarm_mail/目录里，修改sendmail.php配置信息

定时任务（每隔3小时慢查询报警推送一次）

    0 */3 * * * cd /var/www/html/slowquery/alarm_mail;/usr/bin/php  /var/www/html/slowquery/alarm_mail/sendmail.php > /dev/null 2>&1

![image](https://dbaplus.cn/uploadfile/2019/0320/20190320101826150.jpg)

-------------------------------------------
### 2023年9月13日更新: 用自研的[sql_helper](https://github.com/hcymysql/sql_helper/tree/sql_helper_1.1)替换掉soar，无需部署，直接拉取docker pull slowquery即可。

服务端
# 拉取镜像
```
shell> docker pull docker.io/hcymysql/slowquery:2023-09-13
```
# 启动
```
shell> docker run -itd -e "TERM=xterm-256color" --privileged --name slowquery -p 80:80 -p 3306:3306 <IMAGE ID> /usr/sbin/init
```

# 进入docker里，启动httpd服务
```
shell> docker exec -it slowquery /bin/bash
shell> systemctl start httpd.service 
```
### 打开浏览器，输入http://yourIP/slowquery/slowquery.php
