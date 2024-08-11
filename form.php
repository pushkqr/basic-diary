<?php
require __DIR__ . "/inc/db.inc.php";
require __DIR__ . "/inc/func.inc.php";
require __DIR__ . "/inc/header.inc.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = !empty($_POST['title']) ? e($_POST['title']) : null;
  $date = !empty($_POST['date']) ? e($_POST['date']) : null;
  $content = !empty($_POST['content']) ? e($_POST['content']) : null;
  $imageName = null;

  if (!empty($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $nameWithoutExtension = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
    $name = preg_replace('/[^a-zA-Z0-9]/', '', $nameWithoutExtension);
    $originalImage = $_FILES['image']['tmp_name'];
    $imageName = $name . '-' . time() . '.jpg';
    $uploadDir = __DIR__ . '/uploads/';

    if (!file_exists($uploadDir)) {
      mkdir($uploadDir, 0755, true);
    }

    $destImage = $uploadDir . $imageName;
    $imageSize = getimagesize($originalImage);

    if ($imageSize !== false && $imageSize['mime'] === 'image/jpeg') {
      [$width, $height] = $imageSize;
      $maxDim = 400;
      $scaleFactor = $maxDim / max($width, $height);
      $newWidth = (int)($width * $scaleFactor);
      $newHeight = (int)($height * $scaleFactor);

      if (function_exists('imagecreatefromjpeg') && function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled') && function_exists('imagejpeg')) {
        $im = imagecreatefromjpeg($originalImage);
        $newImg = imagecreatetruecolor($newWidth, $newHeight);
        if (imagecopyresampled($newImg, $im, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
          imagejpeg($newImg, $destImage);
        }
        imagedestroy($newImg);
        imagedestroy($im);
      }
    }
  }

  if ($title && $date && $content) {
    $stmt = $pdo->prepare("INSERT INTO entries(`title`, `date`, `content`, `image`) VALUES(:title, :date, :content, :image);");
    $stmt->bindValue(":title", $title);
    $stmt->bindValue(":date", $date);
    $stmt->bindValue(":content", $content);
    $stmt->bindValue(":image", $imageName);
    $stmt->execute();
  }
}
?>

<h1 class="main-heading">NEW ENTRY</h1>
<form action="form.php" method="POST" enctype="multipart/form-data">
  <div class="form-group">
    <label class="form-group_label" for="title">Title:</label>
    <input class="form-group_input" type="text" id="title" name="title" required />
  </div>
  <div class="form-group">
    <label class="form-group_label" for="date">Date:</label>
    <input class="form-group_input" type="date" id="date" name="date" required />
  </div>
  <div class="form-group">
    <label class="form-group_label" for="image">Image:</label>
    <input class="form-group_input" type="file" id="image" name="image" />
  </div>
  <div class="form-group">
    <label class="form-group_label" for="content">Content:</label>
    <textarea class="form-group_input" name="content" id="content" rows="6" required></textarea>
  </div>
  <div class="form-submit">
    <button class="button">
      <svg class="button_icon" viewBox="0 0 34.7163912799 33.4350009649">
        <g style="fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2px;">
          <polygon points="20.6844359446 32.4350009649 33.7163912799 1 1 10.3610302393 15.1899978903 17.5208901631 20.6844359446 32.4350009649" />
          <line x1="33.7163912799" y1="1" x2="15.1899978903" y2="17.5208901631" />
        </g>
      </svg>
      Save
    </button>
  </div>
</form>

<?php require __DIR__ . "/inc/footer.inc.php"; ?>