(function(){
	// If the user clicks the mouse of pushes a key, then load a random gif
	var newGif = function() {
		//window.location = "?id=random";
		window.location = "/id/random";
	};

	var contentSizing = function() {
		function sizeContent() {			
			$("#content")
			.width($(window).width())
			.height($(window).height());
		}
		
		sizeContent();
		$(window).resize(sizeContent);
	};

	var socialSharing = function() {
		var createTwitterLink = function (url, text, hashtags) {
			var link =
				"https://twitter.com/intent/tweet" +
				"?text=" + encodeURI(text) + 
				"&hashtags=" + encodeURI(hashtags) + 
				"&url=" + encodeURI(url);
			return link;
		};

		var createFacebookLink = function(url, title, summary, image) {
			var link =
				"https://www.facebook.com/sharer/sharer.php" +
				"?s=100" + 
				"&p[url]=" + encodeURI(url) + 
				"&p[images][0]=" + encodeURI(image) + 
				"&p[title]=" + encodeURI(title) + 
				"&p[summary]=" + encodeURI(summary);
			return link;
		};

		var makeRandom = function(n) {
			return (Math.floor((Math.random() * n)));
		};

		var messages = [
			"Check out this GIF!",
			"Your mum loves this GIF, and so should you.",
			"I saw this GIF and I thought of you.",
			"This is a really neat sites for viewing randomly, tiled GIFs.",
			"It was the best of times, it was the worst of times.",
			"Gifulous! Filling your screen with GIFs.",
			"Gifulous! Because you need stupid GIFS.",
			"Check this out!",
			"I found this amusing and believe the case will be likewise for you.",
			"Gifulous! For viewing randomly, tiled GIFs.",
			"Does the world need another GIF sharing site? No. Here's one anyway.",
			"I found this amusing and believe the case will be likewise for you.",
			"Look at this thing."
		];

		var imageUrl = $("#content").css("background-image");
        imageUrl = imageUrl.replace("url(","").replace(")","");

		$("#facebook").attr("href",
			createFacebookLink(window.document.location, "Gifulous!", messages[makeRandom(messages.length)], imageUrl)
		);

		$("#twitter").attr("href",
			createTwitterLink(window.document.location, messages[makeRandom(messages.length)], "gifs")
		);
	};

	$(document).ready(function() {
		$("#content")
		.on("click", newGif)
		.on("keyup", newGif);

		if (typeof(random) != "undefined")
			setTimeout(newGif, 500);
			
		contentSizing();
		socialSharing();
	});
}())