<?php
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
	class PRMQueryViewModel extends PRMActionViewModel {
        protected $__ROOT__ = __DIR__;
		private $connection = NULL;
        protected $parent = NULL;
        public function __construct($parent) {
			global $__ROOT_FROM_PAGE__;
			parent::__construct($parent);
			$this->connection = $parent->getDataService()->getHandler();
        }
        public function getName() { return "Query"; }
        public function getTitle() { return "Query"; }
        public function load() {
        	print("<form method = 'post' class='plain-form'>");
            print("<textarea id='query-text' placeholder='Select query' class='query-input' name='query-text'></textarea>");
            print("<input class='query-submit' type='submit' name = 'submit' value = 'Submit'/>");
   			print("<input class='query-clear' type='submit' name='clear' value='Clear'/>");
            print("</form>");
            if(isset($_POST['submit'])) {
                try {
                    $headers = $this->connection->getColumns($_POST['query-text']);
                    $result = $this->connection->query($_POST['query-text']);
                    print("<table id='plain-table'>");
                    print("<tr>");
                    foreach($headers as $header) {
                        print("<th>".str_replace("_", " ", $header)."</th>");
                    }
                    print("</tr>");
                    foreach($result->getRows() as $row) {
                    	print("<tr>");
                       	foreach($row->getColumn() as $cell) {
                            print("<td>{$cell->getValue()}</td>");
                        }
                        print("</tr>");
                    }
                    print("</table>");
                }                                                                                                                                                                                                                                                                                                                               
                catch(Exception $e) { }                                                                                                                                                                                                                                                                                                         
            }
			else if(isset($_POST['clear'])) {
				print("<script>document.getElementById('query-text').value = '';</script>");
				print("<script>window.location=window.location;</script>");
			}                                                                                                                                                                                                                                                                                                                                   
		}		
	}
?>
