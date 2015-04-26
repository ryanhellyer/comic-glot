function toggleClass(element, className){
	if (!element || !className){
		return;
	}

	className = ' ' + className;

	var classString = element.className, nameIndex = classString.indexOf(className);
	if (nameIndex == -1) {
		classString += '' + className;
	}
	else {
		classString = classString.substr(0, nameIndex) + classString.substr(nameIndex+className.length);
	}
	element.className = classString;
}


var bubbles = document.querySelectorAll('.bubble');
var index;
for (index = 0; index < bubbles.length; ++index) {

	// Toggle class on click
	bubbles[index].onclick = function(){
		toggleClass(this, 'active-bubble');
		toggleClass(document.getElementById('bubble-popover-background'), 'active');
		return false;
	};

}

var bubbles = document.querySelectorAll('.bubble-inner-inner');
var index;
for (index = 0; index < bubbles.length; ++index) {

	// Add close buttons to each speech bubble
	bubbles[index].innerHTML = bubbles[index].innerHTML + '<div class="close">Close button</div>';

}
