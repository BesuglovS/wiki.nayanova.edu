$(function() {
	function AfterListsLoaded() {
		setPhrase();	
	
		$(".changeThought").hover(function() {
				$(this).css('cursor','pointer');
			}, function() {
				$(this).css('cursor','auto');
		});
		
		$(".changeThought").click(setPhrase);		
	}
	
	function setPhrase(phraseIndex) {		
		var curPhrase = $("#randomThought").html();
		
		if (curPhrase === phraseArray[4]) return;
	
		var phraseIndex;
		if ((arguments.length != 0) && (typeof arguments[0] == "number"))
		{			
			phraseIndex = arguments[0];			
		}
		else
		{
			phraseIndex = Math.floor(Math.random()*phraseArray.length);	
		}
	
		var phrase = phraseArray[phraseIndex];				
		$("#randomThought").html(phrase);
			
		var imageSource = imageArray[phraseIndex];
		$("#randomImage").attr("src", imageSource);
	}
	
	var phraseArray = [];
	var imageArray = [];

	$.get('upload/thoughts/list.html', 
			function(data) {
				tList = data.split("\n");
				$.each(tList, function() {
					phraseArray.push(this);
				});
				
				$.get('upload/thoughts/imageList.html', 
					function(data) {						
						tList = data.split("\n");
						$.each(tList, function() {
							imageArray.push(this);
						});
						
						AfterListsLoaded();
					}
				);			
			}
	);	
});