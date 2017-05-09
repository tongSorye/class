<?php  
	/*
	 *  time        2017年4月21日14:08:25
	 *  writer      tongSorye
	 *  intro       一个验证码的类库,需要验证码时只需要调用类中的方法,当然方法中的数字部分都可以根据你的具体需求进行改动
	 *  explain     此类中的方法均有注释,当然如果你在使用汉字验证码时请改变一些参数以保证验证码的美观
	 *  notice01    在生成验证码的同时我们已经将其存入了session中,所以你在验证时只用调用$_SESSION['useCode']即可进行验证
	 *  notice02    在使用图片验证码时,需要先在$array中定义图片的相关字段
	 * 
	 */

	class Captcha{

		public $image;
		public $bgcolor;
		public $useCode = '';
		// 验证码的个数
		public $num = 4;
		// 验证码字体的大小
		public $fontSize = 8;
		
		public function __construct (){			
			$this->image = imagecreatetruecolor(100,30);
			$this->bgcolor = imagecolorallocate($this->image,255,255,255);
			self::session();
			imagefill($this->image,0,0,$this->bgcolor);
		}

		// 开启session
		public function session(){
			session_start();
		}

		// 纯数字验证码
		public function getNum(){
			for($i=0;$i<$this->num;$i++){
				$fontsize = $this->fontSize;
				$fontcolor = imagecolorallocate($this->image,rand(0,120),rand(0,120),rand(0,120));
				$fontcontent = rand(0,9);
				$this->useCode .= $fontcontent;
				$x = ($i*100/$this->num)+rand(5,10);
				$y = rand(5,10);
				imagestring($this->image,$fontsize,$x,$y,$fontcontent,$fontcolor);
			}
			self::dop();
			self::line();
			self::over();
		}

		// 数字加字母验证码
		public function getLetterNum(){	
			for($i=0;$i<$this->num;$i++){
				$fontsize = $this->fontSize;
				$fontcolor = imagecolorallocate($this->image,rand(0,120),rand(0,120),rand(0,120));
				$data = "ABCDEFGHJKLMNPQRSTUVWXYabcdefghijkmnpqrstuvwxy3456789";
				$fontcontent = substr($data,rand(0,strlen($data)),1);
				$this->useCode .= $fontcontent;
				$x = ($i*100/$this->num)+rand(5,10);
				$y = rand(5,10);
				imagestring($this->image,$fontsize,$x,$y,$fontcontent,$fontcolor);
			}			
			self::dop();
			self::line();
			self::over();
		}

		// 汉字验证码
		public function getChinese(){
			$fontface = 'STXINGKA.TTF';
			// 此str根据你的具体需求进行设置
			$str = "一时间曾经或如今在手机行业中占据重要地位的相关厂不是在起诉他人的路上就是在被他人起诉的路上而从挑起专利诉讼争端的主体来看既有过去或现在的手机厂商比如诺基亚爱立信三星苹果和华为等也有很多属于";
			$strdb = str_split($str,3);
			for($i=0;$i<$this->num;$i++){
				$fontcolor = imagecolorallocate($this->image,rand(0,120),rand(0,120),rand(0,120));
				$index = rand(0,count($strdb));
				$cn = $strdb[$index];
				$this->useCode .= $cn;
				imagettftext($this->image,mt_rand(10,12),mt_rand(-30,30),(20*$i+10),mt_rand(15,18),$fontcolor,$fontface,$cn);
			}
			self::dop();
			self::line();
			self::over();
		}

		// 图片验证码
		public function getPic(){
			// 此数组键为图片的代表名称,值为存入session后验证时的字段
			$array = array(
				'1' => '一',
				'2' => '二',
				'3' => '三',
				'4' => '四',
				'5' => '五',
				);
			$index = rand(1,5);
			$this->useCode = $array[$index];
			$_SESSION['useCode'] = $this->useCode;
			$filename = dirname(__FILE__).'\\'.$index.'.jpg';
			$content = file_get_contents($filename);
			header('content-type:image/jpg');
			echo $content;
			imagepng($this->image);
			imagedestroy($this->image);
		}
		
		// 干扰元素 点
		public function dop(){
			for($i=0;$i<200;$i++){
				$pointcolor = imagecolorallocate($this->image,rand(50,200),rand(50,200),rand(50,200));
				imagesetpixel($this->image,rand(1,99),rand(1,29),$pointcolor);
			}
		}

		// 干扰元素 线
		public function line(){
			for($i=0;$i<3;$i++){
				$linecolor = imagecolorallocate($this->image,rand(80,220),rand(80,220),rand(80,220));
				imageline($this->image,rand(1,99),rand(1,29),rand(1,99),rand(1,29),$linecolor);
			}
		}
		
		// 输出验证码、存入session并结束GD库
		public function over(){
			$_SESSION['useCode'] = $this->useCode;
			header('content-type:image/png');
			imagepng($this->image);
			imagedestroy($this->image);
		}
	}

?>