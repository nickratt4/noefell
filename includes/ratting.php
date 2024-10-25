<?php if ($isLoggedIn): ?>
            <form id="ratingForm" action="view_work.php?work_id=<?php echo $work['id']; ?>" method="POST">
                <div class="stars">
                    <input type="radio" id="star5" name="rating" value="5" <?php if ($existingRating && $existingRating['rating'] == 5) echo 'checked'; ?>>
                    <label for="star5">★</label>
                    <input type="radio" id="star4" name="rating" value="4" <?php if ($existingRating && $existingRating['rating'] == 4) echo 'checked'; ?>>
                    <label for="star4">★</label>
                    <input type="radio" id="star3" name="rating" value="3" <?php if ($existingRating && $existingRating['rating'] == 3) echo 'checked'; ?>>
                    <label for="star3">★</label>
                    <input type="radio" id="star2" name="rating" value="2" <?php if ($existingRating && $existingRating['rating'] == 2) echo 'checked'; ?>>
                    <label for="star2">★</label>
                    <input type="radio" id="star1" name="rating" value="1" <?php if ($existingRating && $existingRating['rating'] == 1) echo 'checked'; ?>>
                    <label for="star1">★</label>
                </div>
            </form>
        <?php else: ?>
            <p>Please <a href="login.php">login</a> to rate this work.</p>
        <?php endif; ?>