<?php
	class CronShell extends AppShell{
		public $uses = array('Block', 'Brand', 'Location');
		public function updateBlocks(){
		
			parent::sendEmail();
			
			//Add permalinks
			$blocks = $this->Block->find('all');
			for($i = 0; $i < count($blocks); $i++){
				$this->Block->addPermalink($blocks[$i]['Block']['id'], $blocks[$i]['Block']['name']);
				
			}
			$brands = $this->Brand->find('all');
			for($i = 0; $i < count($brands); $i++){
				$block = $this->Block->find("first", array("conditions" => array("number" => (string)$brands[$i]['Brand']['counter'], "brand_id" => $brands[$i]['Brand']['id'])));
				
				if(!$block){
					$block = $this->Block->find("first", array("conditions" => array("number" => (string)$brands[$i]['Brand']['counter']-1, "brand_id" => $brands[$i]['Brand']['id'])));
					$block['Block']['number']++;
					$this->Block->save($block);
				}
				$this->Brand->id = $brands[$i]['Brand']['id'];
				$this->Brand->saveField("active_block", $block['Block'])
				$this->Brand->saveField("counter", $brands[$i]['Brand']['counter']+1);
			}
		}
		
		public function resetBlocks(){
			$brands = $this->Brand->find('all');
			for($i = 0; $i < count($brands); $i++){
				$block = $this->Block->find("first", array("conditions" => array("number" => (string)2, "brand_id" => $brands[$i]['Brand']['id'])));
				
				if($block){
					$this->Brand->id = $brands[$i]['Brand']['id'];
					$this->Brand->saveField("active_block", $block['Block']);
					
					$this->Brand->saveField("counter", 3);
				}
			}
		}
	}