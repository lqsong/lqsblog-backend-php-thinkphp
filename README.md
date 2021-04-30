# lqsblog-backend-php-thinkphp

 它（[Github](https://github.com/lqsong/lqsblog-backend-php-thinkphp) 、 [Gitee](https://gitee.com/lqsong/lqsblog-backend-php-thinkphp)）是一个PHP API后端服务，它基于 [ThinkPHP 6.x](https://www.kancloud.cn/manual/thinkphp6_0/content) 实现（Composer + thinkphp6 + RBAC + Jwt（+ 自动刷新） + Restful）。


## 开发文档

- [lqsBlog官方文档](http://docs.liqingsong.cc/guide/backendservice/php-thinkphp.html)。

- [ADMIN DEMO](http://lqsblog-demo.admin-element-vue.liqingsong.cc/)

- [PC DEMO](http://liqingsong.cc/)

## 功能

```sh
- 登录 / 注销 (RBAC + jwt 验证，自动刷新jwt)
- 首页 / 统计
- 随笔
- 作品
- 专题
- 左邻右舍
- 设置
  - 关于我
  - 标签
  - 账号
  - 角色
  - 后台菜单
  - 后台API
  - 站点配置
```

## 技术选型

- 核心框架：ThinkPHP 6.x
- 安全框架：自定义 RBAC
- 会话管理: JWT
- api风格：restful

## 运行环境要求

- PHP7.1+，兼容PHP8.0
- MySQL5.7 +


## 安装

```bash
composer install
```

如果需要更新框架使用
```bash
composer update xxxx
```



## 捐赠

如果你觉得这个项目帮助到了你，你可以请作者喝咖啡表示鼓励.

**ALIPAY**             |  **WECHAT**
:-------------------------:|:-------------------------:
![Alipay](http://uploads.liqingsong.cc/20210430/f62d2436-8d92-407d-977f-35f1e4b891fc.png)  |  ![Wechat](http://uploads.liqingsong.cc/20210430/3e24efa9-8e79-4606-9bd9-8215ce1235ac.png)
