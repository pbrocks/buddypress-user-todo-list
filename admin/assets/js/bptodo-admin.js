jQuery(document).ready(function(){
	//Support tab accordian
	var acc = document.getElementsByClassName("bptodo-accordion");
	var i;
	for (i = 0; i < acc.length; i++) {
		acc[i].onclick = function() {
			this.classList.toggle("active");
			var panel = this.nextElementSibling;
			if (panel.style.maxHeight){
				panel.style.maxHeight = null;
			} else {
				panel.style.maxHeight = panel.scrollHeight + "px";
			} 
		}
	}

	jQuery(document).on('click', '.bptodo-accordion', function(){
		return false;
	});
});

