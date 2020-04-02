<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Roles extends AbstractMigration
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
            ->removeColumn('can_edit')
            ->update();

        $this->table('stundenplan')
            ->changeColumn('note', 'text', [
                'default' => null,
                'length' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
        $this->table('roles')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('alias', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addIndex(
                [
                    'alias',
                ],
                ['unique' => true]
            )
            ->create();

        $this->table('roles')->insert([
            [
                'id' => '1',
                'name' => 'Newbie',
                'alias' => 'guest'
            ],
            [
                'id' => '2',
                'name' => 'Benutzer',
                'alias' => 'user'
            ],
            [
                'id' => '3',
                'name' => 'Admin',
                'alias' => 'admin'
            ],
        ])->saveData();

        $this->table('users')
            ->addColumn('role_id', 'integer', [
                'after' => 'id',
                'default' => '1',
                'length' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'role_id',
                ],
                [
                    'name' => 'FK_users_roles',
                ]
            )
            ->update();

        $this->table('stundenplan')
            ->addColumn('loggedInNote', 'text', [
                'after' => 'info_for_db',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('users')
            ->addForeignKey(
                'role_id',
                'roles',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'RESTRICT',
                ]
            )
            ->update();

        $this->query('UPDATE users SET role_id = 3 WHERE email = "contact@rindula.de"');
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
            ->dropForeignKey(
                'role_id'
            )->save();

        $this->table('users')
            ->removeIndexByName('FK_users_roles')
            ->update();

        $this->table('users')
            ->addColumn('can_edit', 'boolean', [
                'after' => 'enabled',
                'default' => '0',
                'length' => null,
                'null' => false,
            ])
            ->removeColumn('role_id')
            ->update();

        $this->table('stundenplan')
            ->changeColumn('note', 'string', [
                'default' => null,
                'length' => 255,
                'null' => true,
            ])
            ->removeColumn('loggedInNote')
            ->update();

        $this->table('roles')->drop()->save();
    }
}
