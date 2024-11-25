<?php
session_start();
require 'config/database.php';

$chapterId = isset($_GET['chapter_id']) ? intval($_GET['chapter_id']) : 0;

// Ambil informasi chapter
$query = "SELECT * FROM Chapters WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $chapterId);
$stmt->execute();
$chapter = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$chapter) {
    echo "Chapter not found!";
    exit;
}

$isLoggedIn = isset($_SESSION['user_id']);
$loggedInUserId = $isLoggedIn ? $_SESSION['user_id'] : 0;
$workId = $chapter['work_id'];

// Cek apakah pengguna login adalah penulis chapter
$query = "SELECT author_id FROM Works WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $workId);
$stmt->execute();
$work = $stmt->get_result()->fetch_assoc();
$stmt->close();

$isAuthor = $work['author_id'] == $loggedInUserId;

if ($isLoggedIn) {
    // Cek apakah sudah ada dalam riwayat baca
    $checkQuery = "SELECT * FROM readinghistory WHERE user_id = ? AND chapter_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $loggedInUserId, $chapterId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows == 0) {
        // Simpan riwayat baca baru
        $insertQuery = "INSERT INTO readinghistory (user_id, chapter_id, read_at) VALUES (?, ?, NOW())";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ii", $loggedInUserId, $chapterId);
        $insertStmt->execute();
        $insertStmt->close();
    } else {
        // Perbarui waktu baca terakhir jika sudah ada
        $updateQuery = "UPDATE readinghistory SET read_at = NOW() WHERE user_id = ? AND chapter_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $loggedInUserId, $chapterId);
        $updateStmt->execute();
        $updateStmt->close();
    }
    $checkStmt->close();
}
// **END: Kode untuk menyimpan riwayat baca ditambahkan di sini**


// Ambil komentar terkait chapter ini
$query = "SELECT comments.*, users.username, users.profile_picture FROM Comments 
          JOIN Users ON comments.user_id = users.id 
          WHERE chapter_id = ? AND parent_comment_id IS NULL ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $chapterId);
