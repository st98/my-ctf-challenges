<?php
include 'util.php';
include 'config.php';

error_reporting(0);
session_start();

$method = (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');
$page = (string) ($_GET['page'] ?? 'index');
if (!in_array($page, ['index', 'create', 'insert', 'delete'])) {
  redirect('?page=index');
}

$message = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

if (in_array($page, ['insert', 'delete']) && !isset($_SESSION['database'])) {
  flash("Please create database first.");
}

if (isset($_SESSION['database'])) {
  $pdo = new PDO('sqlite:db/' . $_SESSION['database']);
  $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name <> '" . FLAG_TABLE . "' LIMIT 1;");
  $table_name = $stmt->fetch(PDO::FETCH_ASSOC)['name'];

  $stmt = $pdo->query("PRAGMA table_info(`{$table_name}`);");
  $column_names = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($page === 'insert' && $method === 'POST') {
  $values = $_POST['values'];
  $stmt = $pdo->prepare("INSERT INTO `{$table_name}` VALUES (?" . str_repeat(',?', count($column_names) - 1) . ")");
  $stmt->execute($values);
  redirect('?page=index');
}

if ($page === 'create' && $method === 'POST' && !isset($_SESSION['database'])) {
  if (!isset($_POST['table_name']) || !isset($_POST['columns'])) {
    flash('Parameters missing.');
  }

  $table_name = (string) $_POST['table_name'];
  $columns = $_POST['columns'];
  $filename = bin2hex(random_bytes(16)) . '.db';
  $pdo = new PDO('sqlite:db/' . $filename);

  if (!is_valid($table_name)) {
    flash('Table name contains dangerous characters.');
  }
  if (strlen($table_name) < 4 || 32 < strlen($table_name)) {
    flash('Table name must be 4-32 characters.');
  }
  if (count($columns) <= 0 || 10 < count($columns)) {
    flash('Number of columns is up to 10.');
  }

  $sql = "CREATE TABLE {$table_name} (";
  $sql .= "dummy1 TEXT, dummy2 TEXT";
  for ($i = 0; $i < count($columns); $i++) {
    $column = (string) ($columns[$i]['name'] ?? '');
    $type = (string) ($columns[$i]['type'] ?? '');

    if (!is_valid($column) || !is_valid($type)) {
      flash('Column name or type contains dangerous characters.');
    }
    if (strlen($column) < 1 || 32 < strlen($column) || strlen($type) < 1 || 32 < strlen($type)) {
      flash('Column name and type must be 1-32 characters.');
    }

    $sql .= ', ';
    $sql .= "`$column` $type";
  }
  $sql .= ');';

  $pdo->query('CREATE TABLE `' . FLAG_TABLE . '` (`' . FLAG_COLUMN . '` TEXT);');
  $pdo->query('INSERT INTO `' . FLAG_TABLE . '` VALUES ("' . FLAG . '");');
  $pdo->query($sql);

  $_SESSION['database'] = $filename;
  redirect('?page=index');
}

if ($page === 'delete') {
  $_SESSION = array();
  session_destroy();
  redirect('?page=index');
}

if ($page === 'index' && isset($_SESSION['database'])) {
  $stmt = $pdo->query("SELECT * FROM `{$table_name}`;");

  if ($stmt === FALSE) {
    $_SESSION = array();
    session_destroy();
    redirect('?page=index');
  }

  $result = $stmt->fetchAll(PDO::FETCH_NUM);
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <title>phpNantokaAdmin</title>
  </head>
  <body>
    <h1>phpNantokaAdmin</h1>
<?php if (!empty($message)) { ?>
    <div class="info">Message: <?= $message ?></div>
<?php } ?>
<?php if ($page === 'index') { ?>
<?php if (isset($_SESSION['database'])) { ?>
    <h2><?= e($table_name) ?> (<a href="?page=delete">Delete table</a>)</h2>
    <form action="?page=insert" method="POST">
      <table>
        <tr>
<?php for ($i = 0; $i < count($column_names); $i++) { ?>
          <th><?= e($column_names[$i]['name']) ?></th>
<?php } ?>
        </tr>
<?php for ($i = 0; $i < count($result); $i++) { ?>
        <tr>
<?php for ($j = 0; $j < count($result[$i]); $j++) { ?>
          <td><?= e($result[$i][$j]) ?></td>
<?php } ?>
        </tr>
<?php } ?>
        <tr>
<?php for ($i = 0; $i < count($column_names); $i++) { ?>
          <td><input type="text" name="values[]"></td>
<?php } ?>
        </tr>
      </table>
      <input type="submit" value="Insert values">
    </form>
<?php } else { ?>
    <h2>Create table</h2>
    <form action="?page=create" method="POST">
      <div id="info">
        <label>Table name (4-32 chars): <input type="text" name="table_name" id="table_name" value="neko"></label><br>
        <label>Number of your columns (<= 10): <input type="number" min="1" max="10" id="num" value="1"></label><br>
        <button id="next">Next</button> 
      </div>
      <div id="table" class="hidden">
        <table>
          <tr>
            <th>Name</th>
            <th>Type</th>
          </tr>
          <tr>
            <td>dummy1</td>
            <td>TEXT</td>
          </tr>
          <tr>
            <td>dummy2</td>
            <td>TEXT</td>
          </tr>
        </table>
        <input type="submit" value="Create table">
      </div>
    </form>
    <script>
    $('#next').on('click', () => {
      let num = parseInt($('#num').val(), 10);
      let len = $('#table_name').val().length;

      if (4 <= len && len <= 32 && 0 < num && num <= 10) {
        $('#info').addClass('hidden');
        $('#table').removeClass('hidden');

        for (let i = 0; i < num; i++) {
          $('#table table').append($(`
          <tr>
            <td><input type="text" name="columns[${i}][name]"></td>
            <td>
              <select name="columns[${i}][type]">
                <option value="INTEGER">INTEGER</option>
                <option value="REAL">REAL</option>
                <option value="TEXT">TEXT</option>
              </select>
            </td>
          </tr>`));
        }
      }

      return false;
    });
    </script>
<?php } ?>
<?php } ?>
  </body>
</html>