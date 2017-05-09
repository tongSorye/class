<?php 
	/*
	 *  time        2017年4月24日16:35:14
	 *  writer      tongSorye
	 *  intro       实行文件的上传
	 *  notice01    在new此对象时请传入你的自己的参数(如$filename,$uploadPath等),如果不传则需要在下面手动指定
	 *  notice02	我们在上传图片成功后也可以生成等比例缩放后的图片,你可以在不同地方使用不同尺寸的图片,当然前提是你得在构造函数中开启生成缩略 *              图以及设置缩略图的大小,当然我们默认为200*200并且默认关闭了自动生成
	 *  notice03    当然我们默认生成了一张缩略图,如果你需要生成几张不同的缩略图时,你就需要修改一些生成缩略图的参数
	 *  notice04    error的错误显示格式你也可以在showError()方法中手动设置
	 */
	
	class upload{
		protected $fileName;
		protected $maxSize;
		protected $allowMime;
		protected $allowExt;
		protected $uploadPath;
		protected $imgFlag;
		protected $fileInfo;
		protected $error;
		protected $ext;
		protected $imgRes; // 是否生成等比例缩放图
		protected $maxw;
		protected $maxh;
		protected $pre;

		/*
		 * @param string $fileName
		 * @param string $uploadPath
		 * @param string $imgFlag
		 * @param number $maxSize
		 * @param array $allowExt
		 * @param array $allowMime
		 * 
		 */
		public function __construct($fileName='myFile',$uploadPath='./uploads',$maxw=200,$maxh=200,$pre='s_',$imgFlag=true,$imgRes=true,$maxSize=5242880,$allowExt=array('jpeg','jpg','png','gif'),$allowMime=array('image/jpeg','image/png','image/gif')){
			$this->fileName = $fileName;
			$this->maxSize = $maxSize;
			$this->allowMime = $allowMime;
			$this->allowExt = $allowExt;
			$this->uploadPath = $uploadPath;
			$this->imgFlag = $imgFlag;
			$this->imgRes = $imgRes;
			$this->maxw = $maxw;
			$this->maxh = $maxh;
			$this->pre = $pre;
			$this->fileInfo = $_FILES[$this->fileName];
		}


		/*
		 * 上传文件
		 * @return string
		 * 
		 */
		public function uploadFile(){
			if($this->checkError() && $this->checkSize() && $this->checkExt() && $this->checkMime() && $this->checkTrueImg() && $this->checkHTTPPost()){
				$this->checkUploadPath();
				$this->uniName = $this->getUniName();
				$this->destination = $this->uploadPath.'/'.$this->uniName.'.'.$this->ext;
				if(@move_uploaded_file($this->fileInfo['tmp_name'], $this->destination)){
					if($this->imgRes){
						$this->imgResize($this->uniName.'.'.$this->ext);
					}
					return  $this->destination;
				}else{
					$this->error = '文件上传失败';
					$this->showError();
				}
			}else{
				$this->showError();
			}
		}

		/*
		 * 检测上传文件是否出错
		 * @return boolean
		 * 
		 */
		protected function checkError(){
			if(!is_null($this->fileInfo)){
				if($this->fileInfo['error']>0){
					switch($this->fileInfo['error']){
						case 1:
							$this->error = '超过了PHP配置文件中upload_max_filesize选项的值';
							break;
						case 2:
							$this->error = '超过了表单中MAX_FILE_SIZE设置的值';
							break;
						case 3:
							$this->error = '文件部分被上传';
							break;
						case 4:
							$this->error = '没有选择上传文件';
							break;
						case 6:
							$this->error = '没有找到临时目录';
							break;
						case 7:
							$this->error = '文件不可写';
							break;
						case 8:
							$this->error = '由于PHP的扩展程序中断文件上传';
							break;
							
					}
					return false;
				}else{
					return true;
				}
			}else{
				$this->error = '文件上传出错';
				return false;
			}
		}

		/*
		 * 检测上传文件的大小
		 * @return boolean
		 * 
		 */
		protected function checkSize(){
			if($this->fileInfo['size']>$this->maxSize){
				$this->error = '上传文件过大';
				return false;
			}
			return true;
		}

		/*
		 * 检测扩展名
		 * @return boolean
		 * 
		 */
		protected function checkExt(){
			$this->ext = strtolower(pathinfo($this->fileInfo['name'],PATHINFO_EXTENSION));
			if(!in_array($this->ext,$this->allowExt)){
				$this->error = '不允许的扩展名';
				return false;
			}
			return true;
		}

		/*
		 * 检测文件的类型
		 * @return boolean
		 * 
		 */
		protected function checkMime(){
			if(!in_array($this->fileInfo['type'],$this->allowMime)){
				$this->error = '不允许的文件类型';
				return false;
			}
			return true;
		}

		/*
		 * 检测是否是真实图片
		 * @return boolean
		 * 
		 */
		protected function checkTrueImg(){
			if($this->imgFlag){
				if(!@getimagesize($this->fileInfo['tmp_name'])){
					$this->error = '不是真实图片';
					return false;
				}
				return true;
			}
		}

		/*
		 * 检测是否通过HTTP POST方式上传上来的
		 * @return boolean
		 * 
		 */
		protected function checkHTTPPost(){
			if(!is_uploaded_file($this->fileInfo['tmp_name'])){
				$this->error = '文件不是通过HTTP POST方式上传上来的';
				return false;
			}
			return true;
		}

		/*
		 *显示错误 
		 *
		 */
		protected function showError(){
			exit('<span style="color:red">'.$this->error.'</span>');
		}

		/*
		 * 检测目录不存在则创建
		 * 
		 */
		protected function checkUploadPath(){
			if(!file_exists($this->uploadPath)){
				mkdir($this->uploadPath,0777,true);
			}
		}

		/*
		 * 产生唯一字符串
		 * @return string
		 * 
		 */
		protected function getUniName(){
			return md5(uniqid(microtime(true),true));
		}

		/*
		 * 生成等比例缩放的图片
		 * @return boolean
		 * 
		 */
		public function imgResize($picname){
			$maxw = $this->maxw;
			$maxh = $this->maxh;
			$pre = $this->pre;

			// 格式化路径信息
				$path = rtrim($this->uploadPath,"/")."/";
				
			// 获取图像的详细信息
				$info = getimagesize($path.$picname);
			
			//1. 准备画布
				switch($info[2]){
					case 1:	//生成一个gif的画布资源
						$oldImg = imagecreatefromgif($path.$picname);
					break;
					case 2:	//生成一个jpeg的画布资源
						$oldImg = imagecreatefromjpeg($path.$picname);
					break;
					case 3:	//生成一个png的画布资源
						$oldImg = imagecreatefrompng($path.$picname);
					break;
				}
				
				// 获取原图的宽高
				$oldw = imagesx($oldImg);
				$oldh = imagesy($oldImg);
				
				// 进行等比例缩放
				if($oldw > $oldh){
					// 求得的比例
					$b = $oldw/$maxw;
					
					// 求得缩放后的宽高
					$neww = $oldw/$b;
					$newh = $oldh/$b;
				}else{
					//求得的比例
					$b = $oldh/$maxh;
					
					// 求得缩放后的宽高
					$neww = $oldw/$b;
					$newh = $oldh/$b;
				}
				
				// 根据缩放之后的宽高生成新的画布
				$newImg = imagecreatetruecolor($neww,$newh);
				
			//2. 开始绘画
				imagecopyresampled($newImg,$oldImg,0,0,0,0,$neww,$newh,$oldw,$oldh);
			
			//3. 输出图像
				switch($info[2]){
					case 1:
						imagegif($newImg,$path.$pre.$picname);
					break;
					case 2:
						imagejpeg($newImg,$path.$pre.$picname);
					break;
					case 3:
						imagepng($newImg,$path.$pre.$picname);
					break;
				}
			
			//4. 释放资源
				imagedestroy($newImg);
				imagedestroy($oldImg);
				
			//5.返回布尔型true
				return true;
		}

	}

?>
