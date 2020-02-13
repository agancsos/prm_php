<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmService/PRMService.php");
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmCommon/prmcommon_all.php");
	include_once("PRMLinksViewModel.php");
	function redirectPage($target) { 
		print("<meta http-equiv=\"refresh\" content=\"0; url={$target}\" />"); 
	}
	abstract class PRMViewModel {
		protected $__ROOT__ = __DIR__;
		protected $sessionService = NULL;
		protected $service = NULL;
		protected $descriptor = array();
		protected $isLinksEnabled = True;
		protected $linksViewModel = NULL;
		protected $configService = NULL;
		protected $search = "";
		protected function ensure() {
			if($this->configService->__DISABLE_ACCESS_CHECK__ != "1") {
				return;
			}
			else if(!$this->getIsEnabled()) {
				redirectPage("/");
			}
			else if($this->getIsSecure()) {
				if($this->sessionService->getToken() === NULL && $this->configService->__DISABLE_ACCESS_CHECK__ != "1") {
					redirectPage("/");
				}
				else {
					$this->sessionService->setUser($this->service->getUser($this->sessionService->getToken()));
				}
			}
		}
		public function addDescriptor($a) {
			if(! in_array($a, $this->descriptor)) {
				array_push($this->descriptor, $a);
			}
		}
		public function getDescriptor() { return $this->descriptor; }
		public function __construct($root = "./") {
			$this->__ROOT__ = $root;
			$this->service = PRMService::getInstance();
			$this->sessionService = PRMSessionService::getInstance();
			$this->configService = PRMConfigurationService::getInstance();
			$this->sessionService->heartbeat();
			if(isset($_GET['s'])) {
				$this->search = $_GET['s'];
			}
		}
		public abstract function getName(); 
		public abstract function getTitle();
		public abstract function getIsSecure();
		public abstract function getIsEnabled();
		public abstract function load();
		protected function printHeader() { 
			print("<html>");
			print("<head>");
			print("<title>PRM</title>");
			print("<meta name = 'keywords' contents = ''/>");
			print("<meta name = 'author' contents = 'Abel Gancsos Productions'/>");
			print("<meta name = 'version' contents = 'v. 1.0.0'/>");
			print("<link href='/main.css' rel='stylesheet' type='text/css'/>");
			print("<link rel=\"icon\" type=\"image/png\" href=\"/favicon.png\">");
			print("<script type=\"text/javascript\" src=\"/functions.js\"></script>");
			print("</head>");
			print("<body>");
			print("<div id = 'banner'>");
			print("<div id = 'banner-inner'>");
			print("<a href = '/'><div id = 'logo' class = 'h1'>".SR::$__APPLICATION_NAME__."</div></a>");
			print("</div>");
			print("</div>");
			print("<div id = 'links'>");
			print("<div id = 'links-inner'>");
			if($this->isLinksEnabled && $this->sessionService->shouldShow()) {
				$this->linksViewModel = new PRMLinksViewModel($this->__ROOT__, $this->isLinksEnabled, $this->getName());
				$this->linksViewModel->load();
			}
			print("</div>");
			print("</div>");
			print("<div id = 'main'>");
			print("<div id = 'main-inner'>");
			$this->ensure();

		}
		public function printFooter() { require_once($this->__ROOT__."footer.php"); }
		public function alert($msg) {
			print("<script>alert('".$msg."');</script>");
		}
		public function getSearch() { return $this->search; }
	}
?>
