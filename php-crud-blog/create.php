<?php include 'config.php'; ?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $conn->query("INSERT INTO posts (title, content) VALUES ('$title', '$content')");
    header("Location: index.php");
}
?>
<form method="POST">
    <input type="text" name="title" placeholder="Title" required><br>
    <textarea name="content" placeholder="Content" required></textarea><br>
    <button type="submit">Create</button>
</form>