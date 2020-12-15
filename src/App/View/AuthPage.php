<?php


namespace App\View;


class AuthPage extends TemplatePage
{
    public function render($options)
    {
        $options = $options + ['title' => 'Авторизация', 'error' => null];
        parent::renderHeader($options);
        extract($options);
        ?>
        <form method="POST" action="auth">
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Ошибка авторизации.</strong> <?= htmlspecialchars($error) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label for="formControlInput1">Имя пользователя</label>
                <input name="username" type="text" class="form-control" id="formControlInput1"
                    <?= (isset($options['itemsFormUsername']) && $itemsFormUsername) ? ('value="' . htmlspecialchars($itemsFormUsername) . '"') : '' ?>
                       placeholder="admin" required
                       maxlength="200">
            </div>
            <div class="form-group">
                <label for="formControlInput2">Пароль</label>
                <input name="password" type="password" class="form-control" id="formControlInput2"
                       required maxlength="200">
            </div>
            <button type="submit" class="btn btn-primary">Войти</button>
        </form>
        <?php
        parent::renderFooter();
    }
}