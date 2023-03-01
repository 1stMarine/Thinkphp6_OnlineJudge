# codeSky项目说明书

### 一 、开发环境

项目总体框架基于Thinkphp6 + bootstrap 开发，判题机器额外使用Docker容器进行安全性运行。

1.操作系统 ： windows 11

2.IDE: PhpStorm2022

3.Php7.3.4

4.bootstrap v4

5.Docker Desktop

6.Mysql8

### 二、如何部署环境

#### 1.项目部署

1.导入数据库文件:phpoj.sql

2.phpstudy创建网站，目录指向项目中的public目录

#### 2.判题机部署准备

Windows:

1.下载Docker Desktop

2.使用管理员模式打开window shell 键入以下命令 pull 安全容器

```shell
docker pull image echocen/gcc:v1		#运行.c \ .cpp 文件的镜像
docker pull image echocen/openjdk:v1	#运行java文件的镜像
docker pull image echocen/python:v1		#运行python文件的镜像
```

目前为止，系统只支持三种语言，但是可扩展性极强，只需从docker hub上下载相应编程语言镜像即可。

#### 3.thinkphp workerman定时器部署

workerman将用于项目的竞赛模块中定时开启比赛、关闭比赛。

1. 安装workman

   ```shell
   composer require workerman/workerman
   ```

   2.创建Timer命令

```shell
php think make:command Timers
```

3. 启动、关闭定时器

   ```she
   php think timer start
   
   php think timer stop
   ```




定时器已启动，并且开始从数据库扫描竞赛

### 三、上传题目文件 xml 模板

本系统中，题目添加通过上传xml文件进行上传、解析的方式。

```xml
<?xml version="1.0" encoding="UTF-8"?>   
<Document>
<title>题目</title>
<time_limit>时间限制</time_limit>
<memory_limit>内存限制</memory_limit>

<description>题目描述</description>

<input_style>输入格式说明</input_style> 

<output_style>输出格式说名</output_style>

<sample_input>
	<sample_input1>输入样例一</sample_input1>
</sample_input>
<sample_output>
	<sample_output1>输出样例一</sample_output1>
</sample_output>
<data_range>数据大小说明</data_range>

<resource>来源</resource>
<tag>
	<tag1>题目标签1</tag1>
</tag>

<diffculty></diffculty>

<test_input>
    <test_input1>测试样例——输入1</test_input1>
</test_input>

<test_output>
	<test_output1>测试样例——输出1</test_output1>
</test_output>

</Document>
```

### 四、其他说明

管理员账户（固定）:admin , password: admin

勋章系统已经完成，但是未策划好如何获得