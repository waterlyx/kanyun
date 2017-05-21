# kanyun
基于php的看云下载器
描述：通过php和wkhtmltopdf下载看云不能下载的文档
说明：需要下载wkhtmltopdf，下载地址https://wkhtmltopdf.org/
    需要配置php环境变量，需要用到curl扩展
使用方法：
①不需要登陆的情况
1.不需要登陆的情况，填写index.php文件中的第60行，$url = 'http://www.kancloud.cn/manual/thinkphp5';
2.在根目录下运行 php index.php
3.此时会在data文件夹中生成文档数据，最后在项目根目录生成pdf文档，项目使用完毕需要手动删除data中的html数据
②需要登陆的情况
1.使用火狐浏览器登陆，用火狐浏览器的插件FireBug插件将登陆云片之后的cookie信息填写到项目根目录的tmp.cookie
2.填写index.php文件中的第60行，$url = 'http://www.kancloud.cn/manual/thinkphp5';
3.在根目录下运行 php index.php
4.此时会在data文件夹中生成文档数据，最后在项目根目录生成pdf文档，项目使用完毕需要手动删除data中的html数据