<?php
session_start();
define('SD_ROOT', dirname(__FILE__).'/');
@header("Expires: 0");
@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
@header("Pragma: no-cache");

//这里传过来会有两种类型，一先一后, big和small, 保存成功后返回一个json字串，客户端会再次post下一个.
$type = isset($_GET['type'])?trim($_GET['type']):'small';
$pic_id = trim($_GET['photoId']);
//$orgin_pic_path = $_GET['photoServer']; //原始图片地址，备用.
//$from = $_GET['from']; //原始图片地址，备用.

//生成图片存放路径
$new_avatar_path = 'avatar_'.$type.'/'.$_SESSION['USERID'].'_'.$type.'.jpg';

//将POST过来的二进制数据直接写入图片文件.
$len = file_put_contents(SD_ROOT.'./'.$new_avatar_path,file_get_contents("php://input"));

//原始图片比较大，压缩一下. 效果还是很明显的, 使用80%的压缩率肉眼基本没有什么区别
//小图片 不压缩约6K, 压缩后 2K, 大图片约 50K, 压缩后 10K
$avtar_img = imagecreatefromjpeg(SD_ROOT.'./'.$new_avatar_path);
imagejpeg($avtar_img,SD_ROOT.'./'.$new_avatar_path,80);
//nix系统下有必要时可以使用 chmod($filename,$permissions);

log_result('图片大小: '.$len);


//输出新保存的图片位置, 测试时注意改一下域名路径, 后面的statusText是成功提示信息.
//status 为1 是成功上传，否则为失败.
$d = new pic_data();
//$d->data->urls[0] = 'http://sns.com/upload/'.$new_avatar_path;
$d->data->urls[0] = '/upload/'.$new_avatar_path;
$d->status = 1;
$d->statusText = '上传成功!';

$msg = json_encode($d);

echo $msg;

log_result($msg);
function  log_result($word) {
	@$fp = fopen("log.txt","a");	
	@flock($fp, LOCK_EX) ;
	@fwrite($fp,$word."：执行日期：".strftime("%Y%m%d%H%I%S",time())."\r\n");
	@flock($fp, LOCK_UN); 
	@fclose($fp);
}
class pic_data
{
	 public $data;
	 public $status;
	 public $statusText;
	public function __construct()
	{
		$this->data->urls = array();
	}
}

?>