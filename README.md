# slowquery
Slowquery图形化显示MySQL慢日志工具

背景：由于天兔Lepus慢查询工具是运行在PHP CI框架里，而不是作为一个独立的web页面接口，所以想直接接入自动化运维平台里，移植代码比较困难，固考虑重构。

参考了开源工具Anemometer图形展示思路，并且把小米Soar工具集成进去，开发在页面上点击慢SQL，就会自动反馈优化建议，从而降低DBA人肉成本，同时也支持自动发送邮件报警功能。

agent客户端慢日志采集分析是结合Percona pt-query-digest工具来实现。

需要安装的步骤如下：

    1、percona-toolkit工具的安装
    
    2、php web mysql环境的搭建
    
    # yum install httpd mysql php php-mysql -y
    
    3、安装Slowquery并配置
    
    4、导入慢查询日志
    
    5、访问界面，查看慢查询
    
    6、配置邮件报警
    
