<?php  
	/*
	 *  time        2017年5月8日19:17:05
	 *  writer      me
	 *  intro       一个正则验证的类库,需要进行正则验证时只需要调用类中的is..方法
	 *  explain     此类中的方法均有注释,当然一些方法你可以自己修改,同时常用的正则表达式数组内你也可以根据自己的需求进行修改正则表达式
	 *	notice01    你需要在实例化此类时需要传入参数进行确认输出时的格式,如果不指定则默认验证成功返回true，验证失败返回false
	 *  notice02    当然如果你在实例化时没有确认需要输出的类型,那么我们也提供了一个方法toggleReturnType进行输出转化,前提你得调用这个方法
	 *  notice03    当常用的正则数组中没有你需要的正则表达式时,你可以通过在数组中添加键值对的方法进行验证当然此时别忘了添加输出时的调用方法
	 *              可能第一种有点麻烦所以我们提供了pregCheck方法进行验证,同时在调用该方法时别忘了传入正则表达式和验证的字符串
	 *
	 */

	class Preg{

		// 常用的正则表达式
		private $pregRul = array(
			'require'   =>  '/.+/',
			'email'     =>  '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
			'email2'    =>  '/^\w+(\.\w+)*@\w+(\.\w+)+$/',
			'url'       =>  '/^(https?:\/\/)?(\w+\.)+[a-zA-Z]+$/',
			'number'    =>  '/^\d+$/',
			'integer'   =>  '/^[-\+]?\d+$/',
			'english'   =>  '/^[A-Za-z]+$/',
			'qq'		=>	'/^\d{5,11}$/',
			'mobile'	=>	'/^1((3[0-9])|(4[5|7])|(5([0-3]|[5-9]))|(7([0,7,8]))|(8[0,2-3,5-9]))\d{8}$/',
		);

		// 返回结果  如果为false的时候只返回验证的结果是真还是假，如果为true返回的是匹配到的结果的数组 
		private $resultMatch = false;
		// 修正模式  
		private $fixMode = null;   
		// 匹配到的结果的数组           
		private $matches = array();
		// 验证的结果 验证成功返回true，验证失败返回false
		private $isMatch = false;

		public function __construct($resultMatch = false,$fixMode = null){
			$this->resultMatch = $resultMatch;
			$this->fixMode = $fixMode;
		}

		private function regex($pattern, $subject) {
			if(array_key_exists(strtolower($pattern), $this->pregRul)){
				$pattern = $this->pregRul[$pattern].$this->fixMode;
			}
			$this->resultMatch ?
				preg_match_all($pattern, $subject, $this->matches) :
				$this->isMatch = preg_match($pattern, $subject) === 1;
			return $this->getResult();
		}
		
		private function getResult() {
			if($this->resultMatch) {
				return $this->matches;
			} else {
				return $this->isMatch;
			}
		}
		
		// 转换输出类型(如果你在实例化对象时已经传入了确认要输出的类型时,此方法可以忽略)
		public function toggleReturnType($bool = null) {
			if(empty($bool)) {
				$this->resultMatch = !$this->resultMatch;
			} else {
				$this->resultMatch = is_bool($bool) ? $bool : (bool)$bool;
			}
		}
		
		public function setFixMode($fixMode) {
			$this->fixMode = $fixMode;
		}
		
		public function noEmpty($str) {
			return $this->regex('require', $str);
		}
		
		public function isEmail($email) {
			return $this->regex('email', $email);
		}
		
		public function isMobile($mobile) {
			return $this->regex('mobile', $mobile);
		}
		
		public function isNumber($number) {
			return $this->regex('number', $number);
		}

		public function isUrl($url) {
			return $this->regex('url', $url);
		}

		public function isInteger($integer) {
			return $this->regex('integer', $integer);
		}

		public function isEnglish($english) {
			return $this->regex('english', $english);
		}

		public function isQq($qq) {
			return $this->regex('qq', $qq);
		}

		public function pregCheck($pattern, $subject) {
			return $this->regex($pattern, $subject);
		}
	}



?>