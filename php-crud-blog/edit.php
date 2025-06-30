include 'auth.php';
if ($_SESSION['role'] !== 'admin') {
    echo "Access Denied.";
    exit();
}
<?php
include 'config.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "Invalid post ID.";
    exit();
}

// Fetch the post to edit
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    echo "Post not found.";
    exit();
}

$title = $post['title'];
$content = $post['content'];
$titleErr = $contentErr = "";

// Handle update submission
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
        $updateStmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $updateStmt->bind_param("ssi", $title, $content, $id);
        $updateStmt->execute();

        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
</head>
<body>
<h2>Edit Post</h2>
<form method="POST" onsubmit="return validateForm();">
    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" placeholder="Title" required>
    <span style="color:red;"><?php echo $titleErr; ?></span><br><br>

    <textarea name="content" id="content" placeholder="Content" required><?php echo htmlspecialchars($content); ?></textarea>
    <span style="color:red;"><?php echo $contentErr; ?></span><br><br>

    <button type="submit">Update</button>
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
