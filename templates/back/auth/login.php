<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>BackOffice Login</title>
	<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
	<main>
		<h1>BackOffice Login</h1>
		<form method="post" action="/admin/login">
			<label>Email
				<input type="email" name="email" required>
			</label>
			<label>Password
				<input type="password" name="password" required>
			</label>
			<button type="submit">Login</button>
		</form>
	</main>
</body>
</html>
