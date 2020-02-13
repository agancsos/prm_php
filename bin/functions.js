var isResizing = false;
var focusElement = null;
var selectedNodes = [];
var previousNode = null;

/**
 * This function is called each time the selected node is changed.
 */
function updateSelection(node) {
	if(previousNode == null){
		previousNode = node;
		unselectNodes();
	   	selectedNodes.push(node);
	   	//node.class = "selected-node";
		window.location = "?id=" + node.id.replace("folder", "").replace("child", "");
	}
	else {
		previousNode = null;
	}
}

function getSelectedNode() {
	if(document.getElementById('tree-object') != null) {
		return document.getElementsByClassName('selected-node')[0];
	}
	return null;
}

/**
 * 
 */
function reloadWithType(field) {
	var target = new URL(window.location);
	target.searchParams.set('workitem_type', field.value);
	window.location = target;
}

/**
 * This function acts as a listener when a view is loaded.
 */
window.addEventListener('load', function(e) {
	if(document.getElementById('tree-object')) {
		climbTree(getSelectedNode());
		for(let child of document.getElementById('tree-object').getElementsByTagName("LI")) {
			child.addEventListener('contextmenu', function(ev) {
				ev.preventDefault();
				collapseTreeNode(ev.target, event);
				event.stopPropagation();
				return false;
			}, false);
		}
	}
	e.stopPropagation();
});

/**
 * The purpose of this function is to expand all parent nodes of the selected node.
 * This means a reverse walk of the tree or starting point is a leaf.
 */
function climbTree(node) {
	if(node) {
		while(node.parentElement && node.parentElement.id != "folder-1") {
			node = node.parentElement;
			node.style.display = "block";
		}
	}
}

/**
 * This function unmarks previously selected nodes.
 */
function unselectNodes() {
	for(let node of selectedNodes) {
		node.style.backgroundColor = "";
	}
}

/**
 * This function is also called each time the selected node is changed.
 */
function collapseTreeNode(node) {
	if (previousNode == null) {
		previousNode = node;
		if(node.id.replace("folder", "") != node.id) {
			var children = node.parentElement.getElementsByTagName("UL");
			if (node.id.replace("folder","") == "-1") {
				children = node.getElementsByTagName("LI");
			}
			for(let child of children) {
				if(child.style.display == "none") {
					child.style.display = "block";
				}
				else {
					child.style.display = "none";
				}
			}
		}
	}
	else {
		previousNode = null;
	}
}

/**
 * This function serves as a listener for the grid container flex.
 */
document.addEventListener('mousedown', function(e) {
    if (e.target.className === 'resize-handler-horizontal' || e.target.className === 'resize-handler-vertical') {
        isResizing = true;
        focusElement = e.target;
    }
    else {
        isResizing = false;
        focusElement = null;
    }
});

/**
 * This function serves as a listener for the grid container flex.
 */
document.addEventListener('mousemove', function(e) {
    if (e && e.target) {
        if (!isResizing || focusElement == null || e.target != focusElement) {
            return false;
        }
        if (focusElement.className === 'resize-handler-horizontal') {
            for (let el of focusElement.parentElement.children) {
                if (el === focusElement) {
                    return;
                }
                var rect = el.getBoundingClientRect();
                el.style.width = (1 * e.clientX) + "";
            }
        }
        else if (focusElement.className == 'resize-handler-vertical') {
			for (let el of focusElement.parentElement.children) {
                if (el === focusElement) {
                    return;
                }
                var rect = el.getBoundingClientRect();
                el.style.height = (1 * e.clientY) + "";
            }
        }
    }
});

/**
 * This function serves as a listener for the grid container flex.
 */
document.addEventListener('mouseup', function(e) { 
	isResizing = false; 
	focusElement = null;
});

function gotoPage(page) {
	window.location.href = page;
}

