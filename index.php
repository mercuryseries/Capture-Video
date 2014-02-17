<!doctype html>
<html>
	<head>
		<title>Camera</title>
		<meta charset="utf-8">
		<style>

			body{
				background-color: #012;
			}

			h1{
				color: #FFF;
				font-family: "Comic Sans MS", sans-serif;
			}

			#image{
				border: none;
			}
			
			#video, #image{
				border: 2px solid #FFF;
				padding: 5px;
			}

			#snap,#snap_black_white,#send_snap,#snap_inverse{
				background-color: #ccc;
				color: maroon;
				font-weight: bolder;
				padding: 10px;
				border: none;
				cursor: pointer;
				border-radius: 5px;
			}

		</style>
	</head>
	<body>
		<h1>CAPTURE VIDEO</h1>
		<video id="video" width="640" height="480" autoplay></video>
		<canvas id="canvas" width="640" height="480" style="display:none;"></canvas>
		<img id="image" src="" alt="" width="640" height="480" />

		<button id="snap">Prendre une photo</button>
		<button id="snap_black_white">Convertir en blanc et noir</button>
		<button id="snap_inverse">Inversion de couleurs</button>
		<button id="send_snap">Envoyer sur le serveur</button>

		<script src="js/jquery-1.11.0.min.js"></script>	
		<script>

			$(document).ready(function(){

				function hasGetUserMedia() {
				  return !!(navigator.getUserMedia || navigator.webkitGetUserMedia ||
				            navigator.mozGetUserMedia || navigator.msGetUserMedia);
				}

				if (hasGetUserMedia()) {
				  var canvas = document.getElementById("canvas"),
					context = canvas.getContext("2d"),
					video = document.getElementById("video"),
					videoParams = { "video": true },
					errorCallback = function(error) {
						console.log("Erreur Capture Video: ", error.code); 
					};

					if(navigator.getUserMedia) { 
						navigator.getUserMedia(videoParams, function(stream) {
							video.src = stream;
							video.play();
						}, errorCallback);
					} else if(navigator.webkitGetUserMedia) { 
						navigator.webkitGetUserMedia(videoParams, function(stream){
							video.src = window.webkitURL.createObjectURL(stream);
							video.play();
						}, errorCallback);
					} else if(navigator.mozGetUserMedia) { 
						navigator.mozGetUserMedia(videoParams, function(stream){
							video.src = window.URL.createObjectURL(stream);
							video.play();
						}, errorCallback);
					} else if(navigator.msGetUserMedia) { 
						navigator.msGetUserMedia(videoParams, function(stream){
							video.src = window.URL.createObjectURL(stream);
							video.play();
						}, errorCallback);
					}

					$("#snap").click(function() {
						context.drawImage(video, 0, 0, 640, 480);
						$("#image").attr('src', canvas.toDataURL());
					});

					$("#snap_black_white").click(function() {
						if($("#image").attr('src') != ""){
							var imageData = context.getImageData(0, 0, 640, 480);	

							var pixels = imageData.data; 
							var numPixels = pixels.length; 

							context.clearRect(0, 0, 640, 480); 
							
							for (var i = 0; i < numPixels; i++) { 
								var average = (pixels[i*4]+pixels[i*4+1]+pixels[i*4+2])/3; 
								pixels[i*4] = average; // Red 
								pixels[i*4+1] = average; // Green 
								pixels[i*4+2] = average; // Blue 
							}

							context.putImageData(imageData, 0, 0);
							$("#image").attr('src', canvas.toDataURL());
						} else {
							alert("Il vous faut premièrement prendre une photo SVP !");
						}
					});

					$("#snap_inverse").click(function() {
						if($("#image").attr('src') != ""){
							var imageData = context.getImageData(0, 0, 640, 480);	

							var pixels = imageData.data; 
							var numPixels = pixels.length; 

							context.clearRect(0, 0, 640, 480); 
							
							for (var i = 0; i < numPixels; i++) { 
								pixels[i*4] = 255-pixels[i*4]; // Red 
								pixels[i*4+1] = 255-pixels[i*4+1]; // Green 
								pixels[i*4+2] = 255-pixels[i*4+2]; // Blue 
							}

							context.putImageData(imageData, 0, 0);
							$("#image").attr('src', canvas.toDataURL());
						} else {
							alert("Il vous faut premièrement prendre une photo SVP !");
						}
					});

					$("#send_snap").click(function() {
						if($("#image").attr('src') != ""){
							var dataURL = canvas.toDataURL("image/png");
							
							 $.ajax({
								type: "post",
								url:  "save_to_img.php",
								data: {'img_data' : dataURL },
								success: function(data){
										alert('Enregistrement effectué !');
								}
							}); 
						} else {
							alert("Il vous faut premièrement prendre une photo SVP !");
						}
					});

				} else {
				  alert("getUserMedia() n'est pas supporté par votre navigateur !");
				}
			});

		</script>
	</body>
</html>