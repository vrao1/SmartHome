
	<?php

	class itemClass{
		public $id;
		public $total_consumption;

		public function item(){
			$this->id = 0;
			$this->total_consumption = array();
		}

		public printFunc(){
			for(var i = 0;i < $this->id ; i++){
				echo $this->total_consumption[i];
				echo "<BR>";
			}
		}
	}
	
	f = new itemClass();
	f->total_consumption[f->id] = 5;
	f->id = f->id + 1;
	f->printFunc();

	?>
