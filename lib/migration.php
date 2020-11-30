<?php


namespace Adv\Migrations;


use Adv\Migrations;

class Migration
{
    protected $users;
    protected $disk;

    public function __construct()
    {
        $this->users = Includer::getMigrationUsers();
        $this->disk = new Entity\Disk;
    }

    public function run()
    {
        foreach ($this->users as $newUser => $oldUser) {
            Entity\Tasks::updateTasks($oldUser, $newUser);
            Entity\Messages\Forum::changeMessageAuthor($oldUser, $newUser);
            $this->disk->setRightsToDiskObjects($oldUser, $newUser);
        }
    }
}