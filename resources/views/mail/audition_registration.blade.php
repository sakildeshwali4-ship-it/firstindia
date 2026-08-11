<!DOCTYPE html>
<html>

<head>
    <title>{{ $title }}</title>
</head>

<body>
    <p>Dear participant,</p>
	<table style="width:40%; border:none; text-align:left;">
		<tr><th>Reference ID: </th><td><?php echo $audition->application_ref ?></td></tr>
		<tr><th>Username: </th><td><?php echo $audition->username; ?></td></tr>
		<tr><th>Password: </th><td><?php echo $audition->password; ?></td></tr>
	</table>
	<p>Congratulations! You have successfully signed up for the Super Singer+ <?php echo $audition->audition->city->city_name; ?>. We are thrilled to have you on board and look forward to witnessing your incredible talent. You are now requested to complete the payment process to register yourself for the competition.</p>
	<p>Please keep an eye on your inbox for any further communication regarding venue details and other important updates.</p>

	<p><b>Warm regards,</b><br>
	First India Plus Entertainment Team </p>
</body>
</html>