<?php
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/db.php';

// --- Filters (GET) ---
$q = trim($_GET["q"] ?? "");
$category = (int)($_GET["category"] ?? 0);
$low = (int)($_GET["low"] ?? 0); // low-stock threshold; 0 means off

// --- Pagination ---
$page = max(1, (int)($_GET["page"] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

// --- Load categories for filter dropdown ---
$catStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// --- Build dynamic WHERE conditions ---
$where = [];
$params = [];

if ($q !== "") {
    $where[] = "(i.name LIKE ? OR i.description LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

if ($category > 0) {
    $where[] = "i.category_id = ?";
    $params[] = $category;
}

if ($low > 0) {
    $where[] = "i.quantity <= ?";
    $params[] = $low;
}

$whereSql = "";
if (count($where) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $where);
}

// --- Total count for pagination ---
$countSql = "
    SELECT COUNT(*) AS total
    FROM items i
    $whereSql
";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total = (int)$countStmt->fetch(PDO::FETCH_ASSOC)["total"];
$totalPages = max(1, (int)ceil($total / $perPage));

// Clamp page within range
if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

// --- Fetch items (with category name) ---
$listSql = "
    SELECT i.id, i.name, i.description, i.quantity, i.created_at,
           c.name AS category_name
    FROM items i
    JOIN categories c ON i.category_id = c.id
    $whereSql
    ORDER BY i.created_at DESC
    LIMIT $perPage OFFSET $offset
";
$listStmt = $pdo->prepare($listSql);
$listStmt->execute($params);
$items = $listStmt->fetchAll(PDO::FETCH_ASSOC);

// Helper for building query strings
function buildQuery(array $overrides = []): string {
    $merged = array_merge($_GET, $overrides);
    // remove empty values
    foreach ($merged as $k => $v) {
        if ($v === "" || $v === null) unset($merged[$k]);
    }
    return http_build_query($merged);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Items Dashboard</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial; margin:0; background:#f5f6f7;}
    .container{max-width:1100px; margin:0 auto; padding:24px;}
    .topbar{display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:14px; flex-wrap:wrap;}
    .title h1{margin:0; font-size:20px;}
    .title p{margin:4px 0 0; color:#555; font-size:13px;}
    .actions{display:flex; gap:10px; align-items:center; flex-wrap:wrap;}
    .btn{display:inline-block; padding:9px 12px; border-radius:10px; text-decoration:none; font-size:14px; border:1px solid #ddd; background:#fff; color:#111;}
    .btn.primary{background:#111; color:#fff; border-color:#111;}
    .card{background:#fff; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,.08); overflow:hidden;}
    table{width:100%; border-collapse:collapse;}
    th, td{padding:12px 12px; border-bottom:1px solid #eee; text-align:left; vertical-align:top;}
    th{font-size:12px; color:#444; background:#fafafa; letter-spacing:.02em; text-transform:uppercase;}
    td{font-size:14px;}
    .muted{color:#666; font-size:13px;}
    .row-actions{display:flex; gap:10px;}
    .link{color:#111; text-decoration:none; border-bottom:1px dotted #999;}
    .danger{color:#b00020;}
    .empty{padding:18px; color:#555;}
    .badge{display:inline-block; padding:3px 8px; border-radius:999px; background:#f0f0f0; font-size:12px;}
    .badge.low{background:#ffe9ec; color:#b00020;}
    .filters{margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;}
    .filters input,.filters select{padding:10px 12px; border:1px solid #ddd; border-radius:10px; font-size:14px; outline:none; background:#fff;}
    .filters .field{display:flex; flex-direction:column; gap:6px;}
    .filters label{font-size:12px; color:#444; text-transform:uppercase; letter-spacing:.02em;}
    .pager{display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 12px;}
    .pager .info{font-size:13px; color:#555;}
    .pager .nav{display:flex; gap:8px; flex-wrap:wrap;}
    .table-wrap{overflow:auto;}
  </style>
</head>
<body>
  <div class="container">

    <div class="topbar">
      <div class="title">
        <h1>Items Dashboard</h1>
        <p>Welcome, <strong><?= htmlspecialchars($_SESSION["user_name"] ?? "User") ?></strong></p>

        <!-- Filters -->
        <form class="filters" method="GET" action="/items/index.php">
          <div class="field">
            <label>Search</label>
            <input type="text" name="q" placeholder="Name or description..." value="<?= htmlspecialchars($q) ?>">
          </div>

          <div class="field">
            <label>Category</label>
            <select name="category">
              <option value="0">All</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= (int)$c["id"] ?>" <?= ($category === (int)$c["id"]) ? "selected" : "" ?>>
                  <?= htmlspecialchars($c["name"]) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label>Low Stock ≤</label>
            <input type="number" name="low" min="0" placeholder="e.g. 3" value="<?= $low > 0 ? (int)$low : "" ?>">
          </div>

          <div class="field" style="justify-content:flex-end;">
            <label>&nbsp;</label>
            <button class="btn primary" type="submit">Apply</button>
          </div>

          <div class="field" style="justify-content:flex-end;">
            <label>&nbsp;</label>
            <a class="btn" href="/items/index.php">Reset</a>
          </div>
        </form>
      </div>

      <div class="actions">
        <a class="btn" href="/users/index.php">Manage Users</a>
        <a class="btn" href="/categories/index.php">Manage Categories</a>
        <a class="btn primary" href="/items/create.php">+ Add Item</a>
        <a class="btn" href="/auth/logout.php">Logout</a>
      </div>
    </div>

    <div class="card">
      <?php if (count($items) === 0): ?>
        <div class="empty">
          No items found.
          <?php if ($q || $category || $low): ?>
            Try clearing filters.
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Date Added</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td><strong><?= htmlspecialchars($item["name"]) ?></strong></td>
                <td><span class="badge"><?= htmlspecialchars($item["category_name"]) ?></span></td>
                <td class="muted"><?= htmlspecialchars($item["description"] ?? "") ?></td>
                <td>
                  <?php if ((int)$item["quantity"] <= 3): ?>
                    <span class="badge low"><?= (int)$item["quantity"] ?> Low</span>
                  <?php else: ?>
                    <?= (int)$item["quantity"] ?>
                  <?php endif; ?>
                </td>
                <td class="muted"><?= htmlspecialchars($item["created_at"]) ?></td>
                <td>
                  <div class="row-actions">
                    <a class="link" href="/items/edit.php?id=<?= (int)$item["id"] ?>">Edit</a>
                    <a class="link danger" href="/items/delete.php?id=<?= (int)$item["id"] ?>">Delete</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="pager">
          <div class="info">
            Showing <?= min($total, $offset + 1) ?>–<?= min($total, $offset + count($items)) ?> of <?= $total ?> item(s)
          </div>

          <div class="nav">
            <?php if ($page > 1): ?>
              <a class="btn" href="/items/index.php?<?= buildQuery(["page" => 1]) ?>">First</a>
              <a class="btn" href="/items/index.php?<?= buildQuery(["page" => $page - 1]) ?>">Prev</a>
            <?php endif; ?>

            <span class="btn" style="pointer-events:none; opacity:.75;">
              Page <?= $page ?> / <?= $totalPages ?>
            </span>

            <?php if ($page < $totalPages): ?>
              <a class="btn" href="/items/index.php?<?= buildQuery(["page" => $page + 1]) ?>">Next</a>
              <a class="btn" href="/items/index.php?<?= buildQuery(["page" => $totalPages]) ?>">Last</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>

  </div>
</body>
</html>
