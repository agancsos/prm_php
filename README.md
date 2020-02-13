# README

## Implemtnation Details
Private Record Management (PRM) is a web application intended to help organize and manage software during all stages of the Software Development Life Cycle (SDLC).  PRM targets software development teams ranging from 1 to 10 people using various customizable development paradigms such as Waterfall and Scrum.

## Assumptions
•	There is a need for a record managed system
•	Other systems either don’t have the right features or aren’t free
•	Users will want to customize their environment 
•	Teams can range from a single user up to (but not limited) 10 members
•	System must be modular and scalable 

## Requirements
•	PRM shall manage work items
•	PRM shall manage knowledgebase articles
•	PRM shall manage uploaded files
•	PRM shall manage users
•	PRM shall manage teams
•	PRM shall manage groups
•	PRM shall manage item security
•	PRM shall manage page security

## Constraints
•	PRM must be compatible with Google Chrome versions 65+
•	PRM must be compatible with Mozilla Firefox versions 65+
•	PRM must be compatible with Windows 10+
•	PRM must be compatible with Mac OS 10.13+
•	PRM must be compatible with Linux kernel 7+
•	PRM must be compatible with Java 1.8
•	PRM must load pages within 30 seconds

## Implementation Description
PRM uses a combination of different design patterns, specifically MVVM (Model-View-View Model) for the graphical user interface (GUI).  Similar to MVC (Model-View-Controller), this is meant to separate the data from the graphical components; however, with MVVM, the View Model should be able to be reused, despite PRM not doing so since there isn't really a need.  Also, in the case of PRM, more data manipulation along with rendering is done in the View Model.

PRM also uses Singleton for the custom services that are used to perform operations based on the service as well as Observer for certain services, such as the PRMSelectionSErvice.  The reaosn this was done is to limit the number of instances for the different data that will be manipulated as well as to make sure that all views and users are accessing the same data.  All services are designed to be independent of each other, but most services are made up of other services.

# Instructions
## Add a View Module
1. (optional) Add a directory in the prmGUI directory
2. (optional) Add an all.php file including all of the View Modules
3. Add the new View Model and implement the PRMViewModel class
4. Add the class to the all.php file.  If steps 1-2 were followed, the added all.php file should be added

## Add a module
1. Add a directory in the admins directory
2. Add an index.php
3. Add the appropriate View Model

## Note from the developer
Just wanted to note that this is a culmination of previous system designs over the past 10 years.  PRM demonstrates how a developer can grow over time, starting with CrystalWorks to PRM.  Full history of this project is:

Project				  ; Year				; Description																		   
CrystalWorks		; 2008-2009		; Used separate database schemas for each module.  Data warehouse.  Overscaled.		 
PocketManager		; 2010-2012		; Hosted.  Network based (multiple companies).  Overscaled. Reporting.  Feature rich.   
Repos					  ; 2014-2015		; Clean.  View and database driven.  Feature rich.  Functional. Filters using queries.  
PRM					    ; 2018-2019		; Revamped version of Repos.  Customer filter mechanism.  OOP driven.  Model-ViewModel. 
