<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * AddStatusToUsers migration
 *
 * Adds a status column to the users table to track user account status.
 * Valid statuses are: 'active', 'new', 'blocked'
 */
class AddStatusToUsers extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('users');

        $table->addColumn('status', 'string', [
            'default' => 'new',
            'limit' => 20,
            'null' => false,
            'after' => 'password',
        ]);

        $table->addIndex(['status'], [
            'name' => 'IDX_STATUS',
        ]);

        $table->update();
    }
}
