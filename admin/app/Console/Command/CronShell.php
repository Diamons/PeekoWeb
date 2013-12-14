<?php
	class CronShell extends AppShell{
		public $uses = array('Block', 'Brand', 'Location');
		
		public function updateBlocks(){
			$brands = $this->Brand->find('all');
			for($i = 0; $i < count($brands); $i++){
				$block = $this->Block->find("first", array("conditions" => array("number" => (string)$brands[$i]['Brand']['counter'], "brand_id" => $brands[$i]['Brand']['id'])));
				
				if($block){
					debug($block);
					$this->Brand->id = $brands[$i]['Brand']['id'];
					$this->Brand->saveField("active_block", $block['Block']);
					
					$this->Brand->saveField("counter", $brands[$i]['Brand']['counter']+1);
				}
			}
		}
	}