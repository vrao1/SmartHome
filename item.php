<?php
	class item{
		public $id;
		public $name;
		public $usage;
		public $operating_duration;
		public $priority;
		public $total_consumption;
		public $start_time;
		public $end_time;
		public $dayslot;
		public $total_bill;

		public function item(){

		}
		public function setTotal_consumption(){
			$this->total_consumption = ($this->usage * $this->operating_duration)/60;
		}
	}
?>
