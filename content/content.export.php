<?php
		require_once(TOOLKIT . '/class.xsltpage.php');
		require_once(TOOLKIT . '/class.administrationpage.php');
		require_once(TOOLKIT . '/class.sectionmanager.php');
		require_once(TOOLKIT . '/class.fieldmanager.php');
		require_once(TOOLKIT . '/class.entrymanager.php');
		require_once(TOOLKIT . '/class.entry.php');
		require_once(BOOT . '/func.utilities.php');
		require_once(EXTENSIONS . '/importcsv/lib/php-export-data/php-export-data.class.php');
		require_once(EXTENSIONS . '/importcsv/lib/parsecsv-0.3.2/parsecsv.lib.php');
		require_once(CORE . '/class.cacheable.php');
		require_once(TOOLKIT . '/class.jsonpage.php');		
		class contentExtensionImportcsvExport extends JSONPage{
			
			
			
			 public function view()
			{	
				
				if(isset($_REQUEST['headers'])){
					$this->__addheaders();
				}elseif(isset($_REQUEST['section'])){
					$this->__ajaxexport();
				}
			}
			private function __addheaders(){
				$sectionID = $_REQUEST['headers'];
				$sm = new SectionManager($this);
				$section = $sm->fetch($sectionID);			
				$fields = $section->fetchFields(); 
				$fieldscols = $this->__getField($fields);
				$entries = $fieldscols;
				$file = MANIFEST.'/tmp/data-'.$sectionID.'.csv';				
				$handle = fopen($file,'r+');				
				
				
				fwrite($handle,$entries);
				fclose($handle);				
				$this->_Result = array('progress'=>'headers','file'=>$file);	
				
			}
			
			private function __ajaxexport(){
				 // Load the fieldmanager:           
					$filter = $filter_value = $where = $joins = NULL;
					$page = (int)$_REQUEST['page'];					
					$sectionID = (int)$_REQUEST['section'];
					$limit = $_REQUEST['limit'];
					$em = new EntryManager($this);
					$count = $em->fetchCount($sectionID,$where,$joins);
					$totalpages = round(($count / $limit),0);
					$pageentries = $em->fetchByPage($page,$sectionID,$limit,$where,$joins);
					$totalpages = $pageentries['total-pages'];
					$entrys = $this->__getValues($pageentries['records']);
					
					$entries = array('entries' => $entrys); 
					$this->__insert($entries,$sectionID);				
					if($totalpages != $page){
						
						$next = array(
									'section' => $sectionID,
									'page' => $page,
									'limit' => $limit,
									'progress'=>'success',
									'total-pages'=>$totalpages
						);
						$this->_Result = $next;
					}else{						
						$this->_Result = array('progress'=>'completed');									
					}
			}
			private function __getField($fields){
				$r = array();		
				foreach($fields as $field){	
					
					$f = array_values((array) $field);
					
					$r[] = $f[1]['label'];															
					unset($f);
				}				
				$fieldscols = implode(',',$r)."\r\n";		
				return $fieldscols;
			}
			
			
			
			private function __getValues($array){		
				$all = array();
				$ents = array();
				foreach($array as $en => $ent){
					$data = $ent->getData();
					$all = $this->getVals($data);
					$ents[] = implode(',',$all);
				}
				$l = implode("\r\n",$ents);		
				return $l;
			}
			
			public function getData(){
				return $this->_data;
			}
			
			private function getVals($data){
				$a = array();
				foreach($data as $d => $dat){
					$a[] = $dat['value'];
				}
				return $a;
			}
			
			private function __insert($array,$sectionID){
				$file = MANIFEST.'/tmp/data-'.$sectionID.'.csv';				
				foreach($array as $data => $dat){
					$dat = $dat . "\r\n";
					file_put_contents($file, $dat, FILE_APPEND | LOCK_EX);			
				}
				unset($array);
				unset($file);
			}
		}
?>