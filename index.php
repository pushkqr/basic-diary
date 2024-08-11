<?php
require __DIR__ . "/inc/db.inc.php";
require __DIR__ . "/inc/func.inc.php";
require __DIR__ . "/inc/header.inc.php";

$perPage = 4;

date_default_timezone_set('Asia/Kolkata');

try {
  $stmt = $pdo->prepare("SELECT COUNT(id) AS 'count' FROM entries;");
  $stmt->execute();
  $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
  $totalPages = $count / $perPage;
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}

if (isset($_GET['page']) && $_GET['page'] <= $totalPages && $_GET['page'] > 0) {
  $page = (int)$_GET['page'];
} else {
  $page = 1;
}

$offset = ($page - 1) * $perPage;

$data = [];
try {
  $stmt = $pdo->prepare("SELECT * FROM entries ORDER BY `date` DESC, id DESC LIMIT :limit OFFSET :offset;");
  $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->execute();

  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}

?>

<h1 class="main-heading">Entries</h1>
<?php foreach ($data as $entry): ?>
  <div class="card">
    <?php if (!empty($entry['image'])): ?>
      <div class="card_image-container">
        <img
          class="card_image"
          src="uploads/<?php echo e($entry['image']); ?>" />
      </div>
    <?php endif; ?>
    <div class="card_desc-container">
      <?php
      $date = strtotime($entry['date']);
      ?>
      <div class="card_desc-time"><?php echo e(date('d/m/Y', $date)); ?></div>
      <h2 class="card_desc-heading"><?php echo e($entry['title']); ?></h2>
      <p class="card_desc-overview"><?php echo nl2br(e($entry['content'])); ?>
      </p>
    </div>
  </div>
<?php endforeach; ?>

<?php if ($totalPages > 1): ?>
  <div class="container">
    <ul class="pagination">
      <?php if ($page > 1): ?>
        <li class="pagination_li">
          <a class="pagination_link" href="index.php?<?php if ($page - 1 > 0) echo http_build_query(["page" => $page - 1]); ?>">⏴</a>
        </li>
      <?php endif; ?>
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="pagination_li">
          <a class="pagination_link <?php if ($i == $page) echo e("pagination_link--active"); ?>" href="index.php?<?php echo http_build_query(["page" => $i]) ?>"><?php echo e($i); ?></a>
        </li>
      <?php endfor; ?>
      <?php if ($page < $totalPages - 1): ?>
        <li class="pagination_li">
          <a class="pagination_link" href="index.php?<?php echo http_build_query(["page" => $page + 1]); ?>">⏵</a>
        </li>
      <?php endif; ?>
    </ul>
  <?php endif; ?>



  <?php require __DIR__ . "/inc/footer.inc.php"; ?>