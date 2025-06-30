<?php
include 'auth.php';
if (!in_array($_SESSION['role'], ['admin', 'editor'])) {
    echo "Access Denied.";
    exit();
}
 include 'config.php'; 
$titleErr = $contentErr = "";
$title = $content = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $isValid = true;

    if (empty($title) || strlen($title) > 100) {
        $titleErr = "Title must be 1–100 characters.";
        $isValid = false;
    }

    if (empty($content) || strlen($content) < 10) {
        $contentErr = "Content must be at least 10 characters.";
        $isValid = false;
    }

    if ($isValid) {
        $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        $stmt->execute();
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Post</title>
</head>
<body>
<h2>Add New Post</h2>
<form method="POST" onsubmit="return validateForm();">
    <input type="text" name="title" id="title" placeholder="Title" value="<?php echo htmlspecialchars($title); ?>" required>
    <span style="color:red;"><?php echo $titleErr; ?></span><br><br>

    <textarea name="content" id="content" placeholder="Content" required><?php echo htmlspecialchars($content); ?></textarea>
    <span style="color:red;"><?php echo $contentErr; ?></span><br><br>

    <button type="submit">Create</button>
</form>

<script>
function validateForm() {
    const title = document.getElementById("title").value.trim();
    const content = document.getElementById("content").value.trim();
    if (title.length === 0 || title.length > 100) {
        alert("Title must be 1–100 characters.");
        return false;
    }
    if (content.length < 10) {
        alert("Content must be at least 10 characters.");
        return false;
    }
    return true;
}
</script>
</body>
</html>
