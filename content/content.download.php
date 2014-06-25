<?php
require_once(TOOLKIT . '/class.xsltpage.php');
require_once(TOOLKIT . '/class.jsonpage.php');
require_once(TOOLKIT . '/class.sectionmanager.php');
require_once(TOOLKIT . '/class.fieldmanager.php');
require_once(TOOLKIT . '/class.entrymanager.php');
require_once(TOOLKIT . '/class.entry.php');
require_once(BOOT . '/func.utilities.php');
require_once(EXTENSIONS . '/importcsv/lib/php-export-data/php-export-data.class.php');
require_once(EXTENSIONS . '/importcsv/lib/parsecsv-0.3.2/parsecsv.lib.php');
require_once(CORE . '/class.cacheable.php');
//ini_set('max_execution_time', 0);

ini_set('upload_max_filesize','70M');
class contentExtensionImportcsvDownload extends JSONPage
{		  
			public function view()
			{	
				$this->download($_REQUEST);
				
			}
			private function download($filelocation){
					
					$file = 'data-'.time('e').'-'.date('z').'.csv';	
					header("Content-type: text/csv");
					header("Content-Disposition: attachment; filename=$file");
					header("Pragma: no-cache");
					header("Expires: 0");
					ob_clean();
					flush();
					readfile($filelocation['file']); // outputs file 
					unlink($filelocation['file']); // once output complete remove tmp file
					exit;
			}

}

?>