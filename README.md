# 数据标注任务发布平台

基于 ThinkPHP 6.x 框架开发的数据标注任务发布系统，提供用户系统、任务管理、收益管理、FAQ 等功能。

## 功能特性

### 前台功能
- **用户系统**：注册、登录、自动登录、补充教育经历和工作经历
- **任务广场**：浏览所有任务、按名称搜索、按时间/热度/价格排序
- **任务详情**：任务基本信息、招募要求、评论、申请流程（钉钉群二维码引导）
- **我参与的**：任务列表按状态分类（全部、待提交、待审核、通过审核、已拒绝）
- **个人中心**：待入账/累计收益展示、收益明细查看
- **FAQ功能**：常见问题解答、问题分类

### 管理后台功能
- **用户管理**：用户列表、用户详情、状态管理、密码重置
- **任务管理**：任务列表、任务添加/编辑、任务审核、任务分类管理
- **FAQ管理**：FAQ列表、FAQ添加/编辑、FAQ分类管理
- **系统配置**：网站基本配置

## 技术栈

- **后端**：PHP 7.2.5+、ThinkPHP 6.x
- **前端**：Bootstrap 5.3、Bootstrap Icons、Axios
- **数据库**：MySQL 5.7+

## 项目结构

```
biaozhu/
├── app/
│   ├── controller/           # 控制器
│   │   ├── admin/           # 后台控制器
│   │   │   ├── Index.php    # 后台首页/登录
│   │   │   ├── User.php     # 用户管理
│   │   │   ├── Task.php     # 任务管理
│   │   │   ├── Faq.php      # FAQ管理
│   │   │   └── Config.php   # 系统配置
│   │   ├── Index.php        # 前台首页/登录/注册/FAQ
│   │   ├── Task.php         # 任务相关
│   │   └── User.php         # 用户相关
│   ├── middleware/          # 中间件
│   │   ├── Auth.php         # 用户认证
│   │   └── AdminAuth.php    # 管理员认证
│   ├── model/               # 模型
│   │   ├── User.php
│   │   ├── UserEducation.php
│   │   ├── UserWork.php
│   │   ├── Task.php
│   │   ├── TaskCategory.php
│   │   ├── UserTask.php
│   │   ├── Earnings.php
│   │   ├── Faq.php
│   │   ├── FaqCategory.php
│   │   ├── TaskComment.php
│   │   ├── Admin.php
│   │   └── Config.php
│   ├── view/                # 视图
│   │   ├── admin/          # 后台视图
│   │   │   ├── index/
│   │   │   ├── user/
│   │   │   └── layout/
│   │   ├── index/          # 前台视图
│   │   ├── task/
│   │   ├── user/
│   │   └── layout/
│   ├── BaseController.php
│   └── common.php          # 公共函数
├── config/                 # 配置文件
│   ├── app.php
│   ├── database.php
│   └── view.php
├── database/               # 数据库文件
│   └── init.sql           # 数据库初始化脚本
├── public/                 # 公共目录
│   └── index.php          # 入口文件
├── route/                  # 路由配置
│   └── app.php
├── composer.json
└── .env                    # 环境配置
```

## 安装部署

### 环境要求

- PHP >= 7.2.5
- MySQL >= 5.7
- Composer

### 安装步骤

1. **克隆项目**

```bash
cd your-project-directory
```

2. **安装依赖**

```bash
composer install
```

3. **配置数据库**

创建数据库 `biaozhu`，然后导入数据库初始化脚本：

```bash
mysql -u root -p biaozhu < database/init.sql
```

4. **修改配置文件**

复制 `.env` 文件并修改数据库配置：

```env
[DATABASE]
HOSTNAME = 127.0.0.1
DATABASE = biaozhu
USERNAME = root
PASSWORD = your_password
HOSTPORT = 3306
CHARSET = utf8mb4
PREFIX = bz_
```

5. **配置 Web 服务器**

**Nginx 配置示例：**

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/biaozhu/public;
    index index.php;

    location / {
        if (!-e $request_filename) {
            rewrite ^(.*)$ /index.php?s=$1 last;
        }
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

**Apache 配置：**

确保开启 `mod_rewrite`，项目已包含 `.htaccess` 配置。

6. **访问系统**

- 前台地址：`http://your-domain.com/`
- 后台地址：`http://your-domain.com/admin`

### 默认账号

- **管理员账号**：`admin` / `admin123`

## 数据库说明

主要数据表：

| 表名 | 说明 |
|------|------|
| bz_users | 用户表 |
| bz_user_education | 用户教育经历 |
| bz_user_work | 用户工作经历 |
| bz_tasks | 任务表 |
| bz_task_categories | 任务分类 |
| bz_user_tasks | 用户任务关联 |
| bz_earnings | 收益明细 |
| bz_faqs | FAQ表 |
| bz_faq_categories | FAQ分类 |
| bz_task_comments | 任务评论 |
| bz_admins | 管理员表 |
| bz_configs | 系统配置 |

## 功能说明

### 用户系统流程

1. 用户注册 → 自动登录 → 提示补充教育经历和工作经历
2. 完善资料后可正常申请任务

### 任务申请流程

1. 用户在任务广场浏览任务
2. 点击任务进入详情页
3. 点击"申请任务"按钮
4. 弹出钉钉群二维码，引导用户加群
5. 用户完成任务后提交
6. 管理员审核通过后计算收益

### 收益结算

- 任务审核通过后，收益进入"待入账"状态
- 每月15日统一结算上月通过审核的任务收益

## 开发说明

### 控制器命名规范

- 前台控制器：`app\controller\` 目录下
- 后台控制器：`app\controller\admin\` 目录下

### 路由规则

路由配置在 `route/app.php` 文件中，主要路由：

| 路由 | 控制器方法 | 说明 |
|------|-----------|------|
| GET / | Index@index | 首页 |
| GET/POST /login | Index@login | 登录 |
| GET/POST /register | Index@register | 注册 |
| GET /task | Task@index | 任务广场 |
| GET /task/detail | Task@detail | 任务详情 |
| GET /user/profile | User@profile | 完善资料 |
| GET /user/center | User@center | 个人中心 |
| GET /admin | admin\Index@index | 后台首页 |

## 界面设计

- 使用 Bootstrap 5.3 框架
- 渐变紫色主题色
- 卡片式布局
- 响应式设计，支持移动端

## 注意事项

1. 请确保 PHP 开启了 `pdo_mysql`、`mbstring`、`json` 等扩展
2. 生产环境请关闭调试模式（`APP_DEBUG = false`）
3. 请修改默认管理员密码
4. 钉钉群二维码需要在添加任务时配置

## 许可证

MIT License
