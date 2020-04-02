<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CanEdit extends AbstractMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function up()
    {

        $this->table('users')
            ->addColumn('can_edit', 'boolean', [
                'after' => 'enabled',
                'default' => '0',
                'length' => null,
                'null' => false,
            ])
            ->update();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     * @return void
     */
    public function down()
    {

        $this->table('users')
            ->removeColumn('can_edit')
            ->update();
    }
}
