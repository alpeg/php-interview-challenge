<?php


namespace App\View;


use App\View;

abstract class TemplatePage extends View
{
public function renderHeader($options = [])
{
header('Content-Type: text/html; charset=utf-8');
extract($options + ['title' => '', 'description' => '', 'keywords' => '']);
?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?= htmlspecialchars(@$title) ?></title>
    <base href="<?= BASE ?>">
    <meta name="description" content="<?= htmlspecialchars(@$description) ?>"/>
    <meta name="keywords" content="<?= htmlspecialchars(@$keywords) ?>"/>
    <meta name="robots" content="index,follow"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
          integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
            integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
            crossorigin="anonymous"></script>
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <a class="navbar-brand" href="<?= BASE ?>">ToDo-список</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
            aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="<?= BASE ?>">Список</a>
            </li>
            <?php if (!$this->isAuthed()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE ?>auth">Вход</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE ?>auth/logout">👤 Выход</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<main role="main" class="container">
    <?php
    }

    public function renderFooter($options = [])
    {
    ?></main>
</body>
</html><?php
}

}