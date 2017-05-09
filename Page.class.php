<?php  
	/*
	 *  time        2017年5月5日11:06:22
	 *  writer      tongSorye
	 *  intro       一个分页的类库,需要分页时只需要调用类中的方法,当然该类与Model类一起使用时效果更佳
	 *  explain     在new此类时请传入分页的大小和总共有多少条数据
	 * 
	 */
	class Page
	{
		public $size;       // 页大小
		public $maxpage;    // 最大页数
		public function __construct($size,$count){
			$this->size = $size;
			$this->maxpage = ceil($count/$size); // 获取总页数
			$this->getPage();
		}

		// 获取当前页数方法
		public function getPage(){
			$this->page = isset($_GET['p'])?$_GET['p']:1;
			if($this->page < 1){
				$this->page = 1;
			}
			if($this->page > $this->maxpage){
				$this->page = $this->maxpage;
			}
		}

		// 返回分页条件
		public function limit(){
			return ($this->page-1)*$this->size.','.$this->size;
		}

		// 显示分页
		public function show(){
			echo "<a href='./index.php?p=1'>首页</a>";
			echo "<a href='./index.php?p=".($this->page-1)."'>上一页</a>";
			echo "<a href='./index.php?p=".($this->page+1)."'>下一页</a>";
			echo "<a href='./index.php?p=".$this->maxpage."'>尾页</a>";
		}	
	}
?>