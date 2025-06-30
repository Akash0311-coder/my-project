<?php
include 'auth.php';
include 'config.php';

// Pagination setup
$postsPerPage = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $postsPerPage;

// Search setup
$search = '';
$totalPosts = 0;

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $searchParam = "%" . $search . "%";
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM posts WHERE title LIKE ? OR content LIKE ?");
    $countStmt->bind_param("ss", $searchParam, $searchParam);
} else {
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM posts");
}

$countStmt->execute();
$countStmt->bind_result($totalPosts);
$countStmt->fetch();
$countStmt->close();

$totalPages = ceil($totalPosts / $postsPerPage);

// Fetch posts for current page
if (isset($_GET['search'])) {
    $stmt = $conn->prepare("SELECT * FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT ?, ?");
    $stmt->bind_param("ssii", $searchParam, $searchParam, $offset, $postsPerPage);
} else {
    $stmt = $conn->prepare("SELECT * FROM posts ORDER BY created_at DESC LIMIT ?, ?");
    $stmt->bind_param("ii", $offset, $postsPerPage);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blog Posts</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Blog Posts</h2>
        <div>
            <span class="me-2">
  Welcome, <?php echo $_SESSION['username']; ?> 
  (<?php echo ucfirst($_SESSION['role']); ?>)
</span>
<a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
<a href="create.php" class="btn btn-success btn-sm ms-2">+ Add New Post</a>

        </div>
    </div>

    <!-- Search Form -->
    <form class="mb-4" method="GET" action="index.php">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <!-- Post List -->
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
<?php if ($_SESSION['role'] === 'admin'): ?>
    <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a>
    <a href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
<?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">← Previous</a>
                </li>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next →</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

</body>
</html>
