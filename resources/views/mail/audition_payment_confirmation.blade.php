<!DOCTYPE html>
<html>

<head>
    <title>{{ $title }}</title>
</head>

<body>
    <p>Dear participant,</p>

	<p>We are delighted to inform you that your payment of Rs <?php echo $audition->amount; ?> for registering in the Super Singer+ <?php echo $audition->audition->city->city_name; ?> competition has been successfully processed. Thank you for your payment. Your registration is now complete and you are officially confirmed as a participant in the competition. We can't wait to see you showcase your talent on the stage.
	Below are the details you provided during registration:</p>
	<table style="width:40%; border:none; text-align:left;">
		<tr><th>Name: </th><td><?php echo $audition->first_name.' '.$audition->last_name; ?></td></tr>
		<tr><th>Age: </th><td><?php echo $audition->age ?></td></tr>
		<tr><th>Gender: </th><td><?php echo ucfirst($audition->gender); ?></td></tr>
		<tr><th>Address: </th><td><?php echo $audition->address; ?></td></tr>
		<tr><th>Mobile: </th><td><?php echo $audition->mobile; ?></td></tr>
		<tr><th>Email: </th><td><?php echo $audition->email; ?></td></tr>
		<tr><th>Reference ID: </th><td><?php echo $audition->application_ref ?></td></tr>
	</table>
	<p>Please stay tuned for further updates regarding venue details, and other important information.</p> 

	<p><b>Warm regards,</b><br>
	First India Plus Entertainment Team </p>

	<!--<p>Find your registration ticket attached below:</p>-->
</body>

</html>