<?php
session_start();
$_SESSION['nonce'] = substr(md5('7h15_15_5417!' . mt_rand(0, 0xffffffff)), 0, 16);
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="dist/semantic.min.css">
    <style>
    .main.container {
      margin-top: 5em;
    }
    </style>
    <title>Lorem ipsum - My Blog</title>
  </head>
  <body>
    <div class="ui fixed menu">
      <div class="ui container">
        <a href="/" class="header item">My Blog</a>
        <a href="/profile.php" class="item">Profile</a>
        <a href="/archive.php" class="item">Archive</a>
        <?php if (isset($_SESSION['username'])) { ?>
        <div class="right menu">
          <span class="item">Hello, <?= $_SESSION['username']; ?>!</span>
          <a href="/admin.php" class="item">Admin</a>
          <a href="/logout.php" class="item">Logout</a>
        </div>
        <?php } else { ?>
        <div class="right item">
          <div class="ui action input">
            <input type="text" id="username" placeholder="Username">
            <input type="password" id="password" placeholder="Password">
            <div id="submit" class="ui submit button">Login</div>
            <input type="hidden" id="nonce" value="<?= $_SESSION['nonce']; ?>">
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
    <div class="ui main text container">
      <h1 class="ui header">
        <a href="post.php?id=<?= $_GET['id'] ?>">Lorem ipsum</a>
        <div class="sub header">posted at 2018-02-01 <?= $_GET['id'] === '1' ? '09' : '21'; ?>:00:00 UTC</div>
      </h1>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque suscipit ipsum eu sapien euismod ullamcorper. In pellentesque tortor eu lacus ultricies tincidunt. Nullam tempor posuere metus a auctor. Vestibulum luctus porta sapien, nec pharetra ex vestibulum a. Integer feugiat mauris eget leo convallis pretium. Quisque sit amet diam porttitor, ornare arcu ut, condimentum arcu. Sed vitae lectus congue, eleifend augue id, venenatis nulla. Aliquam augue libero, condimentum ut mi eget, lobortis condimentum lorem. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Maecenas placerat felis turpis, sit amet blandit orci vulputate non. Ut tincidunt purus id nunc ultricies varius. Nam nec augue sollicitudin, mattis felis ac, mollis orci.</p>
      <p>Nulla facilisi. Phasellus feugiat enim sit amet tempus tempus. Nullam sed libero id dui tempus tristique sit amet vel felis. Mauris venenatis massa vitae tellus condimentum vulputate. Proin molestie turpis vitae dictum porttitor. Phasellus dapibus metus in libero faucibus aliquam. Sed sit amet gravida eros, pharetra scelerisque ligula. Proin velit tellus, bibendum et ex vitae, pharetra cursus tortor. Nullam nibh nibh, vehicula in quam ut, posuere venenatis enim. Nullam vitae gravida tortor. Maecenas lorem lorem, pharetra nec nibh at, ornare blandit justo. Vivamus at leo eu tellus tincidunt commodo non non justo. In hac habitasse platea dictumst.</p>
      <p>Morbi porta gravida dapibus. Nulla lobortis tristique sem, a venenatis diam ultrices quis. Donec non fringilla dui. Fusce tellus erat, ullamcorper eget facilisis eget, tincidunt vitae ex. Cras non leo arcu. Vestibulum quis venenatis augue. Etiam nec sagittis urna. Morbi mattis auctor ante, quis semper justo condimentum et. Sed at quam mauris. Nam enim erat, dapibus nec felis et, porta facilisis massa. Cras convallis dolor id gravida congue. Fusce interdum cursus nisi, ut rutrum est luctus vulputate. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc luctus ante ante, sit amet tincidunt orci ornare porttitor.</p>
      <p>Maecenas dignissim elementum risus nec interdum. Nullam dapibus massa vel pellentesque luctus. Aliquam pharetra mollis tortor, at vulputate neque molestie sit amet. In hac habitasse platea dictumst. Sed sed ultrices elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Curabitur luctus nibh nec eros semper pretium. Aenean imperdiet ornare dapibus. Vestibulum vel consectetur purus, at mollis felis. Fusce nec euismod nunc, sit amet fringilla tortor. Vestibulum sed pulvinar mi. Aliquam non augue turpis.</p>
      <p>Donec purus arcu, dapibus non suscipit vel, cursus nec sem. Curabitur vehicula ex eget odio cursus tempor. Phasellus dictum leo in leo vehicula, eu ullamcorper lacus faucibus. Fusce luctus orci facilisis, hendrerit libero nec, consequat sem. Cras sollicitudin viverra imperdiet. Sed et faucibus neque. Quisque ac condimentum dolor, sit amet malesuada augue. Integer id pellentesque arcu, nec viverra odio. Nam convallis lacus mi, eu efficitur purus vestibulum ut. Nullam sit amet velit sem.</p>
    </div>
    <script src="dist/bundle.js"></script>
    </script>
  </body>
</html>