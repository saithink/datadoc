<h1 align="center">wiki</h1>
<p align="center">
ThinkPHP6.0 数据字典
</p>

## 环境需求

>  - PHP >= 5.4

## 安装

使用 [composer](http://getcomposer.org/):

```shell
$ composer require saithink/datadoc
```

## 项目地址
下载项目

```shell
git clone https://github.com/saithink/datadoc.git
```
## 使用方式
#### 预览地址
http://xxx.com/datadoc/docs
### 自定义接口
在config目录下包含一个datadoc.php,这个文件是项目的配置文件,可以设置白名单，过滤掉某些表
```php
<?php
// +----------------------------------------------------------------------
// | 数据字典设置
// +----------------------------------------------------------------------
return [
    // 应用名称
    'app_name' => '项目管理系统数据字典',
    // 应用版本
    'app_ver' => 'V1.0.0',
    // 屏蔽表
    'white_list' => [],
];
```
### 预览效果
![https://raw.githubusercontent.com/saithink/datadoc/main/images/preview.png](https://raw.githubusercontent.com/saithink/datadoc/main/images/preview.png)
