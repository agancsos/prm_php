<?php
	include_once("PRMObjectService.php");
	include_once("PRMDataService.php");
	class PRMFormService {
		public static function getFilters($filterId) {																																																																										 
			$result = array();		   
			$dataService = PRMDataService::getInstance();																																																																											   
			$rawRows = $dataService->getHandler()->query("SELECT * FROM PRM_QUERY_FILTER WHERE PRM_QUERY_ID = '".$filterId."'");																																																				  
			foreach($rawRows->getRows() as $row) {																																																																									  
				$item = new PRMListFormItem($row->getColumn("PRM_QUERY_FILTER_ID")->getValue());																																																														
				$joinConditionField = new PRMAdvancedFormField("select","PRM_QUERY_JOIN_CONDITION", "filter-join-condition");																																																						   
				$options = array();																																																																													 
				array_push($options, new PRMGeneralItem("", "Join", ""));																																																																			   
				array_push($options, new PRMGeneralItem("AND", "AND", ""));																																																																			 
				array_push($options, new PRMGeneralItem("OR", "OR", ""));																																																																			   
				$joinConditionField->setOptions($options);																																																																							  
				$joinConditionField->setValue($row->getColumn("PRM_QUERY_JOIN_CONDITION")->getValue());																																																												 
				$joinConditionField->setRequiresPrevious(True);																																																																						 
				$item->addField($joinConditionField);																																																																								   
																																																																																						
				$fieldField = new PRMAdvancedFormField("select","PRM_QUERY_FIELD", "filter-field");																																																													 
				$options = PRMWorkItemService::getInstance()->getColumns();																																																																										 
				$fieldField->setOptions($options);																																																																									  
				$fieldField->setValue($row->getColumn("PRM_QUERY_FIELD")->getValue());																																																																  
				$item->addField($fieldField);																																																																										   
																																																																																						
				$conditionField = new PRMAdvancedFormField("select","PRM_QUERY_CONDITION", "filter-condition");																																																										 
				$options = array();																																																																													 
				array_push($options, new PRMGeneralItem(">", ">", ""));																																																																				 
				array_push($options, new PRMGeneralItem("<", "<", ""));																																																																				 
				array_push($options, new PRMGeneralItem("=", "=", ""));																																																																				 
				array_push($options, new PRMGeneralItem("<>", "<>", ""));																																																																			   
				array_push($options, new PRMGeneralItem("LIKE", "LIKE", ""));																																																																		   
				array_push($options, new PRMGeneralItem("IN", "IN", ""));																																																																			   
				$conditionField->setOptions($options);																																																																								  
				$conditionField->setValue($row->getColumn("PRM_QUERY_CONDITION")->getValue());																																																														  
				$item->addField($conditionField);																																																																									   
																																																																																						
				$valueField = new PRMAdvancedFormField("input type='text'","PRM_QUERY_VALUE", "filter-value");																																																										  
				$valueField->setValue($row->getColumn("PRM_QUERY_VALUE")->getValue());																																																																  
				$item->addField($valueField);																																																																										   
																																																																																						
				array_push($result, $item);																																																																											 
			}																																																																																		   
			return $result;																																																																															 
		}								 
		public static function getFormFields($viewModel) {
			$dataService = PRMDataService::getInstance();
			$objectService = PRMObjectService::getInstance($dataService);
			$result = array();
			$rawResult = $dataService->getHandler()->getColumns("SELECT * FROM PRM_{$viewModel}");
			foreach($rawResult as $row) {
				$tempField = new PRMFormField();
				if(strpos($row,"_AVATAR")) {
					continue;
				}
				else if(strpos($row, "{$viewModel}_ID") || strpos($row, "_DATE")) {
					$tempField->setEnabled(False);
				}
				else if(strpos($row, "_ISSYSADMIN")) {
					$tempField->setFieldType("select");
					$option1 = new PRMGeneralItem();
					$option2 = new PRMGeneralItem();
					$option1->setName("False");
					$option1->setId(0);
					$option2->setName("True");
					$option2->setId(1);
					$tempField->setOptions([$option1, $option2]);
				}
				else if(strpos($row, "_STATUS")) {
					$tempField->setFieldType("select");
					if($viewModel == "USER") {
						$tempOptions = array();
						foreach(PRMUserStatus::getItterator() as $status) {
							$option = new PRMGeneralItem();
							$option->setName($status);
							$option->setCanDelete(False);
							$option->setId(PRMUserStatus::fromName($status));
							array_push($tempOptions, $option);
						}
						$tempField->setOptions($tempOptions);
						if($viewModel != "USER") {
						}																																														 
					}
					else {
						$tempField->setOptions($objectService->getStatuses());
					}
				}
				else if(strpos($row, "_STATE_ID")) {
					$tempField->setFieldType("select");
					$tempField->setOptions($objectService->getStates());
				}
				else if(strpos($row, "_CITY_ID")) {
					$tempField->setFieldType("select");
					$tempField->setOptions($objectService->getCities());
				}
				else if(strpos($row, "_COUNTRY_ID")) {
					$tempField->setFieldType("select");
					$tempField->setOptions($objectService->getCountries());
				}
				$tempField->setColumnName($row);
				if(strpos($row, "_PASS")) {
					$tempField->setFieldType("password");
				}
				array_push($result, $tempField);
			}			  
			return $result;																																															   
		}

		public static function userFromForm($form) {
			$user = new PRMUser();
			$user->setId($form['PRM-USER-ID']);
			$user->setFirst($form['PRM-USER-FIRST']);
			$user->setLast($form['PRM-USER-LAST']);
			$user->setName($form['PRM-USER-NAME']);
			$user->setPassword($form['PRM-USER-PASS']);
			$user->setStatus($form['PRM-USER-STATUS']);
			$user->setCity($form['PRM-CITY-ID']);
			$user->setState($form['PRM-STATE-ID']);
			$user->setCountry($form['PRM-COUNTRY-ID']);
			$user->setIsSysAdmin($form['PRM-USER-ISSYSADMIN']);
			$user->setRole($form['PRM-USER-ROLE']);
			$user->setCreatedDate($form['CREATED-DATE']);
			$user->setLastUpdatedDate($form['LAST-UPDATED-DATE']);
			return $user;
		}  

		public static function articleFromForm($form) {
			$article = new PRMArticle();
			$article->setId($form['PRM-ARTICLE-ID']);
		   	$article->setTitle($form['PRM-ARTICLE-TITLE']);
			$article->setStatus($form['PRM-ARTICLE-STATUS']);
			$article->setAccess($form['PRM-ARTICLE-ACCESS']);
			$article->setDescription($form['PRM-ARTICLE-DESCRIPTION']);
			$article->setText($form['PRM-ARTICLE-TEXT']);
			$article->setState($form['PRM-ARTICLE-STATE']);
			$article->setCreatedDate($form['CREATED-DATE']);
			$article->setLastUpdatedDate($form['LAST-UPDATED-DATE']);
			return $article;																																																			   
		}
	}
?>
