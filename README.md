<img align="right" width="100" src="https://kodo-cdn.dtapp.net/04/999e9f2f06d396968eacc10ce9bc8a.png" alt="www.dtapp.net"/>

<h1 align="left"><a href="https://www.dtapp.net/">ThinkPHP6七牛云短信扩展包</a></h1>

📦 ThinkPHP6七牛云短信扩展包

[![Code Health](https://hn.devcloud.huaweicloud.com/codecheck/v1/codecheck/task/codehealth.svg?taskId=9046b097b0164105b8a3078be4d2fa95)](https://hn.devcloud.huaweicloud.com/codecheck/project/b7a03c9ea96e40cb93fed6e23a27a7be/codecheck/task/9046b097b0164105b8a3078be4d2fa95/detail)
[![Latest Stable Version](https://poser.pugx.org/dtapps/think-qiniu-sms/v/stable)](https://packagist.org/packages/dtapps/think-qiniu-sms)
[![Latest Unstable Version](https://poser.pugx.org/dtapps/think-qiniu-sms/v/unstable)](https://packagist.org/packages/dtapps/think-qiniu-sms)
[![Total Downloads](https://poser.pugx.org/dtapps/think-qiniu-sms/downloads)](https://packagist.org/packages/dtapps/think-qiniu-sms)
[![License](https://poser.pugx.org/dtapps/think-qiniu-sms/license)](https://packagist.org/packages/liguangchun/think-library)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.1-8892BF.svg)](http://www.php.net/)
[![Build Status](https://travis-ci.org/GC0202/ThinkLibrary.svg?branch=6.0)](https://travis-ci.org/dtapps/think-qiniu-sms)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dtapps/think-qiniu-sms/badges/quality-score.png?b=6.0)](https://scrutinizer-ci.com/g/dtapps/think-qiniu-sms/?branch=6.0)
[![Code Coverage](https://scrutinizer-ci.com/g/dtapps/think-qiniu-sms/badges/coverage.png?b=6.0)](https://scrutinizer-ci.com/g/dtapps/think-qiniu-sms/?branch=6.0)

## 依赖环境

1. PHP7.1 版本及以上

## 托管

- 国外仓库地址：[https://github.com/dtapps/think-qiniu-sms](https://github.com/dtapps/think-qiniu-sms)
- 国内仓库地址：[https://gitee.com/dtapps/think-qiniu-sms](https://gitee.com/dtapps/think-qiniu-sms)
- Packagist
  地址：[https://packagist.org/packages/dtapps/think-qiniu-sms](https://packagist.org/packages/dtapps/think-qiniu-sms)

### 开发版

```text
composer require dtapps/think-qiniu-sms ^6.x-dev -vvv
```

### 稳定版

```text
composer require dtapps/think-qiniu-sms ^6.0.* -vvv
```

## 更新

```text
composer update dtapps/think-qiniu-sms -vvv
```

## 删除

```text
composer remove dtapps/think-qiniu-sms -vvv
```

## 获取电脑Mac地址服务使用示例

```php
use dtapps\qiniu\sms\SmsService;

list($ret, $err) = SmsService::instance()
    ->setParam([
        "template_id" => "",
        "mobiles" => [""],
        "parameters" => [
            "code" => ""
        ],
    ])
    ->accessKey("")
    ->secretKey("")
    ->toArray();

var_dump($ret);
var_dump($err);
```