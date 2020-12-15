<?php


namespace App\View;


use App\AppUtil;
use App\Model\TaskItem;
use App\View;

class ItemsView extends View
{
    const ITEM_ADDED_OK = 1;
    const ITEM_ADDED_EDIT = 2;
    const ITEM_ADDED_NOEDIT = 3;

    public function render($options)
    {
        $options = $options + ['items' => [], 'order' => '', 'self' => '', 'itemsPage' => 0, 'itemsEdit' => null, 'itemsAdded' => null];
        extract($options);
        /** @var TaskItem[] $items */
        ?>
        <?php if ($itemsAdded): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>
                <?php if ($itemsAdded == self::ITEM_ADDED_OK): ?>
                    Элемент списка успешно добавлен.
                <?php elseif ($itemsAdded == self::ITEM_ADDED_EDIT): ?>
                    Элемент списка успешно отредактирован.
                <?php elseif ($itemsAdded == self::ITEM_ADDED_NOEDIT): ?>
                    Вы ничего не поменяли.
                <?php endif; ?>
            </strong>
            <?php if ($itemsAdded == 2): ?>
                Ему добавлена красивая иконка ✍️️.
            <?php endif; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

        <table class="table table-striped table-sm">
        <thead>
        <tr>
            <th></th>
            <th class="text-nowrap">Пользователь
                <a class="btn <?= $order === 'username_asc' ? 'btn-primary' : 'btn-light' ?> btn-sm"
                   href="<?= BASE ?>tasks/order/username_asc">🔼</a>
                <a class="btn <?= $order === 'username_desc' ? 'btn-primary' : 'btn-light' ?> btn-sm"
                   href="<?= BASE ?>tasks/order/username_desc">🔽</a>
            </th>
            <th class="text-nowrap">E-mail
                <a class="btn <?= $order === 'email_asc' ? 'btn-primary' : 'btn-light' ?> btn-sm"
                   href="<?= BASE ?>tasks/order/email_asc">🔼</a>
                <a class="btn <?= $order === 'email_desc' ? 'btn-primary' : 'btn-light' ?> btn-sm"
                   href="<?= BASE ?>tasks/order/email_desc">🔽</a>
            </th>
            <th class="text-nowrap">Текст
                <a class="btn <?= $order === 'text_asc' ? 'btn-primary' : 'btn-light' ?> btn-sm"
                   href="<?= BASE ?>tasks/order/text_asc">🔼</a>
                <a class="btn <?= $order === 'text_desc' ? 'btn-primary' : 'btn-light' ?> btn-sm"
                   href="<?= BASE ?>tasks/order/text_desc">🔽</a>
            </th>
            <?php if ($this->isAdmin()): ?>
                <th></th><?php endif; ?>
        </tr>
        </thead>
        <tbody><?php
        foreach ($items as $item) {
            ?>
            <tr>
                <td class="text-nowrap">
                    <?= $item->getComplete() ? '☑️' : '🔲' ?>
                    <abbr title="Отредактировано администратором"><?= $item->getEdited() ? '✍️️' : '' ?></abbr>
                </td>
                <td><?= htmlspecialchars($item->getUsername()) ?></td>
                <td><?= htmlspecialchars($item->getEmail()) ?></td>
                <td>
                    <?php if ($itemsEdit === $item->getId() && $this->isAdmin()): ?>

                        <form action="tasks/edit/<?= $item->getId() ?>?next=<?= htmlspecialchars(rawurlencode($self . '?page=' . ($itemsPage + 1))) ?>"
                              method="POST" class="form-inline">
                            <input name="task[text]" type="text" class="form-control form-control-sm mb-2 mr-sm-2"
                                   placeholder="" required maxlength="200"
                                   value="<?= htmlspecialchars($item->getText()) ?>">
                            <button type="submit" class="btn btn-primary mb-2 btn-sm">💾</button>
                            <a href="<?= $self . '?page=' . ($itemsPage + 1) ?>"
                               class="btn btn-warning mb-2 btn-sm">🚫</a>
                        </form>
                    <?php else: ?>
                        <?= htmlspecialchars($item->getText()) ?>
                    <?php endif; ?>
                </td>
                <?php if ($this->isAdmin()): ?>
                    <td class="text-nowrap">
                    <?php if (!$item->getComplete()): ?>
                        <a title="Отметить завершенным" class="btn btn-success btn-sm"
                           href="<?= BASE ?>tasks/edit/<?= $item->getId() ?>/done?next=<?= htmlspecialchars(rawurlencode($self . '?page=' . ($itemsPage + 1))) ?>">
                            ✅
                        </a>
                    <?php endif; ?>
                    <a title="Редактировать описание" class="btn btn-primary btn-sm"
                       href="<?= $self . '?page=' . ($itemsPage + 1) . '&edit=' . $item->getId() ?>">
                        ✏️
                    </a>
                    </td><?php endif; ?>
            </tr>
            <?php
        }
        ?></tbody><?php

        if (isset($options['itemsPage']) && isset($options['itemsPageMax']) && $itemsPageMax > 0): ?>
            <tfoot>
        <tr>
            <td colspan="4">
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php foreach (AppUtil::pageView($itemsPage, $itemsPageMax) as $p): ?>
                            <li class="page-item<?= $p['active'] ? ' active' : '' ?>">
                                <a class="page-link" href="<?= $self ?>?page=<?= $p['page'] ?>"><?= $p['text'] ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </td>
        </tr>
            </tfoot><?php
        endif;

        if (isset($options['itemsPage']) && isset($options['itemsPageMax']) && $itemsPage === $itemsPageMax):
            ?>
            <tfoot>
        <tr>
            <td colspan="4" class="text-center">
                <small class="text-muted">
                    <?= count($items) > 0 ? 'Вы достигли конца списка.' : 'Элементов нет' ?>
                </small>
            </td>
        </tr>
            </tfoot><?php
        endif;

        ?></table><?php

        if (isset($options['itemsAddForm']) && $itemsAddForm):
            ?>
            <form method="POST" action="tasks/add">
            <div class="form-group">
                <label for="formControlInput1">Имя пользователя</label>
                <input name="task[username]" type="text" class="form-control" id="formControlInput1"
                    <?= (isset($options['itemsFormUsername']) && $itemsFormUsername) ? ('value="' . htmlspecialchars($itemsFormUsername) . '"') : '' ?>
                       placeholder="Александр" required
                       maxlength="200">
            </div>
            <div class="form-group">
                <label for="formControlInput2">E-mail</label>
                <input name="task[email]" type="email" class="form-control" id="formControlInput2"
                    <?= (isset($options['itemsFormEmail']) && $itemsFormEmail) ? ('value="' . htmlspecialchars($itemsFormEmail) . '"') : '' ?>
                       placeholder="alexander@example.com"
                       required maxlength="200">
            </div>
            <div class="form-group">
                <label for="formControlInput3">Описание задачи</label>
                <input name="task[text]" type="text" class="form-control" id="formControlInput3" placeholder=""
                       required
                       maxlength="200">
            </div>
            <button type="submit" class="btn btn-primary">Добавить задачу</button>
            </form><?php
        endif;

    }
}