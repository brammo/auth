<?php
/**
 * Flash success element
 * 
 * @var \Cake\View\View $this
 * @var array $params
 * @var string $message
 */
?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= h($message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
