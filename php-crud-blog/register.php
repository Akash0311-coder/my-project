<?php include 'config.php'; ?>
<?php
$usernameErr = $passwordErr = "";
$username = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $isValid = true;

    if (strlen($username) < 3) {
        $usernameErr = "Username must be at least 3 characters.";
        $isValid = false;
    }

    if (strlen($password) < 6) {
        $passwordErr = "Password must be at least 6 characters.";
        $isValid = false;
    }

    if ($isValid) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed);
        $stmt->execute();
        header("Location: login.php");
        exit();
    }
}
?>

<form method="POST" onsubmit="return validateForm();">
    <h2>Register</h2>
    <input type="text" name="username" id="username" placeholder="Username" required>
    <span style="color:red;"><?php echo $usernameErr; ?></span><br><br>

    <input type="password" name="password" id="password" placeholder="Password" required>
    <span style="color:red;"><?php echo $passwordErr; ?></span><br><br>

    <button type="submit">Register</button>
</form>

<script>
function validateForm() {
    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();
    if (username.length < 3) {
        alert("Username must be at least 3 characters.");
        return false;
    }
    if (password.length < 6) {
        alert("Password must be at least 6 characters.");
        return false;
    }
    return true;
}
</script>
