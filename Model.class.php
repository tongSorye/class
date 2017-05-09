<?php  
	/*
	 *  time        2017年5月5日11:13:50
	 *  writer      tongSorye
	 *  intro       一个Model操作数据库的类库,当然该类与Page类一起使用时效果更佳
	 *  explain     此类中的方法均有注释,当然你也可以根据你自己的需要进行一些修改
	 *  notice      在使用此类时请在构造函数中设置一些与你数据库相关的设置
	 * 
	 */
	class Model
	{
		public $table;              //表名
		public $link; 				//数据库链接
		public $field=array();		//表字段信息
		public $pk;					//表主键字段
		public $where;				//搜索条件
		public $order;				//排序条件
		public $limit;				//分页条件

		// 初始化
		public function __construct($table){
			$this->table = $table;
			// 链接数据库
			$this->link = mysqli_connect('','','');
			// 设置字符集
			mysqli_set_charset($this->link,'utf8');
			// 选择数据库
			mysqli_select_db($this->link,'');

			// 获取表字段信息
			$this->getField();
		}

		// 获取表字段信息方法
		private function getField(){
			$sql = 'desc '.$this->table;	
			$res = mysqli_query($this->link,$sql);
			while($row = mysqli_fetch_assoc($res)){
				$this->field[] = $row['Field'];
				// 获取主键字段
				if($row['Key'] == 'PRI'){
					$this->pk = $row['Field'];
				}	
			}	
		}	

		// 添加
		public function add($arr){
			// 数据验证
			foreach($arr as  $k=>$v){
				if(!in_array($k,$this->field)){
					// 删除非法字段
					unset($arr[$k]);
				}
			}	
			$key = implode(',',array_keys($arr));
			$val = '"'.implode('","',array_values($arr)).'"';
			// 封装sql语句
			$sql = 'insert into '.$this->table.'('.$key.') values('.$val.')';
			// 执行sql语句
			$res = mysqli_query($this->link,$sql);
			if($res && mysqli_affected_rows($this->link)>0){
				return mysqli_insert_id($this->link);        // 自增id
			}else{
				return false;
			}	
		}

		// 删除
		public function del($id){
			$sql = 'delete from '.$this->table.' where '.$this->pk.'='.$id;	
			$res = mysqli_query($this->link,$sql);
			if($res && mysqli_affected_rows($this->link)>0){
				return mysqli_affected_rows($this->link);
			}else{
				return false;	
			}
		}

		// 修改
		public function update($arr){
			//数据检测
			$arr1 = array();
			foreach($arr as $k=>$v){
				if(!in_array($k,$this->field)){
					unset($arr[$k]);
				}else{
					// 合法字段
					if($k! = $this->pk){
						$arr1[]=$k.'="'.$v.'"';
					}	
				}
			}
			$set=implode(',',$arr1);
			$sql='update '.$this->table.' set '.$set.' where '.$this->pk.'='.$arr[$this->pk];
			$res = mysqli_query($this->link,$sql);
			if($res && mysqli_affected_rows($this->link)>0){
				return mysqli_affected_rows($this->link);
			}else{
				return false;
			}
		}

		// 查询单条数据
		public function find($id){
			$sql = 'select * from '.$this->table.' where '.$this->pk.'='.$id;
			$res = mysqli_query($this->link,$sql);
			if($res && mysqli_num_rows($res)>0){
				return mysqli_fetch_assoc($res);
			}else{
				return false;
			}
		}

		// 查询所有数据
		public function select(){
			// 判断是否有where 搜索
			$where = null;
			if(count($this->where) > 0){
				$where = ' where ';
				$where .= implode(' and ',$this->where);
			}

			// 判断是否有order 排序
			$order = null;
			if($this->order != null){
				$order = ' order by '.$this->order;
			}

			// 判断是否有limit  分页
			$limit = null;
			if($this->limit != null){
				$limit = ' limit '.$this->limit;
			}	

			$sql = 'select * from '.$this->table.$where.$order.$limit;
			$res  = mysqli_query($this->link,$sql);	
			$arr1=array();	
			if($res && mysqli_num_rows($res)>0){
				// 解析结果集并返回数据
				while($row = mysqli_fetch_assoc($res)){
					$arr1[]=$row;	
				}
				return $arr1;
			}else{
				return false;
			}
		}
		// 封装搜索条件
		public function where($where){
			$this->where[] = $where;
			return $this;
		}
		// 封装排序条件
		public function order($order){
			$this->order = $order;
			return $this;	
		}
		// 封装分页条件
		public function limit($limit){
			$this->limit = $limit;
			return $this;
		}

		// 获取总数据条数
		public function count(){
			$sql = 'select count(*) as c from '.$this->table;
			$res = mysqli_query($this->link,$sql);
			return mysqli_fetch_assoc($res)['c'];	
		}	
	}	
?>