<?php
use Migrations\AbstractMigration;

class AddRecoveryHashToUsers extends AbstractMigration
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
        $table = $this->table('users');
        $table->addColumn('recovery_hash', 'char', [
            'default' => null,
            'limit' => 64,
            'null' => true,
        ]);
        $table->update();
    }
}
