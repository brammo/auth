<?php
/**
 * User login template
 * 
 * @var \Cake\View\View $this
 */

// Set the layout for the login page
$this->setLayout('Brammo/Auth.login');
?>
<div class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh">
    <div class="container-sm" style="max-width:25rem">
        <?= $this->Flash->render() ?>
        <div class="card">
            <div class="card-body p-4 p-lg-5">
                <?= $this->Flash->render() ?>
                <?= $this->Form->create(null, [
                    'class' => 'login-form',
                    'url' => ['controller' => 'User', 'action' => 'login']
                ]) ?>
                <div class="mb-3">
                    <?= $this->Form->control('email', [
                        'type' => 'email',
                        'label' => false,
                        'placeholder' => __('Email'),
                        'class' => 'form-control',
                        'required' => true,
                        'autocomplete' => 'email',
                        'autofocus' => true
                    ]) ?>
                </div>
                <div class="mb-3">
                    <?= $this->Form->control('password', [
                        'type' => 'password',
                        'label' => false,
                        'placeholder' => __('Password'),
                        'class' => 'form-control',
                        'required' => true,
                        'autocomplete' => 'current-password'
                    ]) ?>
                </div>
                <div class="mb-3">
                    <?= $this->Form->control('remember_me', [
                        'type' => 'checkbox',
                        'label' => __('Remember me'),
                        'class' => 'form-check-input',
                        'id' => 'remember_me'
                    ]) ?>
                </div>
                <div class="d-grid">
                    <?= $this->Form->button(__('Log in'), [
                            'type' => 'submit', 
                            'class' => 'btn btn-primary'
                        ]) 
                    ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