$stmt->execute();
$commentsResult = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>View Chapter</title>

    <style>
        .chapter-content {
            white-space: pre-wrap;
            word-wrap: break-word;
            overflow-wrap: break-word;
            overflow-x: auto;
            font-size: 18px; 
            line-height: 1.6; 
            max-width: 8000px; 
            margin: 0 auto;
            padding: 10px;
            background-color: #f9f9f9; 
            border-radius: 8px; 
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-5">
        <div class="chapter-content ">
        <h1><?php echo htmlspecialchars($chapter['name']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($chapter['content'])); ?></p>
        </div>
        <?php if ($isAuthor): ?>
            <a href="edit_chapter.php?chapter_id=<?php echo $chapter['id']; ?>" class="btn btn-warning">Edit Chapter</a>
            <a href="delete_chapter.php?chapter_id=<?php echo $chapter['id']; ?>" class="btn btn-danger">Delete Chapter</a>
        <?php endif; ?>
    </div>

    <div class="container mt-4">
        <?php if ($isLoggedIn): ?>
            <h4>Add a comment</h4>
            <form action="add_comment.php" method="post">
                <div class="form-group">
                    <textarea name="content" class="form-control" rows="3" placeholder="Write your comment here..." required></textarea>
                </div>
                <input type="hidden" name="chapter_id" value="<?php echo $chapterId; ?>">
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Login</a> to add a comment.</p>
        <?php endif; ?>

        menampilkan komentar
        <h3>Comments</h3>
        <?php if ($commentsResult->num_rows > 0): ?>
            <ul class="list-group mb-4">
                <?php while ($comment = $commentsResult->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <div class="media">
                            <img src="uploads/<?php echo htmlspecialchars($comment['profile_picture'] ?: 'default_profile.jpg'); ?>" alt="Profile Picture" class="mr-3" style="width:50px;height:50px;border-radius:50%;">
                            <div class="media-body">
                                <h5 class="mt-0"><?php echo htmlspecialchars($comment['username']); ?></h5>
                                <p><?php echo htmlspecialchars($comment['content']); ?></p>
                                <small class="text-muted"><?php echo date('F j, Y, g:i a', strtotime($comment['created_at'])); ?></small>
                                
                                <?php if ($isLoggedIn): ?>
                                    <button class="btn btn-secondary btn-sm mt-2 reply-button" data-comment-id="<?php echo $comment['id']; ?>">Reply</button>
                                    <br>
                                    <!--Report komentar -->
<a href="#" class="report-btn" data-type="comment" data-id="<?php echo $comment['id']; ?>" data-user-id="<?php echo $comment['user_id']; ?>">Report Comment</a>
                                <?php endif; ?>
                            

                            </div>
                            
                        </div>

                        <?php
                        //meng inputkan komen ke database
                        $parentCommentId = $comment['id'];
                        $replyQuery = "SELECT comments.*, users.username, users.profile_picture FROM Comments 
                                       JOIN Users ON comments.user_id = users.id 
                                       WHERE parent_comment_id = ? ORDER BY created_at ASC";
                        $replyStmt = $conn->prepare($replyQuery);
                        $replyStmt->bind_param("i", $parentCommentId);
                        $replyStmt->execute();
                        $repliesResult = $replyStmt->get_result();
                        ?>

                        <?php if ($repliesResult->num_rows > 0): ?>
                            <ul class="list-group mt-3">
                                <?php while ($reply = $repliesResult->fetch_assoc()): ?>
                                    <li class="list-group-item">
                                        <div class="media">
                                            <img src="uploads/<?php echo htmlspecialchars($reply['profile_picture'] ?: 'default_profile.jpg'); ?>" alt="Profile Picture" class="mr-3" style="width:50px;height:50px;border-radius:50%;">
                                            <div class="media-body">
                                                <h5 class="mt-0"><?php echo htmlspecialchars($reply['username']); ?></h5>
                                                <p><?php echo htmlspecialchars($reply['content']); ?></p>
                                                <small class="text-muted"><?php echo date('F j, Y, g:i a', strtotime($reply['created_at'])); ?></small>
                                            </div>
                                        </div>
                                        
                                        <?php if ($isLoggedIn): ?>
                                    <button class="btn btn-secondary btn-sm mt-2 reply-button" data-comment-id="<?php echo $comment['id']; ?>">Reply</button>
                                    <a href="#" class="report-btn" data-type="comment" data-id="<?php echo $comment['id']; ?>" data-user-id="<?php echo $comment['user_id']; ?>">Report Comment</a>

                                <?php endif; ?>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php endif; ?>
                        <?php $replyStmt->close(); ?>

                        <!-- Reply  -->
                        <?php if ($isLoggedIn): ?>
                            <div class="reply-form mt-3" id="reply-form-<?php echo $comment['id']; ?>" style="display: none;">

                                <form action="add_comment.php" method="post">
                                    <input type="hidden" name="chapter_id" value="<?php echo $chapterId; ?>">
                                    <input type="hidden" name="parent_comment_id" value="<?php echo $comment['id']; ?>">

                                    <div class="form-group">
                                        <textarea name="content" class="form-control" rows="3" placeholder="Write your reply here..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit Reply</button>

                                </form>

                            </div>

                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Script untuk toggle form reply
        document.querySelectorAll('.reply-button').forEach(button => {
            button.addEventListener('click', () => {
                const commentId = button.getAttribute('data-comment-id');
                const replyForm = document.getElementById(`reply-form-${commentId}`);
                replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
            });
        });
    </script>
<script>
document.querySelectorAll('.report-btn').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();
        const type = this.getAttribute('data-type'); // work or comment
        const reportedId = this.getAttribute('data-id');
        const reportedUserId = this.getAttribute('data-user-id');
        const reason = prompt("Enter the reason for reporting:");

        if (reason) {
            fetch('report.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    type: type,
                    reportedId: reportedId,
                    reportedUserId: reportedUserId,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
            });
        }
    });
});
</script>

    
</body>
</html>
