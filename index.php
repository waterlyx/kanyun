<?php
/**
 * User: 刘业兴
 * @param string $url 目标文档url
 * @param bool $https 是否启用https
 * @param string $method 获取页面的方式
 * @param null $data 如果是post方式传入的数据
 * @param null $cookie_file cookie存放的路径
 * @return mixed 返回的页面数据
 * 描述：获取页面数据函数
 */
function request($url, $https = true, $method = 'get', $data = null, $cookie_file = null)
{
    //1.初始化url
    $ch = curl_init($url);
    //2.设置相关的参数
    //字符串不直接输出,进行一个变量的存储
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //判断是否为https请求
    //https:是以安全为目标的HTTP通道，简单讲是HTTP的安全版
    if ($https === true) {
        //验证对方的SSL证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //检查声称服务器的证书的身份
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    //判断是否为post请求
    if ($method == 'post') {
        //发送post请求
        curl_setopt($ch, CURLOPT_POST, true);
        //设置post参数
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    if (!empty($cookie_file)) {
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); //使用上面获取的cookies
    }
    //3.发送请求
    $str = curl_exec($ch);
    //4.关闭连接
    curl_close($ch);
    //返回请求到的结果
    return $str;
}

/**
 * User: 刘业兴
 * @param string $url 目标文档url
 * @param bool $https 是否启用https
 * @param string $method 获取页面的方式
 * @param null $data 如果是post方式传入的数据
 * @param null $cookie_file cookie存放的路径
 * @return mixed 返回的页面数据
 * 描述：获取页面的内容
 */
function content($url, $https = true, $method = 'get', $data = null, $cookie_file = null){
    $data = request($url, $https, $method, $data, $cookie_file);
    preg_match('|think-editor-content\">(.*?)<\/div>|is',$data,$k);
    return $k[1];
}
$url = 'http://www.kancloud.cn/manual/thinkphp5';
//获取文档首页
$index = request($url, false, 'get', '', './tmp.cookie');
//找到阅读按钮
preg_match('|<a class=\"e-cover\" href=\"(.*?)\">|i',$index,$m);
//解析文档
$d_info = request($m[1], false, 'get', '', './tmp.cookie');
//获取全目录
preg_match_all('|data-id=\"(.*?)\">|i',$d_info,$k);
//获得一级目录
preg_match_all('|data-pid=\"0\" data-disable=\"0\" data-id=\"(.*?)\">|is',$d_info,$kl);
//pdf头
$content = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Title</title><link rel="stylesheet" href="./css/style.css"><link rel="stylesheet" href="./css/kancloud.min.css"></head><body><div class="article-wrap"><div class="article-view"><div class="view-body think-editor-content">';
file_put_contents('./data/content.html',$content,FILE_APPEND);
foreach ($k[1] as $k => $v){
    //获取文章标题
    preg_match('|data-id=\"'.$v.'\">(.*?)<\/a>|is',$d_info,$kh);
    //显示标题
    if(in_array($v,$kl[1])){
        $content = '<h1>'.$kh[1].'</h1>';
    }else{
        $content = '<h2>'.$kh[1].'</h2>';
    }
    //获取文章内容
    $data = content($url.'/'.$v, false, 'get', '', './tmp.cookie');
    $data = str_replace("<h1>","<h3>",$data);
    $data = str_replace("</h1>","</h3>",$data);
    $data = str_replace("<h2>","<h4>",$data);
    $data = str_replace("</h2>","</h4>",$data);
    $data .= "<P style='page-break-after:always'>&nbsp;</P>";
    $content .= $data;
    file_put_contents('./data/content.html',$content,FILE_APPEND);
}
//添加文章尾部
$content = '</div></div></div></body></html>';
file_put_contents('./data/content.html',$content,FILE_APPEND);
//生成pdf
exec('wkhtmltopdf ./data/content.html content.pdf');


