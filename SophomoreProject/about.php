<?php
require('templates/header.html');
?>
<h3>How our site works</h3>
<p>
	First an administrator submits a phrase.<br>
	Then 20 images are acquired from a Google image search using the phrase.<br>
	The images are then sent to the Watson AI to teach it.<br>
	<br>
	Now you, our users, come in.<br>
	You may select to input your image through a file upload or by URL.<br>
	Then the image is sent to the Watson AI which gives a response that is given to you.<br>
</p>
<?php
require('templates/footer.html');
?>