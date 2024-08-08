<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data</title>
</head>
<body>
    <form action="save_command.php" method="post">
        <label for="label">Label:</label>
        <input type="text" id="label" name="label" required><br>

        <label for="text">Text:</label>
        <textarea id="text" name="text" required></textarea><br>

        <input type="submit" value="Save">
    </form>
</body>
</html>
