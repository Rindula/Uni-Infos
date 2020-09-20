<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class StundenplanNotiz extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('stundenplan', ['id' => false, 'primary_key' => ['uid']]);

        $table->addColumn('uid', 'string', ['limit' => 255])
            ->addColumn('note', 'text')
            ->addColumn('info_for_db', 'string', ['limit' => 255])
            ->create();
    }
}
