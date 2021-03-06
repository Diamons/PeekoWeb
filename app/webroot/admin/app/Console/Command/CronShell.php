<?php
	class CronShell extends AppShell{
		public $uses = array('Block', 'Brand', 'Location');
		public function updateBlocks(){
		
			parent::sendEmail();
			
			$brands = $this->Brand->find('all');
			for($i = 0; $i < count($brands); $i++){
				$block = $this->Block->find("first", array("conditions" => array("number" => (int)$brands[$i]['Brand']['counter'], "brand_id" => $brands[$i]['Brand']['id'])));
				
				if(!$block){
					$block = $this->Block->find("first", array("conditions" => array("number" => (int)($brands[$i]['Brand']['counter']-1), "brand_id" => $brands[$i]['Brand']['id'])));
					$this->Block->create();
					unset($block['Block']['id']);
					$block['Block']['number'] = (int)($block['Block']['number']+1);
					$this->Block->save($block);
				}
				$this->Brand->id = $brands[$i]['Brand']['id'];
				$this->Brand->saveField("active_block", $block['Block']);

				$this->Brand->saveField("counter", (int)$brands[$i]['Brand']['counter']+1);
			}
			/*
			//Cleanup
			$blocks = $this->Block->find('all');
			for($i = 0; $i < count($blocks); $i++){
				if(!isset($blocks[$i]['Block']['name'])){
					$this->Block->delete($blocks[$i]['Block']['id']);
				}
			}*/
		}
		
		public function addPermalinks(){
			$blocks = $this->Block->find('all');
			for($i = 0; $i < count($blocks); $i++){
				$this->Block->addPermalink($blocks[$i]['Block']['id'], $blocks[$i]['Block']['name']);
				
			}
		}
		
		public function addVotes(){
			parent::sendEmail();
			$this->Block->addVotes();
		}
		
		public function number(){
			$blocks = $this->Block->find('all');
			for($i = 0; $i < count($blocks); $i++){
				$this->Block->id = $blocks[$i]['Block']['id'];
				$this->Block->saveField('number', (int)$blocks[$i]['Block']['number']);
				
			}
		}
		/*
		public function resetBlocks(){
			$brands = $this->Brand->find('all');
			for($i = 0; $i < count($brands); $i++){
				$block = $this->Block->find("first", array("conditions" => array("number" => ($brands[$i]['Brand']['counter']-1), "brand_id" => $brands[$i]['Brand']['id'])));
				
				if(!$block){
					$block = $this->Block->find("first", array("conditions" => array("number" => ($brands[$i]['Brand']['counter']-2), "brand_id" => $brands[$i]['Brand']['id'])));
					$this->Block->create();
					unset($block['Block']['id']);
					$block['Block']['number'] = ($block['Block']['number']+1);
					$this->Block->save($block);
				}
				$this->Brand->id = $brands[$i]['Brand']['id'];
				$this->Brand->saveField("active_block", $block['Block']);
				//$this->Brand->saveField("counter", $brands[$i]['Brand']['counter']+1);
			}
		}
		*/
		
		public function fixNumber(){
			$brands = $this->Brand->find('all');
			for($i = 0; $i < count($brands); $i++){
				$this->Block->fix($brands[$i]['Brand']['id']);
			}
		}
		
		public function fixElysium(){
			$brands = $this->Brand->find('all');
			
			for($i = 0; $i < count($brands); $i++){
				if(!isset($brands[$i]['Brand']['active_block'])){
					$this->Brand->id = $brands[$i]['Brand']['id'];
					$this->Brand->saveField('elysium', '6 January 2014');
				}
			}
		}
		
		public function cleanBlocks(){
			$blocks = $this->Block->find('all');
			for($i = 0; $i < count($blocks); $i++){
				if(!isset($blocks[$i]['Block']['description']) || empty($blocks[$i]['Block']['description'])){
					$this->Block->delete($blocks[$i]['Block']['id']);
				}
			}
		}
	}