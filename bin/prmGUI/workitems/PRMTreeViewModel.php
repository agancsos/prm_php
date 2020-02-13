<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMViewModel.php");
    class PRMTreeViewModel extends PRMViewModel {
        private $selectionService = NULL;
        private $workItemService = NULL;
        public function __construct($root = "./") {
            global $__ROOT_FROM_PAGE__;
            parent::__construct($root);
            $this->service = PRMService::getInstance();
            $this->selectionService = PRMSelectionService::getInstance();
            $this->workItemService = PRMWorkItemService::getInstance();
			if(isset($_GET['id'])){
				$this->selectionService->setSelectedId($_GET['id']);
			}
			if($this->selectionService->getSelectedNode() == null) {
            	$this->selectionService->setSelectedNode($this->workItemService->getRoot());
			}
        }
        public function getName() { return "workitems"; }
        public function getTitle() { return "WorkItems"; }
        public function getIsSecure() { return TRUE; }
        public function getIsEnabled() { return TRUE; }
        public function load() {
			global $__ROOT_FROM_PAGE__;
            print("<h2>".SR::$__TREE_HEADER__."</h2>");
			print("<hr/>");
            print("<form method='POST' class='tree-tools'>");
            print("<input type='hidden' value='".$this->selectionService->getSelectedNode()->getId()."'/>");
			print("<input type='submit' name='add-input' value='Folder'/>");
			print("<input type='submit' name='add-input' value='Query'/>");
			print("</form>");
            if(isset($_POST['add-input'])) {
				if($this->selectionService->getSelectedNode()->getIsFolder()) {
					$newNode = null;
                	if($_POST['add-input'] == "Folder") {
						$newNode = new PRMTreeNode("", "NEW", "Folder", True);
                	}
                	else if($_POST['add-input'] == "Query") {
						$newNode = new PRMTreeNode("", "NEW", "Query", True);
                	}
					$this->workItemService->addItem($newNode, $this->selectionService->getSelectedNode());
                	print("<script>window.location=window.location;</script>");                    
				}
				else {
					$this->alert("Cannot add a child to a query object");
				}
            }
			print("<ul id='tree-object'>");
			if($this->workItemService->getRoot() != NULL) {
				print("<li onclick=\"updateSelection(this)\" oncontextmenu=\"collapseTreeNode(this)\" id='folder".$this->workItemService->getRoot()->getId()."' class='root-node'>");
				print("{$this->workItemService->getRoot()->getName()}");
				$this->setNodes($this->workItemService->getRoot(), intval($this->configService->__TREE_INDENTATION__));
				print("</li>");
			}
			print("</ul>");
        }
		private function setNodes($node, $padding=0) {
			print("<ul oncontextmenu=\"collapseTreeNode(this)\" class='nested-node'>");
			foreach($node->getChildren() as $child){
				if($child->getIsFolder()){
					print("<li onclick=\"updateSelection(this)\" id='folder".$child->getId()."' style='padding-left:". $padding.";' class='tree-node");
					if($this->selectionService->getSelectedNode()->getId() == $child->getId()) {
                    	print(" selected-node");
                	}
					print("' >");
					print($child->getName());
					print("</li>");
					$this->setNodes($child, $padding + intval($this->configService->__TREE_INDENTATION__));
					print("</li>");
				}
				else {
					print("<li onclick=\"updateSelection(this)\" onclick=\"updateSelection(this)\" id=\"child".$child->getId()."\" class='query-node");
					if($this->selectionService->getSelectedNode()->getId() == $child->getId()) {
						print(" selected-node");
					}
					print("' style='padding-left: " . ($padding + intval($this->configService->__TREE_INDENTATION__)).";'>{$child->getName()}</li>");
				}
			}
			print("</ul>");
		}
    }
?>
