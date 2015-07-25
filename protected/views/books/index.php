<?php
/**
 * @var int $author_id
 * @var Author[] $authors
 * @var string $name
 * @var string[] $date
 * @var ActiveDataProvider $booksDp
 *
 * @var Book $book
 */
use app\models\Author;

// Defaults.
$author_id = !empty($author_id) ? $author_id : '';
$authors = !empty($authors) ? $authors : [];
$name = !empty($name) ? $name : '';
$date = !empty($date) ? $date : [];
$date['to'] = !empty($date['to']) ? $date['to'] : '';
$date['from'] = !empty($date['from']) ? $date['from'] : '';
$books = !empty($books) ? $books : [];

function isSelected($item, $value) {
    return !empty($value) && $value == $item->id;
}
?>
<?= \yii\jui\DatePicker::widget([
   'name' => 'date_of_birth',
   'language' => 'en-GB',
   'dateFormat' => 'yyyy-MM-dd',
   'options' => [
      'changeMonth' => true,
      'changeYear' => true,
      'yearRange' => '1996:2015',
      'showOn' => 'button',
      'buttonImage' => 'images/calendar.gif',
      'buttonImageOnly' => true,
      'buttonText' => 'Select date'
    ],
]) ?>
<?= \himiklab\colorbox\Colorbox::widget([
    'targets' => [
        'a.colorbox' => [
            'maxWidth' => 800,
            'maxHeight' => 600,
        ],
    ],
    'coreStyle' => 2
]) ?>
<form>
    <select name="author_id">
        <!-- <option disabled="disabled" <?= array_filter($authors, function($item) use($author_id) { return isSelected($item, $author_id); }) ? '' : 'selected="selected"' ?>>Выберите автора</option> -->
        <option <?= array_filter($authors, function($item) use($author_id) { return isSelected($item, $author_id); }) ? '' : 'selected="selected"' ?>>Выберите автора</option>
        <?php foreach ($authors as $author): ?>
            <option value="<?= $author->id ?>" <?= isSelected($author, $author_id) ? 'selected="selected"' : '' ?>><?= $author->getName(Author::NAME_FORMAT_LF) ?></option>
        <?php endforeach ?>
    </select>
    <input type="text" name="name" placeholder="Название книги" value="<?= $name ?>"/>
    <fieldset>
        <div>Дата выхода книги:</div>
        <input type="text" name="date[from]" value="<?= $date['from'] ?>"/> до <input type="text" name="date[to]" value="<?= $date['to'] ?>"/>
    </fieldset>
    <input type="submit" value="Искать"/>
</form>
<table>
    <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Превью</th>
        <th>Автор</th>
        <th>Дата выхода книги</th>
        <th>Дата добавления</th>
        <th>Кнопки действий</th>
    </tr>
    <?php foreach ($booksDp->getModels() as $book): ?>
    <tr>
        <td><?= $book->id ?></td>
        <td><?= $book->name ?></td>
        <td>
            <a href="<?= $book->preview ?>" class="colorbox">
                <img src="<?= $book->preview ?>"/>
            </a>
        </td>
        <td><?= $book->getAuthor()->one()->getName(Author::NAME_FORMAT_FL) ?></td>
        <td><?= \Yii::$app->formatter->asDate($book->date) ?></td>
        <td><?= \Carbon\Carbon::createFromTimeStamp($book->date_create)->diffForHumans(); ?></td>
        <td>
            <ul>
                <li><a href="">[ред]</a></li>
                <li><a href="">[просм]</a></li>
                <li><a href="">[удл]</a></li>
            </ul>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?= \yii\widgets\LinkPager::widget(['pagination'=>$booksDp->pagination]); ?>