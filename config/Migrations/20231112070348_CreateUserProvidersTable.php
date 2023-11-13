<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateUserProvidersTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('user_providers');
        $table->addColumn('user_id', 'integer')
            ->addColumn('provider_name', 'string')
            ->addColumn('provider_id', 'string')
            ->addTimestamps()
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
